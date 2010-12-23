<?php

class MergeHelper_AutoloaderTest extends PHPUnit_Framework_TestCase {

	public function test_existingFile() {
		$this->assertSame('Repohandler.php', MergeHelper_Autoloader::load('MergeHelper_Repohandler'));
	}

	public function test_nonExistantFile() {
		$this->assertFalse(MergeHelper_Autoloader::load('dewdew.php'));
	}

}
