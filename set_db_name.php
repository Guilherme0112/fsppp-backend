<?php
$envPath = __DIR__ . '/.env';
$content = file_get_contents($envPath);
$content = preg_replace('/^DB_DATABASE=.*/m', 'DB_DATABASE=fsppp', $content);
file_put_contents($envPath, $content);
echo "Set DB_DATABASE=fsppp\n";
