#!/usr/bin/php
<?php

if (is_file($argv[1])) {
	require_once($argv[1]);
} else {
	$sRepoLocation = $argv[1];
	$sRepoUsername = $argv[2];
	$sRepoPassword = $argv[3];
	$sRepoCacheConnectionString = $argv[4];
}

if (empty($sRepoLocation)) {
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

if (empty($sRepoCacheConnectionString)) {
	echo "No cache db connection string given.\n";
	exit(1);
}

require_once('../lib/MergeHelper.php');

$oRepo = new MergeHelper_Repo();
$oRepo->setLocation($sRepoLocation);
$oRepo->setAuthinfo($sRepoUsername, $sRepoPassword);

$oCommandLineExecutor = MergeHelper_CommandLineExecutor::oGetInstance();
$oCommandLineBuilder = new MergeHelper_CommandLineBuilder();
$oLogInterpreter = new MergeHelper_RepoLogInterpreter();

$oRepoCache = new MergeHelper_RepoCache(new PDO($sRepoCacheConnectionString, NULL, NULL));
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
	$oCommandLog->setRevision(new MergeHelper_Revision((string)$iCurrentRevision));
	$sCommandline = $oCommandLog->sGetCommandline();
	$sLogOutput = $oCommandLineExecutor->sGetCommandResult($sCommandline);

	$aoChangesets = $oLogInterpreter->aoCreateChangesetsFromVerboseXml($sLogOutput);

	foreach ($aoChangesets as $oChangeset) {
		$oRepoCache->addChangeset($oChangeset);
		$iCurrentRevision++;
	}
}

echo "All revisions imported to cache.\n";
exit(0);
