<?php

/**
 *
 * @package MergeHelper
 * @subpackage Command
 */
class MergeHelper_RepoCommandLog extends MergeHelper_Base {
	
	private $oRepo = NULL;
	private $aoRevisions = NULL;
	private $bVerbose = FALSE;
	private $bXml = FALSE;
	
	public function __construct(MergeHelper_Repo $oRepo) {
		
		parent::__preConstruct();
		$this->oRepo = $oRepo;
		parent::__construct();
		
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
		if (is_array($this->aoRevisions) && sizeof($this->aoRevisions) > 0) {
			foreach ($this->aoRevisions as $oRevision) {
				$sCommandline = 'svn --no-auth-cache --username='.$this->oRepo->sGetAuthinfoUsername().' --password='.$this->oRepo->sGetAuthinfoPassword().' log -r '.$oRevision->sGetNumber().' ';
				if ($this->bVerbose) $sCommandline .= '-v ';
				if ($this->bXml) $sCommandline .= '--xml ';
				$sCommandline .= $this->oRepo->sGetLocation();
				$asReturn[] = $sCommandline;
			}
		} else {
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
		return $asReturn;

	}
	
	public function aoGetPaths() {
	
		$aoReturn = array();
		$this->enableVerbose();
		$this->enableXml();
		$asCommandlines = $this->asGetCommandlines();
		foreach ($asCommandlines as $sCommandline) {
			$sOutput = MergeHelper_RepoCommandExecutor::oGetInstance()->sGetCommandResult("$sCommandline | grep -v '<paths>' | grep -v '</paths>' | grep '<path' -A 2 | grep 'action'");
			$asLines = explode("\n", $sOutput);
			foreach ($asLines as $sLine) {
				if (mb_strstr($sLine, 'action')) {
					// each line contains something like '   action="M">/branches/my-hammer2/_production/2010-01-01/c/d.php</path>'
					preg_match_all('/   action="(.*)">(.*)<\/path>/', $sLine, $asMatches);
					if(!is_null($asMatches[2][0])) $aoReturn[] = new MergeHelper_RepoPath($asMatches[2][0]);
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
