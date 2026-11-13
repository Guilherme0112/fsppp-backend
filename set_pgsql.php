<?php
$envPath = __DIR__ . '/.env';
$content = file_get_contents($envPath);
$content = preg_replace('/^DB_CONNECTION=.*/m', 'DB_CONNECTION=pgsql', $content);
file_put_contents($envPath, $content);
echo "Set DB_CONNECTION=pgsql\n";
