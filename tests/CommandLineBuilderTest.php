<?php

class MergeHelper_CommandLineBuilderTest extends PHPUnit_Framework_TestCase {

	public function test_CommandLineBuilderImplementsCommandLineBuilderInterface() {
		$oCommandLineBuilder = new MergeHelper_CommandLineBuilder();

		$this->assertTrue(is_a($oCommandLineBuilder, 'MergeHelper_CommandLineBuilderInterface'));
	}
	
	public function test_setCommand() {
		$oCommandLineBuilder = new MergeHelper_CommandLineBuilder();
		$oCommandLineBuilder->setCommand('ls');

		$this->assertSame('ls', $oCommandLineBuilder->sGetCommandLine());
	}
	
	public function test_addParameter() {
		$oCommandLineBuilder = new MergeHelper_CommandLineBuilder();
		$oCommandLineBuilder->setCommand('svn');
		$oCommandLineBuilder->addParameter('info');

		$this->assertSame('svn info', $oCommandLineBuilder->sGetCommandLine());
	}
	
	public function test_addParameters() {
		$oCommandLineBuilder = new MergeHelper_CommandLineBuilder();
		$oCommandLineBuilder->setCommand('svn');
		$oCommandLineBuilder->addParameter('help');
		$oCommandLineBuilder->addParameter('info');

		$this->assertSame('svn help info', $oCommandLineBuilder->sGetCommandLine());
	}
	
	public function test_addShortSwitch() {
		$oCommandLineBuilder = new MergeHelper_CommandLineBuilder();
		$oCommandLineBuilder->setCommand('ls');
		$oCommandLineBuilder->addShortSwitch('lah');

		$this->assertSame('ls -lah', $oCommandLineBuilder->sGetCommandLine());
	}

	public function test_addShortSwitchWithValue() {
		$oCommandLineBuilder = new MergeHelper_CommandLineBuilder();
		$oCommandLineBuilder->setCommand('svn');
		$oCommandLineBuilder->addShortSwitchWithValue('r', '12345');

		$this->assertSame('svn -r 12345', $oCommandLineBuilder->sGetCommandLine());
	}
	
	public function test_addLongSwitch() {
		$oCommandLineBuilder = new MergeHelper_CommandLineBuilder();
		$oCommandLineBuilder->setCommand('svn');
		$oCommandLineBuilder->addLongSwitch('xml');

		$this->assertSame('svn --xml', $oCommandLineBuilder->sGetCommandLine());
	}

	public function test_addLongSwitchWithValue() {
		$oCommandLineBuilder = new MergeHelper_CommandLineBuilder();
		$oCommandLineBuilder->setCommand('svn');
		$oCommandLineBuilder->addLongSwitchWithValue('username', 'manuel');

		$this->assertSame('svn --username=manuel', $oCommandLineBuilder->sGetCommandLine());
	}
	
}
