<?php

  /**
   * PHPMergeHelper
   *
   * Copyright (c) 2010, Manuel Kiessling <manuel@kiessling.net>
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
   * @package    PHPMergeHelper
   * @subpackage Repository
   * @author     Manuel Kiessling <manuel@kiessling.net>
   * @copyright  2010 Manuel Kiessling <manuel@kiessling.net>
   * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
   * @link       http://manuelkiessling.github.com/PHPMergeHelper
   */

  /**
   * Class representing an existing SVN repository
   *
   * @category   VersionControl
   * @package    PHPMergeHelper
   * @subpackage Cache
   * @author     Manuel Kiessling <manuel@kiessling.net>
   * @copyright  2010 Manuel Kiessling <manuel@kiessling.net>
   * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
   * @link       http://manuelkiessling.github.com/PHPMergeHelper
   */
class MergeHelper_RepoCache {

	protected $oDb = NULL;

	public function __construct($oDb) {
		$this->oDb = $oDb;
		$this->setupDatabaseIfNecessary();
	}

	public function resetCache() {
		$asSql = array();

		$asSql[] = 'DROP TABLE IF EXISTS revisions;';
		$asSql[] = 'CREATE TABLE revisions(revision INTEGER PRIMARY KEY NOT NULL, author TEXT(64), datetime DATETIME, message TEXT(2048));';

		$asSql[] = 'CREATE INDEX author ON revisions(author);';
		$asSql[] = 'CREATE INDEX message ON revisions(message);';
		$asSql[] = 'CREATE INDEX datetime ON revisions(date, time);';

		$asSql[] = 'DROP TABLE IF EXISTS pathoperations;';
		$asSql[] = 'CREATE TABLE pathoperations (id INTEGER PRIMARY KEY, revision INTEGER NOT NULL, action TEXT(1), path TEXT(512), revertedpath TEXT(512), copyfrompath TEXT(512), copyfromrev INTEGER, FOREIGN KEY(revision) REFERENCES revisions(revision));';

		$asSql[] = 'CREATE INDEX path ON pathoperations(path);';
		$asSql[] = 'CREATE INDEX revertedpath ON pathoperations(revertedpath);';

		foreach ($asSql as $sSql) {
			$this->oDb->exec($sSql);
		}
	}

	public function addChangeset(MergeHelper_Changeset $oChangeset) {
		$oStatement = $this->oDb->prepare('INSERT INTO revisions (revision, author, datetime, message) VALUES (?, ?, ?, ?)');

		$bSuccessful = $oStatement->execute(array($oChangeset->oGetRevision()->sGetAsString(),
		                                          $oChangeset->sGetAuthor(),
		                                          $oChangeset->sGetDateTime(),
		                                          $oChangeset->sGetMessage()));
		if (!$bSuccessful) {
			throw new MergeHelper_RepoCacheRevisionAlreadyInCacheException();
		}

		$aaPathOperations = $oChangeset->aaGetPathOperations();
		foreach($aaPathOperations as $aPathOperation) {
			$oStatement = $this->oDb->prepare('INSERT INTO pathoperations (revision, action, path, revertedpath, copyfrompath, copyfromrev) VALUES (?, ?, ?, ?, ?, ?)');
			$oStatement->execute(array($oChangeset->oGetRevision()->sGetAsString(),
			                           $aPathOperation['sAction'],
			                           $aPathOperation['oPath']->sGetAsString(),
			                           strrev($aPathOperation['oPath']->sGetAsString()),
			                           (!is_null($aPathOperation['oCopyfromPath'])) ? $aPathOperation['oCopyfromPath']->sGetAsString() : '',
			                           (!is_null($aPathOperation['oCopyfromRev'])) ? $aPathOperation['oCopyfromRev']->sGetNumber() : 0));
		}
	}

	public function oGetHighestRevision() {
		foreach ($this->oDb->query('SELECT revision
		                            FROM revisions
		                            ORDER BY revision DESC
		                            LIMIT 1')
				 as $asRow) {
			return new MergeHelper_Revision($asRow['revision']);
		}
		return FALSE;
	}

	public function oGetChangesetForRevision(MergeHelper_Revision $oRevision) {
		$oChangeset = new MergeHelper_Changeset($oRevision);

		$oStatement = $this->oDb->prepare('SELECT author, datetime, message FROM revisions WHERE revision = ?');
		$oStatement->execute(array($oRevision->sGetAsString()));

		$oRows = $oStatement->fetchAll();
		foreach ($oRows as $asRow) {
			$oChangeset->setAuthor($asRow['author']);
			$oChangeset->setDateTime($asRow['datetime']);
			$oChangeset->setMessage($asRow['message']);
		}

		$oStatement = $this->oDb->prepare('SELECT action, path, copyfrompath, copyfromrev FROM pathoperations WHERE revision = ?');
		$oStatement->execute(array($oRevision->sGetAsString()));

		$oRows = $oStatement->fetchAll();
		foreach ($oRows as $asRow) {
			$oChangeset->addPathOperation($asRow['action'],
			                              new MergeHelper_RepoPath($asRow['path']),
			                              ($asRow['copyfrompath'] != '') ? new MergeHelper_RepoPath($asRow['copyfrompath']) : NULL,
			                              ($asRow['copyfromrev'] != 0) ? new MergeHelper_Revision($asRow['copyfromrev']) : NULL);
		}

		return $oChangeset;
	}

	public function aoGetChangesetsWithPathEndingOn($sString) {
		$asReturn = array();
		$oStatement = $this->oDb->prepare('SELECT revision
		                                     FROM pathoperations
		                                    WHERE revertedpath LIKE ?
		                                 GROUP BY revision
		                                 ORDER BY revision ASC');
		if ($oStatement->execute(array(strrev($sString).'%'))) {
			while ($asRow = $oStatement->fetch()) {
				$asReturn[] = $this->oGetChangesetForRevision(new MergeHelper_Revision($asRow['revision']));
			}
		}
		return $asReturn;
	}

	public function aoGetChangesetsWithMessageContainingText($sText) {
		if ((string)$sText === '') return array();
		$asReturn = array();
		$oStatement = $this->oDb->prepare('SELECT revision
		                                     FROM revisions
		                                    WHERE message LIKE ?
		                                 ORDER BY revision ASC');
		if ($oStatement->execute(array('%'.$sText.'%'))) {
			while ($asRow = $oStatement->fetch()) {
				$asReturn[] = $this->oGetChangesetForRevision(new MergeHelper_Revision($asRow['revision']));
			}
		}
		return $asReturn;
	}

	protected function setupDatabaseIfNecessary() {
		$oResult = $this->oDb->query('SELECT revision FROM revisions LIMIT 1');
		if ($oResult === FALSE) { // Database is not yet created
			$this->resetCache();
		}
	}
}
