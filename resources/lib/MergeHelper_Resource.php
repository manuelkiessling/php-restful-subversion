<?php

class MergeHelper_Resource extends Resource {

	protected $aConfig = array();

	function  __construct($aParameters = array()) {
		$this->aConfig = MergeHelper_ResourceConfig::getInstance()->aGetConfig();
		parent::__construct($aParameters);
	}

}
