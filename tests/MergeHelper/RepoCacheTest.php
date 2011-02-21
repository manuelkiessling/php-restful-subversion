<?php

class MergeHelper_RepoCacheTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		$oCacheDb = new PDO('sqlite:/var/tmp/PHPMergeHelper_TestDb.sqlite', NULL, NULL);
		$this->oRepoCache = new MergeHelper_RepoCache($oCacheDb);
	}

	public function tearDown() {
		$this->oRepoCache->resetCache();
	}

	public function test_getHighestRevisionInCache() {
		$oChangeset = new MergeHelper_Changeset(new MergeHelper_Revision('12345'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_RepoPath('/foo/bar.php'));

		$this->oRepoCache->addChangeset($oChangeset);

		$this->assertSame('12345', $this->oRepoCache->sGetHighestRevision());
	}

	public function test_getHighestRevisionInCacheForEmptyCache() {
		$this->assertFalse($this->oRepoCache->sGetHighestRevision());
	}

	public function test_retrieveChangesetFromCache() {
		$oChangeset = new MergeHelper_Changeset(new MergeHelper_Revision('12345'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_RepoPath('/foo/bar.php'));

		$this->oRepoCache->addChangeset($oChangeset);
		unset($this->oRepoCache);
		$this->setUp();

		$this->assertEquals($oChangeset, $this->oRepoCache->oGetChangesetForRevision(new MergeHelper_Revision('12345')));
	}

	public function test_findRevisionsWithPathEndingOn() {
		$oChangeset = new MergeHelper_Changeset(new MergeHelper_Revision('1234'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_RepoPath('/foo/a.php'));

		$this->oRepoCache->addChangeset($oChangeset);

		$oChangeset = new MergeHelper_Changeset(new MergeHelper_Revision('1235'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-19 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_RepoPath('/foo/ar.php'));

		$this->oRepoCache->addChangeset($oChangeset);

		$oChangeset = new MergeHelper_Changeset(new MergeHelper_Revision('1236'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-20 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_RepoPath('/foo/bar/bla.php'));

		$this->oRepoCache->addChangeset($oChangeset);

		$aoExpected = array(new MergeHelper_Revision('1236'), new MergeHelper_Revision('1234'));
		$this->assertEquals($aoExpected, $this->oRepoCache->aoGetRevisionsWithPathEndingOn('a.php'));
	}

	public function test_findRevisionsWithMessageContainingText() {
		$oChangeset = new MergeHelper_Changeset(new MergeHelper_Revision('1234'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_RepoPath('/foo/a.php'));

		$this->oRepoCache->addChangeset($oChangeset);

		$oChangeset = new MergeHelper_Changeset(new MergeHelper_Revision('1235'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-19 22:56:00');
		$oChangeset->setMessage('Helloworlds');
		$oChangeset->addPathOperation('M', new MergeHelper_RepoPath('/foo/ar.php'));

		$this->oRepoCache->addChangeset($oChangeset);

		$oChangeset = new MergeHelper_Changeset(new MergeHelper_Revision('1236'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-20 22:56:00');
		$oChangeset->setMessage('Hello W orld');
		$oChangeset->addPathOperation('M', new MergeHelper_RepoPath('/foo/bar/bla.php'));

		$this->oRepoCache->addChangeset($oChangeset);

		$aoExpected = array(new MergeHelper_Revision('1235'), new MergeHelper_Revision('1234'));
		$this->assertEquals($aoExpected, $this->oRepoCache->aoGetRevisionsWithMessageContainingText('world'));
	}

	/**
	 * @expectedException MergeHelper_RepoCacheRevisionAlreadyInCacheException
	 */
	public function test_cantAddSameRevisionTwice() {
		$oChangeset = new MergeHelper_Changeset(new MergeHelper_Revision('12345'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_RepoPath('/foo/bar.php'));

		$this->oRepoCache->addChangeset($oChangeset);

		$oChangeset = new MergeHelper_Changeset(new MergeHelper_Revision('12345'));
		$oChangeset->setAuthor('Leia Skywalker');
		$oChangeset->setDateTime('2011-02-19 22:57:00');
		$oChangeset->setMessage('...');
		$oChangeset->addPathOperation('A', new MergeHelper_RepoPath('/bar/foo.php'));

		$this->oRepoCache->addChangeset($oChangeset);
	}

}
