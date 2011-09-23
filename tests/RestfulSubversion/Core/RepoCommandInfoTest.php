<?php

namespace RestfulSubversion\Core;
use RestfulSubversion\Helper\Bootstrap;
use RestfulSubversion\Helper\CommandLineBuilder;

class RepoCommandInfoTest extends \PHPUnit_Framework_TestCase
{
    protected $repo;

    public function setUp()
    {
        $repo = new Repo();

        $repo->setUri('http://svn.example.com/repo');
        $repo->setAuthinfo('user.name', 'secret');

        $this->repo = $repo;
    }

    public function test_getInfoCommandForPathWithoutRevision()
    {
        $infoCommand = new RepoCommandInfo($this->repo, new CommandLineBuilder());
        $infoCommand->setPath(new RepoPath('/branches/test/a.php'));
        $commandline = $infoCommand->getCommandline();

        $this->assertSame('svn info --no-auth-cache --username=user.name --password=secret "http://svn.example.com/repo/branches/test/a.php"',
                          $commandline);
    }
    
    public function test_getInfoCommandForPathWithRevision()
    {
        $infoCommand = new RepoCommandInfo($this->repo, new CommandLineBuilder());
        $infoCommand->setRevision(new Revision('1234'));
        $infoCommand->setPath(new RepoPath('/branches/test/a.php'));
        $commandline = $infoCommand->getCommandline();

        $this->assertSame('svn info --no-auth-cache --username=user.name --password=secret -r 1234 "http://svn.example.com/repo/branches/test/a.php"',
                          $commandline);
    }
    
    public function test_getInfoCommandForPathWithRevisionAndXmlEnabled()
    {
        $infoCommand = new RepoCommandInfo($this->repo, new CommandLineBuilder());
        $infoCommand->setRevision(new Revision('1234'));
        $infoCommand->setPath(new RepoPath('/branches/test/a.php'));
        $infoCommand->enableXml();
        $commandline = $infoCommand->getCommandline();

        $this->assertSame('svn info --no-auth-cache --username=user.name --password=secret -r 1234 --xml "http://svn.example.com/repo/branches/test/a.php"',
                          $commandline);
    }
}
