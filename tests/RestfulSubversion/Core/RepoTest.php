<?php

class RestfulSubversion_Core_RepoTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->repo = new RestfulSubversion_Core_Repo();
    }

    public function test_newRepoObjectHasNoLocationSet()
    {
        $this->assertNull($this->repo->getUri());
    }

    public function test_setAndGetLocation()
    {
        $this->repo->setUri('http://svn.example.com/repo');

        $this->assertSame('http://svn.example.com/repo', $this->repo->getUri());
    }

    public function test_setAndGetAuthinfoUsername()
    {
        $this->repo->setAuthinfo('user.name', 'secret');

        $this->assertSame('user.name', $this->repo->getUsername());
    }

    public function test_setAndGetAuthinfoPassword()
    {
        $this->repo->setAuthinfo('user.name', 'secret');

        $this->assertSame('secret', $this->repo->getPassword());
    }
}
