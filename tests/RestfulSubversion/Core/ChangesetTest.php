<?php

class RestfulSubversion_Core_ChangesetTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        $this->oChangeset = new RestfulSubversion_Core_Changeset(new RestfulSubversion_Core_Revision('12345'));
        $this->oChangeset->setAuthor('Han Solo');
        $this->oChangeset->setDateTime('2011-02-18 22:56:00');
        $this->oChangeset->setMessage('Hello World');
        $this->oChangeset->addPathOperation('M', new RestfulSubversion_Core_RepoPath('/foo/bar.php'));
        $this->oChangeset->addPathOperation('A', new RestfulSubversion_Core_RepoPath('/foo/foo.php'));
        $this->oChangeset->addPathOperation('A', new RestfulSubversion_Core_RepoPath('/foo/targetfile.php'), new RestfulSubversion_Core_RepoPath('/foo/sourcefile.php'), new RestfulSubversion_Core_Revision('12344'));
        $this->oChangeset->addPathOperation('D', new RestfulSubversion_Core_RepoPath('/foo/other.php'));
    }

    public function test_getRevision() {
        $this->assertTrue(is_a($this->oChangeset->oGetRevision(), 'RestfulSubversion_Core_Revision'));
        $this->assertSame('12345', (string)$this->oChangeset->oGetRevision());
    }

    public function test_getAuthor() {
        $this->assertSame('Han Solo', $this->oChangeset->sGetAuthor());
    }

    public function test_getDateTime() {
        $this->assertSame('2011-02-18 22:56:00', $this->oChangeset->sGetDateTime());
    }
    
    public function test_getMessage() {
        $this->assertSame('Hello World', $this->oChangeset->sGetMessage());
    }

    public function test_getPathOperations() {
        $aaExpected = array(
                            array('sAction' => 'M', 'oPath' => new RestfulSubversion_Core_RepoPath('/foo/bar.php')),
                            array('sAction' => 'A', 'oPath' => new RestfulSubversion_Core_RepoPath('/foo/foo.php')),
                            array('sAction' => 'A', 'oPath' => new RestfulSubversion_Core_RepoPath('/foo/targetfile.php'), 'oCopyfromPath' => new RestfulSubversion_Core_RepoPath('/foo/sourcefile.php'), 'oCopyfromRev' => new RestfulSubversion_Core_Revision('12344')),
                            array('sAction' => 'D', 'oPath' => new RestfulSubversion_Core_RepoPath('/foo/other.php'))
        );

        $aaActual = $this->oChangeset->aaGetPathOperations();
        
        $this->assertEquals($aaExpected, $aaActual);
    }

}
