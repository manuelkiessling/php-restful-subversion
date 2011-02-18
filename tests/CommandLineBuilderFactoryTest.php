<?php

class MergeHelper_CommandLineBuilderFactoryTest extends PHPUnit_Framework_TestCase {

	public function test_instantiatesObject() {
		$oCommandLineBuilderFactory = new MergeHelper_CommandLineBuilderFactory();
		$oCommandLine = $oCommandLineBuilderFactory->instantiate();

		$this->assertTrue(is_object($oCommandLine));
	}
	
	public function test_instantiatedObjectImplementsCommandLineInterface() {
		$oCommandLineBuilderFactory = new MergeHelper_CommandLineBuilderFactory();
		$oCommandLine = $oCommandLineBuilderFactory->instantiate();

		$this->assertTrue(is_a($oCommandLine, 'MergeHelper_CommandLineBuilderInterface'));
	}

}
