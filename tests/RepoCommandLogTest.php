<?php

/**
 * @todo Flexible solution for the repo cache filenames needed
 */
class MergeHelper_RepoCommandLogTest extends PHPUnit_Framework_TestCase {

	public function setUp() {

		$oRepo = new MergeHelper_Repo();
		$oRepo->setType(MergeHelper_Repo::TYPE_SVN);
		$oRepo->setLocation('file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'));
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

	public function test_getVerboseLogCommandsNoRevisions() {
		
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());
		
		$asCommandlines = $oLogCommand->asGetCommandlines();
		$this->assertSame(1, sizeof($asCommandlines));
		$this->assertSame('svn log --no-auth-cache --username=user.name --password=secret file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'), $asCommandlines[0]);

		$oLogCommand->enableVerbose();
		$asCommandlines = $oLogCommand->asGetCommandlines();
		$this->assertSame(1, sizeof($asCommandlines));
		$this->assertSame('svn log --no-auth-cache --username=user.name --password=secret -v file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'), $asCommandlines[0]);

		$oLogCommand->enableXml();
		$asCommandlines = $oLogCommand->asGetCommandlines();
		$this->assertSame(1, sizeof($asCommandlines));
		$this->assertSame('svn log --no-auth-cache --username=user.name --password=secret -v --xml file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'), $asCommandlines[0]);

	}

	public function test_getPathListForRevision() {

		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());
		$oLogCommand->addRevision(new MergeHelper_Revision('3'));
		$aoPaths = $oLogCommand->aoGetPaths();
		$this->assertSame(2, sizeof($aoPaths));
		$this->assertEquals(new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01/c/d.php'), $aoPaths[0]);
		$this->assertEquals(new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01/a.php'), $aoPaths[1]);
	
	}

	/**
	 * @expectedException MergeHelper_RepoCommandLogNoSuchRevisionException
	 */
	public function test_getPathListForRevisionExceptionIfNoSuchRevision() {

		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());
		$oLogCommand->addRevision(new MergeHelper_Revision('9'));
		$oLogCommand->aoGetPaths();

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
	
	public function test_getRevisionsInRangeTwoNumbers() {
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());
		$aoRevisions = $oLogCommand->aoGetRevisionsInRange(1, 2);
		$this->assertSame(array(2, '1', '2'),
		                  array(sizeof($aoRevisions),
		                        $aoRevisions[0]->sGetNumber(),
		                        $aoRevisions[1]->sGetNumber()));
	}

	public function test_getRevisionsInRangeTwoNumbersReverse() {
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());
		$aoRevisions = $oLogCommand->aoGetRevisionsInRange(4, 1);
		$this->assertSame(array(4, '4', '3', '2', '1'),
		                  array(sizeof($aoRevisions),
		                  $aoRevisions[0]->sGetNumber(),
		                  $aoRevisions[1]->sGetNumber(),
		                  $aoRevisions[2]->sGetNumber(),
						  $aoRevisions[3]->sGetNumber()));
	}

	public function test_getRevisionsInRangeNumberAndHead() {
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());
		$aoRevisions = $oLogCommand->aoGetRevisionsInRange(7, 'head');
		$this->assertSame(array(2, '7', '8'),
		                  array(sizeof($aoRevisions),
		                        $aoRevisions[0]->sGetNumber(),
		                        $aoRevisions[1]->sGetNumber()));
	}

	public function test_getRevisionsInRangeHeadAndNumber() {
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());
		$aoRevisions = $oLogCommand->aoGetRevisionsInRange('head', 7);
		$this->assertSame(array(2, '8', '7'),
		                  array(sizeof($aoRevisions),
		                        $aoRevisions[0]->sGetNumber(),
		                        $aoRevisions[1]->sGetNumber()));
	}

}
