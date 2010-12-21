<?php

class MergeHelper_RepoCacheTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		$oCacheDb = new PDO('sqlite:/var/tmp/PHPMergeHelper_TestDb.sqlite', NULL, NULL);
		$this->oRepoCache = new MergeHelper_RepoCache($oCacheDb);
		$this->oRepoCache->resetCache();
	}

	public function tearDown() {
		$this->oRepoCache->resetCache();
	}

	public function test_getHighestRevisionInCache() {
		$this->oRepoCache->addRevision(1234, 'Hello World', array('/trunk/source/a.php', '/branches/foo/b.php'));
		$this->oRepoCache->addRevision(1236, 'Hello World', array('/trunk/source/d.php', '/branches/foo/b.php', '/branches/bar/a.php'));
		$this->oRepoCache->addRevision(1237, 'Hello World', array('/totally/different.php'));
		$this->oRepoCache->addRevision(1235, 'Hello World', array('/trunk/source/a.php', '/branches/foo/c.php', '/branches/foo/a.php'));

		$this->assertSame(1237, $this->oRepoCache->iGetHighestRevision());
	}

	public function test_getHighestRevisionInCacheForEmptyCache() {
		$this->assertFalse($this->oRepoCache->iGetHighestRevision());
	}

	public function test_addToAndRetrievePathsFromCache() {
		$aPaths = array('/trunk/source', '/branches/foo');
		$this->oRepoCache->addRevision(1234, 'Hello World', $aPaths);

		$this->assertSame($aPaths, $this->oRepoCache->asGetPathsForRevision(1234));
	}

	public function test_addToAndRetrieveMessageFromCache() {
		$aPaths = array('/trunk/source', '/branches/foo');
		$this->oRepoCache->addRevision(1234, 'Hello World', $aPaths);

		$this->assertSame('Hello World', $this->oRepoCache->asGetMessageForRevision(1234));
	}

	/**
	 * @expectedException MergeHelper_RepoCacheRevisionAlreadyInCacheException
	 */
	public function test_cantAddSameRevisionTwice() {
		$this->oRepoCache->addRevision(1234, 'Hello World', array('/trunk/source/a.php', '/branches/foo/b.php'));
		$this->oRepoCache->addRevision(1234, 'Hello World', array('/trunk/source/c.php', '/branches/foo/d.php'));
	}

	public function test_findRevisionsWithPathEndingOn() {
		$this->oRepoCache->addRevision(1234, 'Hello World', array('/trunk/source/a.php', '/branches/foo/b.php'));
		$this->oRepoCache->addRevision(1235, 'Hello World', array('/trunk/source/a.php', '/branches/foo/c.php', '/branches/foo/a.php'));
		$this->oRepoCache->addRevision(1236, 'Hello World', array('/trunk/source/d.php', '/branches/foo/b.php', '/branches/bar/a.php'));
		$this->oRepoCache->addRevision(1237, 'Hello World', array('/totally/different.php'));

		$aExpected = array(1236, 1235, 1234);
		$this->assertSame($aExpected, $this->oRepoCache->aiGetRevisionsWithPathEndingOn('a.php'));
	}

	public function test_findRevisionsWithMessageContainingText() {
		$this->oRepoCache->addRevision(1234, '', array('/trunk/source/a.php', '/branches/foo/b.php'));
		$this->oRepoCache->addRevision(1235, 'Hello World', array('/trunk/source/a.php', '/branches/foo/b.php'));
		$this->oRepoCache->addRevision(1236, 'Hello Other World', array('/trunk/source/a.php', '/branches/foo/b.php'));

		$aExpected = array(1236, 1235);
		$this->assertSame($aExpected, $this->oRepoCache->aiGetRevisionsWithMessageContainingText('world'));
	}

}
