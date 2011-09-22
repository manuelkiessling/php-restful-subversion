<?php

/**
 * PHPRestfulSubversion
 *
 * Copyright (c) 2011, Manuel Kiessling <manuel@kiessling.net>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *   * Neither the name of Manuel Kiessling nor the names of its contributors
 *     may be used to endorse or promote products derived from this software
 *     without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   VersionControl
 * @package    RestfulSubversion
 * @subpackage Core
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2011 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPRestfulSubversion
 */

namespace RestfulSubversion\Core;

/**
 * Class representing the cache of a RestfulSubversion_Repo SVN repository
 *
 * @category   VersionControl
 * @package    RestfulSubversion
 * @subpackage Core
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2011 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPRestfulSubversion
 * @uses       Changeset
 * @uses       Revision
 * @uses       RepoPath
 * @uses       RepoCacheException
 */
class RepoCache
{
    protected $dbHandler = null;

    protected function setupDatabaseIfNecessary()
    {
        $result = $this->dbHandler->query('SELECT revision FROM revisions LIMIT 1');
        if ($result === false) { // Database is not yet created
            $this->resetCache();
        }
    }

    /**
     * @param \PDO $dbHandler
     */
    public function __construct(\PDO $dbHandler)
    {
        $this->dbHandler = $dbHandler;
        $this->setupDatabaseIfNecessary();
    }

    /**
     * Delete all data in the repository cache database and rebuild its structure 
     * @return void
     */
    public function resetCache()
    {
        $queries = array();

        $queries[] = 'DROP TABLE IF EXISTS revisions;';
        $queries[] = 'CREATE TABLE revisions(revision INTEGER PRIMARY KEY NOT null, author TEXT(64), datetime DATETIME, message TEXT(2048));';

        $queries[] = 'CREATE INDEX r_revision ON revisions(revision);';
        $queries[] = 'CREATE INDEX r_author ON revisions(author);';
        $queries[] = 'CREATE INDEX r_message ON revisions(message);';
        $queries[] = 'CREATE INDEX r_datetime ON revisions(date, time);';

        $queries[] = 'DROP TABLE IF EXISTS pathoperations;';
        $queries[] = 'CREATE TABLE pathoperations (id INTEGER PRIMARY KEY, revision INTEGER NOT null, action TEXT(1), path TEXT(512), revertedpath TEXT(512), copyfrompath TEXT(512), copyfromrev INTEGER, FOREIGN KEY(revision) REFERENCES revisions(revision));';

        $queries[] = 'CREATE INDEX p_revision ON pathoperations(revision);';
        $queries[] = 'CREATE INDEX p_path ON pathoperations(path);';
        $queries[] = 'CREATE INDEX p_revertedpath ON pathoperations(revertedpath);';

        foreach ($queries as $query) {
            $this->dbHandler->exec($query);
        }
    }

    /**
     * @throws RepoCacheException
     * @param Changeset $changeset
     * @return void
     */
    public function addChangeset(Changeset $changeset)
    {
        $preparedStatement = $this->dbHandler->prepare('INSERT INTO revisions (revision, author, datetime, message) VALUES (?, ?, ?, ?)');

        $successful = $preparedStatement->execute(array($changeset->getRevision()->getAsString(),
                                                       $changeset->getAuthor(),
                                                       $changeset->getDateTime(),
                                                       $changeset->getMessage()));
        if (!$successful) {
            throw new RepoCacheException('Couldn\'t insert changeset into cache: '.print_r($changeset, true));
        }

        $pathOperations = $changeset->getPathOperations();
        foreach ($pathOperations as $pathOperation) {
            $preparedStatement = $this->dbHandler->prepare('INSERT INTO pathoperations (revision, action, path, revertedpath, copyfrompath, copyfromrev) VALUES (?, ?, ?, ?, ?, ?)');
            $preparedStatement->execute(array($changeset->getRevision()->getAsString(),
                                             $pathOperation['action'],
                                             $pathOperation['path']->getAsString(),
                                             strrev($pathOperation['path']->getAsString()),
                                             (array_key_exists('copyfromPath', $pathOperation))
                                                     ? $pathOperation['copyfromPath']->getAsString() : '',
                                             (array_key_exists('copyfromRev', $pathOperation))
                                                     ? $pathOperation['copyfromRev']->getAsString() : 0));
        }
    }

    /**
     * @return bool|Revision false if the repository cache is empty, highest saved Revision otherwise
     */
    public function getHighestRevision()
    {
        foreach ($this->dbHandler->query('SELECT revision
                                    FROM revisions
                                    ORDER BY revision DESC
                                    LIMIT 1')
            as $row) {
            return new Revision($row['revision']);
        }
        return false;
    }

    /**
     * @param Revision $revision
     * @return null|Changeset null if no Changeset found for this Revision, and the matching Changeset if found
     */
    public function getChangesetForRevision(Revision $revision)
    {
        $changeset = new Changeset($revision);

        $preparedStatement = $this->dbHandler->prepare('SELECT author, datetime, message FROM revisions WHERE revision = ?');
        $preparedStatement->execute(array($revision->getAsString()));

        $rows = $preparedStatement->fetchAll();
        if (sizeof($rows) == 0) return null;
        foreach ($rows as $row) {
            $changeset->setAuthor($row['author']);
            $changeset->setDateTime($row['datetime']);
            $changeset->setMessage($row['message']);
        }

        $preparedStatement = $this->dbHandler->prepare('SELECT action, path, copyfrompath, copyfromrev FROM pathoperations WHERE revision = ?');
        $preparedStatement->execute(array($revision->getAsString()));

        $rows = $preparedStatement->fetchAll();
        foreach ($rows as $row) {
            $changeset->addPathOperation($row['action'],
                                         new RepoPath($row['path']),
                                         ($row['copyfrompath'] != '')
                                                 ? new RepoPath($row['copyfrompath']) : null,
                                         ($row['copyfromrev'] != 0)
                                                 ? new Revision($row['copyfromrev']) : null);
        }

        return $changeset;
    }

    public function getChangesets($order = 'ascending', $startAtRevision = null, $limit = null)
    {
        $orderClause = 'ASC';
        $revisionStartClause = '>=';
        if ($order == 'descending') {
            $orderClause = 'DESC';
            $revisionStartClause = '<=';
            if ($startAtRevision === null) {
                $startAtRevision = 2147483647; // see http://stackoverflow.com/questions/816523/what-is-the-maximum-revision-number-supported-by-svn/816529#816529
            }
        } else {
            if ($startAtRevision === null) {
                $startAtRevision = 1;
            }
        }
        
        $limitClause = '';
        if ($limit !== null) {
            $limitClause = ' LIMIT '.$limit;
        }
        
        $return = array();
        $preparedStatement = $this->dbHandler->prepare('SELECT revision
                                                          FROM revisions
                                                         WHERE revision '.$revisionStartClause.' ?
                                                      ORDER BY revision '.$orderClause.'
                                                       '.$limitClause);
        if ($preparedStatement->execute(array($startAtRevision))) {
            while ($row = $preparedStatement->fetch()) {
                $return[] = $this->getChangesetForRevision(new Revision($row['revision']));
            }
        }
        return $return;
    }
    
    /**
     * @param string $string String to search for
     * @param string $order 'ascending' or 'descending'
     * @param null|int $limit Limits how many results are returned, unlimited if null
     * @return array
     */
    public function getChangesetsWithPathEndingOn($string, $order = 'ascending', $limit = null)
    {
        if ($order === 'descending') {
            $order = 'DESC';
        } else {
            $order = 'ASC';
        }
        $limitClause = '';
        if (!is_null($limit)) {
            $limitClause = ' LIMIT ' . $this->dbHandler->quote((int)$limit);
        }
        $return = array();
        $preparedStatement = $this->dbHandler->prepare('SELECT revision
                                                          FROM pathoperations
                                                         WHERE revertedpath LIKE ?
                                                         GROUP BY revision
                                                         ORDER BY revision ' . $order . $limitClause);
        if ($preparedStatement->execute(array(strrev($string) . '%'))) {
            while ($row = $preparedStatement->fetch()) {
                $return[] = $this->getChangesetForRevision(new Revision($row['revision']));
            }
        }
        return $return;
    }

    /**
     * @param $text Text to search for in commit messages
     * @param string $order
     * @param null $limit
     * @return array Array of changesets found
     */
    public function getChangesetsWithMessageContainingText($text, $order = 'ascending', $limit = null)
    {
        if ((string)$text === '') return array();
        if ($order === 'descending') {
            $order = 'DESC';
        } else {
            $order = 'ASC';
        }
        $limitClause = '';
        if (!is_null($limit)) {
            $limitClause = ' LIMIT ' . $this->dbHandler->quote((int)$limit);
        }
        $return = array();
        $preparedStatement = $this->dbHandler->prepare('SELECT revision
                                             FROM revisions
                                            WHERE message LIKE ?
                                         ORDER BY revision ' . $order . $limitClause);
        if ($preparedStatement->execute(array('%' . $text . '%'))) {
            while ($row = $preparedStatement->fetch()) {
                $return[] = $this->getChangesetForRevision(new Revision($row['revision']));
            }
        }
        return $return;
    }
}

/**
 * Exception for errors in RepoCache
 *
 * @category   VersionControl
 * @package    RestfulSubversion
 * @subpackage Core
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2011 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPRestfulSubversion
 */
class RepoCacheException extends \Exception {}
