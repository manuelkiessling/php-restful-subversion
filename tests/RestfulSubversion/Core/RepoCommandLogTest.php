<?php

namespace RestfulSubversion\Core;
use RestfulSubversion\Helper\Bootstrap;
use RestfulSubversion\Helper\CommandLineBuilder;

class RepoCommandLogTest extends \PHPUnit_Framework_TestCase
{
    protected $repo;

    public function setUp()
    {
        $repo = new Repo();

        $repo->setUri('http://svn.example.com/repo');
        $repo->setAuthinfo('user.name', 'secret');

        $this->repo = $repo;
    }

    public function test_getLogCommandForRevision()
    {
        $logCommand = new RepoCommandLog($this->repo, new CommandLineBuilder());
        $logCommand->setRevision(new Revision('1'));
        $commandline = $logCommand->getCommandline();

        $this->assertSame('svn log --no-auth-cache --username=user.name --password=secret -r 1 "http://svn.example.com/repo"@1',
                          $commandline);
    }

    public function test_getLogCommandForRevisionXmlAndVerbose()
    {
        $logCommand = new RepoCommandLog($this->repo, new CommandLineBuilder());
        $logCommand->setRevision(new Revision('1'));
        $logCommand->enableVerbose();
        $logCommand->enableXml();
        $commandline = $logCommand->getCommandline();

        $this->assertSame('svn log --no-auth-cache --username=user.name --password=secret -r 1 -v --xml "http://svn.example.com/repo"@1',
                          $commandline);
    }

    public function test_getLogCommandNoRevision()
    {
        $logCommand = new RepoCommandLog($this->repo, new CommandLineBuilder());
        $commandline = $logCommand->getCommandline();

        $this->assertSame('svn log --no-auth-cache --username=user.name --password=secret "http://svn.example.com/repo"',
                          $commandline);
    }

    public function test_getLogCommandNoRevisionVerbose()
    {
        $logCommand = new RepoCommandLog($this->repo, new CommandLineBuilder());
        $logCommand->enableVerbose();
        $commandline = $logCommand->getCommandline();

        $this->assertSame('svn log --no-auth-cache --username=user.name --password=secret -v "http://svn.example.com/repo"',
                          $commandline);
    }

    public function test_getLogCommandNoRevisionXml()
    {
        $logCommand = new RepoCommandLog($this->repo, new CommandLineBuilder());
        $logCommand->enableXml();
        $commandline = $logCommand->getCommandline();

        $this->assertSame('svn log --no-auth-cache --username=user.name --password=secret --xml "http://svn.example.com/repo"',
                          $commandline);
    }

    public function test_getLogCommandNoRevisionVerboseAndXml()
    {
        $logCommand = new RepoCommandLog($this->repo, new CommandLineBuilder());
        $logCommand->enableVerbose();
        $logCommand->enableXml();
        $commandline = $logCommand->getCommandline();

        $this->assertSame('svn log --no-auth-cache --username=user.name --password=secret -v --xml "http://svn.example.com/repo"',
                          $commandline);
    }
}
