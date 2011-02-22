<?php

class MergeHelper_RepoCommandMergeTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		$oRepo = new MergeHelper_Repo();

		$oRepo->setLocation('file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'));
		$oRepo->addSourcePath(new MergeHelper_RepoPath('/branches/platform/_production'));
		$oRepo->addSourcePath(new MergeHelper_RepoPath('/branches/platform/_project'));
		$oRepo->setTargetPath(new MergeHelper_RepoPath('/branches/platform/_approval'));

		$this->oRepo = $oRepo;
	}
	
	public function test_getMergeCommandNothingToMerge() {
		$oMergeCommand = new MergeHelper_RepoCommandMerge($this->oRepo, new MergeHelper_CommandLineBuilder());
		$oMergeCommand->enableDryrun();
		$sCommandline = $oMergeCommand->sGetCommandline();

		$this->assertNull($sCommandline);
	}

	public function test_getMergeCommandForRevision() {
		$oMergeCommand = new MergeHelper_RepoCommandMerge($this->oRepo, new MergeHelper_CommandLineBuilder());
		$oMergeCommand->setRevision(new MergeHelper_Revision('5'));
		$oMergeCommand->setRepoPath(new MergeHelper_RepoPath('/branches/platform/_production/2010-01-04'));
		$oMergeCommand->setWorkingCopyPath('/var/tmp/testwc');
		$sCommandline = $oMergeCommand->sGetCommandline();

		$this->assertSame('svn merge -c 5 file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo').'/branches/platform/_production/2010-01-04 /var/tmp/testwc',
		                  $sCommandline);
	}

	public function test_getMergeCommandForRevisionDryRun() {
		$oMergeCommand = new MergeHelper_RepoCommandMerge($this->oRepo, new MergeHelper_CommandLineBuilder());
		$oMergeCommand->setRevision(new MergeHelper_Revision('5'));
		$oMergeCommand->setRepoPath(new MergeHelper_RepoPath('/branches/platform/_production/2010-01-04'));
		$oMergeCommand->setWorkingCopyPath('/var/tmp/testwc');
		$oMergeCommand->enableDryrun();
		$sCommandline = $oMergeCommand->sGetCommandline();

		$this->assertSame('svn merge --dry-run -c 5 file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo').'/branches/platform/_production/2010-01-04 /var/tmp/testwc',
		                  $sCommandline);
	}

	public function test_getMergeCommandForRevisionRollback() {
		$oMergeCommand = new MergeHelper_RepoCommandMerge($this->oRepo, new MergeHelper_CommandLineBuilder());
		$oMergeCommand->setRevision(new MergeHelper_Revision('5'));
		$oMergeCommand->setRepoPath(new MergeHelper_RepoPath('/branches/platform/_production/2010-01-04'));
		$oMergeCommand->setWorkingCopyPath('/var/tmp/testwc');
		$oMergeCommand->enableRollback();
		$sCommandline = $oMergeCommand->sGetCommandline();

		$this->assertSame('svn merge -c -5 file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo').'/branches/platform/_production/2010-01-04 /var/tmp/testwc',
		                  $sCommandline);
	}

}
