<?php

/**
 *
 * @package MergeHelper
 * @subpackage Command
 */
class MergeHelper_RepoCommandMerge extends MergeHelper_Base {

	const SVN_CMD_MERGE = 'svn merge';
	
	private $oRepo = NULL;
	private $aaMerges = NULL;
	private $bDryrun = FALSE;
	
	public function __construct(MergeHelper_Repo $oRepo) {
		
		parent::__preConstruct();
		$this->oRepo = $oRepo;
		parent::__construct();
		
	}
	
	public function addMerge(MergeHelper_Revision $oRevision, MergeHelper_RepoPath $oSourcePath, $sTargetPath, $bIsRollback = FALSE) {
		if ($this->aaMerges === NULL) $this->aaMerges = array();
		$amMergeParts = array();
		if ($bIsRollback) {
			$amMergeParts['oRevision'] = new MergeHelper_Revision($oRevision->sGetNumberInverted());
		} else {
			$amMergeParts['oRevision'] = $oRevision;
		}
		$amMergeParts['oSourcePath'] = $oSourcePath;
		$amMergeParts['sTargetPath'] = $sTargetPath;
		$this->aaMerges[] = $amMergeParts;
	}
		
	public function enableDryrun() {
		$this->bDryrun = TRUE;
	}
		
	public function asGetCommandlines() {

		$asCommandlines = array();
		if (is_array($this->aaMerges) && sizeof($this->aaMerges) > 0) {
			foreach ($this->aaMerges as $amMerge) {
				$sCommandline = self::SVN_CMD_MERGE.' ';
				if ($this->bDryrun) $sCommandline .= '--dry-run ';
				$sRevisionSwitch = '-c';
				if ($amMerge['oRevision']->bIsRange()) $sRevisionSwitch = '-r';
				$sCommandline .= $sRevisionSwitch.' '.$amMerge['oRevision']->sGetNumber().' ';
				$sCommandline .= $this->oRepo->sGetLocation().$amMerge['oSourcePath'].' ';
				$sCommandline .= $amMerge['sTargetPath'];
				$asCommandlines[$amMerge['oRevision']->sGetNumber()] = $sCommandline;
			}
		} else {
			return NULL;
		}
		ksort($asCommandlines); // lower revision numbers must be merged first
		foreach ($asCommandlines as $sCommandline) $asReturn[] = $sCommandline;
		return $asReturn;

	}
	
}
