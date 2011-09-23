<?php

namespace RestfulSubversion\Helper;

class RepoCommandExecutorTest extends \PHPUnit_Framework_TestCase
{
    public function test_execution()
    {
        $command = 'echo Hello World';
        $cle = new CommandLineExecutor();

        $this->assertSame('Hello World' . "\n",
                          $cle->getCommandResult($command));
    }
}
