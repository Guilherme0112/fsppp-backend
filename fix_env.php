<?php
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    $content = file_get_contents($envPath);
    $content = preg_replace('/^DB_CONNECTION=.*/m', 'DB_CONNECTION=sqlite', $content);
    $content = preg_replace('/^#\s*DB_DATABASE=database\/database\.sqlite/m', 'DB_DATABASE=database/database.sqlite', $content);
    if (strpos($content, 'DB_DATABASE=database/database.sqlite') === false) {
        $content .= "\nDB_DATABASE=database/database.sqlite\n";
    }
    file_put_contents($envPath, $content);
    echo "Updated .env successfully.\n";
} else {
    copy(__DIR__ . '/.env.example', $envPath);
    $content = file_get_contents($envPath);
    $content = preg_replace('/^DB_CONNECTION=.*/m', 'DB_CONNECTION=sqlite', $content);
    $content .= "\nDB_DATABASE=database/database.sqlite\n";
    file_put_contents($envPath, $content);
    echo "Created and updated .env successfully.\n";
}
