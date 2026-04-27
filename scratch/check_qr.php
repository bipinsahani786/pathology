<?php
require 'vendor/autoload.php';

use chillerlan\QRCode\Output\QROutputInterface;
use ReflectionClass;

try {
    $ref = new ReflectionClass(QROutputInterface::class);
    print_r($ref->getConstants());
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
