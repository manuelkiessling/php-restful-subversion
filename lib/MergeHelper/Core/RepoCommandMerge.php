<?php

/**
 * PHPMergeHelper
 *
 * Copyright (c) 2011, Manuel Kiessling <manuel@kiessling.net>
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
 * @package    MergeHelper
 * @subpackage Core
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2011 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPMergeHelper
 */

/**
 * Class which allows to build a svn merge command line
 *
 * @category   VersionControl
 * @package    MergeHelper
 * @subpackage Core
 * @author     Manuel Kiessling <manuel@kiessling.net>
 * @copyright  2011 Manuel Kiessling <manuel@kiessling.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link       http://manuelkiessling.github.com/PHPMergeHelper
 * @uses       MergeHelper_Core_Repo
 * @uses       MergeHelper_Core_Revision
 * @uses       MergeHelper_Core_RepoPath
 */
class MergeHelper_Core_RepoCommandMerge {

	const SVN_CMD_MERGE = 'svn merge';
	
	protected $oRepo = NULL;
	protected $oRevision = NULL;
	protected $oRepoPath = NULL;
	protected $sWorkingCopyPath = NULL;
	protected $bDryrun = FALSE;
	protected $bRollback = FALSE;
	protected $oCommandLineBuilderBuilder = NULL;

	public function __construct(MergeHelper_Core_Repo $oRepo, MergeHelper_Core_CommandLineBuilderInterface $oCommandLineBuilderBuilder) {
		$this->oRepo = $oRepo;
		$this->oCommandLineBuilder = $oCommandLineBuilderBuilder;
	}
	
	public function setRevision(MergeHelper_Core_Revision $oRevision) {
		$this->oRevision = $oRevision;
	}

	public function setRepoPath(MergeHelper_Core_RepoPath $oRepoPath) {
		$this->oRepoPath = $oRepoPath;
	}

	public function setWorkingCopyPath($sWorkingCopyPath) {
		$this->sWorkingCopyPath = $sWorkingCopyPath;
	}

	public function enableDryrun() {
		$this->bDryrun = TRUE;
	}

	public function enableRollback() {
		$this->bRollback = TRUE;
	}

	/**
	 * creates commandline for mergeprocess
	 *
	 * @param array $amMerge
	 * @param string $sRevisions
	 * @return varchar
	 */
	public function sGetCommandLine() {
		if (is_null($this->oRevision)) return NULL;

		$this->oCommandLineBuilder->reset();
		$this->oCommandLineBuilder->setCommand('svn');
		$this->oCommandLineBuilder->addParameter('merge');

		if ($this->bDryrun) {
			$this->oCommandLineBuilder->addLongSwitch('dry-run');
		}

		$this->oCommandLineBuilder->addShortSwitch('c');

		if ($this->bRollback) {
			$sRevisionNumber = '-'.$this->oRevision->sGetAsString();
		} else {
			$sRevisionNumber = $this->oRevision->sGetAsString();
		}

		$this->oCommandLineBuilder->addParameter($sRevisionNumber);
		$this->oCommandLineBuilder->addParameter($this->oRepo->sGetLocation() . $this->oRepoPath->sGetAsString());
		$this->oCommandLineBuilder->addParameter($this->sWorkingCopyPath);

		return $this->oCommandLineBuilder->sGetCommandLine();
	}

}
