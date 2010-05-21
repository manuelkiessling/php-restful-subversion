<?php

/**
 *
 * @package MergeHelper
 * @subpackage Core
 * @uses MergeHelper_Autoloader::load
 * @uses MergeHelper_Base
 */

require_once realpath(dirname(__FILE__)).'/Base.php';
require_once realpath(dirname(__FILE__)).'/Autoloader.php';

spl_autoload_register('MergeHelper_Autoloader::load');

/**
 *
 * @package MergeHelper
 */
class MergeHelper_Bootstrap extends MergeHelper_Base {

	public static function sGetPackageRoot() {
		return realpath(dirname(__FILE__));
	}

}
