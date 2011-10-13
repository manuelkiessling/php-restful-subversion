<?php

namespace RestfulSubversion\Core;
use RestfulSubversion\Helper\Bootstrap;
use RestfulSubversion\Helper\CommandLineBuilder;

class RepoCommandPropgetTest extends \PHPUnit_Framework_TestCase
{
    protected $repo;

    public function setUp()
    {
        $repo = new Repo();

        $repo->setUri('http://svn.example.com/repo');
        $repo->setAuthinfo('user.name', 'secret');

        $this->repo = $repo;
    }

    public function test_getPropgetCommandForPathWithoutRevision()
    {
        $propgetCommand = new RepoCommandPropget($this->repo, new CommandLineBuilder());
        $propgetCommand->setPath(new RepoPath('/branches/test/a.php'));
        $propgetCommand->setPropname('svn:mime-type');
        $commandline = $propgetCommand->getCommandline();

        $this->assertSame('svn propget --no-auth-cache --username=user.name --password=secret \'svn:mime-type\' "http://svn.example.com/repo/branches/test/a.php"',
                          $commandline);
    }
    
    public function test_getPropgetCommandForPathWithRevision()
    {
        $propgetCommand = new RepoCommandPropget($this->repo, new CommandLineBuilder());
        $propgetCommand->setRevision(new Revision('2'));
        $propgetCommand->setPath(new RepoPath('/branches/test/a.php'));
        $propgetCommand->setPropname('svn:mime-type');
        $commandline = $propgetCommand->getCommandline();

        $this->assertSame('svn propget --no-auth-cache --username=user.name --password=secret -r 2 \'svn:mime-type\' "http://svn.example.com/repo/branches/test/a.php"@2',
                          $commandline);
    }
}
