<?php

class MergeHelper_CommandLineFactoryTest extends PHPUnit_Framework_TestCase {

	public function test_instantiatesObject() {

		$oCommandLine = $this->oCommandLineFactory::instantiate();
		$this->assertTrue(is_object($oCommandLine));

	}
	
	public function test_instantiatedObjectImplementsCommandLineInterface() {

		$oCommandLine = $this->oCommandLineFactory::instantiate();
		$this->assertTrue(is_a('MergeHelper_CommandLineInterface'));

	}

}
