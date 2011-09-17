<?php

namespace RestfulSubversion\Core;

class CommandLineBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function test_CommandLineBuilderImplementcommandLineBuilderInterface()
    {
        $commandLineBuilder = new CommandLineBuilder();

        $this->assertTrue(is_a($commandLineBuilder, 'RestfulSubversion\Core\CommandLineBuilderInterface'));
    }

    public function test_setCommand()
    {
        $commandLineBuilder = new CommandLineBuilder();
        $commandLineBuilder->setCommand('ls');

        $this->assertSame('ls', $commandLineBuilder->getCommandLine());
    }

    public function test_addParameter()
    {
        $commandLineBuilder = new CommandLineBuilder();
        $commandLineBuilder->setCommand('svn');
        $commandLineBuilder->addParameter('info');

        $this->assertSame('svn info', $commandLineBuilder->getCommandLine());
    }

    public function test_addParameters()
    {
        $commandLineBuilder = new CommandLineBuilder();
        $commandLineBuilder->setCommand('svn');
        $commandLineBuilder->addParameter('help');
        $commandLineBuilder->addParameter('info');

        $this->assertSame('svn help info', $commandLineBuilder->getCommandLine());
    }

    public function test_addShortSwitch()
    {
        $commandLineBuilder = new CommandLineBuilder();
        $commandLineBuilder->setCommand('ls');
        $commandLineBuilder->addShortSwitch('lah');

        $this->assertSame('ls -lah', $commandLineBuilder->getCommandLine());
    }

    public function test_addShortSwitchWithValue()
    {
        $commandLineBuilder = new CommandLineBuilder();
        $commandLineBuilder->setCommand('svn');
        $commandLineBuilder->addShortSwitchWithValue('r', '12345');

        $this->assertSame('svn -r 12345', $commandLineBuilder->getCommandLine());
    }

    public function test_addLongSwitch()
    {
        $commandLineBuilder = new CommandLineBuilder();
        $commandLineBuilder->setCommand('svn');
        $commandLineBuilder->addLongSwitch('xml');

        $this->assertSame('svn --xml', $commandLineBuilder->getCommandLine());
    }

    public function test_addLongSwitchWithValue()
    {
        $commandLineBuilder = new CommandLineBuilder();
        $commandLineBuilder->setCommand('svn');
        $commandLineBuilder->addLongSwitchWithValue('username', 'manuel');

        $this->assertSame('svn --username=manuel', $commandLineBuilder->getCommandLine());
    }

    public function test_reset()
    {
        $commandLineBuilder = new CommandLineBuilder();
        $commandLineBuilder->setCommand('svn');
        $commandLineBuilder->addLongSwitchWithValue('username', 'manuel');
        $commandLineBuilder->reset();

        $this->assertSame('', $commandLineBuilder->getCommandLine());
    }
}
