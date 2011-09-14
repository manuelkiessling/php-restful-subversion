<?php

class RestfulSubversion_Core_CommandLineBuilderTest extends PHPUnit_Framework_TestCase {

    public function test_CommandLineBuilderImplementsCommandLineBuilderInterface() {
        $oCommandLineBuilder = new RestfulSubversion_Core_CommandLineBuilder();

        $this->assertTrue(is_a($oCommandLineBuilder, 'RestfulSubversion_Core_CommandLineBuilderInterface'));
    }
    
    public function test_setCommand() {
        $oCommandLineBuilder = new RestfulSubversion_Core_CommandLineBuilder();
        $oCommandLineBuilder->setCommand('ls');

        $this->assertSame('ls', $oCommandLineBuilder->sGetCommandLine());
    }
    
    public function test_addParameter() {
        $oCommandLineBuilder = new RestfulSubversion_Core_CommandLineBuilder();
        $oCommandLineBuilder->setCommand('svn');
        $oCommandLineBuilder->addParameter('info');

        $this->assertSame('svn info', $oCommandLineBuilder->sGetCommandLine());
    }
    
    public function test_addParameters() {
        $oCommandLineBuilder = new RestfulSubversion_Core_CommandLineBuilder();
        $oCommandLineBuilder->setCommand('svn');
        $oCommandLineBuilder->addParameter('help');
        $oCommandLineBuilder->addParameter('info');

        $this->assertSame('svn help info', $oCommandLineBuilder->sGetCommandLine());
    }
    
    public function test_addShortSwitch() {
        $oCommandLineBuilder = new RestfulSubversion_Core_CommandLineBuilder();
        $oCommandLineBuilder->setCommand('ls');
        $oCommandLineBuilder->addShortSwitch('lah');

        $this->assertSame('ls -lah', $oCommandLineBuilder->sGetCommandLine());
    }

    public function test_addShortSwitchWithValue() {
        $oCommandLineBuilder = new RestfulSubversion_Core_CommandLineBuilder();
        $oCommandLineBuilder->setCommand('svn');
        $oCommandLineBuilder->addShortSwitchWithValue('r', '12345');

        $this->assertSame('svn -r 12345', $oCommandLineBuilder->sGetCommandLine());
    }
    
    public function test_addLongSwitch() {
        $oCommandLineBuilder = new RestfulSubversion_Core_CommandLineBuilder();
        $oCommandLineBuilder->setCommand('svn');
        $oCommandLineBuilder->addLongSwitch('xml');

        $this->assertSame('svn --xml', $oCommandLineBuilder->sGetCommandLine());
    }

    public function test_addLongSwitchWithValue() {
        $oCommandLineBuilder = new RestfulSubversion_Core_CommandLineBuilder();
        $oCommandLineBuilder->setCommand('svn');
        $oCommandLineBuilder->addLongSwitchWithValue('username', 'manuel');

        $this->assertSame('svn --username=manuel', $oCommandLineBuilder->sGetCommandLine());
    }
    
    public function test_reset() {
        $oCommandLineBuilder = new RestfulSubversion_Core_CommandLineBuilder();
        $oCommandLineBuilder->setCommand('svn');
        $oCommandLineBuilder->addLongSwitchWithValue('username', 'manuel');
        $oCommandLineBuilder->reset();

        $this->assertSame('', $oCommandLineBuilder->sGetCommandLine());
    }
    
}
