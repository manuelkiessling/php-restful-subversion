<?php

class MergeHelper_ManagerTest extends PHPUnit_Framework_TestCase {

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
		$oRepoMediator = new oUncachedRepoMediator($this->oRepo);
		$this->assertTrue(is_object($oRepoMediator));
	}

	public function test_getRevisionsInRange() {
		$oRepoMediator = new oUncachedRepoMediator($this->oRepo);
		$aoRevisions = $oRepoMediator->aoGetRevisionsInRange('HEAD', 5);

		$this->assertSame(4, sizeof($aoRevisions));
		$this->assertSame('8', $aoRevisions[0]->sGetNumber());
		$this->assertSame('7', $aoRevisions[1]->sGetNumber());
		$this->assertSame('6', $aoRevisions[2]->sGetNumber());
		$this->assertSame('5', $aoRevisions[3]->sGetNumber());
	}

	public function test_checkIfRevisionsAreInSameSourcePath() {
		$aoRevisions = array(new MergeHelper_Revision('3'), new MergeHelper_Revision('5'));
		$this->assertTrue(MergeHelper_Manager::bRevisionsAreInSameSourcePath($this->oRepo, $aoRevisions));

		$aoRevisions = array(new MergeHelper_Revision('5'), new MergeHelper_Revision('6'));
		$this->assertFalse(MergeHelper_Manager::bRevisionsAreInSameSourcePath($this->oRepo, $aoRevisions));
	}

	/**

	public function test_getRevisionsForString() {
		$oCacheDb = new PDO('sqlite:/var/tmp/PHPMergeHelper_TestDb.sqlite', NULL, NULL);
		$oRepoCache = new MergeHelper_RepoCache($oCacheDb);
		$oRepoCache->resetCache();
		$oRepoCache->addRevision(1234, '', array('/trunk/source/a.php', '/branches/foo/b.php'));
		$oRepoCache->addRevision(1235, 'Hello World', array('/trunk/source/a.php', '/branches/foo/b.php'));
		$oRepoCache->addRevision(1236, 'Hello Other World', array('/trunk/source/a.php', '/branches/foo/b.php'));
		$aoRevisions = MergeHelper_Manager::aoGetRevisionsForString($oRepoCache, 'World');

		$this->assertSame(2, sizeof($aoRevisions));
		$this->assertSame('1236', $aoRevisions[0]->sGetNumber());
		$this->assertSame('1235', $aoRevisions[1]->sGetNumber());
	}
	
	public function test_getRevisionsForStringNoStringGiven() {
		$oCacheDb = new PDO('sqlite:/var/tmp/PHPMergeHelper_TestDb.sqlite', NULL, NULL);
		$oRepoCache = new MergeHelper_RepoCache($oCacheDb);
		$aoRevisions = MergeHelper_Manager::aoGetRevisionsForString($oRepoCache, '');

		$this->assertSame(0, sizeof($aoRevisions));
	}

	public function test_checkIfRevisionsAreInSameSourcePath() {
		$aoRevisions = array(new MergeHelper_Revision('3'), new MergeHelper_Revision('5'));
		$this->assertTrue(MergeHelper_Manager::bRevisionsAreInSameSourcePath($this->oRepo, $aoRevisions));
		
		$aoRevisions = array(new MergeHelper_Revision('5'), new MergeHelper_Revision('6'));
		$this->assertFalse(MergeHelper_Manager::bRevisionsAreInSameSourcePath($this->oRepo, $aoRevisions));
	}
	
	public function test_checkIfRevisionsAreInSameSourcePathNoRevisionsGiven() {
		$aoRevisions = array();
		$this->assertFalse(MergeHelper_Manager::bRevisionsAreInSameSourcePath($this->oRepo, $aoRevisions));
	}
	
	public function test_getCommonSourcePathForFullPath() {
		$this->assertEquals(new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01'), MergeHelper_Manager::oGetCommonSourcePathForFullPath($this->oRepo, new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01/lala/lulu/blah.txt')));
	}
	
	public function test_getCommonSourcePathForFullPathNoSourcePaths() {
		$oRepo = new MergeHelper_Repo();
		$oRepo->setType(MergeHelper_Repo::TYPE_SVN);
		$oRepo->setLocation('file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'));
		$oRepo->setTargetPath(new MergeHelper_RepoPath('/branches/my-hammer2/_approval'));

		$this->assertEquals(NULL, MergeHelper_Manager::oGetCommonSourcePathForFullPath($oRepo, new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01/lala/lulu/blah.txt')));
	}

	public function test_getCommonBasePathForFullPathNoSourcePaths() {
		$oRepo = new MergeHelper_Repo();
		$oRepo->setType(MergeHelper_Repo::TYPE_SVN);
		$oRepo->setLocation('file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'));
		$oRepo->setTargetPath(new MergeHelper_RepoPath('/branches/my-hammer2/_approval'));

		$this->assertEquals(NULL, MergeHelper_Manager::oGetCommonBasePathForFullPath($oRepo, new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01/lala/lulu/blah.txt')));
	}
	
	public function test_getPathsForRevisions() {
		$aoRevisions = array(new MergeHelper_Revision('3'), new MergeHelper_Revision('4'));
		$aoPaths = MergeHelper_Manager::aoGetPathsForRevisions($this->oRepo, $aoRevisions);

		$this->assertSame(3, sizeof($aoPaths));
		$this->assertEquals(new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01/c/d.php'), $aoPaths[0]);
		$this->assertEquals(new MergeHelper_RepoPath('/branches/my-hammer2/_production/2010-01-01/a.php'), $aoPaths[1]);
		$this->assertEquals(new MergeHelper_RepoPath('/branches/my-hammer2/_project/TF-0001/a.php'), $aoPaths[2]);
	}

	public function test_getMessagesForRevisions() {
		$aoRevisions = array(new MergeHelper_Revision('3'), new MergeHelper_Revision('5'));
		$asMessages = MergeHelper_Manager::asGetMessagesForRevisions($this->oRepo, $aoRevisions);

		$this->assertSame(2, sizeof($asMessages));
		$this->assertSame('TF-4001', $asMessages[0]);
		$this->assertSame("TF-4001\n- added jabbadabbadoo", $asMessages[1]);
	}
	
	public function test_getMergeCommandlineForRevisionAndPath() {
		$asCommandlines = MergeHelper_Manager::asGetMergeCommandlinesForRevisionsAndPaths($this->oRepo,
		                                                                                      array(
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
			$this->assertSame('/branches/my-hammer2/_project/TF-0001', (string)MergeHelper_Manager::oGetCommonSourcePathForRevision($this->oRepo, new MergeHelper_Revision('4')));
			$this->assertNull(MergeHelper_Manager::oGetCommonSourcePathForRevision($this->oRepo, new MergeHelper_Revision('8')));
	}

	public function test_getCommonBasePathForRevision() {
			$this->assertSame('/branches/my-hammer2/_approval/2010-01-03_TF-3000', (string)MergeHelper_Manager::oGetCommonBasePathForRevision($this->oRepo, new MergeHelper_Revision('8')));
	}

	public function test_getHighestRevision() {
		$oHighestRevision = MergeHelper_Manager::oGetHighestRevision($this->oRepo);
		$this->assertSame('8', $oHighestRevision->sGetNumber());
	}

	public function test_bCacheIsUpToDate() {
		$oCacheDb = new PDO('sqlite:/var/tmp/PHPMergeHelper_TestDb.sqlite', NULL, NULL);
		$oRepoCache = new MergeHelper_RepoCache($oCacheDb);
		$oRepoCache->resetCache();

		$oHighestRevision = MergeHelper_Manager::oGetHighestRevision($this->oRepo);
		$oRepoCache->addRevision($oHighestRevision->sGetNumber(), '', array());

		$this->assertTrue(MergeHelper_Manager::bCacheIsUpToDate($this->oRepo, $oRepoCache));
	}

	public function test_bCacheIsNotUpToDate() {
		$oCacheDb = new PDO('sqlite:/var/tmp/PHPMergeHelper_TestDb.sqlite', NULL, NULL);
		$oRepoCache = new MergeHelper_RepoCache($oCacheDb);
		$oRepoCache->resetCache();

		$oHighestRevision = MergeHelper_Manager::oGetHighestRevision($this->oRepo);
		$oRepoCache->addRevision($oHighestRevision->sGetNumber() - 1, '', array());

		$this->assertFalse(MergeHelper_Manager::bCacheIsUpToDate($this->oRepo, $oRepoCache));
	}

	public function test_aoGetRevisionsWithPathEndingOn() {
		$oCacheDb = new PDO('sqlite:/var/tmp/PHPMergeHelper_TestDb.sqlite', NULL, NULL);
		$oRepoCache = new MergeHelper_RepoCache($oCacheDb);
		$oRepoCache->resetCache();

		$oRepoCache->addRevision(1234, 'Hello World', array('/trunk/source/a.php', '/branches/foo/b.php'));
		$oRepoCache->addRevision(1235, 'Hello World', array('/trunk/source/a.php', '/branches/foo/c.php', '/branches/foo/a.php'));
		$oRepoCache->addRevision(1236, 'Hello World', array('/trunk/source/d.php', '/branches/foo/b.php', '/branches/bar/a.php'));
		$oRepoCache->addRevision(1237, 'Hello World', array('/totally/different.php'));

		$aoActual = MergeHelper_Manager::aoGetRevisionsWithPathEndingOn($oRepoCache, 'a.php');
		$aoExpected = array(new MergeHelper_Revision(1236),
		                    new MergeHelper_Revision(1235),
		                    new MergeHelper_Revision(1234));

		$this->assertEquals($aoExpected, $aoActual);
	}

	*/
}
