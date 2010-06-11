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
 * @subpackage Command
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2010 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPMergeHelper
 */

/**
 * Class representing the SVN log command
 *
 * @category   VersionControl
 * @package    PHPMergeHelper
 * @subpackage Command
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2010 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPMergeHelper
 * @uses       MergeHelper_Repo
 * @uses       MergeHelper_Revision
 * @uses       MergeHelper_RepoCommandExecutor
 * @uses       MergeHelper_RepoPath
 */
class MergeHelper_RepoCommandLog {
	
	private $oRepo = NULL;
	private $aoRevisions = NULL;
	private $bVerbose = FALSE;
	private $bXml = FALSE;
	private $bCacheEnabled = FALSE;
	private $oCommandLineFactory = NULL;
	
	public function __construct(MergeHelper_Repo $oRepo, MergeHelper_CommandLineFactory $oCommandLineFactory) {

		$this->oRepo = $oRepo;
		$this->oCommandLineFactory = $oCommandLineFactory;
		
	}
	
	public function addRevision(MergeHelper_Revision $oRevision) {
		$this->aoRevisions[] = $oRevision;
	}
	
	public function enableVerbose() {
		$this->bVerbose = TRUE;
	}

	public function enableXml() {
		$this->bXml = TRUE;
	}
	
	public function enableCache() {
		$this->bCacheEnabled = TRUE;
	}
	
	public function disableCache() {
		$this->bCacheEnabled = FALSE;
	}
	
	public function asGetCommandlines() {
	
		$asReturn = array();
		if ($this->bRevisionListNotEmpty()) {	
			$asReturn = $this->asGetCommandlinesForRevisions($this->aoRevisions);
		} else {
			$asReturn = $this->asGetCommandlinesWithoutRevisions();
		}
		return $asReturn;

	}
		
	private function asGetCommandLinesForRevisions($aoRevisions) {

		$asReturn = array();
		foreach ($aoRevisions as $oRevision) {
				
			$oCommandLine = $this->oCommandLineFactory->instantiate();
			$oCommandLine->setCommand('svn');
			$oCommandLine->addParameter('log');
			$oCommandLine->addLongSwitch('no-auth-cache');
			$oCommandLine->addLongSwitchWithValue('username', $this->oRepo->sGetAuthinfoUsername());
			$oCommandLine->addLongSwitchWithValue('password', $this->oRepo->sGetAuthinfoPassword());
			$oCommandLine->addShortSwitchWithValue('r', $oRevision->sGetNumber());
			if ($this->bVerbose) $oCommandLine->addShortSwitch('v');
			if ($this->bXml) $oCommandLine->addLongSwitch('xml');
			$oCommandLine->addParameter($this->oRepo->sGetLocation());
			$asReturn[] = $oCommandLine->sGetCommandLine();
		}
		return $asReturn;

	}
	
	private function asGetCommandLinesWithoutRevisions() {

		$asReturn = array();
		if ($this->bCacheEnabled) {
			if ($this->bVerbose && $this->bXml) {
				$asReturn[] = 'cat '.$this->oRepo->sGetCachepath().'.v.x';
			} elseif (!$this->bVerbose && $this->bXml) {
				$asReturn[] = 'cat '.$this->oRepo->sGetCachepath().'.x';
			} elseif ($this->bVerbose && !$this->bXml) {
				$asReturn[] = 'cat '.$this->oRepo->sGetCachepath().'.v';
			} elseif (!$this->bVerbose && !$this->bXml) {
				$asReturn[] = 'cat '.$this->oRepo->sGetCachepath();
			}
		}
		else {
			$sCommandline = 'svn --no-auth-cache'.
							' --username='.
							$this->oRepo->sGetAuthinfoUsername().
							' --password='.
							$this->oRepo->sGetAuthinfoPassword().
							' log ';
			if ($this->bVerbose) $sCommandline .= '-v ';
			if ($this->bXml) $sCommandline .= '--xml ';
			$sCommandline .= $this->oRepo->sGetLocation();
			$asReturn[] = $sCommandline;
		}
		return $asReturn;

	}
	
	private function bRevisionListNotEmpty() {
		return is_array($this->aoRevisions) && sizeof($this->aoRevisions) > 0;
	}
	
	public function aoGetPaths() {
	
		$aoReturn = array();
		$this->enableVerbose();
		$this->enableXml();
		$asCommandlines = $this->asGetCommandlines();
		foreach ($asCommandlines as $sCommandline) {
			$sCommandline = $sCommandline.
			                ' | grep -v "<paths>"'.
			                ' | grep -v "</paths>"'.
			                ' | grep "<path" -A 2'.
			                ' | grep "action"';
			$oExecutor = MergeHelper_RepoCommandExecutor::oGetInstance();
			$sOutput = $oExecutor->sGetCommandResult($sCommandline);
			$asLines = explode("\n", $sOutput);
			foreach ($asLines as $sLine) {
				if (mb_strstr($sLine, 'action')) {
					preg_match_all('/   action="(.*)">(.*)<\/path>/',
					               $sLine,
					               $asMatches);
					if (!is_null($asMatches[2][0])) {
						$aoReturn[] = new MergeHelper_RepoPath($asMatches[2][0]);
					}
				}
			}
		}
		return $aoReturn;
	
	}
	
	/**
	 * @todo Does currently only work if text is on first line of commit message
	 */
	public function aoGetRevisionsWithMessageContainingText($sText) {

		$aoReturn = array();
		$this->enableXml();
		$asCommandlines = $this->asGetCommandlines();
		foreach ($asCommandlines as $sCommandline) {
           	$sOutput = MergeHelper_RepoCommandExecutor::oGetInstance()->sGetCommandResult("$sCommandline | grep \"$sText\" -B 3| grep revision");
			$asLines = explode("\n", $sOutput);
			foreach ($asLines as $sLine) {
				if (mb_strstr($sLine, 'revision')) {
					// each line contains something like '   revision="5">'
					preg_match_all('/   revision="(.*)">/', $sLine, $asMatches);
					$aoReturn[] = new MergeHelper_Revision($asMatches[1][0]);
				}
			}
		}
		krsort($aoReturn);
		$aoReturnSorted = array();
		foreach ($aoReturn as $oRevision) {
			$aoReturnSorted[] = $oRevision;
		} 
		return $aoReturnSorted;
	
	}

}
