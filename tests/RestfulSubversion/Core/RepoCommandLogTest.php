<?php

class RestfulSubversion_Core_RepoCommandLogTest extends PHPUnit_Framework_TestCase {

    protected $repo;
    
    public function setUp() {
        $repo = new RestfulSubversion_Core_Repo();

        $repo->setUri('file://'.realpath(RestfulSubversion_Helper_Bootstrap::getLibraryRoot().'/../tests/_testrepo'));
        $repo->setAuthinfo('user.name', 'secret');

        $this->repo = $repo;
    }

    public function test_getLogCommandForRevision() {
        $logCommand = new RestfulSubversion_Core_RepoCommandLog($this->repo, new RestfulSubversion_Core_CommandLineBuilder());
        $logCommand->setRevision(new RestfulSubversion_Core_Revision('1'));
        $commandline = $logCommand->getCommandline();

        $this->assertSame('svn log --no-auth-cache --username=user.name --password=secret -r 1 file://'.realpath(RestfulSubversion_Helper_Bootstrap::getLibraryRoot().'/../tests/_testrepo'),
                          $commandline);
    }

    public function test_getLogCommandForRevisionXmlAndVerbose() {
        $logCommand = new RestfulSubversion_Core_RepoCommandLog($this->repo, new RestfulSubversion_Core_CommandLineBuilder());
        $logCommand->setRevision(new RestfulSubversion_Core_Revision('1'));
        $logCommand->enableVerbose();
        $logCommand->enableXml();
        $commandline = $logCommand->getCommandline();

        $this->assertSame('svn log --no-auth-cache --username=user.name --password=secret -r 1 -v --xml file://'.realpath(RestfulSubversion_Helper_Bootstrap::getLibraryRoot().'/../tests/_testrepo'),
                          $commandline);
    }

    public function test_getLogCommandNoRevision() {
        $logCommand = new RestfulSubversion_Core_RepoCommandLog($this->repo, new RestfulSubversion_Core_CommandLineBuilder());
        $commandline = $logCommand->getCommandline();

        $this->assertSame('svn log --no-auth-cache --username=user.name --password=secret file://'.realpath(RestfulSubversion_Helper_Bootstrap::getLibraryRoot().'/../tests/_testrepo'),
                          $commandline);
    }

    public function test_getLogCommandNoRevisionVerbose() {
        $logCommand = new RestfulSubversion_Core_RepoCommandLog($this->repo, new RestfulSubversion_Core_CommandLineBuilder());
        $logCommand->enableVerbose();
        $commandline = $logCommand->getCommandline();

        $this->assertSame('svn log --no-auth-cache --username=user.name --password=secret -v file://'.realpath(RestfulSubversion_Helper_Bootstrap::getLibraryRoot().'/../tests/_testrepo'),
                          $commandline);
    }

    public function test_getLogCommandNoRevisionXml() {
        $logCommand = new RestfulSubversion_Core_RepoCommandLog($this->repo, new RestfulSubversion_Core_CommandLineBuilder());
        $logCommand->enableXml();
        $commandline = $logCommand->getCommandline();

        $this->assertSame('svn log --no-auth-cache --username=user.name --password=secret --xml file://'.realpath(RestfulSubversion_Helper_Bootstrap::getLibraryRoot().'/../tests/_testrepo'),
                          $commandline);
    }

    public function test_getLogCommandNoRevisionVerboseAndXml() {
        $logCommand = new RestfulSubversion_Core_RepoCommandLog($this->repo, new RestfulSubversion_Core_CommandLineBuilder());
        $logCommand->enableVerbose();
        $logCommand->enableXml();
        $commandline = $logCommand->getCommandline();

        $this->assertSame('svn log --no-auth-cache --username=user.name --password=secret -v --xml file://'.realpath(RestfulSubversion_Helper_Bootstrap::getLibraryRoot().'/../tests/_testrepo'),
                          $commandline);
    }

}
