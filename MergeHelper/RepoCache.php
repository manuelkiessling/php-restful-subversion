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

	protected $sDatabaseDirectory = NULL;
	protected $sDatabaseFile = NULL;
	protected $oDb = NULL;

	protected function oGetDb() {
		if (is_null($this->oDb)) {
			$this->oDb = new PDO('sqlite:'.$this->sDatabaseDirectory.'/'.$this->sDatabaseFile, NULL, NULL);

			$oResult = $this->oDb->query('SELECT revision FROM revisions LIMIT 1');
			if ($oResult === FALSE) { // Database is not yet created
				$asSql = array();

				$asSql[] = 'DROP TABLE IF EXISTS revisions;';
				$asSql[] = 'CREATE TABLE revisions(revision INTEGER PRIMARY KEY NOT NULL, author TEXT(64), date TEXT(10), time TEXT(8));';

				$asSql[] = 'CREATE INDEX author ON revisions(author);';
				$asSql[] = 'CREATE INDEX datetime ON revisions(date, time);';

				$asSql[] = 'DROP TABLE IF EXISTS paths;';
				$asSql[] = 'CREATE TABLE paths (id INTEGER PRIMARY KEY, revision INTEGER NOT NULL, type TEXT(2), path TEXT(512), revertedpath TEXT(512), FOREIGN KEY(revision) REFERENCES revisions(revision));';

				$asSql[] = 'CREATE INDEX path ON paths(path);';
				$asSql[] = 'CREATE INDEX revertedpath ON paths(revertedpath);';

				foreach ($asSql as $sSql) {
					$this->oDb->exec($sSql);
				}
			}
		}
		return $this->oDb;
	}

	public function setCacheLocation($sCacheLocation) {
		$this->sDatabaseDirectory = $sCacheLocation;
	}

	public function setCacheIdentifier($sIdentifier) {
		$this->sDatabaseFile = str_replace('/', '_', $sIdentifier);
		$this->sDatabaseFile = str_replace(':', '_', $this->sDatabaseFile);
		$this->sDatabaseFile = str_replace('.', '_', $this->sDatabaseFile);
	}

	public function clear() {
		$oDb = $this->oGetDb();
		$oDb->exec('DELETE FROM revisions');
		$oDb->exec('DELETE FROM paths');
	}

	public function addRevision($iRevision, $aPaths) {
		$oDb = $this->oGetDb();
		$oStatement = $oDb->prepare('INSERT INTO revisions (revision) VALUES (?)');
		$bSuccessful = $oStatement->execute(array($iRevision));
		if (!$bSuccessful) {
			throw new MergeHelper_RepoCacheRevisionAlreadyInCacheException();
		}
		foreach($aPaths as $sPath) {
			$oStatement = $oDb->prepare('INSERT INTO paths (revision, path, revertedpath) VALUES (?, ?, ?)');
			$oStatement->execute(array($iRevision, $sPath, strrev($sPath)));
		}
	}

	public function asGetPathsForRevision($iRevision) {
		$asReturn = array();
		$oDb = $this->oGetDb();
		$oStatement = $oDb->prepare('SELECT path FROM paths WHERE revision = ?');
		$oStatement->execute(array($iRevision));
		$oRows = $oStatement->fetchAll();
		foreach ($oRows as $asRow) {
			$asReturn[] = $asRow['path'];
		}
		return $asReturn;
	}

	public function aiGetRevisionsWithPathEndingOn($sString) {
		$asReturn = array();
		$oDb = $this->oGetDb();
		foreach ($oDb->query('SELECT revision
		                      FROM paths
		                      WHERE revertedpath LIKE "'.strrev($sString).'%" GROUP BY revision ORDER BY revision DESC')
		         as $asRow) {
			$asReturn[] = (int)$asRow['revision'];
		}
		return $asReturn;
	}

}
