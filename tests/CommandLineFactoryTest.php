<?php

class MergeHelper_CommandLineFactoryTest extends PHPUnit_Framework_TestCase {

	public function test_instantiatesObject() {

		$oCommandLineFactory = new MergeHelper_CommandLineFactory();
		$oCommandLine = $oCommandLineFactory->instantiate();
		$this->assertTrue(is_object($oCommandLine));

	}
	
	public function test_instantiatedObjectImplementsCommandLineInterface() {

		$oCommandLineFactory = new MergeHelper_CommandLineFactory();
		$oCommandLine = $oCommandLineFactory->instantiate();
		$this->assertTrue(is_a($oCommandLine, 'MergeHelper_CommandLineInterface'));

	}

}
