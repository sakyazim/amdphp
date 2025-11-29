<?php
/**
 * AMDS - Bootstrap
 * Uygulamayı başlatır ve gerekli ayarları yapar
 */

// Hata raporlama
error_reporting(E_ALL);

// Composer autoload
require_once __DIR__ . '/../vendor/autoload.php';

// Environment değişkenlerini yükle
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
} catch (\Exception $e) {
    // .env dosyası yoksa varsayılan değerler kullanılacak
    error_log('Warning: .env file not found - ' . $e->getMessage());
}

// Konfigürasyonları yükle
$appConfig = require __DIR__ . '/../config/app.php';

// Timezone ayarla
date_default_timezone_set($appConfig['timezone']);

// Hata gösterimi (debug moduna göre)
ini_set('display_errors', $appConfig['debug'] ? '1' : '0');
ini_set('display_startup_errors', $appConfig['debug'] ? '1' : '0');

// Session başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => $appConfig['session']['lifetime'] * 60,
        'cookie_path' => $appConfig['session']['cookie_path'],
        'cookie_httponly' => $appConfig['session']['cookie_httponly'],
        'cookie_samesite' => $appConfig['session']['cookie_samesite'],
    ]);
}

// Global helper fonksiyonlar
require_once __DIR__ . '/helpers.php';

// Tenant'ı çöz (eğer varsa)
$tenant = \Core\TenantResolver::resolve();

// CSRF token oluştur (eğer yoksa)
if (!isset($_SESSION['_csrf_token'])) {
    $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
}

return [
    'config' => $appConfig,
    'tenant' => $tenant,
];
