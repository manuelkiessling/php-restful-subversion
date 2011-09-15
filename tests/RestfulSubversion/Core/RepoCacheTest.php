<?php

class RestfulSubversion_Core_RepoCacheTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $cacheDbHandler = new PDO('sqlite:/var/tmp/PHPRestfulSubversion_TestDb.sqlite', NULL, NULL);
        $this->repoCache = new RestfulSubversion_Core_RepoCache($cacheDbHandler);
    }

    public function tearDown()
    {
        $this->repoCache->resetCache();
    }

    public function test_getHighestRevisionInCache()
    {
        $changeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('12345'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-18 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/bar.php'));

        $this->repoCache->addChangeset($changeset);

        $this->assertEquals(new RestfulSubversion_Core_Revision('12345'), $this->repoCache->getHighestRevision());
    }

    public function test_getHighestRevisionInCacheForEmptyCache()
    {
        $this->assertFalse($this->repoCache->getHighestRevision());
    }

    public function test_getChangesetForRevisionSimple()
    {
        $changeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('12345'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-18 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/bar.php'));

        $this->repoCache->addChangeset($changeset);
        unset($this->repoCache);
        $this->setUp();

        $this->assertEquals($changeset, $this->repoCache->getChangesetForRevision(new RestfulSubversion_Core_Revision('12345')));
    }

    public function test_getChangesetForRevisionComplex()
    {
        $changeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('12345'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-18 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/bar.php'));
        $changeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/bar/bar.php'), new RestfulSubversion_Core_RepoPath('/foo/bar/old.php'), new RestfulSubversion_Core_Revision('12344'));

        $this->repoCache->addChangeset($changeset);
        unset($this->repoCache);
        $this->setUp();

        $this->assertEquals($changeset, $this->repoCache->getChangesetForRevision(new RestfulSubversion_Core_Revision('12345')));
    }

    public function test_getChangesetsWithPathEndingOnAscending()
    {
        $expected = array();

        $changeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1234'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-18 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/a.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[] = $changeset;

        $changeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1235'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-19 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/ar.php'));

        $this->repoCache->addChangeset($changeset);

        $changeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1236'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-20 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/bar/bla.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[] = $changeset;

        $this->assertEquals(array($expected,
                                 $expected),
                            array($this->repoCache->getChangesetsWithPathEndingOn('a.php'),
                                 $this->repoCache->getChangesetsWithPathEndingOn('a.php', 'ascending')));
    }

    public function test_getChangesetsWithPathEndingOnDescending()
    {
        $expected = array();

        $changeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1234'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-18 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/a.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[1] = $changeset;

        $changeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1235'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-19 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/ar.php'));

        $this->repoCache->addChangeset($changeset);

        $changeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1236'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-20 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/bar/bla.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[0] = $changeset;

        $this->assertEquals($expected, $this->repoCache->getChangesetsWithPathEndingOn('a.php', 'descending'));
    }

    public function test_getChangesetsWithPathEndingOnDescendingLimited()
    {
        $expected = array();

        $changeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1234'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-18 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/a.php'));

        $this->repoCache->addChangeset($changeset);

        $changeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1235'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-19 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/ar.php'));

        $this->repoCache->addChangeset($changeset);

        $changeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1236'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-20 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/bar/bla.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[0] = $changeset;

        $this->assertEquals($expected, $this->repoCache->getChangesetsWithPathEndingOn('a.php', 'descending', 1));
    }

    public function test_getChangesetsWithMessageContainingTextAscending()
    {
        $expected = array();

        $changeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1234'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-18 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/a.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[] = $changeset;

        $changeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1235'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-19 22:56:00');
        $changeset->setMessage('Helloworlds');
        $changeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/ar.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[] = $changeset;

        $changeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1236'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-20 22:56:00');
        $changeset->setMessage('Hello W orld');
        $changeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/bar/bla.php'));

        $this->repoCache->addChangeset($changeset);

        $this->assertEquals(array($expected,
                                 $expected),
                            array($this->repoCache->getChangesetsWithMessageContainingText('world'),
                                 $this->repoCache->getChangesetsWithMessageContainingText('world', 'ascending')));
    }

    public function test_getChangesetsWithMessageContainingTextOrderDescending()
    {
        $expected = array();

        $changeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1234'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-18 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/a.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[1] = $changeset;

        $changeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1235'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-19 22:56:00');
        $changeset->setMessage('Helloworlds');
        $changeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/ar.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[0] = $changeset;

        $changeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1236'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-20 22:56:00');
        $changeset->setMessage('Hello W orld');
        $changeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/bar/bla.php'));

        $this->repoCache->addChangeset($changeset);

        $this->assertEquals($expected, $this->repoCache->getChangesetsWithMessageContainingText('world', 'descending'));
    }

    public function test_getChangesetsWithMessageContainingTextOrderDescendingLimited()
    {
        $expected = array();

        $changeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1234'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-18 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/a.php'));

        $this->repoCache->addChangeset($changeset);

        $changeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1235'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-19 22:56:00');
        $changeset->setMessage('Helloworlds');
        $changeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/ar.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[] = $changeset;

        $changeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1236'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-20 22:56:00');
        $changeset->setMessage('Hello W orld');
        $changeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/bar/bla.php'));

        $this->repoCache->addChangeset($changeset);

        reset($expected);
        $this->assertEquals($expected, $this->repoCache->getChangesetsWithMessageContainingText('world', 'descending', 1));
    }

    public function test_getChangesetsWithMessageContainingTextNoTextGiven()
    {
        $changeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1234'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-18 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/a.php'));

        $this->repoCache->addChangeset($changeset);

        $changeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1235'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-19 22:56:00');
        $changeset->setMessage('Helloworlds');
        $changeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/ar.php'));

        $this->repoCache->addChangeset($changeset);

        $changeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('1236'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-20 22:56:00');
        $changeset->setMessage('Hello W orld');
        $changeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/bar/bla.php'));

        $this->repoCache->addChangeset($changeset);

        $expected = array();
        $this->assertEquals($expected, $this->repoCache->getChangesetsWithMessageContainingText(''));
    }

    public function test_getNonExistantChangeset()
    {
        $this->assertNull($this->repoCache->getChangesetForRevision(new RestfulSubversion_Core_Revision('98765')));
    }

    /**
     * @expectedException RestfulSubversion_Core_RepoCacheRevisionAlreadyInCacheCoreException
     */
    public function test_cantAddSameRevisionTwice()
    {
        $changeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('12345'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-18 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/bar.php'));

        $this->repoCache->addChangeset($changeset);

        $changeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('12345'));
        $changeset->setAuthor('Leia Skywalker');
        $changeset->setDateTime('2011-02-19 22:57:00');
        $changeset->setMessage('...');
        $changeset->addPathOperation('A', new RestfulSubversion_Core_RepoPath('/bar/foo.php'));

        $this->repoCache->addChangeset($changeset);
    }
}
