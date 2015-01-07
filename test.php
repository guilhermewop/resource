<?php
error_reporting(-1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);

$loader = require __DIR__.'/vendor/autoload.php';

use Level3\Resource\Format\Reader\HAL;

$json = file_get_contents('/tmp/json-tests/newspaper-item.json');

$reader = new HAL\JsonReader();
$resource = $reader->execute($json);
echo '<pre>';
print_r($resource);
