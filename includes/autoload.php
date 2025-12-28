<?php
// Autoloader simple pour le namespace CorbiDev\ApiBuilder
spl_autoload_register(function ($class) {
    $prefix = 'CorbiDev\\ApiBuilder\\';
    $base_dir = __DIR__ . '/';
    if (strpos($class, $prefix) === 0) {
        $relative_class = substr($class, strlen($prefix));
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});
