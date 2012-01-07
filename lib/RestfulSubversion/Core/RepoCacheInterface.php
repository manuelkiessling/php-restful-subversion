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
use RestfulSubversion\Logger\LoggableInterface;
use RestfulSubversion\Logger\LoggerInterface;

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
interface RepoCacheInterface
{
    /**
     * @param \PDO $dbHandler
     */
    public function __construct(\PDO $dbHandler);
    
    /**
     * Delete all data in the repository cache database and rebuild its structure 
     * @return void
     */
    public function resetCache();

    /**
     * @throws RepoCacheException
     * @param Changeset $changeset
     * @return void
     */
    public function addChangeset(Changeset $changeset);
    
    public function addRepoFile(RepoFile $file);
    
    /**
     * Get file and content for a given revision
     * 
     * Gets the content of the file from the last revision equal or below the given revision
     * 
     * @param Revision $revision
     * @param RepoPath $path
     * @return null|RepoFile
     */
    public function getRepoFileForRevisionAndPath(Revision $revision, RepoPath $path);
    
    /**
     * Get files for a given revision and list of paths
     * 
     * Gets the contents of the files from the last revision equal or below the given revision, for the given list of paths
     * 
     * @param Revision $revision
     * @param Array $paths Array of RepoPath objects
     * @return null|RepoFile
     */
    public function getRepoFilesForRevisionAndPaths(Revision $revision, Array $paths);

    /**
     * @return bool|Revision false if the repository cache is empty, highest saved Revision otherwise
     */
    public function getHighestRevision();

    /**
     * @param Revision $revision
     * @return null|Changeset null if no Changeset found for this Revision, and the matching Changeset if found
     */
    public function getChangesetForRevision(Revision $revision);

    /**
     * @param string $order ascending|descending
     * @param null|int $startAtRevision
     * @param null|int $limit
     * @return array Array of Changesets
     */
    public function getChangesets($order = 'ascending', $startAtRevision = null, $limit = null);
    
    /**
     * @param string $string String to search for
     * @param string $order 'ascending' or 'descending'
     * @param null|int $limit Limits how many results are returned, unlimited if null
     * @return array
     */
    public function getChangesetsWithPathEndingOn($string, $order = 'ascending', $limit = null);

    /**
     * @param $text Text to search for in commit messages
     * @param string $order
     * @param null $limit
     * @return array Array of changesets found
     */
    public function getChangesetsWithMessageContainingText($text, $order = 'ascending', $limit = null);
}
