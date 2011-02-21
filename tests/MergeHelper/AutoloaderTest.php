<?php

class MergeHelper_AutoloaderTest extends PHPUnit_Framework_TestCase {

	public function test_existingFile() {
		$this->assertSame('RepoCache.php', MergeHelper_Autoloader::load('RepoCache'));
	}

	public function test_nonExistantFile() {
		$this->assertFalse(MergeHelper_Autoloader::load('dewdew.php'));
	}

}
