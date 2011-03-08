<?php

/**
 * PHPMergeHelper
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
 * @package    MergeHelper
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2011 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPMergeHelper
 */

require_once realpath(dirname(__FILE__)) . '/MergeHelper/Helper/Bootstrap.php';

/**
 * Class implementing a Mediator pattern to allow effective use of the library
 *
 * @category   VersionControl
 * @package    MergeHelper
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2011 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPMergeHelper
 * @uses       MergeHelper_Core_Repo
 * @uses       MergeHelper_Core_Revision
 * @uses       MergeHelper_Core_RepoPath
 * @uses       MergeHelper_Core_RepoCommandLog
 * @uses       MergeHelper_Core_RepoCommandMerge
 */
class MergeHelper {

	protected $oRepo = NULL;
	protected $oRepoCache = NULL;

	public function __construct(MergeHelper_Core_Repo $oRepo, MergeHelper_Core_RepoCache $oRepoCache) {
		$this->oRepo = $oRepo;
		$this->oRepoCache = $oRepoCache;
	}

	public function oGetHighestRevisionInRepo() {
		$oCommandLineExecutor = MergeHelper_Core_CommandLineExecutor::oGetInstance();
		$oCommandLineBuilder = new MergeHelper_Core_CommandLineBuilder();
		$oLogInterpreter = new MergeHelper_Core_RepoLogInterpreter();

		$oCommandLog = new MergeHelper_Core_RepoCommandLog($this->oRepo, $oCommandLineBuilder);
		$oCommandLog->enableVerbose();
		$oCommandLog->enableXml();
		$oCommandLog->setRevision(new MergeHelper_Core_Revision('HEAD'));
		$sCommandline = $oCommandLog->sGetCommandline();
		$sLogOutput = $oCommandLineExecutor->sGetCommandResult($sCommandline);

		$aoChangesets = $oLogInterpreter->aoCreateChangesetsFromVerboseXml($sLogOutput);

		foreach ($aoChangesets as $oChangeset) {
			return $oChangeset->oGetRevision();
		}
	}

	public function oGetHighestRevisionInRepoCache() {
		return $this->oRepoCache->oGetHighestRevision();
	}

	public function bRepoAndRepoCacheAreInSync() {
		return ($this->oGetHighestRevisionInRepo() == $this->oGetHighestRevisionInRepoCache());
	}

	public function bPathIsOnAtLeastOneSourcePath(MergeHelper_Core_RepoPath $oPath) {
		return (!is_null($this->oGetCommonSourcePathForFullPath($oPath)));
	}

	public function oGetCommonSourcePathOfRevision(MergeHelper_Core_Revision $oRevision) {
		$aoPaths = $this->aoGetPathsForRevision($oRevision);
		$oExpectedCommonSourcePath = $this->oGetCommonSourcePathForFullPath($aoPaths[0]);
		if (is_null($oExpectedCommonSourcePath)) return NULL;
		foreach ($aoPaths as $oPath) {
			$oCommonSourcePath = $this->oGetCommonSourcePathForFullPath($oPath);
			if (is_null($oCommonSourcePath)) return NULL;
			if ($oExpectedCommonSourcePath->sGetAsString() != $oCommonSourcePath->sGetAsString()) {
				return NULL;
			}
		}
		return $oExpectedCommonSourcePath;
	}

	public function oGetCommonBasePathOfRevision(MergeHelper_Core_Revision $oRevision) {
		$aoPaths = $this->aoGetPathsForRevision($oRevision);
		$oExpectedCommonBasePath = $this->oGetCommonBasePathForFullPath($aoPaths[0]);
		if (is_null($oExpectedCommonBasePath)) return NULL;
		foreach ($aoPaths as $oPath) {
			$oCommonBasePath = $this->oGetCommonBasePathForFullPath($oPath);
			if (is_null($oCommonBasePath)) return NULL;
			if ($oExpectedCommonBasePath->sGetAsString() != $oCommonBasePath->sGetAsString()) {
				return NULL;
			}
		}
		return $oExpectedCommonBasePath;
	}

	public function bRevisionsAreOnSameSourcePath(Array $aoRevisions) {
		if (sizeof($aoRevisions) === 0) return FALSE;

		$aoPaths = array();
		foreach ($aoRevisions as $oRevision) {
			$aoPathsForRevision = $this->aoGetPathsForRevision($oRevision);
			foreach ($aoPathsForRevision as $oPath) {
				$aoPaths[] = $oPath;
			}
		}

		if (sizeof($aoPaths) === 0) return FALSE; // no paths, no matches

		$oSourcePath = $this->oGetCommonSourcePathForFullPath($aoPaths[0]);
		if ($oSourcePath === NULL) return FALSE; // first path of first revision did not match any source path

		foreach ($aoPaths as $oPath) {
			if (mb_substr("$oPath", 0, mb_strlen("$oSourcePath")) !== "$oSourcePath") return FALSE;
		}

		return TRUE;
	}

	public function oGetChangesetForRevision(MergeHelper_Core_Revision $oRevision) {
		return $this->oRepoCache->oGetChangesetForRevision($oRevision);
	}

	public function sGetMergeCommandlineForRevision(MergeHelper_Core_Revision $oRevision, $bDryrun = FALSE) {
		$oMergeCommand = new MergeHelper_Core_RepoCommandMerge($this->oRepo, new MergeHelper_Core_CommandLineBuilder());

		$oSourcePath = $this->oGetCommonBasePathOfRevision($oRevision);
		if (is_null($oSourcePath)) {
			throw new MergeHelper_Core_Exception();
		}
		$oMergeCommand->setRevision($oRevision);
		$oMergeCommand->setRepoPath($oSourcePath);
		$oMergeCommand->setWorkingCopyPath('.');

		if ($bDryrun) $oMergeCommand->enableDryrun();

		return $oMergeCommand->sGetCommandline();
	}

	public function sGetRollbackMergeCommandlineForRevision(MergeHelper_Core_Revision $oRevision, $bDryrun = FALSE) {
		$oMergeCommand = new MergeHelper_Core_RepoCommandMerge($this->oRepo, new MergeHelper_Core_CommandLineBuilder());

		$oSourcePath = $this->oGetCommonBasePathOfRevision($oRevision);
		if (is_null($oSourcePath)) {
			throw new MergeHelper_Core_Exception();
		}
		$oMergeCommand->setRevision($oRevision);
		$oMergeCommand->setRepoPath($oSourcePath);
		$oMergeCommand->setWorkingCopyPath('.');
		$oMergeCommand->enableRollback();

		if ($bDryrun) $oMergeCommand->enableDryrun();

		return $oMergeCommand->sGetCommandline();
	}

	public function aoGetRevisionsWithMessageContainingText($sText) {
		$aoRevisions = array();
		$aoChangesets = $this->oRepoCache->aoGetChangesetsWithMessageContainingText($sText);
		foreach ($aoChangesets as $oChangeset) {
			$aoRevisions[] = $oChangeset->oGetRevision();
		}
		return $aoRevisions;
	}

	public function aoGetRevisionsWithPathsEndingOn($sString) {
		$aoRevisions = array();
		$aoChangesets = $this->oRepoCache->aoGetChangesetsWithPathEndingOn($sString);
		foreach ($aoChangesets as $oChangeset) {
			$aoRevisions[] = $oChangeset->oGetRevision();
		}
		return $aoRevisions;
	}

	protected function aoGetPathsForRevision(MergeHelper_Core_Revision $oRevision) {
		$aoPaths = array();
		$oChangeset = $this->oRepoCache->oGetChangesetForRevision($oRevision);
		foreach ($oChangeset->aaGetPathOperations() as $aPathOperation) {
			$aoPaths[] = $aPathOperation['oPath'];
		}
		return $aoPaths;
	}

	protected function oGetCommonBasePathForFullPath(MergeHelper_Core_RepoPath $oPath) {
		$aoSourcePaths = $this->oRepo->aoGetSourcePaths();
		$aoSourcePaths[] = $this->oRepo->oGetTargetPath();

		foreach ($aoSourcePaths as $oSourcePath) {
			if (mb_substr("$oPath", 0, mb_strlen("$oSourcePath")) === "$oSourcePath") {
				// find next directory level name and add it
				$oPathWithoutSourcePath = new MergeHelper_Core_RepoPath(mb_substr("$oPath", mb_strlen("$oSourcePath")));
				$asPathWithoutSourcePathElements = explode('/', "$oPathWithoutSourcePath");
				$oReturn = new MergeHelper_Core_RepoPath("$oSourcePath".'/'.$asPathWithoutSourcePathElements[1]);
				return $oReturn;
			}
		}

		return NULL;
	}

	protected function oGetCommonSourcePathForFullPath(MergeHelper_Core_RepoPath $oPath) {
		$aoSourcePaths = $this->oRepo->aoGetSourcePaths();
		foreach ($aoSourcePaths as $oSourcePath) {
			if (mb_substr("$oPath", 0, mb_strlen("$oSourcePath")) === "$oSourcePath") {
				// find next directory level name and add it
				$oPathWithoutSourcePath = new MergeHelper_Core_RepoPath(mb_substr("$oPath", mb_strlen("$oSourcePath")));
				$asPathWithoutSourcePathElements = explode('/', "$oPathWithoutSourcePath");
				$oReturn = new MergeHelper_Core_RepoPath("$oSourcePath".'/'.$asPathWithoutSourcePathElements[1]);
				return $oReturn;
			}
		}
		return NULL;
	}

}

/**
 * Exception for a merge tried on a revision that doesn't have all files on the same source path
 *
 * @category   VersionControl
 * @package    MergeHelper
 * @subpackage Core
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2011 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPMergeHelper
 * @uses       MergeHelper_Core_Exception
 */
class MergeHelper_Core_CannotMergeRevisionWithMixedPathsCoreException extends MergeHelper_Core_Exception {};
