<?php

class RestfulSubversion_Core_RepoCacheTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        $oCacheDb = new PDO('sqlite:/var/tmp/PHPRestfulSubversion_TestDb.sqlite', NULL, NULL);
        $this->oRepoCache = new RestfulSubversion_Core_RepoCache($oCacheDb);
    }

    public function tearDown() {
        $this->oRepoCache->resetCache();
    }

    public function test_getHighestRevisionInCache() {
        $oChangeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('12345'));
        $oChangeset->setAuthor('Han Solo');
        $oChangeset->setDateTime('2011-02-18 22:56:00');
        $oChangeset->setMessage('Hello World');
        $oChangeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/bar.php'));

        $this->oRepoCache->addChangeset($oChangeset);

        $this->assertEquals(new RestfulSubversion_Core_Revision('12345'), $this->oRepoCache->oGetHighestRevision());
    }

    public function test_getHighestRevisionInCacheForEmptyCache() {
        $this->assertFalse($this->oRepoCache->oGetHighestRevision());
    }

    public function test_getChangesetForRevisionSimple() {
        $oChangeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('12345'));
        $oChangeset->setAuthor('Han Solo');
        $oChangeset->setDateTime('2011-02-18 22:56:00');
        $oChangeset->setMessage('Hello World');
        $oChangeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/bar.php'));

        $this->oRepoCache->addChangeset($oChangeset);
        unset($this->oRepoCache);
        $this->setUp();

        $this->assertEquals($oChangeset, $this->oRepoCache->oGetChangesetForRevision(new RestfulSubversion_Core_Revision('12345')));
    }

    public function test_getChangesetForRevisionComplex() {
        $oChangeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('12345'));
        $oChangeset->setAuthor('Han Solo');
        $oChangeset->setDateTime('2011-02-18 22:56:00');
        $oChangeset->setMessage('Hello World');
        $oChangeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/bar.php'));
        $oChangeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/bar/bar.php'), new RestfulSubversion_Core_RepoPath('/foo/bar/old.php'), new RestfulSubversion_Core_Revision('12344'));

        $this->oRepoCache->addChangeset($oChangeset);
        unset($this->oRepoCache);
        $this->setUp();

        $this->assertEquals($oChangeset, $this->oRepoCache->oGetChangesetForRevision(new RestfulSubversion_Core_Revision('12345')));
    }

    public function test_getChangesetsWithPathEndingOnAscending() {
        $aoExpected = array();

        $oChangeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1234'));
        $oChangeset->setAuthor('Han Solo');
        $oChangeset->setDateTime('2011-02-18 22:56:00');
        $oChangeset->setMessage('Hello World');
        $oChangeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/a.php'));

        $this->oRepoCache->addChangeset($oChangeset);
        $aoExpected[] = $oChangeset;

        $oChangeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1235'));
        $oChangeset->setAuthor('Han Solo');
        $oChangeset->setDateTime('2011-02-19 22:56:00');
        $oChangeset->setMessage('Hello World');
        $oChangeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/ar.php'));

        $this->oRepoCache->addChangeset($oChangeset);

        $oChangeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1236'));
        $oChangeset->setAuthor('Han Solo');
        $oChangeset->setDateTime('2011-02-20 22:56:00');
        $oChangeset->setMessage('Hello World');
        $oChangeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/bar/bla.php'));

        $this->oRepoCache->addChangeset($oChangeset);
        $aoExpected[] = $oChangeset;

        $this->assertEquals(array($aoExpected,
                                  $aoExpected),
                            array($this->oRepoCache->aoGetChangesetsWithPathEndingOn('a.php'),
                                  $this->oRepoCache->aoGetChangesetsWithPathEndingOn('a.php', 'ascending')));
    }

    public function test_getChangesetsWithPathEndingOnDescending() {
        $aoExpected = array();

        $oChangeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1234'));
        $oChangeset->setAuthor('Han Solo');
        $oChangeset->setDateTime('2011-02-18 22:56:00');
        $oChangeset->setMessage('Hello World');
        $oChangeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/a.php'));

        $this->oRepoCache->addChangeset($oChangeset);
        $aoExpected[1] = $oChangeset;

        $oChangeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1235'));
        $oChangeset->setAuthor('Han Solo');
        $oChangeset->setDateTime('2011-02-19 22:56:00');
        $oChangeset->setMessage('Hello World');
        $oChangeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/ar.php'));

        $this->oRepoCache->addChangeset($oChangeset);

        $oChangeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1236'));
        $oChangeset->setAuthor('Han Solo');
        $oChangeset->setDateTime('2011-02-20 22:56:00');
        $oChangeset->setMessage('Hello World');
        $oChangeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/bar/bla.php'));

        $this->oRepoCache->addChangeset($oChangeset);
        $aoExpected[0] = $oChangeset;

        $this->assertEquals($aoExpected, $this->oRepoCache->aoGetChangesetsWithPathEndingOn('a.php', 'descending'));
    }

    public function test_getChangesetsWithPathEndingOnDescendingLimited() {
        $aoExpected = array();

        $oChangeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1234'));
        $oChangeset->setAuthor('Han Solo');
        $oChangeset->setDateTime('2011-02-18 22:56:00');
        $oChangeset->setMessage('Hello World');
        $oChangeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/a.php'));

        $this->oRepoCache->addChangeset($oChangeset);

        $oChangeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1235'));
        $oChangeset->setAuthor('Han Solo');
        $oChangeset->setDateTime('2011-02-19 22:56:00');
        $oChangeset->setMessage('Hello World');
        $oChangeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/ar.php'));

        $this->oRepoCache->addChangeset($oChangeset);

        $oChangeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1236'));
        $oChangeset->setAuthor('Han Solo');
        $oChangeset->setDateTime('2011-02-20 22:56:00');
        $oChangeset->setMessage('Hello World');
        $oChangeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/bar/bla.php'));

        $this->oRepoCache->addChangeset($oChangeset);
        $aoExpected[0] = $oChangeset;

        $this->assertEquals($aoExpected, $this->oRepoCache->aoGetChangesetsWithPathEndingOn('a.php', 'descending', 1));
    }

    public function test_getChangesetsWithMessageContainingTextAscending() {
        $aoExpected = array();
        
        $oChangeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1234'));
        $oChangeset->setAuthor('Han Solo');
        $oChangeset->setDateTime('2011-02-18 22:56:00');
        $oChangeset->setMessage('Hello World');
        $oChangeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/a.php'));

        $this->oRepoCache->addChangeset($oChangeset);
        $aoExpected[] = $oChangeset;

        $oChangeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1235'));
        $oChangeset->setAuthor('Han Solo');
        $oChangeset->setDateTime('2011-02-19 22:56:00');
        $oChangeset->setMessage('Helloworlds');
        $oChangeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/ar.php'));

        $this->oRepoCache->addChangeset($oChangeset);
        $aoExpected[] = $oChangeset;

        $oChangeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1236'));
        $oChangeset->setAuthor('Han Solo');
        $oChangeset->setDateTime('2011-02-20 22:56:00');
        $oChangeset->setMessage('Hello W orld');
        $oChangeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/bar/bla.php'));

        $this->oRepoCache->addChangeset($oChangeset);

        $this->assertEquals(array($aoExpected,
                                  $aoExpected),
                            array($this->oRepoCache->aoGetChangesetsWithMessageContainingText('world'),
                                  $this->oRepoCache->aoGetChangesetsWithMessageContainingText('world', 'ascending')));
    }

    public function test_getChangesetsWithMessageContainingTextOrderDescending() {
        $aoExpected = array();

        $oChangeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1234'));
        $oChangeset->setAuthor('Han Solo');
        $oChangeset->setDateTime('2011-02-18 22:56:00');
        $oChangeset->setMessage('Hello World');
        $oChangeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/a.php'));

        $this->oRepoCache->addChangeset($oChangeset);
        $aoExpected[1] = $oChangeset;

        $oChangeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1235'));
        $oChangeset->setAuthor('Han Solo');
        $oChangeset->setDateTime('2011-02-19 22:56:00');
        $oChangeset->setMessage('Helloworlds');
        $oChangeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/ar.php'));

        $this->oRepoCache->addChangeset($oChangeset);
        $aoExpected[0] = $oChangeset;

        $oChangeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1236'));
        $oChangeset->setAuthor('Han Solo');
        $oChangeset->setDateTime('2011-02-20 22:56:00');
        $oChangeset->setMessage('Hello W orld');
        $oChangeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/bar/bla.php'));

        $this->oRepoCache->addChangeset($oChangeset);

        $this->assertEquals($aoExpected, $this->oRepoCache->aoGetChangesetsWithMessageContainingText('world', 'descending'));
    }

    public function test_getChangesetsWithMessageContainingTextOrderDescendingLimited() {
        $aoExpected = array();

        $oChangeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1234'));
        $oChangeset->setAuthor('Han Solo');
        $oChangeset->setDateTime('2011-02-18 22:56:00');
        $oChangeset->setMessage('Hello World');
        $oChangeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/a.php'));

        $this->oRepoCache->addChangeset($oChangeset);

        $oChangeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1235'));
        $oChangeset->setAuthor('Han Solo');
        $oChangeset->setDateTime('2011-02-19 22:56:00');
        $oChangeset->setMessage('Helloworlds');
        $oChangeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/ar.php'));

        $this->oRepoCache->addChangeset($oChangeset);
        $aoExpected[] = $oChangeset;

        $oChangeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1236'));
        $oChangeset->setAuthor('Han Solo');
        $oChangeset->setDateTime('2011-02-20 22:56:00');
        $oChangeset->setMessage('Hello W orld');
        $oChangeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/bar/bla.php'));

        $this->oRepoCache->addChangeset($oChangeset);

        reset($aoExpected);
        $this->assertEquals($aoExpected, $this->oRepoCache->aoGetChangesetsWithMessageContainingText('world', 'descending', 1));
    }

    public function test_getChangesetsWithMessageContainingTextNoTextGiven() {
        $oChangeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1234'));
        $oChangeset->setAuthor('Han Solo');
        $oChangeset->setDateTime('2011-02-18 22:56:00');
        $oChangeset->setMessage('Hello World');
        $oChangeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/a.php'));

        $this->oRepoCache->addChangeset($oChangeset);

        $oChangeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1235'));
        $oChangeset->setAuthor('Han Solo');
        $oChangeset->setDateTime('2011-02-19 22:56:00');
        $oChangeset->setMessage('Helloworlds');
        $oChangeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/ar.php'));

        $this->oRepoCache->addChangeset($oChangeset);

        $oChangeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1236'));
        $oChangeset->setAuthor('Han Solo');
        $oChangeset->setDateTime('2011-02-20 22:56:00');
        $oChangeset->setMessage('Hello W orld');
        $oChangeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/bar/bla.php'));

        $this->oRepoCache->addChangeset($oChangeset);

        $aoExpected = array();
        $this->assertEquals($aoExpected, $this->oRepoCache->aoGetChangesetsWithMessageContainingText(''));
    }

    public function test_getNonExistantChangeset() {
        $this->assertNull($this->oRepoCache->oGetChangesetForRevision(new RestfulSubversion_Core_Revision('98765')));
    }

    /**
     * @expectedException RestfulSubversion_Core_RepoCacheRevisionAlreadyInCacheCoreException
     */
    public function test_cantAddSameRevisionTwice() {
        $oChangeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('12345'));
        $oChangeset->setAuthor('Han Solo');
        $oChangeset->setDateTime('2011-02-18 22:56:00');
        $oChangeset->setMessage('Hello World');
        $oChangeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/bar.php'));

        $this->oRepoCache->addChangeset($oChangeset);

        $oChangeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('12345'));
        $oChangeset->setAuthor('Leia Skywalker');
        $oChangeset->setDateTime('2011-02-19 22:57:00');
        $oChangeset->setMessage('...');
        $oChangeset->addPathOperation('A', new RestfulSubversion_Core_RepoPath('/bar/foo.php'));

        $this->oRepoCache->addChangeset($oChangeset);
    }

}
