<?php

class MergeHelper_RepoCommandMergeTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		$oRepo = new MergeHelper_Repo();

		$oRepo->setLocation('file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'));
		$oRepo->addSourcePath(new MergeHelper_RepoPath('/branches/my-hammer2/_production'));
		$oRepo->addSourcePath(new MergeHelper_RepoPath('/branches/my-hammer2/_project'));
		$oRepo->setTargetPath(new MergeHelper_RepoPath('/branches/my-hammer2/_approval'));

		$this->oRepo = $oRepo;
	}
	
	public function test_getMergeCommandsNothingToMerge() {
		$oMergeCommand = new MergeHelper_RepoCommandMerge($this->oRepo, new MergeHelper_CommandLineBuilder());
		$oMergeCommand->enableDryrun();
		$asCommandlines = $oMergeCommand->asGetCommandlines();

		$this->assertNull($asCommandlines);
	}

	public function test_getMergeCommandsMultipleRevisions() {
		$oMergeCommand = new MergeHelper_RepoCommandMerge($this->oRepo, new MergeHelper_CommandLineBuilder());
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
		// Important implicit assert: the commands must be sorted by revision number, ascending
		$this->assertSame('svn merge --dry-run -c -7 file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo').'/branches/my-hammer2/_production/2010-01-04/a/b.txt /var/tmp/testwc/a/b.txt', $asCommandlines[0]);
		$this->assertSame('svn merge --dry-run -c -5 file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo').'/branches/my-hammer2/_production/2010-01-04 /var/tmp/testwc', $asCommandlines[1]);
		$this->assertSame('svn merge --dry-run -c 3 file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo').'/branches/my-hammer2/_production/2010-01-04/a/b.txt /var/tmp/testwc/a/b.txt', $asCommandlines[2]);

	}

}
