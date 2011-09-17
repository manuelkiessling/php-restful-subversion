<?php

namespace RestfulSubversion\Core;

class RepoPathTest extends \PHPUnit_Framework_TestCase
{
    public function test_directoryPath()
    {
        $repoPath = new RepoPath('/branches/test');

        $this->assertSame('/branches/test', $repoPath->getAsString());
    }

    public function test_filePath()
    {
        $repoPath = new RepoPath('/branches/test/a.php');

        $this->assertSame('/branches/test/a.php', $repoPath->getAsString());
    }

    public function test_toString()
    {
        $repoPath = new RepoPath('/branches/test/a.php');

        $this->assertSame($repoPath->getAsString(), "$repoPath");
    }

    public function test_filenameWithTwoDotsWorks()
    {
        new RepoPath('/trunk/Monitoring/con/etc/ssl/certs/StartCom_Ltd..pem');
    }

    public function test_endsWithDotWorks()
    {
        new RepoPath('/branches/test.');
    }

    public function test_endsWithTwoDotsWorks()
    {
        new RepoPath('/branches/test..');
    }

    public function test_endsWithThreeDotsWorks()
    {
        new RepoPath('/branches/test...');
    }

    public function test_endsWithSlashAndThreeDotsWorks()
    {
        new RepoPath('/branches/test/...');
    }

    /**
     * @expectedException RestfulSubversion\Core\RepoPathInvalidPathCoreException
     */
    public function test_exceptionsIfContainsRelativePath()
    {
        new RepoPath('/branches/../test/');
    }

    /**
     * @expectedException RestfulSubversion\Core\RepoPathInvalidPathCoreException
     */
    public function test_exceptionsIfBeginsWithRelativePath()
    {
        new RepoPath('../branches/test');
    }

    /**
     * @expectedException RestfulSubversion\Core\RepoPathInvalidPathCoreException
     */
    public function test_exceptionsIfEndsWithRelativePath()
    {
        new RepoPath('/branches/test/..');
    }

    /**
     * @expectedException RestfulSubversion\Core\RepoPathInvalidPathCoreException
     */
    public function test_exceptionsIfEndsWithSlash()
    {
        new RepoPath('/branches/test/');
    }

    /**
     * @expectedException RestfulSubversion\Core\RepoPathInvalidPathCoreException
     */
    public function test_exceptionsIfNotStartsWithSlash()
    {
        new RepoPath('branches/test');
    }

    /**
     * @expectedException RestfulSubversion\Core\RepoPathInvalidPathCoreException
     */
    public function test_exceptionsIfContainsRelativePathPartsOneDot()
    {
        new RepoPath('/branches/test/./');
    }

    /**
     * @expectedException RestfulSubversion\Core\RepoPathInvalidPathCoreException
     */
    public function test_exceptionsIfContainsRelativePathPartsTwoDots()
    {
        new RepoPath('/../branches/test/a.php');
    }

    /**
     * @expectedException RestfulSubversion\Core\RepoPathInvalidPathCoreException
     */
    public function test_exceptionsIfContainsSvnMetadirectory()
    {
        new RepoPath('/branches/test/.svn');
    }
}
