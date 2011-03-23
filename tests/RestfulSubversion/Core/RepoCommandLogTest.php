<?php

class RestfulSubversion_Core_RepoCommandLogTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		$oRepo = new RestfulSubversion_Core_Repo();

		$oRepo->setLocation('file://'.realpath(RestfulSubversion_Helper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'));
		$oRepo->setAuthinfo('user.name', 'secret');
		$oRepo->addSourcePath(new RestfulSubversion_Core_RepoPath('/branches/platform/_production'));
		$oRepo->addSourcePath(new RestfulSubversion_Core_RepoPath('/branches/platform/_project'));
		$oRepo->setTargetPath(new RestfulSubversion_Core_RepoPath('/branches/platform/_approval'));

		$this->oRepo = $oRepo;
	}

	public function test_getLogCommandForRevision() {
		$oLogCommand = new RestfulSubversion_Core_RepoCommandLog($this->oRepo, new RestfulSubversion_Core_CommandLineBuilder());
		$oLogCommand->setRevision(new RestfulSubversion_Core_Revision('1'));
		$sCommandline = $oLogCommand->sGetCommandline();

		$this->assertSame('svn log --no-auth-cache --username=user.name --password=secret -r 1 file://'.realpath(RestfulSubversion_Helper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'),
		                  $sCommandline);
	}

	public function test_getLogCommandForRevisionXmlAndVerbose() {
		$oLogCommand = new RestfulSubversion_Core_RepoCommandLog($this->oRepo, new RestfulSubversion_Core_CommandLineBuilder());
		$oLogCommand->setRevision(new RestfulSubversion_Core_Revision('1'));
		$oLogCommand->enableVerbose();
		$oLogCommand->enableXml();
		$sCommandline = $oLogCommand->sGetCommandline();

		$this->assertSame('svn log --no-auth-cache --username=user.name --password=secret -r 1 -v --xml file://'.realpath(RestfulSubversion_Helper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'),
		                  $sCommandline);
	}

	public function test_getLogCommandNoRevision() {
		$oLogCommand = new RestfulSubversion_Core_RepoCommandLog($this->oRepo, new RestfulSubversion_Core_CommandLineBuilder());
		$sCommandline = $oLogCommand->sGetCommandline();

		$this->assertSame('svn log --no-auth-cache --username=user.name --password=secret file://'.realpath(RestfulSubversion_Helper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'),
		                  $sCommandline);
	}

	public function test_getLogCommandNoRevisionVerbose() {
		$oLogCommand = new RestfulSubversion_Core_RepoCommandLog($this->oRepo, new RestfulSubversion_Core_CommandLineBuilder());
		$oLogCommand->enableVerbose();
		$sCommandline = $oLogCommand->sGetCommandline();

		$this->assertSame('svn log --no-auth-cache --username=user.name --password=secret -v file://'.realpath(RestfulSubversion_Helper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'),
		                  $sCommandline);
	}

	public function test_getLogCommandNoRevisionsXml() {
		$oLogCommand = new RestfulSubversion_Core_RepoCommandLog($this->oRepo, new RestfulSubversion_Core_CommandLineBuilder());
		$oLogCommand->enableXml();
		$sCommandline = $oLogCommand->sGetCommandline();

		$this->assertSame('svn log --no-auth-cache --username=user.name --password=secret --xml file://'.realpath(RestfulSubversion_Helper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'),
		                  $sCommandline);
	}

	public function test_getLogCommandNoRevisionVerboseAndXml() {
		$oLogCommand = new RestfulSubversion_Core_RepoCommandLog($this->oRepo, new RestfulSubversion_Core_CommandLineBuilder());
		$oLogCommand->enableVerbose();
		$oLogCommand->enableXml();
		$sCommandline = $oLogCommand->sGetCommandline();

		$this->assertSame('svn log --no-auth-cache --username=user.name --password=secret -v --xml file://'.realpath(RestfulSubversion_Helper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'),
		                  $sCommandline);
	}

}
