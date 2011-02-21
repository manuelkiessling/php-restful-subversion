<?php

class MergeHelper_RepoTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		$this->oRepo = new MergeHelper_Repo();
	}

	public function test_newRepoObjectHasNoLocationSet() {
		$this->assertNull($this->oRepo->sGetLocation());
	}

	public function test_setAndGetType() {
		$this->oRepo->setType(MergeHelper_Repo::TYPE_SVN);

		$this->assertSame(MergeHelper_Repo::TYPE_SVN, $this->oRepo->iGetType());
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

		$this->oRepo->addSourcePath(new MergeHelper_RepoPath('/branches/my-hammer2/_production'));
		$this->oRepo->addSourcePath(new MergeHelper_RepoPath('/branches/my-hammer2/_project'), 1);

		$aoGetSourcePaths = $this->oRepo->aoGetSourcePaths();

		$this->assertEquals(array(
		                          new MergeHelper_RepoPath('/branches/my-hammer2/_production'),
		                          new MergeHelper_RepoPath('/branches/my-hammer2/_project')
		                         ), $aoGetSourcePaths);
	}

	public function test_addSourcePathAndGetSourceLocations() {
		$this->oRepo->setLocation('http://svn.example.com/repo');
		$this->oRepo->setAuthinfo('user.name', 'secret');

		$this->oRepo->addSourcePath(new MergeHelper_RepoPath('/branches/my-hammer2/_production'));
		$this->oRepo->addSourcePath(new MergeHelper_RepoPath('/branches/my-hammer2/_project'), 1);

		$asGetSourceLocations = $this->oRepo->asGetSourceLocations();

		$this->assertSame(array(
		                        'http://svn.example.com/repo/branches/my-hammer2/_production',
		                        'http://svn.example.com/repo/branches/my-hammer2/_project'
		                       ), $asGetSourceLocations);
	}

	public function test_setAndGetTargetPath() {
		$this->oRepo->setLocation('http://svn.example.com/repo');
		$this->oRepo->setAuthinfo('user.name', 'secret');

		$this->oRepo->setTargetPath(new MergeHelper_RepoPath('/branches/my-hammer2/_approval'));

		$this->assertEquals(new MergeHelper_RepoPath('/branches/my-hammer2/_approval'), $this->oRepo->oGetTargetPath());
	}

	public function test_setTargetPathAndGetTargetLocation() {
		$this->oRepo->setLocation('http://svn.example.com/repo');
		$this->oRepo->setAuthinfo('user.name', 'secret');

		$this->oRepo->setTargetPath(new MergeHelper_RepoPath('/branches/my-hammer2/_approval'));

		$this->assertSame('http://svn.example.com/repo/branches/my-hammer2/_approval', $this->oRepo->sGetTargetLocation());
	}

}
