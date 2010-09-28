<?php

require_once('../MergeHelper/Bootstrap.php');

$sRepoUri = $argv[1];

$oRepo = new MergeHelper_Repo();
$oRepo->setType(MergeHelper_Repo::TYPE_SVN);
$oRepo->setLocation($sRepoUri);
$oRepo->setAuthinfo('user.name', 'secret');

$rDb = new PDO('sqlite:./svn.db');

$oQuery = $rDb->query("SELECT revision FROM revisions ORDER BY revision DESC LIMIT 1");
$oRow = $oQuery->fetch(PDO::FETCH_ASSOC);
echo 'Latest revision in database: '.$oRow['revision'];
$iStart = $oRow['revision'] + 1;

$oMergeHelper = new MergeHelper_Repohandler();
$aoRevisions = MergeHelper_Repohandler::aoGetRevisionsForString($oRepo, 'a', FALSE);

foreach($aoRevisions as $oRevision) {
	echo 'Revision: '.$oRevision->sGetNumber().":\n";
	$rDb->exec('INSERT INTO revisions (revision) VALUES ("'.$oRevision->sGetNumber().'")');
	
	$aoPaths = MergeHelper_Repohandler::aoGetPathsForRevisions($oRepo, array($oRevision));
	foreach($aoPaths as $oPath) {
		echo $oPath."\n";
		$rDb->exec('INSERT INTO paths (revision, path, revertedpath) VALUES ("'.$oRevision->sGetNumber().'", "'.$oPath.'", "'.strrev($oPath).'")');
	}
}
