<?php

/**
 * @todo Flexible solution for the repo cache filenames needed
 */
class MergeHelper_RepoCommandLogTest extends PHPUnit_Framework_TestCase {

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

	public function test_getLogCommandsForOneRevision() {
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineBuilder());
		$oLogCommand->addRevision(new MergeHelper_Revision('1'));
		$asCommandlines = $oLogCommand->asGetCommandlines();

		$this->assertSame(array('svn log --no-auth-cache --username=user.name --password=secret -r 1 file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo')),
		                  $asCommandlines);
	}

	public function test_getLogCommandsForTwoRevisions() {
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineBuilder());
		$oLogCommand->addRevision(new MergeHelper_Revision('1'));
		$oLogCommand->addRevision(new MergeHelper_Revision('2'));
		$asCommandlines = $oLogCommand->asGetCommandlines();

		$this->assertSame(array('svn log --no-auth-cache --username=user.name --password=secret -r 1 file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo'),
		                        'svn log --no-auth-cache --username=user.name --password=secret -r 2 file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo')
		                       ),
		                  $asCommandlines);
	}

	public function test_getLogCommandsNoRevisions() {
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineBuilder());

		$asCommandlines = $oLogCommand->asGetCommandlines();
		$this->assertSame(array('svn log --no-auth-cache --username=user.name --password=secret file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo')),
		                   $asCommandlines
		                  );
	}

	public function test_getLogCommandsNoRevisionsVerbose() {
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineBuilder());
		$oLogCommand->enableVerbose();
		$asCommandlines = $oLogCommand->asGetCommandlines();

		$this->assertSame(array('svn log --no-auth-cache --username=user.name --password=secret -v file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo')),
		                  $asCommandlines
		                 );
	}

	public function test_getLogCommandsNoRevisionsXml() {
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineBuilder());
		$oLogCommand->enableXml();
		$asCommandlines = $oLogCommand->asGetCommandlines();

		$this->assertSame(array('svn log --no-auth-cache --username=user.name --password=secret --xml file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo')),
		                  $asCommandlines
		                 );
	}

	public function test_getLogCommandsNoRevisionsVerboseAndXml() {
		$oLogCommand = new MergeHelper_RepoCommandLog($this->oRepo, new MergeHelper_CommandLineBuilder());
		$oLogCommand->enableVerbose();
		$oLogCommand->enableXml();
		$asCommandlines = $oLogCommand->asGetCommandlines();

		$this->assertSame(array('svn log --no-auth-cache --username=user.name --password=secret -v --xml file://'.realpath(MergeHelper_Bootstrap::sGetPackageRoot().'/../tests/_testrepo')),
		                  $asCommandlines
		                 );
	}

}
