<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    DB::connection()->getPdo();
    echo "Database connection successful!\n";
} catch (\Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}
