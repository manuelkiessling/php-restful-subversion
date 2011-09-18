<?php

namespace RestfulSubversion\Helper;

class RepoCommandExecutorTest extends \PHPUnit_Framework_TestCase
{
    public function test_execution()
    {
        $command = "svn log -r 6 -v --xml file://" . realpath(Bootstrap::getLibraryRoot() . "/../../tests/_testrepo") . " | grep -v '<paths>' | grep -v '</paths>' | grep '<path' -A 2 | grep 'action'";
        $cle = new CommandLineExecutor();

        $this->assertSame('   action="M">/branches/my-hammer2/_production/2010-01-02/b.php</path>' . "\n",
                          $cle->getCommandResult($command));
    }
}
