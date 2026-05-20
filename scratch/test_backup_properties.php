<?php

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$filename = 'test-backup.sql';
$tempPath = storage_path('app/'.$filename);

// Print configs
echo "DB Host: " . config('database.connections.pgsql.host') . "\n";
echo "DB User: " . config('database.connections.pgsql.username') . "\n";
echo "DB Database: " . config('database.connections.pgsql.database') . "\n";

putenv('PGPASSWORD='.config('database.connections.pgsql.password'));

$command = sprintf(
    'pg_dump -h %s -U %s %s > %s',
    config('database.connections.pgsql.host'),
    config('database.connections.pgsql.username'),
    config('database.connections.pgsql.database'),
    $tempPath
);

echo "Running command: {$command}\n";
$output = [];
$returnVar = null;
exec($command, $output, $returnVar);

echo "Exit Code: {$returnVar}\n";
if (file_exists($tempPath)) {
    $size = filesize($tempPath);
    echo "Backup file size: {$size} bytes\n";
    
    // Read first 50 lines of SQL file
    $lines = file($tempPath);
    echo "\n--- First 30 lines of SQL dump ---\n";
    for ($i = 0; $i < min(30, count($lines)); $i++) {
        echo $lines[$i];
    }
    
    // Check if INSERT INTO "lab_tests" exists in the file
    $content = file_get_contents($tempPath);
    $hasLabTestsData = str_contains($content, 'INSERT INTO "lab_tests"') || str_contains($content, 'COPY public.lab_tests');
    echo "\nContains lab_tests data: " . ($hasLabTestsData ? "Yes" : "No") . "\n";
    
    // Delete test file
    unlink($tempPath);
} else {
    echo "Backup file was not created!\n";
}
