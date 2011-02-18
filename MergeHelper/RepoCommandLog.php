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
	
	protected $oRepo = NULL;
	protected $aoRevisions = NULL;
	protected $sRange = NULL;
	protected $bVerbose = FALSE;
	protected $bXml = FALSE;
	protected $oCommandLineBuilder = NULL;
	
	public function __construct(MergeHelper_Repo $oRepo, MergeHelper_CommandLineBuilderInterface $oCommandLineBuilder) {
		$this->oRepo = $oRepo;
		$this->oCommandLineBuilder = $oCommandLineBuilder;
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
		
	public function asGetCommandlines() {
		$asReturn = array();

		if ($this->bRevisionListNotEmpty()) {
			$asReturn = $this->asGetCommandlinesForRevisions($this->aoRevisions);
		} else {
			$asReturn[] = $this->sGetCommandlineWithoutRevisions();
		}

		return $asReturn;
	}

	protected function asGetCommandLinesForRevisions(Array $aoRevisions) {
		$asReturn = array();

		foreach ($aoRevisions as $oRevision) {
			$asReturn[] = $this->asGetCommandLineForRevision($oRevision);
		}

		return $asReturn;
	}

	protected function asGetCommandLineForRevision(MergeHelper_Revision $oRevision) {
		$this->oCommandLineBuilder->reset();
		$this->oCommandLineBuilder->setCommand('svn');
		$this->oCommandLineBuilder->addParameter('log');
		$this->oCommandLineBuilder->addLongSwitch('no-auth-cache');
		$this->oCommandLineBuilder->addLongSwitchWithValue('username', $this->oRepo->sGetAuthinfoUsername());
		$this->oCommandLineBuilder->addLongSwitchWithValue('password', $this->oRepo->sGetAuthinfoPassword());
		$this->oCommandLineBuilder->addShortSwitchWithValue('r', $oRevision->sGetNumber());

		if ($this->bVerbose) $this->oCommandLineBuilder->addShortSwitch('v');
		if ($this->bXml) $this->oCommandLineBuilder->addLongSwitch('xml');

		$this->oCommandLineBuilder->addParameter($this->oRepo->sGetLocation());

		return $this->oCommandLineBuilder->sGetCommandLine();
	}

	protected function sGetCommandLineWithoutRevisions() {
		$this->oCommandLineBuilder->reset();
		$this->oCommandLineBuilder->setCommand('svn');
		$this->oCommandLineBuilder->addParameter('log');
		$this->oCommandLineBuilder->addLongSwitch('no-auth-cache');
		$this->oCommandLineBuilder->addLongSwitchWithValue('username', $this->oRepo->sGetAuthinfoUsername());
		$this->oCommandLineBuilder->addLongSwitchWithValue('password', $this->oRepo->sGetAuthinfoPassword());

		if ($this->bVerbose) $this->oCommandLineBuilder->addShortSwitch('v');
		if ($this->bXml) $this->oCommandLineBuilder->addLongSwitch('xml');

		$this->oCommandLineBuilder->addParameter($this->oRepo->sGetLocation());

		return $this->oCommandLineBuilder->sGetCommandLine();	
	}
	
	protected function bRevisionListNotEmpty() {
		return is_array($this->aoRevisions) && sizeof($this->aoRevisions) > 0;
	}
	
}
