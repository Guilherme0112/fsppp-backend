<?php
$envPath = __DIR__ . '/.env';
$lines = file($envPath);
foreach ($lines as $line) {
    if (strpos($line, 'DB_') === 0) {
        if (strpos($line, 'PASSWORD') !== false) {
            echo "DB_PASSWORD=***\n";
        } else {
            echo trim($line) . "\n";
        }
    }
}
