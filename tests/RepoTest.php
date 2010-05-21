<?php

class MergeHelper_RepoTest extends PHPUnit_Framework_TestCase {

	public function test_setRepoLocationAndPaths() {
	
		$oRepo = new MergeHelper_Repo();
		$this->assertNull($oRepo->sGetLocation());
		
		$oRepo->setType(MergeHelper_Repo::TYPE_SVN);
		$this->assertSame(MergeHelper_Repo::TYPE_SVN, $oRepo->iGetType());
		
		$oRepo->setLocation('http://svn.abacho.net.local/my-hammer');
		$this->assertSame('http://svn.abacho.net.local/my-hammer', $oRepo->sGetLocation());
		$this->assertSame('http://svn.abacho.net.local/my-hammer/branches', $oRepo->sGetLocationBranches());

		$oRepo->setCacheDirectory(realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepocache'));
		$this->assertSame(realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepocache').'/MergeHelper.svncache.'.sha1('http://svn.abacho.net.local/my-hammer'),
		                  $oRepo->sGetCachepath());

		$oRepo->setAuthinfo('user.name', 'secret');
		$this->assertSame('user.name', $oRepo->sGetAuthinfoUsername());
		$this->assertSame('secret', $oRepo->sGetAuthinfoPassword());

		$oRepo->addSourcePath(new MergeHelper_RepoPath('/branches/my-hammer2/_production'), 1);
		$oRepo->addSourcePath(new MergeHelper_RepoPath('/branches/my-hammer2/_project'), 1);
		
		$aoGetSourcePaths = $oRepo->aoGetSourcePaths();
		$this->assertSame(2, sizeof($aoGetSourcePaths));
		$this->assertEquals(new MergeHelper_RepoPath('/branches/my-hammer2/_production'), $aoGetSourcePaths[0]);
		$this->assertEquals(new MergeHelper_RepoPath('/branches/my-hammer2/_project'), $aoGetSourcePaths[1]);
		
		$asGetSourceLocations = $oRepo->asGetSourceLocations();
		$this->assertSame(2, sizeof($asGetSourceLocations));
		$this->assertSame('http://svn.abacho.net.local/my-hammer/branches/my-hammer2/_production', $asGetSourceLocations[0]);
		$this->assertSame('http://svn.abacho.net.local/my-hammer/branches/my-hammer2/_project', $asGetSourceLocations[1]);

		$oRepo->setTargetPath(new MergeHelper_RepoPath('/branches/my-hammer2/_approval'));
		$this->assertEquals(new MergeHelper_RepoPath('/branches/my-hammer2/_approval'), $oRepo->oGetTargetPath());
		$this->assertSame('http://svn.abacho.net.local/my-hammer/branches/my-hammer2/_approval', $oRepo->sGetTargetLocation());

	}

}
