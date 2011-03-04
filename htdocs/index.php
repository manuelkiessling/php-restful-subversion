<?php

if (!is_file('../etc/PHPMergeHelper.conf')) {
	echo '<p>';
	echo 'You need to define a configuration in <em>'.realpath('../etc').'/PHPMergeHelper.conf</em>';
	echo '</p>';
	echo '<p>';
	echo 'See <em>'.realpath('../etc/PHPMergeHelper.sample.conf').'</em> for an example configuration.';
	echo '</p>';
	die();
}

require_once '../resources/Bootstrap.php';
require_once '../etc/PHPMergeHelper.conf';

MergeHelper_Resource_Config::getInstance()->setConfig($aConfig);

$oRequest = new Request(array('baseUri' => '/PHPMergeHelper'));
$oResource = $oRequest->loadResource();

$oResponse = $oResource->exec($oRequest);
$oResponse->output();
