<?php

/**
 *
 * @package MergeHelper
 * @subpackage Exception
 */
class MergeHelper_Exception extends Exception {

	public function __construct($sMessage = NULL, $iCode = 0) {
		parent::__construct($sMessage, $iCode);
	}

}
