<?php
$iniPath = 'C:\\Program Files\\php-8.4.26-nts-Win32-vs17-x64\\php.ini';
if (file_exists($iniPath)) {
    $content = file_get_contents($iniPath);
    $content = str_replace(';extension=pdo_sqlite', 'extension=pdo_sqlite', $content);
    if (strpos($content, 'extension=pdo_sqlite') === false) {
        $content .= "\nextension=pdo_sqlite\n";
    }
    file_put_contents($iniPath, $content);
    echo "Enabled pdo_sqlite in php.ini successfully.\n";
} else {
    echo "php.ini not found at {$iniPath}.\n";
}
