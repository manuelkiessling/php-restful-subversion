#!/usr/bin/php
<?php

function displayErrorWithUsageInformationAndExit($sError) {
	echo "\n";
	echo 'ERROR: '.$sError;

	$sInfo = <<<EOT


Usage information:

There are two ways of using this script:

1: You provide the path to a file containing the necessary SVN and
   cache db information

   or

2: You provide the SVN repository location, a valid SVN username and
   password, and a PDO compatible cache db connection string directly


Example for variant 1:

   buildCache.php /path/to/PHPMergeHelper.conf

See file PHPMergeHelper.sample.conf as an example of how such a config
file needs to be designed.


Example for variant 2:

   buildCache.php http://svn.example.com/ user pass sqlite:/var/tmp/svncache.db 100

This last parameter is optional and defines how many revisions should be
imported to the cache during the run of this script. After reaching this
amount of imports, the script will exit. If no value is provided, an unlimited
number of revisions will be imported (of course limited by the maximum number
of revisions that are in your repository).

EOT;

	echo $sInfo;
	exit(1);
}

if (!array_key_exists(1, $argv)) {
	displayErrorWithUsageInformationAndExit("You need to provide either the path to a valid PHPMergeHelper.conf file or the location of a SVN repository, a SVN username and password, and a PDO compatible connection string.");
}

if (is_file($argv[1])) {
	require_once $argv[1];
	$sRepoLocation = $aConfig['sRepoLocation'];
	$sRepoUsername = $aConfig['sRepoUsername'];
	$sRepoPassword = $aConfig['sRepoPassword'];
	$sRepoCacheConnectionString = $aConfig['sRepoCacheConnectionString'];
	$iMaxImportsPerRun = 0;
	if (array_key_exists('iMaxImportsPerRun', $aConfig)) {
		$iMaxImportsPerRun = $aConfig['iMaxImportsPerRun'];
	}
} else {
	$sRepoLocation = $argv[1];

	if (!array_key_exists(2, $argv)) {
		displayErrorWithUsageInformationAndExit("You need to provide a SVN username.");
	}
	$sRepoUsername = $argv[2];

	if (!array_key_exists(3, $argv)) {
		displayErrorWithUsageInformationAndExit("You need to provide a SVN password.");
	}
	$sRepoPassword = $argv[3];

	if (!array_key_exists(4, $argv)) {
		displayErrorWithUsageInformationAndExit("You need to provide a PDO compatible connection string.");
	}
	$sRepoCacheConnectionString = $argv[4];

	$iMaxImportsPerRun = 0;
	if (array_key_exists(5, $argv)) {
		$iMaxImportsPerRun = (int)$argv[5];
	}
}

if (empty($sRepoLocation)) {
	displayErrorWithUsageInformationAndExit("No repository URI given.");
}

if (empty($sRepoUsername)) {
	displayErrorWithUsageInformationAndExit("No repository username given.");
}

if (empty($sRepoPassword)) {
	displayErrorWithUsageInformationAndExit("No repository password given.");
}

if (empty($sRepoCacheConnectionString)) {
	displayErrorWithUsageInformationAndExit("No cache db connection string given.");
}

require_once('../lib/MergeHelper.php');

$oRepo = new MergeHelper_Core_Repo();
$oRepo->setLocation($sRepoLocation);
$oRepo->setAuthinfo($sRepoUsername, $sRepoPassword);

$oCommandLineExecutor = MergeHelper_Core_CommandLineExecutor::oGetInstance();
$oCommandLineBuilder = new MergeHelper_Core_CommandLineBuilder();
$oLogInterpreter = new MergeHelper_Core_RepoLogInterpreter();

$oRepoCache = new MergeHelper_Core_RepoCache(new PDO($sRepoCacheConnectionString, NULL, NULL));
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

$i = 0;
while ($iCurrentRevision <= $iHighestRevisionInRepo) {
	if ($iMaxImportsPerRun != 0 && $i == $iMaxImportsPerRun) {
		echo "Imported the maximum number of $i revisions for this run, exiting.\n";
		exit(0);
	}

	echo "\n";
	echo 'About to import revision '.$iCurrentRevision.":\n";

	$oRevision = new MergeHelper_Core_Revision((string)$iCurrentRevision);

	$oCommandLog = new MergeHelper_Core_RepoCommandLog($oRepo, $oCommandLineBuilder);
	$oCommandLog->enableVerbose();
	$oCommandLog->enableXml();
	$oCommandLog->setRevision(new MergeHelper_Core_Revision((string)$iCurrentRevision));
	$sCommandline = $oCommandLog->sGetCommandline();
	$sLogOutput = $oCommandLineExecutor->sGetCommandResult($sCommandline);

	$aoChangesets = $oLogInterpreter->aoCreateChangesetsFromVerboseXml($sLogOutput);

	foreach ($aoChangesets as $oChangeset) {
		$oRepoCache->addChangeset($oChangeset);
		$iCurrentRevision++;
	}
	$i++;
}

echo "All revisions imported to cache.\n";
exit(0);
