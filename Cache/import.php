<?php

require_once('../MergeHelper/Bootstrap.php');

$sRepoUri = $argv[1];
$sRepoUsername = $argv[2];
$sRepoPassword = $argv[3];
if (isset($argv[4])) {
	$sCacheDbDirectory = $argv[4];
} else {
	$sCacheDbDirectory = '/var/tmp';
}

$oRepo = new MergeHelper_Repo();
$oRepo->setType(MergeHelper_Repo::TYPE_SVN);
$oRepo->setLocation($sRepoUri);
$oRepo->setAuthinfo($sRepoUsername, $sRepoPassword);
$oRepo->setCacheDirectory($sCacheDbDirectory);
$oRepo->enableCache();

$oDb = new PDO('sqlite:'.$oRepo->sGetCachepath(), NULL, NULL);

$sHighestRevisionSql = 'SELECT revision FROM revisions ORDER BY revision DESC LIMIT 1';
$oQuery = $oDb->query($sHighestRevisionSql);
if (!is_object($oQuery)) {
	require_once('./createStatements.php');
	foreach ($asSql as $sSql) {
		$oQuery = $oDb->query($sSql);
	}
	$oQuery = $oDb->query($sHighestRevisionSql);
}

$oRow = $oQuery->fetch(PDO::FETCH_ASSOC);
if (!$oRow) {
	echo 'Database is empty, starting from scratch'."\n";
} else {
	echo 'Highest revision found in database: '.$oRow['revision']."\n";
}
$iCurrentRevision = $oRow['revision'] + 1;

$bFinished = FALSE;
$oMergeHelper = new MergeHelper_Repohandler();

while (!$bFinished) {
	echo "\n";
	echo 'Revision '.$iCurrentRevision.":\n";

	$oRevision = new MergeHelper_Revision($iCurrentRevision);

	$aoPaths = MergeHelper_Repohandler::aoGetPathsForRevisions($oRepo, array($oRevision));
	if (sizeof($aoPaths) > 0) {
		$oDb->exec('INSERT INTO revisions (revision) VALUES ("'.$oRevision->sGetNumber().'")');
		foreach($aoPaths as $oPath) {
			echo "\t".$oPath."\n";
			$oDb->exec('INSERT INTO paths (revision, path, revertedpath) VALUES ("'.$oRevision->sGetNumber().'",
			                                                                     "'.$oPath->sGetAsString().'",
			                                                                     "'.strrev($oPath->sGetAsString()).'")');
		}
		$iCurrentRevision++;
	} else {
		echo 'Latest revision reached, terminating.'."\n";
		break;
	}
}
