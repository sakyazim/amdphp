<?php
/**
 * AMDS - Uygulama Konfigurasyonu
 * Temel uygulama ayarları
 */

return [

    // Uygulama Bilgileri
    'name' => $_ENV['APP_NAME'] ?? 'AMDS',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'url' => $_ENV['APP_URL'] ?? 'http://localhost',
    'base_path' => $_ENV['BASE_PATH'] ?? '/amdsphp/public',

    // Zaman Ayarları
    'timezone' => $_ENV['TIMEZONE'] ?? 'Europe/Istanbul',
    'locale' => $_ENV['LOCALE'] ?? 'tr_TR',
    'default_language' => $_ENV['DEFAULT_LANGUAGE'] ?? 'tr',

    // Session Ayarları
    'session' => [
        'lifetime' => (int)($_ENV['SESSION_LIFETIME'] ?? 120), // dakika
        'cookie_name' => 'amds_session',
        'cookie_path' => '/',
        'cookie_domain' => null,
        'cookie_secure' => false, // Production'da true yapın
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ],

    // Güvenlik Ayarları
    'security' => [
        'csrf_token_name' => $_ENV['CSRF_TOKEN_NAME'] ?? '_csrf_token',
        'password_min_length' => 8,
        'password_require_uppercase' => true,
        'password_require_lowercase' => true,
        'password_require_numbers' => true,
        'password_require_symbols' => false,
        'max_login_attempts' => 5,
        'lockout_duration' => 15, // dakika
    ],

    // Dosya Yükleme Ayarları
    'upload' => [
        'max_file_size' => (int)($_ENV['MAX_FILE_SIZE'] ?? 10485760), // 10MB
        'allowed_types' => explode(',', $_ENV['ALLOWED_FILE_TYPES'] ?? 'pdf,doc,docx'),
        'upload_path' => __DIR__ . '/../storage/uploads/',
        'temp_path' => __DIR__ . '/../storage/temp/',
    ],

    // Sayfalama
    'pagination' => [
        'per_page' => 20,
        'max_per_page' => 100,
    ],

    // Önbellekleme
    'cache' => [
        'enabled' => true,
        'default_ttl' => 3600, // saniye
        'path' => __DIR__ . '/../storage/cache/',
    ],

    // Loglama
    'logging' => [
        'enabled' => true,
        'level' => $_ENV['LOG_LEVEL'] ?? 'info', // debug, info, warning, error
        'path' => __DIR__ . '/../storage/logs/',
        'max_files' => 30, // Kaç günlük log saklanacak
    ],

];
