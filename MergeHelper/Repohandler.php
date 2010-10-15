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
 * Class implementing a Mediator pattern to allow effective use of the library
 *
 * @category   VersionControl
 * @package    PHPMergeHelper
 * @subpackage Repository
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2010 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPMergeHelper
 * @uses       MergeHelper_Repo
 * @uses       MergeHelper_Revision
 * @uses       MergeHelper_RepoPath
 * @uses       MergeHelper_RepoCommandLog
 * @uses       MergeHelper_RepoCommandMerge
 */
class MergeHelper_Repohandler {

	/**
	 * @return array Array of MergeHelper_Revision objects
	 */
	public static function aoGetRevisionsForString(MergeHelper_Repo $oRepo, $sString, $bUseCache) {

		if ($sString === '') return array();
		if ($bUseCache) {
			$oRepo->enableCache();
		} else {
			$oRepo->disableCache();
		}
		$oLogCommand = new MergeHelper_RepoCommandLog($oRepo, new MergeHelper_CommandLineFactory);
		return $oLogCommand->aoGetRevisionsWithMessageContainingText($sString);

	}

	public static function aoGetRevisionsInRange(MergeHelper_Repo $oRepo, $sRangeStart, $sRangeEnd) {
		$oLogCommand = new MergeHelper_RepoCommandLog($oRepo, new MergeHelper_CommandLineFactory);
		return $oLogCommand->aoGetRevisionsInRange($sRangeStart, $sRangeEnd);
	}

	public static function oGetCommonBasePathForFullPath(MergeHelper_Repo $oRepo, MergeHelper_RepoPath $oPath) {

		$aoSourcePaths = $oRepo->aoGetSourcePaths();
		$aoSourcePaths[] = $oRepo->oGetTargetPath();
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

	public static function oGetCommonBasePathForRevision(MergeHelper_Repo $oRepo, MergeHelper_Revision $oRevision) {

		$aoPaths = self::aoGetPathsForRevisions($oRepo, array($oRevision));
		if (!isset($aoPaths[0])) return NULL;
		return self::oGetCommonBasePathForFullPath($oRepo, $aoPaths[0]);

	}

	/**
	 * Returns the common path all paths on the given path will share
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
	public static function oGetCommonSourcePathForFullPath(MergeHelper_Repo $oRepo, MergeHelper_RepoPath $oPath) {

		$aoSourcePaths = $oRepo->aoGetSourcePaths();
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

	public static function oGetCommonSourcePathForRevision(MergeHelper_Repo $oRepo, MergeHelper_Revision $oRevision) {

		$aoPaths = self::aoGetPathsForRevisions($oRepo, array($oRevision));
		return self::oGetCommonSourcePathForFullPath($oRepo, $aoPaths[0]);

	}

	/**
	 * Check if a list of revisions share the same $oRepo source path
	 *
	 * @return bool TRUE if all $aoRevisions are on the one $oRepo source path, FALSE if not
	 */
	public static function bRevisionsAreInSameSourcePath(MergeHelper_Repo $oRepo, Array $aoRevisions) {

		if (sizeof($aoRevisions) === 0) return FALSE;
		$aoPaths = self::aoGetPathsForRevisions($oRepo, $aoRevisions);
		if (sizeof($aoPaths) === 0) return FALSE; // no paths, no matches
		$oSourcePath = self::oGetCommonSourcePathForFullPath($oRepo, $aoPaths[0]);
		if ($oSourcePath === NULL) return FALSE; // first path of first revision did not match any source path
		foreach ($aoPaths as $oPath) {
			if (mb_substr("$oPath", 0, mb_strlen("$oSourcePath")) !== "$oSourcePath") return FALSE;
		}
		return TRUE;
		
	}

	/**
	 * Get all file and directory paths for given list of revisions
	 *
	 * @return array Array of MergeHelper_RepoPath objects representing the paths of all elements of the given revisions
	 */
	public static function aoGetPathsForRevisions(MergeHelper_Repo $oRepo, Array $aoRevisions) {

		$oLogCommand = new MergeHelper_RepoCommandLog($oRepo, new MergeHelper_CommandLineFactory);
		foreach ($aoRevisions as $oRevision) {
			$oLogCommand->addRevision($oRevision);
		}
		return $oLogCommand->aoGetPaths();

	}

	public static function asGetMergeCommandlinesForRevisionsAndPaths(MergeHelper_Repo $oRepo, Array $aaRevisionsAndPaths, $bDryrun = FALSE, $bIsRollback = FALSE) {

		$oMergeCommand = new MergeHelper_RepoCommandMerge($oRepo);
		if ($bDryrun) $oMergeCommand->enableDryrun();

		foreach ($aaRevisionsAndPaths as $amRevisionAndPath) {
			$oRevision = $amRevisionAndPath[0];
			$oSourcePath = $amRevisionAndPath[1];
			$sTargetBasePath = $amRevisionAndPath[2];
			if ($amRevisionAndPath[3] === TRUE) $oRevision = new MergeHelper_Revision($oRevision->sGetNumberInverted());
			$sTargetPath = $sTargetBasePath . mb_substr($oSourcePath, mb_strlen(MergeHelper_Repohandler::oGetCommonBasePathForFullPath($oRepo, $oSourcePath)));
			$oMergeCommand->addMerge($oRevision, $oSourcePath, $sTargetPath, $bIsRollback);
		}

		return $oMergeCommand->asGetCommandlines();

	}
}
