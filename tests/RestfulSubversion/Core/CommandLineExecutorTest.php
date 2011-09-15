<?php

class RestfulSubversion_Core_RepoCommandExecutorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        `rm -rf /var/tmp/RestfulSubversionExecutorTest`;
    }

    public function test_execution()
    {
        $command = "svn log -r 6 -v --xml file://" . realpath(RestfulSubversion_Helper_Bootstrap::getLibraryRoot() . "/../../tests/_testrepo") . " | grep -v '<paths>' | grep -v '</paths>' | grep '<path' -A 2 | grep 'action'";

        $this->assertSame('   action="M">/branches/my-hammer2/_production/2010-01-02/b.php</path>' . "\n",
                          RestfulSubversion_Core_CommandLineExecutor::getInstance()->getCommandResult($command));
    }

    public function test_singletonReturnsSameInstance()
    {
        $this->assertTrue(RestfulSubversion_Core_CommandLineExecutor::getInstance() === RestfulSubversion_Core_CommandLineExecutor::getInstance(),
                          'Singleton is not working!'
        );

    }

    public function test_singletonIsNotDirectlyInstantiable()
    {
        $oReflection = new ReflectionClass('RestfulSubversion_Core_CommandLineExecutor');

        $this->assertFalse($oReflection->isInstantiable(),
                           'Singleton instantiable. Please declare the construct as private or protected'
        );
    }

    public function test_cloningImpossible()
    {
        $exceptionThrown = FALSE;
        $o = RestfulSubversion_Core_CommandLineExecutor::getInstance();

        try {
            clone($o);
        } catch (RestfulSubversion_Core_Exception $e) {
            $exceptionThrown = TRUE;
        }

        $this->assertTrue($exceptionThrown);
    }
}
