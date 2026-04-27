<?php
require 'vendor/autoload.php';

try {
    $options = new \chillerlan\QRCode\QROptions([
        'outputType' => 'png',
        'scale'      => 5,
    ]);
    $qrcode = new \chillerlan\QRCode\QRCode($options);
    $data = $qrcode->render('test');
    echo "Success: " . substr($data, 0, 50) . "...";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
