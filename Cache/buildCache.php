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

$oCommandLineExecutor = MergeHelper_CommandLineExecutor::oGetInstance();
$oCommandLineBuilder = new MergeHelper_CommandLineBuilder();
$oLogInterpreter = new MergeHelper_RepoLogInterpreter();

$oRepoCache = new MergeHelper_RepoCache(new PDO('sqlite:'.$sCacheDbFilename, NULL, NULL));

$sHighestRevision = $oRepoCache->sGetHighestRevision();

if (!$sHighestRevision) {
	echo 'Database is empty, starting from scratch'."\n";
} else {
	echo 'Highest revision found in database: '.$sHighestRevision."\n";
}
$sCurrentRevision = (string)((int)$sHighestRevision + 1);

$bFinished = FALSE;

while (!$bFinished) {
	echo "\n";
	echo 'Revision '.$sCurrentRevision.":\n";

	$oRevision = new MergeHelper_Revision($sCurrentRevision);

	$oCommandLog = new MergeHelper_RepoCommandLog($oRepo, $oCommandLineBuilder);
	$oCommandLog->enableVerbose();
	$oCommandLog->enableXml();
	$oCommandLog->addRevision(new MergeHelper_Revision($sCurrentRevision));
	$aoCommandlines = $oCommandLog->asGetCommandlines();
	$sLogOutput = $oCommandLineExecutor->sGetCommandResult($aoCommandlines[0]);

	try { // TODO: We are only guessing here...
		$aoChangesets = $oLogInterpreter->aoCreateChangesetsFromVerboseXml($sLogOutput);
	} catch (Exception $e) {
		echo "All revisions imported to cache.\n";
		exit(0);
	}

	if (sizeof($aoChangesets) > 0) {
		foreach ($aoChangesets as $oChangeset) {
			$oRepoCache->addChangeset($oChangeset);
			print_r($oChangeset)."\n";
			$sCurrentRevision = (string)((int)$sCurrentRevision + 1);
		}
	} else {
		echo "All revisions imported to cache.\n";
		exit(0);
	}
}
