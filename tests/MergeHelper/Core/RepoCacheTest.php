<?php

class MergeHelper_Core_RepoCacheTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		$oCacheDb = new PDO('sqlite:/var/tmp/PHPMergeHelper_TestDb.sqlite', NULL, NULL);
		$this->oRepoCache = new MergeHelper_Core_RepoCache($oCacheDb);
	}

	public function tearDown() {
		$this->oRepoCache->resetCache();
	}

	public function test_getHighestRevisionInCache() {
		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12345'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/foo/bar.php'));

		$this->oRepoCache->addChangeset($oChangeset);

		$this->assertEquals(new MergeHelper_Core_Revision('12345'), $this->oRepoCache->oGetHighestRevision());
	}

	public function test_getHighestRevisionInCacheForEmptyCache() {
		$this->assertFalse($this->oRepoCache->oGetHighestRevision());
	}

	public function test_getChangesetForRevisionSimple() {
		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12345'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/foo/bar.php'));

		$this->oRepoCache->addChangeset($oChangeset);
		unset($this->oRepoCache);
		$this->setUp();

		$this->assertEquals($oChangeset, $this->oRepoCache->oGetChangesetForRevision(new MergeHelper_Core_Revision('12345')));
	}

	public function test_getChangesetForRevisionComplex() {
		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12345'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/foo/bar.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/foo/bar/bar.php'), new MergeHelper_Core_RepoPath('/foo/bar/old.php'), new MergeHelper_Core_Revision('12344'));

		$this->oRepoCache->addChangeset($oChangeset);
		unset($this->oRepoCache);
		$this->setUp();

		$this->assertEquals($oChangeset, $this->oRepoCache->oGetChangesetForRevision(new MergeHelper_Core_Revision('12345')));
	}

	public function test_getChangesetsWithPathEndingOn() {
		$aoExpected = array();

		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('1234'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/foo/a.php'));

		$this->oRepoCache->addChangeset($oChangeset);
		$aoExpected[] = $oChangeset;

		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('1235'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-19 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/foo/ar.php'));

		$this->oRepoCache->addChangeset($oChangeset);

		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('1236'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-20 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/foo/bar/bla.php'));

		$this->oRepoCache->addChangeset($oChangeset);
		$aoExpected[] = $oChangeset;

		$this->assertEquals($aoExpected, $this->oRepoCache->aoGetChangesetsWithPathEndingOn('a.php'));
	}

	public function test_getChangesetsWithMessageContainingText() {
		$aoExpected = array();
		
		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('1234'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/foo/a.php'));

		$this->oRepoCache->addChangeset($oChangeset);
		$aoExpected[] = $oChangeset;

		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('1235'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-19 22:56:00');
		$oChangeset->setMessage('Helloworlds');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/foo/ar.php'));

		$this->oRepoCache->addChangeset($oChangeset);
		$aoExpected[] = $oChangeset;

		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('1236'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-20 22:56:00');
		$oChangeset->setMessage('Hello W orld');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/foo/bar/bla.php'));

		$this->oRepoCache->addChangeset($oChangeset);

		$this->assertEquals($aoExpected, $this->oRepoCache->aoGetChangesetsWithMessageContainingText('world'));
	}

	public function test_getChangesetsWithMessageContainingTextNoTextGiven() {
		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('1234'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/foo/a.php'));

		$this->oRepoCache->addChangeset($oChangeset);

		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('1235'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-19 22:56:00');
		$oChangeset->setMessage('Helloworlds');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/foo/ar.php'));

		$this->oRepoCache->addChangeset($oChangeset);

		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('1236'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-20 22:56:00');
		$oChangeset->setMessage('Hello W orld');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/foo/bar/bla.php'));

		$this->oRepoCache->addChangeset($oChangeset);

		$aoExpected = array();
		$this->assertEquals($aoExpected, $this->oRepoCache->aoGetChangesetsWithMessageContainingText(''));
	}

	/**
	 * @expectedException MergeHelper_Core_RepoCacheRevisionAlreadyInCacheCoreException
	 */
	public function test_cantAddSameRevisionTwice() {
		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12345'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/foo/bar.php'));

		$this->oRepoCache->addChangeset($oChangeset);

		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12345'));
		$oChangeset->setAuthor('Leia Skywalker');
		$oChangeset->setDateTime('2011-02-19 22:57:00');
		$oChangeset->setMessage('...');
		$oChangeset->addPathOperation('A', new MergeHelper_Core_RepoPath('/bar/foo.php'));

		$this->oRepoCache->addChangeset($oChangeset);
	}

}
