<?php

class MergeHelper_Helper_AutoloaderTest extends PHPUnit_Framework_TestCase {

	public function test_existingFile() {
		$this->assertSame('Core/RepoCache.php', MergeHelper_Helper_Autoloader::load('MergeHelper_Core_RepoCache'));
	}

	public function test_nonExistantFile() {
		$this->assertFalse(MergeHelper_Helper_Autoloader::load('dewdew.php'));
	}

}
