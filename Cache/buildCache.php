#!/usr/bin/php
<?php

require_once('../MergeHelper/Bootstrap.php');

$sRepoUri = $argv[1];
$sRepoUsername = $argv[2];
$sRepoPassword = $argv[3];
$sCacheDbFilename = $argv[4];

if (empty($sRepoUri)) {
	echo "No repository URI given.\n";
	exit(1);
}

if (empty($sRepoUsername)) {
	echo "No repository username given.\n";
	exit(1);
}

if (empty($sRepoPassword)) {
	echo "No repository password given.\n";
	exit(1);
}

if (empty($sCacheDbFilename)) {
	echo "No cache db filename given.\n";
	exit(1);
}

$oRepo = new MergeHelper_Repo();
$oRepo->setType(MergeHelper_Repo::TYPE_SVN);
$oRepo->setLocation($sRepoUri);
$oRepo->setAuthinfo($sRepoUsername, $sRepoPassword);

$oRepoCache = new MergeHelper_RepoCache(new PDO('sqlite:'.$sCacheDbFilename, NULL, NULL));

$iHighestRevision = $oRepoCache->iGetHighestRevision();

if (!$iHighestRevision) {
	echo 'Database is empty, starting from scratch'."\n";
} else {
	echo 'Highest revision found in database: '.$iHighestRevision."\n";
}
$iCurrentRevision = $iHighestRevision + 1;

$bFinished = FALSE;
$oMergeHelper = new MergeHelper_Repohandler();

while (!$bFinished) {
	echo "\n";
	echo 'Revision '.$iCurrentRevision.":\n";

	$oRevision = new MergeHelper_Revision($iCurrentRevision);

	try {
		$aoPaths = MergeHelper_Repohandler::aoGetPathsForRevisions($oRepo, array($oRevision));
	} catch (MergeHelper_RepoCommandLogNoSuchRevisionException $e)  {
		echo "All revisions imported to cache.\n";
		exit(0);
	}
	if (sizeof($aoPaths) > 0) {
		$sPaths = array();
		foreach ($aoPaths as $oPath) {
			$sPaths[] = $oPath->sGetAsString();
		}
		$oRepoCache->addRevision($oRevision->sGetNumber(), '', $sPaths);
		$iCurrentRevision++;
	} else {
		echo 'Latest revision reached, terminating.'."\n";
		break;
	}
}
