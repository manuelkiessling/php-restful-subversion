<?php

class MergeHelper_RepoCommandExecutorTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		$sCommandOutput = `rm -rf /var/tmp/MergeHelperExecutorTest`;
	}

	public function test_execution() {

		$sCommand = "svn log -r 6 -v --xml file://".realpath(MergeHelper_Bootstrap::sGetPackageRoot()."/../tests/_testrepo")." | grep -v '<paths>' | grep -v '</paths>' | grep '<path' -A 2 | grep 'action'";
		$this->assertSame('   action="M">/branches/my-hammer2/_production/2010-01-02/b.php</path>'."\n",
		                  MergeHelper_RepoCommandExecutor::oGetInstance()->sGetCommandResult($sCommand));
		
	}

	public function test_cachingWorks() {

		$sCommandOutput = `mkdir /var/tmp/MergeHelperExecutorTest`;
		$sCommandOutput = `touch /var/tmp/MergeHelperExecutorTest/test1.txt`;
		$this->assertSame('test1.txt'."\n",
		                  MergeHelper_RepoCommandExecutor::oGetInstance()->sGetCommandResult('ls /var/tmp/MergeHelperExecutorTest'));

		$sCommandOutput = `touch /var/tmp/MergeHelperExecutorTest/test2.txt`;
		// We assert the same result, because an identical command returns a previously cached result
		$this->assertSame('test1.txt'."\n",
		                  MergeHelper_RepoCommandExecutor::oGetInstance()->sGetCommandResult('ls /var/tmp/MergeHelperExecutorTest'));

	}

	public function test_singleton() {
		self::assertTrue(MergeHelper_RepoCommandExecutor::oGetInstance() === MergeHelper_RepoCommandExecutor::oGetInstance(),
                         'Singleton is not working!'
		                );

		$oReflection = new ReflectionClass('MergeHelper_RepoCommandExecutor');

		self::assertFalse($oReflection->isInstantiable(),
		                  'Singleton instantiable. Please declare the construct as private or protected'
		                 );

		try {
			$cloneMethod = $oReflection->getMethod('__clone');
		} catch (ReflectionException $e) {
			self::fail($e->getMessage());
		}

		self::assertTrue($cloneMethod->isPrivate(), 'Singleton is clonable');
	}

}
