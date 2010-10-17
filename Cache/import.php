<?php

require_once('../MergeHelper/Bootstrap.php');

$sRepoUri = $argv[1];

$oRepo = new MergeHelper_Repo();
$oRepo->setType(MergeHelper_Repo::TYPE_SVN);
$oRepo->setLocation($sRepoUri);
$oRepo->setAuthinfo('user.name', 'secret');

$rDb = new PDO('sqlite:./svn.db', NULL, NULL);

$oQuery = $rDb->query("SELECT revision FROM revisions ORDER BY revision DESC LIMIT 1");
$oRow = $oQuery->fetch(PDO::FETCH_ASSOC);
if (!$oRow) {
	echo 'Database is empty, starting from scratch'."\n";
} else {
	echo 'Highest revision in database: '.$oRow['revision']."\n";
}
$iStartRevision = $oRow['revision'] + 1;

$oMergeHelper = new MergeHelper_Repohandler();
$aoRevisions = MergeHelper_Repohandler::aoGetRevisionsInRange($oRepo, $iStartRevision, 'HEAD');

foreach($aoRevisions as $oRevision) {
	echo "\n";
	echo 'Revision '.$oRevision->sGetNumber().":\n";
	$rDb->exec('INSERT INTO revisions (revision) VALUES ("'.$oRevision->sGetNumber().'")');
	
	$aoPaths = MergeHelper_Repohandler::aoGetPathsForRevisions($oRepo, array($oRevision));
	foreach($aoPaths as $oPath) {
		echo "\t".$oPath."\n";
		$rDb->exec('INSERT INTO paths (revision, path, revertedpath) VALUES ("'.$oRevision->sGetNumber().'", "'.$oPath->sGetAsString().'", "'.strrev($oPath->sGetAsString()).'")');
	}
}
