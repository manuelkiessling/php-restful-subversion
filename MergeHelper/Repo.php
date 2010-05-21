<?php

/**
 *
 * @package MergeHelper
 * @subpackage Repository
 * @uses MergeHelper_RepoPath
 */
class MergeHelper_Repo extends MergeHelper_Base {

	private $sLocation = NULL;
	private $sCacheDirectory = NULL;
	private $sAuthinfoUsername = NULL;
	private $sAuthinfoPassword = NULL;
	private $aoSourcePaths = array();
	private $oTargetPath = NULL;
	private $iType = NULL;

	const TYPE_SVN = 0;

	public function __construct() {

		parent::__preConstruct();
		parent::__construct();

	}

	public function setType($iType) {
		$this->iType = $iType;
	}
	
	public function iGetType() {
		return $this->iType;
	}

	public function setLocation($sLocation) {
		$this->sLocation = $sLocation;
	}

	public function sGetLocation() {
		return $this->sLocation;
	}

	public function sGetLocationBranches() {
		return $this->sGetLocation().'/branches';
	}

	public function setCacheDirectory($sDirectoryName) {
		$this->sCacheDirectory = $sDirectoryName;
	}

	public function sGetCachepath() {
		return $this->sCacheDirectory.'/MergeHelper.svncache.'.sha1($this->sLocation);
	}

  	public function setAuthinfo($sUsername, $sPassword) {
		$this->sAuthinfoUsername = $sUsername;
		$this->sAuthinfoPassword = $sPassword;
	}

	public function sGetAuthinfoUsername() {
		return $this->sAuthinfoUsername;
	}

	public function sGetAuthinfoPassword() {
		return $this->sAuthinfoPassword;
	}

	public function addSourcePath(MergeHelper_RepoPath $oPath) {
		$this->aoSourcePaths[] = $oPath;
	}

	public function aoGetSourcePaths() {
		return $this->aoSourcePaths;
	}
	
	public function asGetSourceLocations() {
		$asReturn = array();
		foreach ($this->aoSourcePaths as $oSourcePath) {
			$asReturn[] = $this->sGetLocation()."$oSourcePath";
		}
		return $asReturn;
	}
		
	public function setTargetPath(MergeHelper_RepoPath $oPath) {
		$this->oTargetPath = $oPath;
	}

	public function oGetTargetPath() {
		return $this->oTargetPath;
	}
	
	public function sGetTargetLocation() {
		return $this->sGetLocation()."$this->oTargetPath";
	}

}
