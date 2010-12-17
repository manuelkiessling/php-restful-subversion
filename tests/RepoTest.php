<?php

class MergeHelper_RepoTest extends PHPUnit_Framework_TestCase {

	public function test_setRepoLocationAndPaths() {
	
		$oRepo = new MergeHelper_Repo();
		$this->assertNull($oRepo->sGetLocation());
		
		$oRepo->setType(MergeHelper_Repo::TYPE_SVN);
		$this->assertSame(MergeHelper_Repo::TYPE_SVN, $oRepo->iGetType());
		
		$oRepo->setLocation('http://svn.example.com/repo');
		$this->assertSame('http://svn.example.com/repo', $oRepo->sGetLocation());
		$this->assertSame('http://svn.example.com/repo/branches', $oRepo->sGetLocationBranches());

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
		$this->assertSame('http://svn.example.com/repo/branches/my-hammer2/_production', $asGetSourceLocations[0]);
		$this->assertSame('http://svn.example.com/repo/branches/my-hammer2/_project', $asGetSourceLocations[1]);

		$oRepo->setTargetPath(new MergeHelper_RepoPath('/branches/my-hammer2/_approval'));
		$this->assertEquals(new MergeHelper_RepoPath('/branches/my-hammer2/_approval'), $oRepo->oGetTargetPath());
		$this->assertSame('http://svn.example.com/repo/branches/my-hammer2/_approval', $oRepo->sGetTargetLocation());

	}
	
	public function test_setAndEnableCacheAndGetCachePath() {

		$oRepo = new MergeHelper_Repo();		
		$oRepo->setType(MergeHelper_Repo::TYPE_SVN);		
		$oRepo->setLocation('http://svn.example.com/repo');
	
		$oRepo->setCacheDirectory(realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepocache'));
		$oRepo->enableCache();
		$this->assertSame(realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepocache').'/PHPMergeHelper.SVNCache.http___svn.example.com_repo',
		                  $oRepo->sGetCachePath());

	}
	
	public function test_getCachePathThrowsExceptionIfCacheIsNotEnabled() {
	
		$oRepo = new MergeHelper_Repo();		
		$oRepo->setType(MergeHelper_Repo::TYPE_SVN);		
		$oRepo->setLocation('http://svn.example.com/repo');
		
		$bThrown = FALSE;
		try {
			$oRepo->sGetCachePath();
		} catch (MergeHelper_RepoCannotReturnCacheDirectoryPathIfCacheIsNotEnabledException $e) {
			$bThrown = TRUE;
		}
		$this->assertTrue($bThrown);
	
	}
	
	public function test_enableCacheThrowsExceptionIfNoCacheDirectoryWasSet() {

		$oRepo = new MergeHelper_Repo();		
		$oRepo->setType(MergeHelper_Repo::TYPE_SVN);		
		$oRepo->setLocation('http://svn.example.com/repo');
	
		$bThrown = FALSE;
		try {
			$oRepo->enableCache();
		} catch (MergeHelper_RepoCannotEnableCacheIfNoCacheDirectoryWasSetException $e) {
			$bThrown = TRUE;
		}
		$this->assertTrue($bThrown);

	}
		
	public function test_disableCache() {

		$oRepo = new MergeHelper_Repo();		
		$oRepo->setType(MergeHelper_Repo::TYPE_SVN);		
		$oRepo->setLocation('http://svn.example.com/repo');
	
		$oRepo->setCacheDirectory(realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepocache'));
		$oRepo->enableCache();
		$oRepo->disableCache();
		$this->assertFalse($oRepo->bHasUsableCache());
	
	}
	
	public function test_hasUsableCache() {

		$oRepo = new MergeHelper_Repo();		
		$oRepo->setType(MergeHelper_Repo::TYPE_SVN);		
		$oRepo->setLocation('http://svn.example.com/repo');
	
		$oRepo->setCacheDirectory(realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepocache'));
		$oRepo->enableCache();
		$this->assertTrue($oRepo->bHasUsableCache());
	
	}
	
}
