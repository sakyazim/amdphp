<?php
/**
 * AMDS - Helper Fonksiyonlar
 * Projenin her yerinden kullanılabilecek yardımcı fonksiyonlar
 */

if (!function_exists('env')) {
    /**
     * Environment değişkenini al
     */
    function env(string $key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }
}

if (!function_exists('config')) {
    /**
     * Config değerini al
     */
    function config(string $key, $default = null)
    {
        static $configs = [];

        // Nokta notasyonu desteği: app.name, database.default, vb.
        $keys = explode('.', $key);
        $file = array_shift($keys);

        if (!isset($configs[$file])) {
            $configPath = __DIR__ . "/../config/{$file}.php";
            if (file_exists($configPath)) {
                $configs[$file] = require $configPath;
            } else {
                return $default;
            }
        }

        $value = $configs[$file];

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }
}

if (!function_exists('dd')) {
    /**
     * Dump and Die (Debug için)
     */
    function dd(...$vars): void
    {
        echo '<pre>';
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo '</pre>';
        die();
    }
}

if (!function_exists('csrf_token')) {
    /**
     * CSRF token al
     */
    function csrf_token(): string
    {
        return $_SESSION['_csrf_token'] ?? '';
    }
}

if (!function_exists('csrf_field')) {
    /**
     * CSRF hidden input field
     */
    function csrf_field(): string
    {
        $token = csrf_token();
        $name = config('app.security.csrf_token_name', '_csrf_token');
        return "<input type=\"hidden\" name=\"{$name}\" value=\"{$token}\">";
    }
}

if (!function_exists('verify_csrf')) {
    /**
     * CSRF token doğrula
     */
    function verify_csrf(string $token): bool
    {
        return hash_equals(csrf_token(), $token);
    }
}

if (!function_exists('old')) {
    /**
     * Eski form değerini al (validation hatası sonrası)
     */
    function old(string $key, $default = '')
    {
        return $_SESSION['_old_input'][$key] ?? $default;
    }
}

if (!function_exists('flash')) {
    /**
     * Flash mesaj ekle/al
     */
    function flash(?string $key = null, $value = null)
    {
        if ($key === null) {
            // Tüm flash mesajları al ve temizle
            $messages = $_SESSION['_flash'] ?? [];
            unset($_SESSION['_flash']);
            return $messages;
        }

        if ($value === null) {
            // Belirli bir flash mesajı al
            return $_SESSION['_flash'][$key] ?? null;
        }

        // Flash mesaj ekle
        $_SESSION['_flash'][$key] = $value;
    }
}

if (!function_exists('redirect')) {
    /**
     * Yönlendirme
     */
    function redirect(string $url, int $statusCode = 302): void
    {
        \Core\Router::redirect($url, $statusCode);
    }
}

if (!function_exists('json_response')) {
    /**
     * JSON response
     */
    function json_response(array $data, int $statusCode = 200): void
    {
        \Core\Router::json($data, $statusCode);
    }
}

if (!function_exists('sanitize')) {
    /**
     * String'i temizle (XSS koruması)
     */
    function sanitize(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('current_tenant')) {
    /**
     * Mevcut tenant'ı al
     */
    function current_tenant(): ?object
    {
        return \Core\TenantResolver::current();
    }
}

if (!function_exists('tenant_db')) {
    /**
     * Tenant database bağlantısı al
     */
    function tenant_db(): ?\PDO
    {
        $dbName = \Core\TenantResolver::getCurrentDatabase();
        return $dbName ? \Core\Database::getTenantConnection($dbName) : null;
    }
}

if (!function_exists('now')) {
    /**
     * Şimdiki zamanı datetime formatında al
     */
    function now(): string
    {
        return date('Y-m-d H:i:s');
    }
}

if (!function_exists('generate_code')) {
    /**
     * Rastgele kod oluştur
     */
    function generate_code(int $length = 8): string
    {
        return strtoupper(substr(bin2hex(random_bytes($length)), 0, $length));
    }
}

if (!function_exists('base_url')) {
    /**
     * Base URL ile birlikte tam URL oluştur
     * Örnek: base_url('/dashboard') -> /amdsphp/public/dashboard
     */
    function base_url(string $path = ''): string
    {
        $basePath = config('app.base_path', '');
        $path = ltrim($path, '/');

        if (empty($path)) {
            return $basePath;
        }

        return $basePath . '/' . $path;
    }
}

if (!function_exists('asset')) {
    /**
     * Asset URL oluştur (CSS, JS, images için)
     * Örnek: asset('css/style.css') -> /amdsphp/public/assets/css/style.css
     */
    function asset(string $path): string
    {
        return base_url('assets/' . ltrim($path, '/'));
    }
}
