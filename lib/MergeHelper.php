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
 * @package    PHPMergeHelper
 * @subpackage Repository
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2011 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPMergeHelper
 */

require_once realpath(dirname(__FILE__)).'/MergeHelper/Bootstrap.php';

/**
 * Class implementing a Mediator pattern to allow effective use of the library
 *
 * @category   VersionControl
 * @package    PHPMergeHelper
 * @subpackage Repository
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2011 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPMergeHelper
 * @uses       MergeHelper_Repo
 * @uses       MergeHelper_Revision
 * @uses       MergeHelper_RepoPath
 * @uses       MergeHelper_RepoCommandLog
 * @uses       MergeHelper_RepoCommandMerge
 */
class MergeHelper {

	protected $oRepo = NULL;
	protected $oRepoCache = NULL;

	public function __construct(MergeHelper_Repo $oRepo, MergeHelper_RepoCache $oRepoCache) {
		$this->oRepo = $oRepo;
		$this->oRepoCache = $oRepoCache;
	}

	public function oGetHighestRevisionInRepo() {
		$oCommandLineExecutor = MergeHelper_CommandLineExecutor::oGetInstance();
		$oCommandLineBuilder = new MergeHelper_CommandLineBuilder();
		$oLogInterpreter = new MergeHelper_RepoLogInterpreter();

		$oCommandLog = new MergeHelper_RepoCommandLog($this->oRepo, $oCommandLineBuilder);
		$oCommandLog->enableVerbose();
		$oCommandLog->enableXml();
		$oCommandLog->addRevision(new MergeHelper_Revision('HEAD'));
		$aoCommandlines = $oCommandLog->asGetCommandlines();
		$sLogOutput = $oCommandLineExecutor->sGetCommandResult($aoCommandlines[0]);

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

	public function bPathIsOnAtLeastOneSourcePath(MergeHelper_RepoPath $oPath) {
		return (!is_null($this->oGetCommonSourcePathForFullPath($oPath)));
	}

	public function oGetCommonSourcePathOfRevision(MergeHelper_Revision $oRevision) {
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

	public function oGetCommonBasePathOfRevision(MergeHelper_Revision $oRevision) {
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

	/**
	 * Check if all files of a list of revisions share the same Repo source path
	 *
	 * @return bool TRUE if all paths of all $aoRevisions are on the same Repo source path, FALSE if not
	 */
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

	public function oGetChangesetForRevision(MergeHelper_Revision $oRevision) {
		return $this->oRepoCache->oGetChangesetForRevision($oRevision);
	}

	public function sGetMergeCommandlineForRevision(MergeHelper_Revision $oRevision, $bDryrun = FALSE) {
		$oMergeCommand = new MergeHelper_RepoCommandMerge($this->oRepo, new MergeHelper_CommandLineBuilder());

		$oSourcePath = $this->oGetCommonBasePathOfRevision($oRevision);
		if (is_null($oSourcePath)) {
			throw new MergeHelper_CannotMergeRevisionWithMixedPathsException();
		}
		$oMergeCommand->addMerge($oRevision, $oSourcePath, '.', FALSE);
		if ($bDryrun) $oMergeCommand->enableDryrun();

		$asCommandlines = $oMergeCommand->asGetCommandlines();
		foreach ($asCommandlines as $sCommandline) {
			return $sCommandline;
		}
	}

	public function sGetRollbackMergeCommandlineForRevision(MergeHelper_Revision $oRevision, $bDryrun = FALSE) {
		$oMergeCommand = new MergeHelper_RepoCommandMerge($this->oRepo, new MergeHelper_CommandLineBuilder());

		$oSourcePath = $this->oGetCommonBasePathOfRevision($oRevision);
		if (is_null($oSourcePath)) {
			throw new MergeHelper_CannotMergeRevisionWithMixedPathsException();
		}
		$oMergeCommand->addMerge($oRevision, $oSourcePath, '.', TRUE);
		if ($bDryrun) $oMergeCommand->enableDryrun();

		$asCommandlines = $oMergeCommand->asGetCommandlines();
		foreach ($asCommandlines as $sCommandline) {
			return $sCommandline;
		}
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

	/**
	 * Get all file and directory paths for a given revision
	 *
	 * @return array Array of MergeHelper_RepoPath objects representing the paths of all elements of the given revision
	 */
	protected function aoGetPathsForRevision(MergeHelper_Revision $oRevision) {
		$aoPaths = array();
		$oChangeset = $this->oRepoCache->oGetChangesetForRevision($oRevision);
		foreach ($oChangeset->aaGetPathOperations() as $aPathOperation) {
			$aoPaths[] = $aPathOperation['oPath'];
		}
		return $aoPaths;
	}

	protected function oGetCommonBasePathForFullPath(MergeHelper_RepoPath $oPath) {
		$aoSourcePaths = $this->oRepo->aoGetSourcePaths();
		$aoSourcePaths[] = $this->oRepo->oGetTargetPath();

		foreach ($aoSourcePaths as $oSourcePath) {
			if (mb_substr("$oPath", 0, mb_strlen("$oSourcePath")) === "$oSourcePath") {
				// find next directory level name and add it
				$oPathWithoutSourcePath = new MergeHelper_RepoPath(mb_substr("$oPath", mb_strlen("$oSourcePath")));
				$asPathWithoutSourcePathElements = explode('/', "$oPathWithoutSourcePath");
				$oReturn = new MergeHelper_RepoPath("$oSourcePath".'/'.$asPathWithoutSourcePathElements[1]);
				return $oReturn;
			}
		}

		return NULL;
	}

	/**
	 * Returns the source path all files on the given path would share
	 *
	 * Example: If the full path is
	 * <pre>/branches/_production/2010-04-15/cooperations/logo_acme_123x30.gif</pre>
	 * and one of the source paths is
	 * <pre>/branches/_production</pre>
	 * then the common source path is
	 * <pre>/branches/_production/2010-04-15</pre>
	 *
	 * @uses MergeHelper_Repo::aoGetSourcePaths()
	 * @uses MergeHelper_RepoPath
	 * @return MergeHelper_RepoPath|NULL MergeHelper_RepoPath if a common path could be found, NULL if none of the $oRepo source paths matched the given path
	 * @todo Source paths should know themselves at which level the common path starts, we currently assume sourcepath + 1
	 */
	protected function oGetCommonSourcePathForFullPath(MergeHelper_RepoPath $oPath) {
		$aoSourcePaths = $this->oRepo->aoGetSourcePaths();
		foreach ($aoSourcePaths as $oSourcePath) {
			if (mb_substr("$oPath", 0, mb_strlen("$oSourcePath")) === "$oSourcePath") {
				// find next directory level name and add it
				$oPathWithoutSourcePath = new MergeHelper_RepoPath(mb_substr("$oPath", mb_strlen("$oSourcePath")));
				$asPathWithoutSourcePathElements = explode('/', "$oPathWithoutSourcePath");
				$oReturn = new MergeHelper_RepoPath("$oSourcePath".'/'.$asPathWithoutSourcePathElements[1]);
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
 * @package    PHPMergeHelper
 * @subpackage Exception
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2011 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPMergeHelper
 * @uses       MergeHelper_Exception
 */
class MergeHelper_CannotMergeRevisionWithMixedPathsException extends MergeHelper_Exception {};
