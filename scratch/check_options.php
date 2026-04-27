<?php
require 'vendor/autoload.php';

$options = new \chillerlan\QRCode\QROptions();
$ref = new ReflectionClass($options);
foreach ($ref->getProperties() as $prop) {
    echo $prop->getName() . "\n";
}
