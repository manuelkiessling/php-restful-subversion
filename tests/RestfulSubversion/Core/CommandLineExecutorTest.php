<?php

namespace RestfulSubversion\Core;
use RestfulSubversion\Helper\Bootstrap;

class RepoCommandExecutorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        `rm -rf /var/tmp/RestfulSubversionExecutorTest`;
    }

    public function test_execution()
    {
        $command = "svn log -r 6 -v --xml file://" . realpath(Bootstrap::getLibraryRoot() . "/../../tests/_testrepo") . " | grep -v '<paths>' | grep -v '</paths>' | grep '<path' -A 2 | grep 'action'";

        $this->assertSame('   action="M">/branches/my-hammer2/_production/2010-01-02/b.php</path>' . "\n",
                          CommandLineExecutor::getInstance()->getCommandResult($command));
    }

    public function test_singletonReturnsSameInstance()
    {
        $this->assertTrue(CommandLineExecutor::getInstance() === CommandLineExecutor::getInstance(),
                          'Singleton is not working!'
        );

    }

    public function test_singletonIsNotDirectlyInstantiable()
    {
        $oReflection = new \ReflectionClass('RestfulSubversion\Core\CommandLineExecutor');

        $this->assertFalse($oReflection->isInstantiable(),
                           'Singleton instantiable. Please declare the construct as private or protected'
        );
    }

    public function test_cloningImpossible()
    {
        $exceptionThrown = FALSE;
        $o = CommandLineExecutor::getInstance();

        try {
            clone($o);
        } catch (Exception $e) {
            $exceptionThrown = TRUE;
        }

        $this->assertTrue($exceptionThrown);
    }
}
