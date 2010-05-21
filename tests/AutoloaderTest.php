<?php

class MergeHelper_AutoloaderTest extends PHPUnit_Framework_TestCase {

	public function test() {
	
		$this->assertSame('Repohandler.php', MergeHelper_Autoloader::load('MergeHelper_Repohandler'));
		$this->assertFalse(MergeHelper_Autoloader::load('dewdew.php'));

	}

}
