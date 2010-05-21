<?php

class MergeHelper_RepoPathTest extends PHPUnit_Framework_TestCase {

	public function testNormal() {
	
		$oRepoPath = new MergeHelper_RepoPath('/branches/test');
		$this->assertSame('/branches/test', $oRepoPath->sGetAsString());
		$this->assertSame('/branches/test', "$oRepoPath");
		
		$oRepoPath = new MergeHelper_RepoPath('/branches/test/a.php');
		$this->assertSame('/branches/test/a.php', $oRepoPath->sGetAsString());
		$this->assertSame('/branches/test/a.php', "$oRepoPath");

	}

	public function testExceptions() {
	
		$bThrown = FALSE;
		try {
			$oRepoPath = new MergeHelper_RepoPath('/branches/test/');
		} catch (MergeHelper_RepoPathInvalidPathException $e) {
			$bThrown = TRUE;
		}
		$this->assertTrue($bThrown);
		
		$bThrown = FALSE;
		try {
			$oRepoPath = new MergeHelper_RepoPath('branches/test');
		} catch (MergeHelper_RepoPathInvalidPathException $e) {
			$bThrown = TRUE;
		}
		$this->assertTrue($bThrown);
		
		$bThrown = FALSE;
		try {
			$oRepoPath = new MergeHelper_RepoPath('/branches/test.');
		} catch (MergeHelper_RepoPathInvalidPathException $e) {
			$bThrown = TRUE;
		}
		$this->assertTrue($bThrown);
		
		$bThrown = FALSE;
		try {
			$oRepoPath = new MergeHelper_RepoPath('/branches/test/./');
		} catch (MergeHelper_RepoPathInvalidPathException $e) {
			$bThrown = TRUE;
		}
		$this->assertTrue($bThrown);
		
		$bThrown = FALSE;
		try {
			$oRepoPath = new MergeHelper_RepoPath('/../branches/test/a.php');
		} catch (MergeHelper_RepoPathInvalidPathException $e) {
			$bThrown = TRUE;
		}
		$this->assertTrue($bThrown);
		
		$bThrown = FALSE;
		try {
			$oRepoPath = new MergeHelper_RepoPath('/branches/test/.svn');
		} catch (MergeHelper_RepoPathInvalidPathException $e) {
			$bThrown = TRUE;
		}
		$this->assertTrue($bThrown);

	}

}
