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
 * Class representing the SVN merge command
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
 * @uses       MergeHelper_RepoPath
 */
class MergeHelper_RepoCommandMerge {

	const SVN_CMD_MERGE = 'svn merge';
	
	protected $oRepo = NULL;
	protected $aaMerges = NULL;
	protected $bDryrun = FALSE;
	
	public function __construct(MergeHelper_Repo $oRepo) {
		$this->oRepo = $oRepo;
	}
	
	public function addMerge(MergeHelper_Revision $oRevision, MergeHelper_RepoPath $oSourcePath, $sTargetPath, $bIsRollback = FALSE) {
		if ($this->aaMerges === NULL) $this->aaMerges = array();
		$amMergeParts = array();

		if ($bIsRollback) {
			$amMergeParts['oRevision'] = $oRevision->getRevertedRevisionAsObject();
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

	/**
	 * aggregates commandlines for svn merge
	 * 
	 * @return array
	 */
	public function asGetCommandlines() {
		$asCommandlines = array();

		if (is_array($this->aaMerges) && sizeof($this->aaMerges) > 0) {
			foreach ($this->aaMerges as $amMerge) {
				$asCommandlines[$amMerge['oRevision']->sGetNumber()] = $this->asGetCommandLine($amMerge, $amMerge['oRevision']->sGetNumber());
			}
		} else {
			return NULL;
		}

		ksort($asCommandlines); // lower revision numbers must be merged first
		foreach ($asCommandlines as $sCommandline) $asReturn[] = $sCommandline;
		return $asReturn;
	}

	/**
	 * creates commandline for mergeprocess
	 *
	 * @param array $amMerge
	 * @param string $sRevisions
	 * @return varchar
	 */
	protected function asGetCommandLine($amMerge, $sRevisions) {
		$oCommandLineFactory = new MergeHelper_CommandLineFactory();
		$oCommandLine = $oCommandLineFactory->instantiate();

		$oCommandLine->addParameter('merge');
		$oCommandLine->setCommand('svn');

		if ($this->bDryrun) {
			$oCommandLine->addLongSwitch('dry-run');
		}

		if ($amMerge['oRevision']->bIsRange()) {
			$oCommandLine->addShortSwitch('r');
		} else {
			$oCommandLine->addShortSwitch('c');
		}

		$oCommandLine->addParameter($sRevisions);
		$oCommandLine->addParameter($this->oRepo->sGetLocation() . $amMerge['oSourcePath']);
		$oCommandLine->addParameter($amMerge['sTargetPath']);

		return $oCommandLine->sGetCommandLine();
	}

}