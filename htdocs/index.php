<?php

if (!is_file('../etc/PHPMergeHelper.conf')) {
	die('You need to define a configuration in ../etc/PHPMergeHelper.conf');
}

require_once('../etc/PHPMergeHelper.conf');
require_once('../lib/Tonic.php');
require_once '../resources/Changeset.php';

$request = new Request(array('baseUri' => '/PHPMergeHelper'));
$resource = $request->loadResource();

$response = $resource->exec($request);
$response->output();
