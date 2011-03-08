<?php

class MergeHelper_Core_RepoCommandLogTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		$oRepo = new MergeHelper_Core_Repo();

		$oRepo->setLocation('file://'.realpath(MergeHelper_Helper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'));
		$oRepo->setAuthinfo('user.name', 'secret');
		$oRepo->addSourcePath(new MergeHelper_Core_RepoPath('/branches/platform/_production'));
		$oRepo->addSourcePath(new MergeHelper_Core_RepoPath('/branches/platform/_project'));
		$oRepo->setTargetPath(new MergeHelper_Core_RepoPath('/branches/platform/_approval'));

		$this->oRepo = $oRepo;
	}

	public function test_getLogCommandForRevision() {
		$oLogCommand = new MergeHelper_Core_RepoCommandLog($this->oRepo, new MergeHelper_Core_CommandLineBuilder());
		$oLogCommand->setRevision(new MergeHelper_Core_Revision('1'));
		$sCommandline = $oLogCommand->sGetCommandline();

		$this->assertSame('svn log --no-auth-cache --username=user.name --password=secret -r 1 file://'.realpath(MergeHelper_Helper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'),
		                  $sCommandline);
	}

	public function test_getLogCommandForRevisionXmlAndVerbose() {
		$oLogCommand = new MergeHelper_Core_RepoCommandLog($this->oRepo, new MergeHelper_Core_CommandLineBuilder());
		$oLogCommand->setRevision(new MergeHelper_Core_Revision('1'));
		$oLogCommand->enableVerbose();
		$oLogCommand->enableXml();
		$sCommandline = $oLogCommand->sGetCommandline();

		$this->assertSame('svn log --no-auth-cache --username=user.name --password=secret -r 1 -v --xml file://'.realpath(MergeHelper_Helper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'),
		                  $sCommandline);
	}

	public function test_getLogCommandNoRevision() {
		$oLogCommand = new MergeHelper_Core_RepoCommandLog($this->oRepo, new MergeHelper_Core_CommandLineBuilder());
		$sCommandline = $oLogCommand->sGetCommandline();

		$this->assertSame('svn log --no-auth-cache --username=user.name --password=secret file://'.realpath(MergeHelper_Helper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'),
		                  $sCommandline);
	}

	public function test_getLogCommandNoRevisionVerbose() {
		$oLogCommand = new MergeHelper_Core_RepoCommandLog($this->oRepo, new MergeHelper_Core_CommandLineBuilder());
		$oLogCommand->enableVerbose();
		$sCommandline = $oLogCommand->sGetCommandline();

		$this->assertSame('svn log --no-auth-cache --username=user.name --password=secret -v file://'.realpath(MergeHelper_Helper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'),
		                  $sCommandline);
	}

	public function test_getLogCommandNoRevisionsXml() {
		$oLogCommand = new MergeHelper_Core_RepoCommandLog($this->oRepo, new MergeHelper_Core_CommandLineBuilder());
		$oLogCommand->enableXml();
		$sCommandline = $oLogCommand->sGetCommandline();

		$this->assertSame('svn log --no-auth-cache --username=user.name --password=secret --xml file://'.realpath(MergeHelper_Helper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'),
		                  $sCommandline);
	}

	public function test_getLogCommandNoRevisionVerboseAndXml() {
		$oLogCommand = new MergeHelper_Core_RepoCommandLog($this->oRepo, new MergeHelper_Core_CommandLineBuilder());
		$oLogCommand->enableVerbose();
		$oLogCommand->enableXml();
		$sCommandline = $oLogCommand->sGetCommandline();

		$this->assertSame('svn log --no-auth-cache --username=user.name --password=secret -v --xml file://'.realpath(MergeHelper_Helper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'),
		                  $sCommandline);
	}

}
