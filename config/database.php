<?php
/**
 * AMDS - Veritabanı Konfigurasyonu
 * Database bağlantı ayarları
 */

return [

    // Varsayılan bağlantı
    'default' => 'mysql',

    // Bağlantı ayarları
    'connections' => [

        'mysql' => [
            'driver' => 'mysql',
            'host' => $_ENV['DB_HOST'] ?? 'localhost',
            'port' => $_ENV['DB_PORT'] ?? 3306,
            'database' => $_ENV['DB_DATABASE'] ?? 'amds_core',
            'username' => $_ENV['DB_USERNAME'] ?? 'root',
            'password' => $_ENV['DB_PASSWORD'] ?? '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => 'InnoDB',
        ],

    ],

    // PDO opsiyonları
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => false,
    ],

    // Migration ayarları
    'migrations' => [
        'table' => 'migrations',
        'path' => __DIR__ . '/../migrations/',
    ],

];
