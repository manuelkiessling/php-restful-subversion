<?php

class MergeHelper_Core_ChangesetTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		$this->oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12345'));
		$this->oChangeset->setAuthor('Han Solo');
		$this->oChangeset->setDateTime('2011-02-18 22:56:00');
		$this->oChangeset->setMessage('Hello World');
		$this->oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/foo/bar.php'));
		$this->oChangeset->addPathOperation('A', new MergeHelper_Core_RepoPath('/foo/foo.php'));
		$this->oChangeset->addPathOperation('A', new MergeHelper_Core_RepoPath('/foo/targetfile.php'), new MergeHelper_Core_RepoPath('/foo/sourcefile.php'), new MergeHelper_Core_Revision('12344'));
		$this->oChangeset->addPathOperation('D', new MergeHelper_Core_RepoPath('/foo/other.php'));
	}

	public function test_getRevision() {
		$this->assertTrue(is_a($this->oChangeset->oGetRevision(), 'MergeHelper_Core_Revision'));
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
		                    array('sAction' => 'M', 'oPath' => new MergeHelper_Core_RepoPath('/foo/bar.php')),
		                    array('sAction' => 'A', 'oPath' => new MergeHelper_Core_RepoPath('/foo/foo.php')),
		                    array('sAction' => 'A', 'oPath' => new MergeHelper_Core_RepoPath('/foo/targetfile.php'), 'oCopyfromPath' => new MergeHelper_Core_RepoPath('/foo/sourcefile.php'), 'oCopyfromRev' => new MergeHelper_Core_Revision('12344')),
		                    array('sAction' => 'D', 'oPath' => new MergeHelper_Core_RepoPath('/foo/other.php'))
		);

		$aaActual = $this->oChangeset->aaGetPathOperations();
		
		$this->assertEquals($aaExpected, $aaActual);
	}

}
