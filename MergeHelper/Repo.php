<?php

/**
 * PHPMergeHelper
 *
 * Copyright (c) 2010, Manuel Kiessling <manuel@kiessling.net>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *   * Neither the name of Manuel Kiessling nor the names of its contributors
 *     may be used to endorse or promote products derived from this software
 *     without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   VersionControl
 * @package    PHPMergeHelper
 * @subpackage Repository
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2010 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPMergeHelper
 */

/**
 * Class representing an existing SVN repository
 *
 * @category   VersionControl
 * @package    PHPMergeHelper
 * @subpackage Repository
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2010 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPMergeHelper
 * @uses       MergeHelper_RepoPath
 * @uses       MergeHelper_Base
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
