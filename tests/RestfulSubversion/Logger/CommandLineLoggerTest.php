<?php

namespace RestfulSubversion\Logger;

class CommandLineLoggerTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $cll = new CommandLineLogger();
        $this->assertTrue($cll->log(''));
    }
}
