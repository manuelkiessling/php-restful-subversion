<?php

class MergeHelper_CommandLineTest extends PHPUnit_Framework_TestCase {

	public function test_CommandLineImplementsCommandLineInterface() {
		$oCommandLine = new MergeHelper_CommandLine();

		$this->assertTrue(is_a($oCommandLine, 'MergeHelper_CommandLineInterface'));
	}
	
	public function test_setCommand() {
		$oCommandLine = new MergeHelper_CommandLine();
		$oCommandLine->setCommand('ls');

		$this->assertSame('ls', $oCommandLine->sGetCommandLine());
	}
	
	public function test_addParameter() {
		$oCommandLine = new MergeHelper_CommandLine();
		$oCommandLine->setCommand('svn');
		$oCommandLine->addParameter('info');

		$this->assertSame('svn info', $oCommandLine->sGetCommandLine());
	}
	
	public function test_addParameters() {
		$oCommandLine = new MergeHelper_CommandLine();
		$oCommandLine->setCommand('svn');
		$oCommandLine->addParameter('help');
		$oCommandLine->addParameter('info');

		$this->assertSame('svn help info', $oCommandLine->sGetCommandLine());
	}
	
	public function test_addShortSwitch() {
		$oCommandLine = new MergeHelper_CommandLine();
		$oCommandLine->setCommand('ls');
		$oCommandLine->addShortSwitch('lah');

		$this->assertSame('ls -lah', $oCommandLine->sGetCommandLine());
	}

	public function test_addShortSwitchWithValue() {
		$oCommandLine = new MergeHelper_CommandLine();
		$oCommandLine->setCommand('svn');
		$oCommandLine->addShortSwitchWithValue('r', '12345');

		$this->assertSame('svn -r 12345', $oCommandLine->sGetCommandLine());
	}
	
	public function test_addLongSwitch() {
		$oCommandLine = new MergeHelper_CommandLine();
		$oCommandLine->setCommand('svn');
		$oCommandLine->addLongSwitch('xml');

		$this->assertSame('svn --xml', $oCommandLine->sGetCommandLine());
	}

	public function test_addLongSwitchWithValue() {
		$oCommandLine = new MergeHelper_CommandLine();
		$oCommandLine->setCommand('svn');
		$oCommandLine->addLongSwitchWithValue('username', 'manuel');

		$this->assertSame('svn --username=manuel', $oCommandLine->sGetCommandLine());
	}
	
}
