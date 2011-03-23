<?php

class RestfulSubversion_Core_RepoCommandExecutorTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		`rm -rf /var/tmp/RestfulSubversionExecutorTest`;
	}

	public function test_execution() {
		$sCommand = "svn log -r 6 -v --xml file://".realpath(RestfulSubversion_Helper_Bootstrap::sGetPackageRoot()."/../../tests/_testrepo")." | grep -v '<paths>' | grep -v '</paths>' | grep '<path' -A 2 | grep 'action'";

		$this->assertSame('   action="M">/branches/my-hammer2/_production/2010-01-02/b.php</path>'."\n",
		                  RestfulSubversion_Core_CommandLineExecutor::oGetInstance()->sGetCommandResult($sCommand));
	}

	public function test_singletonReturnsSameInstance() {
		$this->assertTrue(RestfulSubversion_Core_CommandLineExecutor::oGetInstance() === RestfulSubversion_Core_CommandLineExecutor::oGetInstance(),
                         'Singleton is not working!'
		                );

	}

	public function test_singletonIsNotDirectlyInstantiable() {
		$oReflection = new ReflectionClass('RestfulSubversion_Core_CommandLineExecutor');

		$this->assertFalse($oReflection->isInstantiable(),
		                  'Singleton instantiable. Please declare the construct as private or protected'
		                 );
	}

	public function test_cloningImpossible() {
		$bExceptionThrown = FALSE;
		$o = RestfulSubversion_Core_CommandLineExecutor::oGetInstance();

		try {
			clone($o);
		} catch (RestfulSubversion_Core_Exception $e) {
			$bExceptionThrown = TRUE;
		}

		$this->assertTrue($bExceptionThrown);
	}

}
