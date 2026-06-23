<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Default driver: " . config('filesystems.default') . "\n";
echo "Root: " . config('filesystems.disks.' . config('filesystems.default') . '.root') . "\n";
