<?php

namespace RestfulSubversion\Core;

class RepoCacheTest extends \PHPUnit_Framework_TestCase
{
    protected $repoCache;
    
    public function setUp()
    {
        $cacheDbHandler = new \PDO('sqlite:/var/tmp/PHPRestfulSubversion_TestDb.sqlite', null, null);
        $this->repoCache = new RepoCache($cacheDbHandler);
    }

    public function tearDown()
    {
        $this->repoCache->resetCache();
    }

    public function test_getHighestRevisionInCache()
    {
        $changeset = new Changeset(new Revision('12345'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-18 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RepoPath('/foo/bar.php'));

        $this->repoCache->addChangeset($changeset);

        $this->assertEquals(new Revision('12345'), $this->repoCache->getHighestRevision());
    }

    public function test_getHighestRevisionInCacheForEmptyCache()
    {
        $this->assertFalse($this->repoCache->getHighestRevision());
    }

    public function test_getChangesetForRevisionSimple()
    {
        $changeset = new Changeset(new Revision('12345'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-18 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RepoPath('/foo/bar.php'));

        $this->repoCache->addChangeset($changeset);
        unset($this->repoCache);
        $this->setUp();

        $this->assertEquals($changeset, $this->repoCache->getChangesetForRevision(new Revision('12345')));
    }

    public function test_getChangesetForRevisionComplex()
    {
        $changeset = new Changeset(new Revision('12345'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-18 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RepoPath('/foo/bar.php'));
        $changeset->addPathOperation('M', new RepoPath('/foo/bar/bar.php'), new RepoPath('/foo/bar/old.php'), new Revision('12344'));

        $this->repoCache->addChangeset($changeset);
        unset($this->repoCache);
        $this->setUp();

        $this->assertEquals($changeset, $this->repoCache->getChangesetForRevision(new Revision('12345')));
    }
    
    public function test_getRepoFileForRevisionAndPath()
    {
        $revision = new Revision('1234');
        $path = new RepoPath('/a/b.php');
        $file = new RepoFile($revision, $path);
        $file->setContent('Hello World');
        
        $this->repoCache->addRepoFile($file);
        unset($this->repoCache);
        $this->setUp();
        
        $actual = $this->repoCache->getRepoFileForRevisionAndPath($revision, $path);
        
        $this->assertEquals($file, $actual);
    }
    
    public function test_getRepoFileForRevisionAndPathAskingForHigherRevision()
    {
        $revision = new Revision('1234');
        $path = new RepoPath('/a/b.php');
        $file = new RepoFile($revision, $path);
        $file->setContent('Hello World');
        
        $this->repoCache->addRepoFile($file);
        
        $revision = new Revision('1235');
        $path = new RepoPath('/a/b.php');
        $file = new RepoFile($revision, $path);
        $file->setContent('Goodbye World');
        
        $this->repoCache->addRepoFile($file);
        unset($this->repoCache);
        $this->setUp();
        
        $revision = new Revision('9999');
        $path = new RepoPath('/a/b.php');
        $file = new RepoFile($revision, $path);
        $file->setContent('Goodbye World');
        
        $actual = $this->repoCache->getRepoFileForRevisionAndPath(new Revision('9999'), $path);
        
        $this->assertEquals($file, $actual);
    }
    
    public function test_getRepoFilesForRevisionAndPaths()
    {
        $paths = array();
        $files = array();

        // This should not be in the result, because the same path exists at revision 1234
        $revision = new Revision('700');
        $path = new RepoPath('/a/a.php');
        $file = new RepoFile($revision, $path);
        $file->setContent('Hello 700');
        $this->repoCache->addRepoFile($file);
        
        $revision = new Revision('1234');
        $path = new RepoPath('/a/a.php');
        $file = new RepoFile($revision, $path);
        $file->setContent('Hello World');
        $this->repoCache->addRepoFile($file);
        $paths[] = $path;
        $files[] = $file;
        
        $revision = new Revision('1234');
        $path = new RepoPath('/a/b.php');
        $file = new RepoFile($revision, $path);
        $file->setContent('Foo Bar');
        $this->repoCache->addRepoFile($file);
        $paths[] = $path;
        $files[] = $file;

        // This should be in the result, but with revision 1234
        $revision = new Revision('900');
        $path = new RepoPath('/a/c.php');
        $file = new RepoFile($revision, $path);
        $file->setContent('Hello World');
        $this->repoCache->addRepoFile($file);
        $revision = new Revision('1234');
        $file = new RepoFile($revision, $path);
        $file->setContent('Hello World');
        $paths[] = $path;
        $files[] = $file;
        
        unset($this->repoCache);
        $this->setUp();
        
        $actual = $this->repoCache->getRepoFilesForRevisionAndPaths($revision, $paths);
        
        $this->assertEquals($files, $actual);
    }

    public function test_getChangesetsWithPathEndingOnAscending()
    {
        $expected = array();

        $changeset = new Changeset(new Revision('1234'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-18 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RepoPath('/foo/a.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[] = $changeset;

        $changeset = new Changeset(new Revision('1235'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-19 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RepoPath('/foo/ar.php'));

        $this->repoCache->addChangeset($changeset);

        $changeset = new Changeset(new Revision('1236'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-20 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RepoPath('/foo/bar/bla.php'));

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

        $changeset = new Changeset(new Revision('1234'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-18 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RepoPath('/foo/a.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[1] = $changeset;

        $changeset = new Changeset(new Revision('1235'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-19 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RepoPath('/foo/ar.php'));

        $this->repoCache->addChangeset($changeset);

        $changeset = new Changeset(new Revision('1236'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-20 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RepoPath('/foo/bar/bla.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[0] = $changeset;

        $this->assertEquals($expected, $this->repoCache->getChangesetsWithPathEndingOn('a.php', 'descending'));
    }

    public function test_getChangesetsWithPathEndingOnDescendingLimited()
    {
        $expected = array();

        $changeset = new Changeset(new Revision('1234'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-18 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RepoPath('/foo/a.php'));

        $this->repoCache->addChangeset($changeset);

        $changeset = new Changeset(new Revision('1235'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-19 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RepoPath('/foo/ar.php'));

        $this->repoCache->addChangeset($changeset);

        $changeset = new Changeset(new Revision('1236'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-20 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RepoPath('/foo/bar/bla.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[0] = $changeset;

        $this->assertEquals($expected, $this->repoCache->getChangesetsWithPathEndingOn('a.php', 'descending', 1));
    }

    public function test_getChangesetsWithMessageContainingTextAscending()
    {
        $expected = array();

        $changeset = new Changeset(new Revision('1234'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-18 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RepoPath('/foo/a.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[] = $changeset;

        $changeset = new Changeset(new Revision('1235'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-19 22:56:00');
        $changeset->setMessage('Helloworlds');
        $changeset->addPathOperation('M', new RepoPath('/foo/ar.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[] = $changeset;

        $changeset = new Changeset(new Revision('1236'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-20 22:56:00');
        $changeset->setMessage('Hello W orld');
        $changeset->addPathOperation('M', new RepoPath('/foo/bar/bla.php'));

        $this->repoCache->addChangeset($changeset);

        $this->assertEquals(array($expected,
                                 $expected),
                            array($this->repoCache->getChangesetsWithMessageContainingText('world'),
                                 $this->repoCache->getChangesetsWithMessageContainingText('world', 'ascending')));
    }

    public function test_getChangesetsWithMessageContainingTextOrderDescending()
    {
        $expected = array();

        $changeset = new Changeset(new Revision('1234'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-18 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RepoPath('/foo/a.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[1] = $changeset;

        $changeset = new Changeset(new Revision('1235'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-19 22:56:00');
        $changeset->setMessage('Helloworlds');
        $changeset->addPathOperation('M', new RepoPath('/foo/ar.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[0] = $changeset;

        $changeset = new Changeset(new Revision('1236'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-20 22:56:00');
        $changeset->setMessage('Hello W orld');
        $changeset->addPathOperation('M', new RepoPath('/foo/bar/bla.php'));

        $this->repoCache->addChangeset($changeset);

        $this->assertEquals($expected, $this->repoCache->getChangesetsWithMessageContainingText('world', 'descending'));
    }

    public function test_getChangesetsWithMessageContainingTextOrderDescendingLimited()
    {
        $expected = array();

        $changeset = new Changeset(new Revision('1234'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-18 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RepoPath('/foo/a.php'));

        $this->repoCache->addChangeset($changeset);

        $changeset = new Changeset(new Revision('1235'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-19 22:56:00');
        $changeset->setMessage('Helloworlds');
        $changeset->addPathOperation('M', new RepoPath('/foo/ar.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[] = $changeset;

        $changeset = new Changeset(new Revision('1236'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-20 22:56:00');
        $changeset->setMessage('Hello W orld');
        $changeset->addPathOperation('M', new RepoPath('/foo/bar/bla.php'));

        $this->repoCache->addChangeset($changeset);

        reset($expected);
        $this->assertEquals($expected, $this->repoCache->getChangesetsWithMessageContainingText('world', 'descending', 1));
    }

    public function test_getChangesetsWithMessageContainingTextNoTextGiven()
    {
        $changeset = new Changeset(new Revision('1234'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-18 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RepoPath('/foo/a.php'));

        $this->repoCache->addChangeset($changeset);

        $changeset = new Changeset(new Revision('1235'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-19 22:56:00');
        $changeset->setMessage('Helloworlds');
        $changeset->addPathOperation('M', new RepoPath('/foo/ar.php'));

        $this->repoCache->addChangeset($changeset);

        $changeset = new Changeset(new Revision('1236'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-20 22:56:00');
        $changeset->setMessage('Hello W orld');
        $changeset->addPathOperation('M', new RepoPath('/foo/bar/bla.php'));

        $this->repoCache->addChangeset($changeset);

        $expected = array();
        $this->assertEquals($expected, $this->repoCache->getChangesetsWithMessageContainingText(''));
    }

    public function test_getNonExistantChangeset()
    {
        $this->assertNull($this->repoCache->getChangesetForRevision(new Revision('98765')));
    }

    /**
     * @expectedException RestfulSubversion\Core\RepoCacheException
     */
    public function test_cantAddSameRevisionTwice()
    {
        $changeset = new Changeset(new Revision('12345'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-18 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RepoPath('/foo/bar.php'));

        $this->repoCache->addChangeset($changeset);

        $changeset = new Changeset(new Revision('12345'));
        $changeset->setAuthor('Leia Skywalker');
        $changeset->setDateTime('2011-02-19 22:57:00');
        $changeset->setMessage('...');
        $changeset->addPathOperation('A', new RepoPath('/bar/foo.php'));

        $this->repoCache->addChangeset($changeset);
    }
    
    public function test_getChangesetsWithDefaultOptions()
    {
        $expected = array();
        
        $changeset = new Changeset(new Revision('1234'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-18 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RepoPath('/foo/a.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[] = $changeset;

        $changeset = new Changeset(new Revision('1235'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-19 22:56:00');
        $changeset->setMessage('Helloworlds');
        $changeset->addPathOperation('M', new RepoPath('/foo/ar.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[] = $changeset;

        $changeset = new Changeset(new Revision('1236'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-20 22:56:00');
        $changeset->setMessage('Hello W orld');
        $changeset->addPathOperation('M', new RepoPath('/foo/bar/bla.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[] = $changeset;
        
        $this->assertEquals($expected, $this->repoCache->getChangesets());
    }
    
    public function test_getChangesetsOrdered() {
        $expected = array();
        
        $changeset = new Changeset(new Revision('1234'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-18 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RepoPath('/foo/a.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[] = $changeset;

        $changeset = new Changeset(new Revision('1235'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-19 22:56:00');
        $changeset->setMessage('Helloworlds');
        $changeset->addPathOperation('M', new RepoPath('/foo/ar.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[] = $changeset;

        $changeset = new Changeset(new Revision('1236'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-20 22:56:00');
        $changeset->setMessage('Hello W orld');
        $changeset->addPathOperation('M', new RepoPath('/foo/bar/bla.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[] = $changeset;
        
        $expectedDesc = array();
        $i = 2;
        foreach ($expected as $changeset) {
            $expectedDesc[$i] = $changeset;
            $i--;
        }
        
        $this->assertEquals($expected, $this->repoCache->getChangesets('ascending'));
        $this->assertEquals($expectedDesc, $this->repoCache->getChangesets('descending'));
    }
    
    public function test_getChangesetsWithStartAt()
    {
        $expected = array();
        
        $changeset = new Changeset(new Revision('1234'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-18 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RepoPath('/foo/a.php'));

        $this->repoCache->addChangeset($changeset);

        $changeset = new Changeset(new Revision('1235'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-19 22:56:00');
        $changeset->setMessage('Helloworlds');
        $changeset->addPathOperation('M', new RepoPath('/foo/ar.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[] = $changeset;

        $changeset = new Changeset(new Revision('1236'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-20 22:56:00');
        $changeset->setMessage('Hello W orld');
        $changeset->addPathOperation('M', new RepoPath('/foo/bar/bla.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[] = $changeset;
        
        $this->assertEquals($expected, $this->repoCache->getChangesets('ascending', '1235'));
    }
    
    public function test_getChangesetsWithLimit()
    {
        $expected = array();
        
        $changeset = new Changeset(new Revision('1234'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-18 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RepoPath('/foo/a.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[] = $changeset;

        $changeset = new Changeset(new Revision('1235'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-19 22:56:00');
        $changeset->setMessage('Helloworlds');
        $changeset->addPathOperation('M', new RepoPath('/foo/ar.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[] = $changeset;

        $changeset = new Changeset(new Revision('1236'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-20 22:56:00');
        $changeset->setMessage('Hello W orld');
        $changeset->addPathOperation('M', new RepoPath('/foo/bar/bla.php'));

        $this->repoCache->addChangeset($changeset);
        
        $this->assertEquals($expected, $this->repoCache->getChangesets('ascending', 0, 2));
    }
    
    public function test_getChangesetsWithAllParams()
    {
        $expected = array();
        
        $changeset = new Changeset(new Revision('1234'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-18 22:56:00');
        $changeset->setMessage('Hello World');
        $changeset->addPathOperation('M', new RepoPath('/foo/a.php'));

        $this->repoCache->addChangeset($changeset);

        $changeset = new Changeset(new Revision('1235'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-19 22:56:00');
        $changeset->setMessage('Helloworlds');
        $changeset->addPathOperation('M', new RepoPath('/foo/ar.php'));

        $this->repoCache->addChangeset($changeset);

        $changeset = new Changeset(new Revision('1236'));
        $changeset->setAuthor('Han Solo');
        $changeset->setDateTime('2011-02-20 22:56:00');
        $changeset->setMessage('Hello W orld');
        $changeset->addPathOperation('M', new RepoPath('/foo/bar/bla.php'));

        $this->repoCache->addChangeset($changeset);
        $expected[] = $changeset;
        
        $this->assertEquals($expected, $this->repoCache->getChangesets('descending', 1239, 1));
    }
}
