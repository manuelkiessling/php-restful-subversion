<?php

namespace RestfulSubversion\Core;

class RepoFileTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $revision = new Revision('1234');
        $path = new RepoPath('/a/b.php');
        
        $file = new RepoFile($revision, $path);
        $file->setContent('Hello World');
        
        $this->assertEquals($path, $file->getPath());
        $this->assertEquals($revision, $file->getRevision());
        $this->assertEquals('Hello World', $file->getContent());
    }
}
