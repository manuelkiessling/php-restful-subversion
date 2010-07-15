<?php

class MergeHelper_RepoCommandMergeTest extends PHPUnit_Framework_TestCase {

	public function setUp() {

		$oRepo = new MergeHelper_Repo();
		$oRepo->setType(MergeHelper_Repo::TYPE_SVN);
		$oRepo->setLocation('file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'));
		$oRepo->addSourcePath(new MergeHelper_RepoPath('/branches/my-hammer2/_production'));
		$oRepo->addSourcePath(new MergeHelper_RepoPath('/branches/my-hammer2/_project'));
		$oRepo->setTargetPath(new MergeHelper_RepoPath('/branches/my-hammer2/_approval'));
		$this->oRepo = $oRepo;

	}
	
	public function test_getMergeCommandsNothingToMerge() {
	
		$oMergeCommand = new MergeHelper_RepoCommandMerge($this->oRepo);
		$oMergeCommand->enableDryrun();
		$asCommandlines = $oMergeCommand->asGetCommandlines();
		$this->assertNull($asCommandlines);

	}

	public function test_getMergeCommandsSingleRevision() {
	
		$oMergeCommand = new MergeHelper_RepoCommandMerge($this->oRepo);
		$oMergeCommand->addMerge(new MergeHelper_Revision('5'),
		                         new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-04'),
		                         '/var/tmp/testwc',
		                         TRUE);
		$oMergeCommand->addMerge(new MergeHelper_Revision('3'),
		                         new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-04/a/b.txt'),
		                         '/var/tmp/testwc/a/b.txt');
		$oMergeCommand->addMerge(new MergeHelper_Revision('7'),
		                         new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-04/a/b.txt'),
		                         '/var/tmp/testwc/a/b.txt',
		                         TRUE);

		$oMergeCommand->enableDryrun();
		$asCommandlines = $oMergeCommand->asGetCommandlines();
		$this->assertSame(3, sizeof($asCommandlines));
		// Important assert: the commands must be sorted by revision number, ascending
		$this->assertSame('svn merge --dry-run -c -7 file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo').'/branches/my-hammer2/_production/2010-01-04/a/b.txt /var/tmp/testwc/a/b.txt', $asCommandlines[0]);
		$this->assertSame('svn merge --dry-run -c -5 file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo').'/branches/my-hammer2/_production/2010-01-04 /var/tmp/testwc', $asCommandlines[1]);
		$this->assertSame('svn merge --dry-run -c 3 file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo').'/branches/my-hammer2/_production/2010-01-04/a/b.txt /var/tmp/testwc/a/b.txt', $asCommandlines[2]);

	}

	public function test_getMergeCommandsRevisionRangeNumberOfCommandsIsCorrect() {
	
		$oMergeCommand = new MergeHelper_RepoCommandMerge($this->oRepo);
		$oMergeCommand->addMerge(new MergeHelper_Revision('5', '12'),
		                         new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-04'),
		                         '/var/tmp/testwc',
		                         TRUE);
		$asCommandlines1 = $oMergeCommand->asGetCommandlines();
		
		unset($oMergeCommand);
		
		$oMergeCommand = new MergeHelper_RepoCommandMerge($this->oRepo);
		$oMergeCommand->addMerge(new MergeHelper_Revision('5', '12'),
		                         new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-04'),
		                         '/var/tmp/testwc',
		                         TRUE);
		$oMergeCommand->addMerge(new MergeHelper_Revision('13', '15'),
		                         new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-04'),
		                         '/var/tmp/testwc',
		                         TRUE);
		$asCommandlines2 = $oMergeCommand->asGetCommandlines();
		
		$this->assertTrue((sizeof($asCommandlines1) === 1) && (sizeof($asCommandlines2) === 2));
	}

	public function test_getMergeCommandsRevisionRangeCommandLineIsCorrect() {
	
		$oMergeCommand = new MergeHelper_RepoCommandMerge($this->oRepo);
		$oMergeCommand->addMerge(new MergeHelper_Revision('5', '12'),
		                         new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-04'),
		                         '/var/tmp/testwc',
		                         TRUE);
		$asCommandlines = $oMergeCommand->asGetCommandlines();
		
		$this->assertSame('svn merge -r 12:5 file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo').'/branches/my-hammer2/_production/2010-01-04 /var/tmp/testwc', $asCommandlines[0]);
	}

}
