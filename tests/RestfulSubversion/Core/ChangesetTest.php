<?php

class RestfulSubversion_Core_ChangesetTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->changeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('12345'));
        $this->changeset->setAuthor('Han Solo');
        $this->changeset->setDateTime('2011-02-18 22:56:00');
        $this->changeset->setMessage('Hello World');
        $this->changeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/bar.php'));
        $this->changeset->addPathOperation('A', new RestfulSubversion_Core_RepoPath('/foo/foo.php'));
        $this->changeset->addPathOperation('A', new RestfulSubversion_Core_RepoPath('/foo/targetfile.php'), new RestfulSubversion_Core_RepoPath('/foo/sourcefile.php'), new RestfulSubversion_Core_Revision('12344'));
        $this->changeset->addPathOperation('D', new RestfulSubversion_Core_RepoPath('/foo/other.php'));
    }

    public function test_getRevision()
    {
        $this->assertTrue(is_a($this->changeset->getRevision(), 'RestfulSubversion_Core_Revision'));
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
            array('action' => 'M', 'path' => new RestfulSubversion_Core_RepoPath('/foo/bar.php')),
            array('action' => 'A', 'path' => new RestfulSubversion_Core_RepoPath('/foo/foo.php')),
            array('action' => 'A', 'path' => new RestfulSubversion_Core_RepoPath('/foo/targetfile.php'), 'copyfromPath' => new RestfulSubversion_Core_RepoPath('/foo/sourcefile.php'), 'copyfromRev' => new RestfulSubversion_Core_Revision('12344')),
            array('action' => 'D', 'path' => new RestfulSubversion_Core_RepoPath('/foo/other.php'))
        );

        $actual = $this->changeset->getPathOperations();

        $this->assertEquals($expected, $actual);
    }
}
