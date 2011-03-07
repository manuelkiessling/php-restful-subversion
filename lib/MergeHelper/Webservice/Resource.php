<?php

class MergeHelper_Webservice_Resource extends Resource {

	protected $aConfig = array();

	function  __construct($aParameters = array()) {
		$this->aConfig = MergeHelper_Webservice_Helper_Config::getInstance()->aGetConfig();
		parent::__construct($aParameters);
	}

}
