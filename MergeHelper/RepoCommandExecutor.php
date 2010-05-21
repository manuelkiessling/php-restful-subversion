<?php

/**
 *
 * @package MergeHelper
 * @subpackage Command
 */
class MergeHelper_RepoCommandExecutor {
	
	private static $oInstance = NULL;
	private static $asCache = array();
	
	private function __construct() {}

	private final function __clone() {}
	
	public static function oGetInstance() {

		if (is_null(self::$oInstance)) self::$oInstance = new self;
		return self::$oInstance;

	}
	
	public function sGetCommandResult($sCommand) {

		if (isset(self::$asCache[$sCommand])) return self::$asCache[$sCommand];
		self::$asCache[$sCommand] = shell_exec($sCommand);
		return self::$asCache[$sCommand];

	}

}
