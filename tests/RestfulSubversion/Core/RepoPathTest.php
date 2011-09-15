<?php

class RestfulSubversion_Core_RepoPathTest extends PHPUnit_Framework_TestCase {

    public function test_directoryPath() {
        $repoPath = new RestfulSubversion_Core_RepoPath('/branches/test');

        $this->assertSame('/branches/test', $repoPath->getAsString());
    }

    public function test_filePath() {
        $repoPath = new RestfulSubversion_Core_RepoPath('/branches/test/a.php');

        $this->assertSame('/branches/test/a.php', $repoPath->getAsString());
    }

    public function test_toString() {
        $repoPath = new RestfulSubversion_Core_RepoPath('/branches/test/a.php');

        $this->assertSame($repoPath->getAsString(), "$repoPath");
    }

    public function test_filenameWithTwoDotsWorks() {
        new RestfulSubversion_Core_RepoPath('/trunk/Monitoring/con/etc/ssl/certs/StartCom_Ltd..pem');
    }

    public function test_endsWithDotWorks() {
        new RestfulSubversion_Core_RepoPath('/branches/test.');
    }

    public function test_endsWithTwoDotsWorks() {
        new RestfulSubversion_Core_RepoPath('/branches/test..');
    }

    public function test_endsWithThreeDotsWorks() {
        new RestfulSubversion_Core_RepoPath('/branches/test...');
    }

    public function test_endsWithSlashAndThreeDotsWorks() {
        new RestfulSubversion_Core_RepoPath('/branches/test/...');
    }

    /**
     * @expectedException RestfulSubversion_Core_RepoPathInvalidPathCoreException
     */
    public function test_exceptionsIfContainsRelativePath() {
        new RestfulSubversion_Core_RepoPath('/branches/../test/');
    }

    /**
     * @expectedException RestfulSubversion_Core_RepoPathInvalidPathCoreException
     */
    public function test_exceptionsIfBeginsWithRelativePath() {
        new RestfulSubversion_Core_RepoPath('../branches/test');
    }

    /**
     * @expectedException RestfulSubversion_Core_RepoPathInvalidPathCoreException
     */
    public function test_exceptionsIfEndsWithRelativePath() {
        new RestfulSubversion_Core_RepoPath('/branches/test/..');
    }
    
    /**
     * @expectedException RestfulSubversion_Core_RepoPathInvalidPathCoreException
     */
    public function test_exceptionsIfEndsWithSlash() {
        new RestfulSubversion_Core_RepoPath('/branches/test/');
    }

    /**
     * @expectedException RestfulSubversion_Core_RepoPathInvalidPathCoreException
     */
    public function test_exceptionsIfNotStartsWithSlash() {
        new RestfulSubversion_Core_RepoPath('branches/test');
    }

    /**
     * @expectedException RestfulSubversion_Core_RepoPathInvalidPathCoreException
     */
    public function test_exceptionsIfContainsRelativePathPartsOneDot() {
        new RestfulSubversion_Core_RepoPath('/branches/test/./');
    }

    /**
     * @expectedException RestfulSubversion_Core_RepoPathInvalidPathCoreException
     */
    public function test_exceptionsIfContainsRelativePathPartsTwoDots() {
        new RestfulSubversion_Core_RepoPath('/../branches/test/a.php');
    }

    /**
     * @expectedException RestfulSubversion_Core_RepoPathInvalidPathCoreException
     */
    public function test_exceptionsIfContainsSvnMetadirectory() {
        new RestfulSubversion_Core_RepoPath('/branches/test/.svn');
    }

}
