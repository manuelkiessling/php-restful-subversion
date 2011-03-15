<?php

class MergeHelperTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		$oRepo = new MergeHelper_Core_Repo();

		$oRepo->setLocation('file://'.realpath(MergeHelper_Helper_Bootstrap::sGetPackageRoot().'/../../tests/_testrepo'));
		$oRepo->setAuthinfo('user.name', 'secret');
		$oRepo->addSourcePath(new MergeHelper_Core_RepoPath('/branches/platform/_production'));
		$oRepo->addSourcePath(new MergeHelper_Core_RepoPath('/branches/platform/_project'));
		$oRepo->setTargetPath(new MergeHelper_Core_RepoPath('/branches/platform/_approval'));

		$this->oRepo = $oRepo;

		$oCacheDb = new PDO('sqlite:/var/tmp/PHPMergeHelper_TestDb.sqlite', NULL, NULL);
		$oRepoCache = new MergeHelper_Core_RepoCache($oCacheDb);
		$oRepoCache->resetCache();

		$this->oRepoCache = $oRepoCache;

		$this->oMergeHelper = new MergeHelper($this->oRepo, $this->oRepoCache);
	}

	public function tearDown() {
		$this->oRepoCache->resetCache();
	}

	public function test_getHighestRevisionInRepo() {
		$this->assertSame('8', $this->oMergeHelper->oGetHighestRevisionInRepo()->sGetAsString());
	}

	public function test_getHighestRevisionInRepoCache() {
		$this->assertEquals($this->oRepoCache->oGetHighestRevision(), $this->oMergeHelper->oGetHighestRevisionInRepoCache());
	}

	public function test_repoAndRepoCacheAreInSync() {
		for ($i = 1; $i < 9; $i++) {
			$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision((string)$i));
			$oChangeset->setAuthor('Han Solo');
			$oChangeset->setDateTime('2011-02-18 22:56:0'.$i);
			$oChangeset->setMessage('Hello World '.$i);
			$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/foo/bar.php'));
			$this->oRepoCache->addChangeset($oChangeset);
		}

		$this->assertTrue($this->oMergeHelper->bRepoAndRepoCacheAreInSync());
	}

	public function test_repoAndRepoCacheAreInSyncFailsIfNotInSync() {
		for ($i = 1; $i < 8; $i++) {
			$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision((string)$i));
			$oChangeset->setAuthor('Han Solo');
			$oChangeset->setDateTime('2011-02-18 22:56:0'.$i);
			$oChangeset->setMessage('Hello World '.$i);
			$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/foo/bar.php'));
			$this->oRepoCache->addChangeset($oChangeset);
		}

		$this->assertFalse($this->oMergeHelper->bRepoAndRepoCacheAreInSync());
	}

	public function test_revisionsAreOInSameSourcePath() {
		for ($i = 1; $i < 3; $i++) {
			$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision((string)$i));
			$oChangeset->setAuthor('Han Solo');
			$oChangeset->setDateTime('2011-02-18 22:56:0'.$i);
			$oChangeset->setMessage('Hello World '.$i);
			$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar'.$i.'.php'));
			$this->oRepoCache->addChangeset($oChangeset);
		}

		$this->assertTrue($this->oMergeHelper->bRevisionsAreOnSameSourcePath(array(new MergeHelper_Core_Revision('1'), new MergeHelper_Core_Revision('2'))));
	}

	public function test_revisionsAreOnSameSourcePathFailsIfOneRevisionIsOnDifferentSourcePaths() {
		for ($i = 1; $i < 3; $i++) {
			$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision((string)$i));
			$oChangeset->setAuthor('Han Solo');
			$oChangeset->setDateTime('2011-02-18 22:56:0'.$i);
			$oChangeset->setMessage('Hello World '.$i);
			$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/'.$i.'/platform/_production/test/foobar'.$i.'.php'));
			$this->oRepoCache->addChangeset($oChangeset);
		}

		$this->assertFalse($this->oMergeHelper->bRevisionsAreOnSameSourcePath(array(new MergeHelper_Core_Revision('1'), new MergeHelper_Core_Revision('2'))));
	}

	public function test_revisionsAreOnSameSourcePathFailsIfOnePathInOneRevisionIsOnDifferentSourcePaths() {
		for ($i = 1; $i < 3; $i++) {
			$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision((string)$i));
			$oChangeset->setAuthor('Han Solo');
			$oChangeset->setDateTime('2011-02-18 22:56:0'.$i);
			$oChangeset->setMessage('Hello World '.$i);
			$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar'.$i.'.php'));
			$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar2'.$i.'.php'));
			$this->oRepoCache->addChangeset($oChangeset);
		}

		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('3'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:03');
		$oChangeset->setMessage('Hello World 3');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar3.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_project/test/foobar23.php'));
		$this->oRepoCache->addChangeset($oChangeset);

		$this->assertFalse($this->oMergeHelper->bRevisionsAreOnSameSourcePath(array(new MergeHelper_Core_Revision('1'), new MergeHelper_Core_Revision('2'), new MergeHelper_Core_Revision('3'))));
	}
	
	public function test_revisionsAreOnSameSourcePathFailsIfNoRevisionsGiven() {
		$this->assertFalse($this->oMergeHelper->bRevisionsAreOnSameSourcePath(array()));
	}

	public function test_pathIsOnAtLeastOneSourcePath() {
		$this->assertTrue($this->oMergeHelper->bPathIsOnAtLeastOneSourcePath(new MergeHelper_Core_RepoPath('/branches/platform/_project/deathstar/cannon.php')));
	}

	public function test_getCommonSourcePathOfRevision() {
		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12345'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar1.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar2.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar3.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar4.php'));
		$this->oRepoCache->addChangeset($oChangeset);

		$this->assertEquals(new MergeHelper_Core_RepoPath('/branches/platform/_production/test'),
		                    $this->oMergeHelper->oGetCommonSourcePathOfRevision(new MergeHelper_Core_Revision('12345')));
	}

	public function test_getCommonSourcePathOfRevisionSourcePathIsTreatedWithOneLevelDown() {
		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12345'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar1.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar2.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test2/foobar3.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar4.php'));
		$this->oRepoCache->addChangeset($oChangeset);

		$this->assertNull($this->oMergeHelper->oGetCommonSourcePathOfRevision(new MergeHelper_Core_Revision('12345')));
	}

	public function test_getCommonSourcePathOfRevisionFailsIfNotAllOnSameSourcePath() {
		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12345'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar1.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar2.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_project/test/foobar3.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar4.php'));
		$this->oRepoCache->addChangeset($oChangeset);

		$this->assertNull($this->oMergeHelper->oGetCommonSourcePathOfRevision(new MergeHelper_Core_Revision('12345')));
	}

	public function test_getCommonSourcePathOfRevisionFailsIfRevisionIsOnTargetPath() {
		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12345'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_approval/test/foobar1.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_approval/test/foobar2.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_approval/test/foobar3.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_approval/test/foobar4.php'));
		$this->oRepoCache->addChangeset($oChangeset);

		$this->assertNull($this->oMergeHelper->oGetCommonSourcePathOfRevision(new MergeHelper_Core_Revision('12345')));
	}

	public function test_getCommonSourcePathOfRevisionFailsIfRevisionIsOnNoSourcePathAtAll() {
		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12345'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/trunk/test/foobar1.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/trunk/test/foobar2.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/trunk/test/foobar3.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/trunk/test/foobar4.php'));
		$this->oRepoCache->addChangeset($oChangeset);

		$this->assertNull($this->oMergeHelper->oGetCommonSourcePathOfRevision(new MergeHelper_Core_Revision('12345')));
	}

	public function test_getCommonBasePathOfRevision() {
		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12345'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_approval/test/foobar1.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_approval/test/foobar2.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_approval/test/foobar3.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_approval/test/foobar4.php'));
		$this->oRepoCache->addChangeset($oChangeset);

		$this->assertEquals(new MergeHelper_Core_RepoPath('/branches/platform/_approval/test'),
		                    $this->oMergeHelper->oGetCommonBasePathOfRevision(new MergeHelper_Core_Revision('12345')));
	}

	public function test_getCommonBasePathOfRevisionFailsIfNotAllOnSameBasePathBecauseThereIsOneSourcePath() {
		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12345'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_approval/test/foobar1.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_project/test/foobar2.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_approval/test/foobar3.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_approval/test/foobar4.php'));
		$this->oRepoCache->addChangeset($oChangeset);

		$this->assertNull($this->oMergeHelper->oGetCommonBasePathOfRevision(new MergeHelper_Core_Revision('12345')));
	}

	public function test_getCommonBasePathOfRevisionFailsIfNotAllOnSameBasePathBecauseThereIsOneDifferentTargetPath() {
		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12345'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_approval/test/foobar1.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_approval/test2/foobar2.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_approval/test/foobar3.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_approval/test/foobar4.php'));
		$this->oRepoCache->addChangeset($oChangeset);

		$this->assertNull($this->oMergeHelper->oGetCommonBasePathOfRevision(new MergeHelper_Core_Revision('12345')));
	}

	public function test_getCommonBasePathOfRevisionFailsIfRevisionIsOnNoBasePathAtAll() {
		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12345'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/trunk/test/foobar1.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/trunk/test/foobar2.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/trunk/test/foobar3.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/trunk/test/foobar4.php'));
		$this->oRepoCache->addChangeset($oChangeset);

		$this->assertNull($this->oMergeHelper->oGetCommonBasePathOfRevision(new MergeHelper_Core_Revision('12345')));
	}

	public function test_getChangesetForRevision() {
		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12345'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/trunk/test/foobar1.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/trunk/test/foobar2.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/trunk/test/foobar3.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/trunk/test/foobar4.php'));
		$this->oRepoCache->addChangeset($oChangeset);

		$this->assertEquals($this->oRepoCache->oGetChangesetForRevision(new MergeHelper_Core_Revision('12345')),
		                    $this->oMergeHelper->oGetChangesetForRevision(new MergeHelper_Core_Revision('12345')));
	}

	public function test_getMergeCommandlineForRevisionNoDryrun() {
		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12345'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar1.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar2.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/other/foobar3.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar4.php'));
		$this->oRepoCache->addChangeset($oChangeset);

		$sExpected = 'svn merge -c 12345 file://'.realpath(MergeHelper_Helper_Bootstrap::sGetPackageRoot().'/../../tests/_testrepo').'/branches/platform/_production/test .';
		$this->assertSame($sExpected, $this->oMergeHelper->sGetMergeCommandlineForRevision(new MergeHelper_Core_Revision('12345')));
	}

	public function test_getMergeCommandlineForRevisionDryrun() {
		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12345'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar1.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar2.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/other/foobar3.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar4.php'));
		$this->oRepoCache->addChangeset($oChangeset);

		$sExpected = 'svn merge --dry-run -c 12345 file://'.realpath(MergeHelper_Helper_Bootstrap::sGetPackageRoot().'/../../tests/_testrepo').'/branches/platform/_production/test .';
		$this->assertSame($sExpected, $this->oMergeHelper->sGetMergeCommandlineForRevision(new MergeHelper_Core_Revision('12345'), TRUE));
	}

	public function test_getRollbackMergeCommandlineForRevisionNoDryrun() {
		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12345'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar1.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar2.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/other/foobar3.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar4.php'));
		$this->oRepoCache->addChangeset($oChangeset);

		$sExpected = 'svn merge -c -12345 file://'.realpath(MergeHelper_Helper_Bootstrap::sGetPackageRoot().'/../../tests/_testrepo').'/branches/platform/_production/test .';
		$this->assertSame($sExpected, $this->oMergeHelper->sGetRollbackMergeCommandlineForRevision(new MergeHelper_Core_Revision('12345')));
	}

	public function test_getRollbackMergeCommandlineForRevisionDryrun() {
		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12345'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:00');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar1.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar2.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/other/foobar3.php'));
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar4.php'));
		$this->oRepoCache->addChangeset($oChangeset);

		$sExpected = 'svn merge --dry-run -c -12345 file://'.realpath(MergeHelper_Helper_Bootstrap::sGetPackageRoot().'/../../tests/_testrepo').'/branches/platform/_production/test .';
		$this->assertSame($sExpected, $this->oMergeHelper->sGetRollbackMergeCommandlineForRevision(new MergeHelper_Core_Revision('12345'), TRUE));
	}
	
	public function test_getRevisionsWithMessageContainingText() {
		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12345'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:01');
		$oChangeset->setMessage('Helloworld');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar1.php'));
		$this->oRepoCache->addChangeset($oChangeset);

		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12346'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:02');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar1.php'));
		$this->oRepoCache->addChangeset($oChangeset);

		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12347'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:03');
		$oChangeset->setMessage('Hello W orld');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar1.php'));
		$this->oRepoCache->addChangeset($oChangeset);

		$aoExpected = array(new MergeHelper_Core_Revision('12345'), new MergeHelper_Core_Revision('12346'));
		$this->assertEquals($aoExpected, $this->oMergeHelper->aoGetRevisionsWithMessageContainingText('world'));
	}

	public function test_getRevisionsWithMessageContainingTextNoTextGiven() {
		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12345'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:01');
		$oChangeset->setMessage('Helloworld');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar1.php'));
		$this->oRepoCache->addChangeset($oChangeset);

		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12346'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:02');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar1.php'));
		$this->oRepoCache->addChangeset($oChangeset);

		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12347'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:03');
		$oChangeset->setMessage('Hello W orld');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar1.php'));
		$this->oRepoCache->addChangeset($oChangeset);

		$aoExpected = array();
		$this->assertEquals($aoExpected, $this->oMergeHelper->aoGetRevisionsWithMessageContainingText(''));
	}

	public function test_getRevisionsWithPathsEndingOn() {
		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12345'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:01');
		$oChangeset->setMessage('Helloworld');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar1.php'));
		$this->oRepoCache->addChangeset($oChangeset);

		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12346'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:02');
		$oChangeset->setMessage('Hello World');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobbbbar1.php'));
		$this->oRepoCache->addChangeset($oChangeset);

		$oChangeset = new MergeHelper_Core_Changeset(new MergeHelper_Core_Revision('12347'));
		$oChangeset->setAuthor('Han Solo');
		$oChangeset->setDateTime('2011-02-18 22:56:03');
		$oChangeset->setMessage('Hello W orld');
		$oChangeset->addPathOperation('M', new MergeHelper_Core_RepoPath('/branches/platform/_production/test/foobar2.php'));
		$this->oRepoCache->addChangeset($oChangeset);

		$aoExpected = array(new MergeHelper_Core_Revision('12345'), new MergeHelper_Core_Revision('12346'));
		$this->assertEquals($aoExpected, $this->oMergeHelper->aoGetRevisionsWithPathsEndingOn('bar1.php'));
	}

}
