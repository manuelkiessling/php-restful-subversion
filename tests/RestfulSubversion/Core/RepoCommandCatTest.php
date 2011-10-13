<?php

namespace RestfulSubversion\Core;
use RestfulSubversion\Helper\Bootstrap;
use RestfulSubversion\Helper\CommandLineBuilder;

class RepoCommandCatTest extends \PHPUnit_Framework_TestCase
{
    protected $repo;

    public function setUp()
    {
        $repo = new Repo();

        $repo->setUri('http://svn.example.com/repo');
        $repo->setAuthinfo('user.name', 'secret');

        $this->repo = $repo;
    }

    public function test_getCatCommandForPathWithoutRevision()
    {
        $catCommand = new RepoCommandCat($this->repo, new CommandLineBuilder());
        $catCommand->setPath(new RepoPath('/branches/test/a.php'));
        $commandline = $catCommand->getCommandline();

        $this->assertSame('svn cat --no-auth-cache --username=user.name --password=secret "http://svn.example.com/repo/branches/test/a.php"',
                          $commandline);
    }
    
    public function test_getCatCommandForPathWithRevision()
    {
        $catCommand = new RepoCommandCat($this->repo, new CommandLineBuilder());
        $catCommand->setRevision(new Revision('12345'));
        $catCommand->setPath(new RepoPath('/branches/test/a.php'));
        $commandline = $catCommand->getCommandline();

        $this->assertSame('svn cat --no-auth-cache --username=user.name --password=secret -r 12345 "http://svn.example.com/repo/branches/test/a.php"@12345',
                          $commandline);
    }
}
