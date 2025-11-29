<?php
/**
 * AMDS - E-posta Konfigurasyonu
 * PHPMailer ayarları
 */

return [

    // SMTP Ayarları
    'host' => $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com',
    'port' => (int)($_ENV['MAIL_PORT'] ?? 587),
    'username' => $_ENV['MAIL_USERNAME'] ?? '',
    'password' => $_ENV['MAIL_PASSWORD'] ?? '',
    'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls', // tls veya ssl

    // Gönderici Bilgileri
    'from' => [
        'address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@amds.com',
        'name' => $_ENV['MAIL_FROM_NAME'] ?? 'AMDS',
    ],

    // E-posta Ayarları
    'charset' => 'UTF-8',
    'timeout' => 30, // saniye

    // Debug modları (1-4, production'da 0 olmalı)
    'debug' => $_ENV['MAIL_DEBUG'] ?? 0,

];
