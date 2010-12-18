<?php

class MergeHelper_RepoCacheTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		$this->oRepoCache = new MergeHelper_RepoCache('sqlite');
		$this->oRepoCache->setCacheLocation('/var/tmp');
		$this->oRepoCache->setCacheIdentifier('PHPMergeHelper_Test');
		$this->oRepoCache->clear();
	}

	public function tearDown() {
		$this->oRepoCache->clear();
	}

	public function test_addToAndRetrieveFromCache() {
		$aPaths = array('/trunk/source', '/branches/foo');
		$this->oRepoCache->addRevision(1234, $aPaths);

		$this->assertSame($aPaths, $this->oRepoCache->asGetPathsForRevision(1234));
	}

	/**
	 * @expectedException MergeHelper_RepoCacheRevisionAlreadyInCacheException
	 */
	public function test_cantAddSameRevisionTwice() {
		$this->oRepoCache->addRevision(1234, array('/trunk/source/a.php', '/branches/foo/b.php'));
		$this->oRepoCache->addRevision(1234, array('/trunk/source/c.php', '/branches/foo/d.php'));
	}

	public function test_findRevisionsWithPathEndingOn() {
		$this->oRepoCache->addRevision(1234, array('/trunk/source/a.php', '/branches/foo/b.php'));
		$this->oRepoCache->addRevision(1235, array('/trunk/source/a.php', '/branches/foo/c.php', '/branches/foo/a.php'));
		$this->oRepoCache->addRevision(1236, array('/trunk/source/d.php', '/branches/foo/b.php', '/branches/bar/a.php'));
		$this->oRepoCache->addRevision(1237, array('/totally/different.php'));

		$aExpected = array(1236, 1235, 1234);
		$this->assertSame($aExpected, $this->oRepoCache->aiGetRevisionsWithPathEndingOn('a.php'));
	}

}
