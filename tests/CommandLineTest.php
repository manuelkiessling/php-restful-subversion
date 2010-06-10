<?php

class MergeHelper_CommandLineTest extends PHPUnit_Framework_TestCase {

	public function test_setAndGetCommand() {

		$oCommandLine = new MergeHelper_CommandLine();
		$oCommandLine->setCommand('ls');
		$this->assertSame($oCommandLine->sGetCommandLine(), 'ls');

	}
	
	public function test_addParameter() {

		$oCommandLine = new MergeHelper_CommandLine();
		$oCommandLine->setCommand('svn');
		$oCommandLine->addParameter('info');
		$this->assertSame($oCommandLine->sGetCommandLine(), 'svn info');

	}
	
	public function test_addShortSwitch() {

		$oCommandLine = new MergeHelper_CommandLine();
		$oCommandLine->setCommand('ls');
		$oCommandLine->addShortSwitch('lah');
		$this->assertSame($oCommandLine->sGetCommandLine(), 'ls -lah');

	}

	public function test_addShortSwitchWithValue() {

		$oCommandLine = new MergeHelper_CommandLine();
		$oCommandLine->setCommand('svn');
		$oCommandLine->addShortSwitchWithValue('r', '12345');
		$this->assertSame($oCommandLine->sGetCommandLine(), 'svn -r 12345');

	}
	
	public function test_addLongSwitchWithValue() {

		$oCommandLine = new MergeHelper_CommandLine();
		$oCommandLine->setCommand('svn');
		$oCommandLine->addLongSwitchWithValue('username', 'manuel');
		$this->assertSame($oCommandLine->sGetCommandLine(), 'svn --username=manuel');

	}
	
}
