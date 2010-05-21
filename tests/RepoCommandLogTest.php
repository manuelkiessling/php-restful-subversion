<?php

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
		
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo);
		$oLogCommand->addRevision(new MergeHelper_Revision('2', '4'));
		$oLogCommand->enableVerbose();
		$asCommandlines = $oLogCommand->asGetCommandlines();
		$this->assertSame(1, sizeof($asCommandlines));
		$this->assertSame('svn --no-auth-cache --username=user.name --password=secret log -r 2:4 -v file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'), $asCommandlines[0]);
		
	}

	public function test_getLogCommandsForRevisionsNoCache() {
		
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo);
		$oLogCommand->addRevision(new MergeHelper_Revision('1', '2'));
		$oLogCommand->addRevision(new MergeHelper_Revision('3'));
		$oLogCommand->enableVerbose();
		$asCommandlines = $oLogCommand->asGetCommandlines();
		$this->assertSame(2, sizeof($asCommandlines));
		$this->assertSame('svn --no-auth-cache --username=user.name --password=secret log -r 1:2 -v file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'), $asCommandlines[0]);
		$this->assertSame('svn --no-auth-cache --username=user.name --password=secret log -r 3 -v file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'), $asCommandlines[1]);
		
	}

	public function test_getVerboseLogCommandsNoRevisionsFromCache() {

		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo);

		$asCommandlines = $oLogCommand->asGetCommandlines();
		$this->assertSame(1, sizeof($asCommandlines));
		$this->assertSame('cat '.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepocache/MergeHelper.svncache.85fea2b672885627f28d477af65ffc2221292ecb'), $asCommandlines[0]);

		$oLogCommand->enableVerbose();
		$asCommandlines = $oLogCommand->asGetCommandlines();
		$this->assertSame(1, sizeof($asCommandlines));
		$this->assertSame('cat '.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepocache/MergeHelper.svncache.85fea2b672885627f28d477af65ffc2221292ecb.v'), $asCommandlines[0]);

		$oLogCommand->enableXml();
		$asCommandlines = $oLogCommand->asGetCommandlines();
		$this->assertSame(1, sizeof($asCommandlines));
		$this->assertSame('cat '.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepocache/MergeHelper.svncache.85fea2b672885627f28d477af65ffc2221292ecb.v.x'), $asCommandlines[0]);

	}

	public function test_getPathListForRevision() {

		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo);
		$oLogCommand->addRevision(new MergeHelper_Revision('3'));
		$aoPaths = $oLogCommand->aoGetPaths();
		$this->assertSame(2, sizeof($aoPaths));
		$this->assertEquals(new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01/c/d.php'), $aoPaths[0]);
		$this->assertEquals(new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01/a.php'), $aoPaths[1]);
	
	}
	
	public function test_getPathListForRevisions() {

		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo);
		$oLogCommand->addRevision(new MergeHelper_Revision('3'));
		$oLogCommand->addRevision(new MergeHelper_Revision('4'));
		$aoPaths = $oLogCommand->aoGetPaths();
		$this->assertSame(3, sizeof($aoPaths));
		$this->assertEquals(new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01/c/d.php'), $aoPaths[0]);
		$this->assertEquals(new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01/a.php'), $aoPaths[1]);
		$this->assertEquals(new MergeHelper_RepoPath('/branches/my-hammer2/_project/TF-0001/a.php'), $aoPaths[2]);
	
	}
	
	public function test_getRevisionsBySearchingForMessage() {

		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo);
		$aoRevisions = $oLogCommand->aoGetRevisionsWithMessageContainingText('TF-4001');
		$this->assertSame(2, sizeof($aoRevisions));
		$this->assertSame('3', $aoRevisions[0]->sGetNumber());
		$this->assertSame('5', $aoRevisions[1]->sGetNumber());
		// Revision
	
	}

}
