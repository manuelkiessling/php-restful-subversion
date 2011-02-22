#!/usr/bin/php
<?php

require_once('../MergeHelper.php');

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
$oRepo->setLocation($sRepoUri);
$oRepo->setAuthinfo($sRepoUsername, $sRepoPassword);

$oCommandLineExecutor = MergeHelper_CommandLineExecutor::oGetInstance();
$oCommandLineBuilder = new MergeHelper_CommandLineBuilder();
$oLogInterpreter = new MergeHelper_RepoLogInterpreter();

$oRepoCache = new MergeHelper_RepoCache(new PDO('sqlite:'.$sCacheDbFilename, NULL, NULL));
$oMergeHelper = new MergeHelper($oRepo, $oRepoCache);

$iHighestRevisionInRepo = (int)$oMergeHelper->oGetHighestRevisionInRepo()->sGetAsString();

$iHighestRevisionInRepoCache = 0;
$oRevision = $oMergeHelper->oGetHighestRevisionInRepoCache();
if (is_object($oRevision)) $iHighestRevisionInRepoCache = (int)$oRevision->sGetAsString();

echo 'Highest revision found in repository: '.$iHighestRevisionInRepo."\n";
if ($iHighestRevisionInRepoCache == 0) {
	echo 'Cache database is empty, starting from scratch'."\n";
	$iCurrentRevision = 1;
} else {
	echo 'Highest revision found in cache database: '.$iHighestRevisionInRepoCache."\n";
	$iCurrentRevision = $iHighestRevisionInRepoCache + 1;
}

while ($iCurrentRevision <= $iHighestRevisionInRepo) {
	echo "\n";
	echo 'About to import revision '.$iCurrentRevision.":\n";

	$oRevision = new MergeHelper_Revision((string)$iCurrentRevision);

	$oCommandLog = new MergeHelper_RepoCommandLog($oRepo, $oCommandLineBuilder);
	$oCommandLog->enableVerbose();
	$oCommandLog->enableXml();
	$oCommandLog->addRevision(new MergeHelper_Revision((string)$iCurrentRevision));
	$aoCommandlines = $oCommandLog->asGetCommandlines();
	$sLogOutput = $oCommandLineExecutor->sGetCommandResult($aoCommandlines[0]);

	$aoChangesets = $oLogInterpreter->aoCreateChangesetsFromVerboseXml($sLogOutput);

	foreach ($aoChangesets as $oChangeset) {
		$oRepoCache->addChangeset($oChangeset);
		$iCurrentRevision++;
	}
}

echo "All revisions imported to cache.\n";
exit(0);
