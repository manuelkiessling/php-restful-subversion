<?php

/**
 * Facade for the other repo classes
 *
 * @package MergeHelper
 * @subpackage Repository
 */
class MergeHelper_Repohandler extends MergeHelper_Base {

	/**
	 * @return array Array of MergeHelper_Revision objects
	 */
	public static function aoGetRevisionsForString(MergeHelper_Repo $oRepo, $sString) {

		if ($sString === '') return array();
		$aoReturn = array();
		$oLogCommand = new MergeHelper_RepoCommandLog($oRepo);
		return $oLogCommand->aoGetRevisionsWithMessageContainingText($sString);

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
	 * <pre>/branches/my-hammer2/_production/2010-04-15/cooperationsbox/log_conrad_123x30_AT.gif</pre>
	 * and one of the source paths is
	 * <pre>/branches/my-hammer2/_production</pre>
	 * then the common source path is
	 * <pre>/branches/my-hammer2/_production/2010-04-15</pre>
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

		$oLogCommand = new MergeHelper_RepoCommandLog($oRepo);
		foreach ($aoRevisions as $oRevision) {
			$oLogCommand->addRevision($oRevision);
		}
		return $oLogCommand->aoGetPaths();

	}

	public static function asGetMergeCommandlinesForRevisionsAndPaths(MergeHelper_Repo $oRepo, Array $aaRevisionsAndPaths, $bDryrun = FALSE) {

		$oMergeCommand = new MergeHelper_RepoCommandMerge($oRepo);
		if ($bDryrun) $oMergeCommand->enableDryrun();

		foreach ($aaRevisionsAndPaths as $amRevisionAndPath) {
			$oRevision = $amRevisionAndPath[0];
			$oSourcePath = $amRevisionAndPath[1];
			$sTargetBasePath = $amRevisionAndPath[2];
			if ($amRevisionAndPath[3] === TRUE) $oRevision = new MergeHelper_Revision($oRevision->sGetNumberInverted());
			$sTargetPath = $sTargetBasePath . mb_substr($oSourcePath, mb_strlen(MergeHelper_Repohandler::oGetCommonBasePathForFullPath($oRepo, $oSourcePath)));
			$oMergeCommand->addMerge($oRevision, $oSourcePath, $sTargetPath);
		}

		return $oMergeCommand->asGetCommandlines();

	}
}
