<?php

/**
 * Represents a repository revision number
 *
 * @package MergeHelper
 * @subpackage Repository
 */
class MergeHelper_Revision extends MergeHelper_Base {

	private $sBegin = NULL;
	private $sEnd = NULL;
	
	/**
	 * @todo Check and Exception needed for number format
	 */
	public function __construct($sBegin, $sEnd = NULL) {
	
		parent::__preConstruct();
		$this->sBegin = $sBegin;
		$this->sEnd = $sEnd;
		parent::__construct();
	
	}
	
	public function sGetNumber() {
	
		if (is_null($this->sEnd)) return $this->sBegin;
		return $this->sBegin.':'.$this->sEnd;
	
	}
	
	public function sGetNumberInverted() {
	
		if (is_null($this->sEnd)) return '-'.$this->sBegin;
		return $this->sEnd.':'.$this->sBegin;
	
	}
	
	public function __toString() {
		return (string)$this->sGetNumber();
	}
	
	public function sGetNumberBegin() {
		return $this->sBegin;
	}
	
	public function sGetNumberEnd() {
		return $this->sEnd;
	}
	
	public function bIsRange() {
	
		if (is_null($this->sEnd)) return FALSE;
		return TRUE;
	
	}
	
}
