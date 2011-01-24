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

		$this->assertSame(array('svn log --no-auth-cache --username=user.name --password=secret -r 2:4 -v file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo')), $asCommandlines);
	}

	public function test_getLogCommandsForRevisionsNoCache() {
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());
		$oLogCommand->addRevision(new MergeHelper_Revision('1', '2'));
		$oLogCommand->addRevision(new MergeHelper_Revision('3'));
		$oLogCommand->enableVerbose();
		$asCommandlines = $oLogCommand->asGetCommandlines();

		$this->assertSame(array(
		                        'svn log --no-auth-cache --username=user.name --password=secret -r 1:2 -v file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'),
		                        'svn log --no-auth-cache --username=user.name --password=secret -r 3 -v file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo')
		                       ),
		                  $asCommandlines);
	}

	public function test_getLogCommandNoRevisions() {
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());

		$asCommandlines = $oLogCommand->asGetCommandlines();
		$this->assertSame(array('svn log --no-auth-cache --username=user.name --password=secret file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo')),
		                   $asCommandlines
		                  );
	}

	public function test_getLogCommandsNoRevisionsVerbose() {
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());
		$oLogCommand->enableVerbose();
		$asCommandlines = $oLogCommand->asGetCommandlines();

		$this->assertSame(array('svn log --no-auth-cache --username=user.name --password=secret -v file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo')),
		                  $asCommandlines
		                 );
	}

	public function test_getLogCommandsNoRevisionsXml() {
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());
		$oLogCommand->enableXml();
		$asCommandlines = $oLogCommand->asGetCommandlines();

		$this->assertSame(array('svn log --no-auth-cache --username=user.name --password=secret --xml file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo')),
		                  $asCommandlines
		                 );
	}

	public function test_getLogCommandsNoRevisionsVerboseAndXml() {
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());
		$oLogCommand->enableVerbose();
		$oLogCommand->enableXml();
		$asCommandlines = $oLogCommand->asGetCommandlines();

		$this->assertSame(array('svn log --no-auth-cache --username=user.name --password=secret -v --xml file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo')),
		                  $asCommandlines
		                 );
	}

	public function test_getPathListForOneRevision() {
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());
		$oLogCommand->addRevision(new MergeHelper_Revision('3'));
		$aoPaths = $oLogCommand->aoGetPaths();

		$this->assertEquals(array(new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01/c/d.php'),
		                          new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01/a.php')
		                         ),
		                    $aoPaths);
	}

	public function test_getPathListForMultipleRevisions() {
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());
		$oLogCommand->addRevision(new MergeHelper_Revision('3'));
		$oLogCommand->addRevision(new MergeHelper_Revision('4'));
		$aoPaths = $oLogCommand->aoGetPaths();

		$this->assertEquals(array(new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01/c/d.php'),
		                          new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01/a.php'),
		                          new MergeHelper_RepoPath('/branches/my-hammer2/_project/TF-0001/a.php')
		                         ),
		                    $aoPaths);
	}

	/**
	 * @expectedException MergeHelper_RepoCommandLogNoSuchRevisionException
	 */
	public function test_getPathListForRevisionExceptionIfNoSuchRevision() {
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());
		$oLogCommand->addRevision(new MergeHelper_Revision('9'));
		$oLogCommand->aoGetPaths();
	}

	public function test_getMessageForOneRevision() {
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());
		$oLogCommand->addRevision(new MergeHelper_Revision('3'));
		$asMessages = $oLogCommand->asGetMessages();

		$this->assertEquals(array('TF-4001'), $asMessages);
	}

	public function test_getMessageForMultipleRevisions() {
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());
		$oLogCommand->addRevision(new MergeHelper_Revision('3'));
		$oLogCommand->addRevision(new MergeHelper_Revision('5'));
		$asMessages = $oLogCommand->asGetMessages();

		$this->assertEquals(array('TF-4001',
		                          "TF-4001\n- added jabbadabbadoo"
		                         ),
		                    $asMessages);
	}

	/**
	 * @expectedException MergeHelper_RepoCommandLogNoSuchRevisionException
	 */
	public function test_getMessageForRevisionExceptionIfNoSuchRevision() {
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());
		$oLogCommand->addRevision(new MergeHelper_Revision('9'));
		$oLogCommand->asGetMessages();
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
		                        $aoRevisions[3]->sGetNumber()
		                       )
		                 );
	}

	public function test_getRevisionsInRangeNumberAndHead() {
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());
		$aoRevisions = $oLogCommand->aoGetRevisionsInRange(7, 'head');

		$this->assertSame(array(2, '7', '8'),
		                  array(sizeof($aoRevisions),
		                        $aoRevisions[0]->sGetNumber(),
		                        $aoRevisions[1]->sGetNumber()
		                       )
		                 );
	}

	public function test_getRevisionsInRangeHeadAndNumber() {
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());
		$aoRevisions = $oLogCommand->aoGetRevisionsInRange('head', 7);

		$this->assertSame(array(2, '8', '7'),
		                  array(sizeof($aoRevisions),
		                        $aoRevisions[0]->sGetNumber(),
		                        $aoRevisions[1]->sGetNumber()
		                       )
		                 );
	}

	public function test_getRevisionsBySearchingForMessage() {
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineFactory());
		$aoRevisions = $oLogCommand->aoGetRevisionsWithMessageContainingText('TF-4001');
		$this->assertSame(3, sizeof($aoRevisions));
		$this->assertSame('3', $aoRevisions[0]->sGetNumber());
		$this->assertSame('5', $aoRevisions[1]->sGetNumber());
		$this->assertSame('7', $aoRevisions[2]->sGetNumber());
	}

}
