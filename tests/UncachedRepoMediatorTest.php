<?php

class MergeHelper_UncachedRepoMediatorTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		$oRepo = new MergeHelper_Repo();

		$oRepo->setType(MergeHelper_Repo::TYPE_SVN);
		$oRepo->setLocation('file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'));
		$oRepo->setAuthinfo('user.name', 'secret');
		$oRepo->addSourcePath(new MergeHelper_RepoPath('/branches/my-hammer2/_production'));
		$oRepo->addSourcePath(new MergeHelper_RepoPath('/branches/my-hammer2/_project'));
		$oRepo->setTargetPath(new MergeHelper_RepoPath('/branches/my-hammer2/_approval'));

		$this->oRepo = $oRepo;
	}

	public function test_construct() {
		$oRepoMediator = new MergeHelper_UncachedRepoMediator($this->oRepo);
		$this->assertTrue(is_object($oRepoMediator));
	}

	public function test_getRevisionsInRange() {
		$oRepoMediator = new MergeHelper_UncachedRepoMediator($this->oRepo);
		$aoRevisions = $oRepoMediator->aoGetRevisionsInRange('HEAD', 5);

		$this->assertSame(4, sizeof($aoRevisions));
		$this->assertSame('8', $aoRevisions[0]->sGetNumber());
		$this->assertSame('7', $aoRevisions[1]->sGetNumber());
		$this->assertSame('6', $aoRevisions[2]->sGetNumber());
		$this->assertSame('5', $aoRevisions[3]->sGetNumber());
	}

	public function test_getRevisionsForString() {
		$oRepoMediator = new MergeHelper_UncachedRepoMediator($this->oRepo);
		$aoRevisions = $oRepoMediator->aoGetRevisionsForString('TF-4001');

		$this->assertSame(3, sizeof($aoRevisions));
		$this->assertSame('3', $aoRevisions[0]->sGetNumber());
		$this->assertSame('5', $aoRevisions[1]->sGetNumber());
		$this->assertSame('7', $aoRevisions[2]->sGetNumber());
	}

	public function test_getRevisionsForStringNoStringGiven() {
		$oRepoMediator = new MergeHelper_UncachedRepoMediator($this->oRepo);

		$aoRevisions = $oRepoMediator->aoGetRevisionsForString('');

		$this->assertSame(0, sizeof($aoRevisions));
	}

	public function test_checkIfRevisionsAreInSameSourcePath() {
		$oRepoMediator = new MergeHelper_UncachedRepoMediator($this->oRepo);

		$aoRevisions = array(new MergeHelper_Revision('3'), new MergeHelper_Revision('5'));
		$this->assertTrue($oRepoMediator->bRevisionsAreInSameSourcePath($aoRevisions));
		
		$aoRevisions = array(new MergeHelper_Revision('5'), new MergeHelper_Revision('6'));
		$this->assertFalse($oRepoMediator->bRevisionsAreInSameSourcePath($aoRevisions));
	}
	
	public function test_checkIfRevisionsAreInSameSourcePathNoRevisionsGiven() {
		$oRepoMediator = new MergeHelper_UncachedRepoMediator($this->oRepo);

		$aoRevisions = array();
		$this->assertFalse($oRepoMediator->bRevisionsAreInSameSourcePath($aoRevisions));
	}
	
	public function test_getCommonSourcePathForFullPath() {
		$oRepoMediator = new MergeHelper_UncachedRepoMediator($this->oRepo);

		$this->assertEquals(new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01'), $oRepoMediator->oGetCommonSourcePathForFullPath(new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01/lala/lulu/blah.txt')));
	}
	
	public function test_getCommonSourcePathForFullPathNoSourcePaths() {
		$oRepo = new MergeHelper_Repo();
		$oRepo->setType(MergeHelper_Repo::TYPE_SVN);
		$oRepo->setLocation('file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'));
		$oRepo->setTargetPath(new MergeHelper_RepoPath('/branches/my-hammer2/_approval'));

		$oRepoMediator = new MergeHelper_UncachedRepoMediator($oRepo);

		$this->assertEquals(NULL, $oRepoMediator->oGetCommonSourcePathForFullPath(new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01/lala/lulu/blah.txt')));
	}

	public function test_getCommonBasePathForFullPathNoSourcePaths() {
		$oRepo = new MergeHelper_Repo();
		$oRepo->setType(MergeHelper_Repo::TYPE_SVN);
		$oRepo->setLocation('file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'));
		$oRepo->setTargetPath(new MergeHelper_RepoPath('/branches/my-hammer2/_approval'));

		$oRepoMediator = new MergeHelper_UncachedRepoMediator($oRepo);

		$this->assertEquals(NULL, $oRepoMediator->oGetCommonBasePathForFullPath(new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01/lala/lulu/blah.txt')));
	}
	
	public function test_getPathsForRevisions() {
		$oRepoMediator = new MergeHelper_UncachedRepoMediator($this->oRepo);

		$aoRevisions = array(new MergeHelper_Revision('3'), new MergeHelper_Revision('4'));
		$aoPaths = $oRepoMediator->aoGetPathsForRevisions($aoRevisions);

		$this->assertSame(3, sizeof($aoPaths));
		$this->assertEquals(new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01/c/d.php'), $aoPaths[0]);
		$this->assertEquals(new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01/a.php'), $aoPaths[1]);
		$this->assertEquals(new MergeHelper_RepoPath('/branches/my-hammer2/_project/TF-0001/a.php'), $aoPaths[2]);
	}

	public function test_getMessagesForRevisions() {
		$oRepoMediator = new MergeHelper_UncachedRepoMediator($this->oRepo);

		$aoRevisions = array(new MergeHelper_Revision('3'), new MergeHelper_Revision('5'));
		$asMessages = $oRepoMediator->asGetMessagesForRevisions($aoRevisions);

		$this->assertSame(2, sizeof($asMessages));
		$this->assertSame('TF-4001', $asMessages[0]);
		$this->assertSame("TF-4001\n- added jabbadabbadoo", $asMessages[1]);
	}
	
	public function test_getMergeCommandlineForRevisionAndPath() {
		$oRepoMediator = new MergeHelper_UncachedRepoMediator($this->oRepo);

		$asCommandlines = $oRepoMediator->asGetMergeCommandlinesForRevisionsAndPaths(array(
		                                                                              array(
		                                                                               new MergeHelper_Revision('4'),
		                                                                               new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01/c/d.php'),
		                                                                               '.',
		                                                                               FALSE
		                                                                              ),
		                                                                              array(
		                                                                               new MergeHelper_Revision('3'),
		                                                                               new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01/c/d.php'),
		                                                                               '.',
		                                                                               TRUE
		                                                                              )
		                                                                             ));

		$this->assertSame('svn merge -c -3 file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo').'/branches/my-hammer2/_production/2010-01-01/c/d.php ./c/d.php',
		                  $asCommandlines[0]);
		$this->assertSame('svn merge -c 4 file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo').'/branches/my-hammer2/_production/2010-01-01/c/d.php ./c/d.php',
		                  $asCommandlines[1]);
	}
	
	public function test_getCommonSourcePathForRevision() {
		$oRepoMediator = new MergeHelper_UncachedRepoMediator($this->oRepo);

		$this->assertSame('/branches/my-hammer2/_project/TF-0001', (string)$oRepoMediator->oGetCommonSourcePathForRevision(new MergeHelper_Revision('4')));
		$this->assertNull($oRepoMediator->oGetCommonSourcePathForRevision(new MergeHelper_Revision('8')));
	}

	public function test_getCommonBasePathForRevision() {
		$oRepoMediator = new MergeHelper_UncachedRepoMediator($this->oRepo);
		$this->assertSame('/branches/my-hammer2/_approval/2010-01-03_TF-3000', (string)$oRepoMediator->oGetCommonBasePathForRevision(new MergeHelper_Revision('8')));
	}

	public function test_getHighestRevision() {
		$oRepoMediator = new MergeHelper_UncachedRepoMediator($this->oRepo);

		$oHighestRevision = $oRepoMediator->oGetHighestRevision($this->oRepo);
		$this->assertSame('8', $oHighestRevision->sGetNumber());
	}

	/**
	 * @expectedException MergeHelper_Exception
	 */
	public function test_aoGetRevisionsWithPathEndingOn() {
		$oRepoMediator = new MergeHelper_UncachedRepoMediator($this->oRepo);

		$aoActual = $oRepoMediator->aoGetRevisionsWithPathEndingOn('a.php');
	}

}
