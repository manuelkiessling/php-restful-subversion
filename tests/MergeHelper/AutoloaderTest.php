<?php

class MergeHelper_AutoloaderTest extends PHPUnit_Framework_TestCase {

	public function test_existingFile() {
		$this->assertSame('Manager.php', MergeHelper_Autoloader::load('MergeHelper_Manager'));
	}

	public function test_nonExistantFile() {
		$this->assertFalse(MergeHelper_Autoloader::load('dewdew.php'));
	}

}
