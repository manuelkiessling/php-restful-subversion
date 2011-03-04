<?php

class MergeHelper_Resource_Config {

	static private $oInstance = NULL;
	static private $aConfig = array();

	static public function getInstance() {
		if (NULL === self::$oInstance) {
			self::$oInstance = new self;
		}
		return self::$oInstance;
	}

	private function __construct() {}
	private function __clone() {}

	public function setConfig($aConfig) {
		$this->aConfig = $aConfig;
	}

	public function aGetConfig() {
		return $this->aConfig;
	}

}
