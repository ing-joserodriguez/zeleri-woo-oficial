<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

spl_autoload_register(function ($class) {
    // Prefijo del espacio de nombres del proyecto
    $prefix = 'Zeleri\\WooCommerce\\Zeleripay\\Blocks\\';

    // Directorio base para el prefijo del espacio de nombres
    $base_dir = __DIR__ . '/includes/blocks/';

    // ¿La clase usa el prefijo del espacio de nombres?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // No, pasa al siguiente autocargador registrado
        return;
    }

    // Obtén el nombre de la clase relativa
    $relative_class = substr($class, $len);

    // Reemplaza el espacio de nombres por directorios
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // Si el archivo existe, requiérelo
    if (file_exists($file)) {
        require $file;
    }
});
