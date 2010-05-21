<?php

/**
 *
 * @package MergeHelper
 * @subpackage Core
 * @uses MergeHelper_Bootstrap::sGetPackageRoot()
 */
class MergeHelper_Autoloader extends MergeHelper_Base {
	
	public static function load($sClass)
	{
		$asPaths = array(realpath(MergeHelper_Bootstrap::sGetPackageRoot()));
		$sFilename = mb_substr($sClass, 12).'.php';
		foreach ($asPaths as $sPath) {
			if (file_exists(realpath($sPath.'/'.$sFilename))) {
				require_once realpath($sPath.'/'.$sFilename);
				return $sFilename;
			}
		}
		return FALSE;
	}

}
