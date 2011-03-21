<?php

class MergeHelper_Core_RepoPathTest extends PHPUnit_Framework_TestCase {

	public function test_directoryPath() {
		$oRepoPath = new MergeHelper_Core_RepoPath('/branches/test');

		$this->assertSame('/branches/test', $oRepoPath->sGetAsString());
	}

	public function test_filePath() {
		$oRepoPath = new MergeHelper_Core_RepoPath('/branches/test/a.php');

		$this->assertSame('/branches/test/a.php', $oRepoPath->sGetAsString());
	}

	public function test_toString() {
		$oRepoPath = new MergeHelper_Core_RepoPath('/branches/test/a.php');

		$this->assertSame($oRepoPath->sGetAsString(), "$oRepoPath");
	}

	public function test_filenameWithTwoDotsWorks() {
		new MergeHelper_Core_RepoPath('/trunk/Monitoring/con/etc/ssl/certs/StartCom_Ltd..pem');
	}

	public function test_endsWithDotWorks() {
		new MergeHelper_Core_RepoPath('/branches/test.');
	}

	public function test_endsWithTwoDotsWorks() {
		new MergeHelper_Core_RepoPath('/branches/test..');
	}

	public function test_endsWithThreeDotsWorks() {
		new MergeHelper_Core_RepoPath('/branches/test...');
	}

	public function test_endsWithSlashAndThreeDotsWorks() {
		new MergeHelper_Core_RepoPath('/branches/test/...');
	}

	/**
	 * @expectedException MergeHelper_Core_RepoPathInvalidPathCoreException
	 */
	public function test_exceptionsIfContainsRelativePath() {
		new MergeHelper_Core_RepoPath('/branches/../test/');
	}

	/**
	 * @expectedException MergeHelper_Core_RepoPathInvalidPathCoreException
	 */
	public function test_exceptionsIfBeginsWithRelativePath() {
		new MergeHelper_Core_RepoPath('../branches/test');
	}

	/**
	 * @expectedException MergeHelper_Core_RepoPathInvalidPathCoreException
	 */
	public function test_exceptionsIfEndsWithRelativePath() {
		new MergeHelper_Core_RepoPath('/branches/test/..');
	}
	
	/**
	 * @expectedException MergeHelper_Core_RepoPathInvalidPathCoreException
	 */
	public function test_exceptionsIfEndsWithSlash() {
		new MergeHelper_Core_RepoPath('/branches/test/');
	}

	/**
	 * @expectedException MergeHelper_Core_RepoPathInvalidPathCoreException
	 */
	public function test_exceptionsIfNotStartsWithSlash() {
		new MergeHelper_Core_RepoPath('branches/test');
	}

	/**
	 * @expectedException MergeHelper_Core_RepoPathInvalidPathCoreException
	 */
	public function test_exceptionsIfContainsRelativePathPartsOneDot() {
		new MergeHelper_Core_RepoPath('/branches/test/./');
	}

	/**
	 * @expectedException MergeHelper_Core_RepoPathInvalidPathCoreException
	 */
	public function test_exceptionsIfContainsRelativePathPartsTwoDots() {
		new MergeHelper_Core_RepoPath('/../branches/test/a.php');
	}

	/**
	 * @expectedException MergeHelper_Core_RepoPathInvalidPathCoreException
	 */
	public function test_exceptionsIfContainsSvnMetadirectory() {
		new MergeHelper_Core_RepoPath('/branches/test/.svn');
	}

}
