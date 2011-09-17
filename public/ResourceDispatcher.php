<?php

if (!is_file('../etc/PHPRestfulSubversion.conf')) {
    echo '<p>';
    echo 'You need to define a configuration in <em>'.realpath('../etc').'/PHPRestfulSubversion.conf</em>';
    echo '</p>';
    echo '<p>';
    echo 'See <em>'.realpath('../etc/PHPRestfulSubversion.sample.conf').'</em> for an example configuration.';
    echo '</p>';
    die();
}

require_once '../lib/RestfulSubversion/Webservice/Bootstrap.php';
require_once '../etc/PHPRestfulSubversion.conf';

RestfulSubversion\Webservice\Helper\Config::getInstance()->setConfig($configValues);

$request = new Request();
$resource = $request->loadResource();

$response = $resource->exec($request);
$response->output();
