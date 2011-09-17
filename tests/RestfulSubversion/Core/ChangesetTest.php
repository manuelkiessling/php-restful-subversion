<?php

namespace RestfulSubversion\Core;

class ChangesetTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->changeset = new Changeset(new Revision('12345'));
        $this->changeset->setAuthor('Han Solo');
        $this->changeset->setDateTime('2011-02-18 22:56:00');
        $this->changeset->setMessage('Hello World');
        $this->changeset->addPathOperation('M', new RepoPath('/foo/bar.php'));
        $this->changeset->addPathOperation('A', new RepoPath('/foo/foo.php'));
        $this->changeset->addPathOperation('A', new RepoPath('/foo/targetfile.php'), new RepoPath('/foo/sourcefile.php'), new Revision('12344'));
        $this->changeset->addPathOperation('D', new RepoPath('/foo/other.php'));
    }

    public function test_getRevision()
    {
        $this->assertTrue(is_a($this->changeset->getRevision(), 'RestfulSubversion\Core\Revision'));
        $this->assertSame('12345', (string)$this->changeset->getRevision());
    }

    public function test_getAuthor()
    {
        $this->assertSame('Han Solo', $this->changeset->getAuthor());
    }

    public function test_getDateTime()
    {
        $this->assertSame('2011-02-18 22:56:00', $this->changeset->getDateTime());
    }

    public function test_getMessage()
    {
        $this->assertSame('Hello World', $this->changeset->getMessage());
    }

    public function test_getPathOperations()
    {
        $expected = array(
            array('action' => 'M', 'path' => new RepoPath('/foo/bar.php')),
            array('action' => 'A', 'path' => new RepoPath('/foo/foo.php')),
            array('action' => 'A', 'path' => new RepoPath('/foo/targetfile.php'), 'copyfromPath' => new RepoPath('/foo/sourcefile.php'), 'copyfromRev' => new Revision('12344')),
            array('action' => 'D', 'path' => new RepoPath('/foo/other.php'))
        );

        $actual = $this->changeset->getPathOperations();

        $this->assertEquals($expected, $actual);
    }
}
