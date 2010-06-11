<?php

/**
 * @todo Flexible solution for the repo cache filenames needed
 */
class MergeHelper_RepoCommandLogTest extends PHPUnit_Framework_TestCase {

	public function setUp() {

		$oRepo = new MergeHelper_Repo();
		$oRepo->setType(MergeHelper_Repo::TYPE_SVN);
		$oRepo->setLocation('file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'));
		$oRepo->setCacheDirectory(realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepocache'));
		$oRepo->setAuthinfo('user.name', 'secret');
		$oRepo->addSourcePath(new MergeHelper_RepoPath('/branches/my-hammer2/_production'));
		$oRepo->addSourcePath(new MergeHelper_RepoPath('/branches/my-hammer2/_project'));
		$oRepo->setTargetPath(new MergeHelper_RepoPath('/branches/my-hammer2/_approval'));
		$this->oRepo = $oRepo;

	}

	public function test_getVerboseLogForRevision() {
		
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());
		$oLogCommand->addRevision(new MergeHelper_Revision('2', '4'));
		$oLogCommand->enableVerbose();
		$asCommandlines = $oLogCommand->asGetCommandlines();
		$this->assertSame(1, sizeof($asCommandlines));
		$this->assertSame('svn log --no-auth-cache --username=user.name --password=secret -r 2:4 -v file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'), $asCommandlines[0]);
		
	}

	public function test_getLogCommandsForRevisionsNoCache() {
		
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());
		$oLogCommand->addRevision(new MergeHelper_Revision('1', '2'));
		$oLogCommand->addRevision(new MergeHelper_Revision('3'));
		$oLogCommand->enableVerbose();
		$asCommandlines = $oLogCommand->asGetCommandlines();
		$this->assertSame(2, sizeof($asCommandlines));
		$this->assertSame('svn log --no-auth-cache --username=user.name --password=secret -r 1:2 -v file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'), $asCommandlines[0]);
		$this->assertSame('svn log --no-auth-cache --username=user.name --password=secret -r 3 -v file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'), $asCommandlines[1]);
		
	}

	public function test_getVerboseLogCommandsNoRevisionsFromCacheAndRemote() {
		
		// With cache
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());
		$oLogCommand->enableCache();
		
		$asCommandlines = $oLogCommand->asGetCommandlines();
		$this->assertSame(1, sizeof($asCommandlines));
		$this->assertSame('cat '.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepocache/MergeHelper.svncache.300b8d25873d3a25c651dc0825703bc08e48d754'), $asCommandlines[0]);

		$oLogCommand->enableVerbose();
		$asCommandlines = $oLogCommand->asGetCommandlines();
		$this->assertSame(1, sizeof($asCommandlines));
		$this->assertSame('cat '.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepocache/MergeHelper.svncache.300b8d25873d3a25c651dc0825703bc08e48d754.v'), $asCommandlines[0]);

		$oLogCommand->enableXml();
		$asCommandlines = $oLogCommand->asGetCommandlines();
		$this->assertSame(1, sizeof($asCommandlines));
		$this->assertSame('cat '.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepocache/MergeHelper.svncache.300b8d25873d3a25c651dc0825703bc08e48d754.v.x'), $asCommandlines[0]);

		// Without cache
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());
		$oLogCommand->disableCache();
		
		$asCommandlines = $oLogCommand->asGetCommandlines();
		$this->assertSame(1, sizeof($asCommandlines));
		$this->assertSame('svn --no-auth-cache --username=user.name --password=secret log file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'), $asCommandlines[0]);

		$oLogCommand->enableVerbose();
		$asCommandlines = $oLogCommand->asGetCommandlines();
		$this->assertSame(1, sizeof($asCommandlines));
		$this->assertSame('svn --no-auth-cache --username=user.name --password=secret log -v file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'), $asCommandlines[0]);

		$oLogCommand->enableXml();
		$asCommandlines = $oLogCommand->asGetCommandlines();
		$this->assertSame(1, sizeof($asCommandlines));
		$this->assertSame('svn --no-auth-cache --username=user.name --password=secret log -v --xml file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'), $asCommandlines[0]);

	}

	public function test_getPathListForRevision() {

		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());
		$oLogCommand->addRevision(new MergeHelper_Revision('3'));
		$aoPaths = $oLogCommand->aoGetPaths();
		$this->assertSame(2, sizeof($aoPaths));
		$this->assertEquals(new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01/c/d.php'), $aoPaths[0]);
		$this->assertEquals(new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01/a.php'), $aoPaths[1]);
	
	}
	
	public function test_getPathListForRevisions() {

		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());
		$oLogCommand->addRevision(new MergeHelper_Revision('3'));
		$oLogCommand->addRevision(new MergeHelper_Revision('4'));
		$aoPaths = $oLogCommand->aoGetPaths();
		$this->assertSame(3, sizeof($aoPaths));
		$this->assertEquals(new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01/c/d.php'), $aoPaths[0]);
		$this->assertEquals(new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01/a.php'), $aoPaths[1]);
		$this->assertEquals(new MergeHelper_RepoPath('/branches/my-hammer2/_project/TF-0001/a.php'), $aoPaths[2]);
	
	}
	
	public function test_getRevisionsBySearchingForMessage() {

		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());
		
		// With cache (test cache is old, gives less revisions!)
		$oLogCommand->enableCache();
		$aoRevisions = $oLogCommand->aoGetRevisionsWithMessageContainingText('TF-4001');
		$this->assertSame(2, sizeof($aoRevisions));
		$this->assertSame('3', $aoRevisions[0]->sGetNumber());
		$this->assertSame('5', $aoRevisions[1]->sGetNumber());

		// Without cache
		$oLogCommand->disableCache();
		$aoRevisions = $oLogCommand->aoGetRevisionsWithMessageContainingText('TF-4001');
		$this->assertSame(3, sizeof($aoRevisions));
		$this->assertSame('3', $aoRevisions[0]->sGetNumber());
		$this->assertSame('5', $aoRevisions[1]->sGetNumber());
		$this->assertSame('7', $aoRevisions[2]->sGetNumber());

	
	}

}
