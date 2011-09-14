<?php

class RestfulSubversion_Core_RepoTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        $this->oRepo = new RestfulSubversion_Core_Repo();
    }

    public function test_newRepoObjectHasNoLocationSet() {
        $this->assertNull($this->oRepo->sGetLocation());
    }

    public function test_setAndGetLocation() {
        $this->oRepo->setLocation('http://svn.example.com/repo');

        $this->assertSame('http://svn.example.com/repo', $this->oRepo->sGetLocation());
    }

    public function test_getLocationBranches() {
        $this->oRepo->setLocation('http://svn.example.com/repo');

        $this->assertSame('http://svn.example.com/repo/branches', $this->oRepo->sGetLocationBranches());
    }

    public function test_setAndGetAuthinfoUsername() {
        $this->oRepo->setAuthinfo('user.name', 'secret');
        
        $this->assertSame('user.name', $this->oRepo->sGetAuthinfoUsername());
    }

    public function test_setAndGetAuthinfoPassword() {
        $this->oRepo->setAuthinfo('user.name', 'secret');

        $this->assertSame('secret', $this->oRepo->sGetAuthinfoPassword());
    }

    public function test_addSourcePathAndGetSourcePaths() {
        $this->oRepo->setLocation('http://svn.example.com/repo');
        $this->oRepo->setAuthinfo('user.name', 'secret');

        $this->oRepo->addSourcePath(new RestfulSubversion_Core_RepoPath('/branches/platform/_production'));
        $this->oRepo->addSourcePath(new RestfulSubversion_Core_RepoPath('/branches/platform/_project'), 1);

        $aoGetSourcePaths = $this->oRepo->aoGetSourcePaths();

        $this->assertEquals(array(
                                  new RestfulSubversion_Core_RepoPath('/branches/platform/_production'),
                                  new RestfulSubversion_Core_RepoPath('/branches/platform/_project')
                                 ), $aoGetSourcePaths);
    }

    public function test_addSourcePathAndGetSourceLocations() {
        $this->oRepo->setLocation('http://svn.example.com/repo');
        $this->oRepo->setAuthinfo('user.name', 'secret');

        $this->oRepo->addSourcePath(new RestfulSubversion_Core_RepoPath('/branches/platform/_production'));
        $this->oRepo->addSourcePath(new RestfulSubversion_Core_RepoPath('/branches/platform/_project'), 1);

        $asGetSourceLocations = $this->oRepo->asGetSourceLocations();

        $this->assertSame(array(
                                'http://svn.example.com/repo/branches/platform/_production',
                                'http://svn.example.com/repo/branches/platform/_project'
                               ), $asGetSourceLocations);
    }

    public function test_setAndGetTargetPath() {
        $this->oRepo->setLocation('http://svn.example.com/repo');
        $this->oRepo->setAuthinfo('user.name', 'secret');

        $this->oRepo->setTargetPath(new RestfulSubversion_Core_RepoPath('/branches/platform/_approval'));

        $this->assertEquals(new RestfulSubversion_Core_RepoPath('/branches/platform/_approval'), $this->oRepo->oGetTargetPath());
    }

    public function test_setTargetPathAndGetTargetLocation() {
        $this->oRepo->setLocation('http://svn.example.com/repo');
        $this->oRepo->setAuthinfo('user.name', 'secret');

        $this->oRepo->setTargetPath(new RestfulSubversion_Core_RepoPath('/branches/platform/_approval'));

        $this->assertSame('http://svn.example.com/repo/branches/platform/_approval', $this->oRepo->sGetTargetLocation());
    }

}
