<?php
namespace App\Middleware;

/**
 * AMDS - Auth Middleware
 * Kullanıcı kimlik doğrulaması kontrolü yapar
 */
class AuthMiddleware
{
    /**
     * Kullanıcının giriş yapıp yapmadığını kontrol eder
     */
    public static function check(): bool
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Kullanıcının belirli bir role sahip olup olmadığını kontrol eder
     */
    public static function hasRole(string $role): bool
    {
        if (!self::check()) {
            return false;
        }

        $userRoles = $_SESSION['user_roles'] ?? [];
        return in_array($role, $userRoles);
    }

    /**
     * Kullanıcının belirli rollerden birine sahip olup olmadığını kontrol eder
     */
    public static function hasAnyRole(array $roles): bool
    {
        if (!self::check()) {
            return false;
        }

        $userRoles = $_SESSION['user_roles'] ?? [];
        return !empty(array_intersect($roles, $userRoles));
    }

    /**
     * Giriş yapmış kullanıcıyı döndürür
     */
    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'] ?? '',
            'ad' => $_SESSION['user_ad'] ?? '',
            'soyad' => $_SESSION['user_soyad'] ?? '',
            'roles' => $_SESSION['user_roles'] ?? [],
        ];
    }

    /**
     * Kullanıcı giriş yapmamışsa login sayfasına yönlendirir
     */
    public static function requireAuth(): void
    {
        if (!self::check()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            header('Location: /login');
            exit;
        }
    }

    /**
     * Kullanıcı belirli role sahip değilse 403 döndürür
     */
    public static function requireRole(string $role): void
    {
        self::requireAuth();

        if (!self::hasRole($role)) {
            http_response_code(403);
            echo json_encode([
                'error' => true,
                'message' => 'Bu işlem için yetkiniz yok'
            ]);
            exit;
        }
    }

    /**
     * CSRF token kontrolü
     */
    public static function verifyCsrfToken(): bool
    {
        $token = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        $sessionToken = $_SESSION['_csrf_token'] ?? '';

        return hash_equals($sessionToken, $token);
    }

    /**
     * CSRF token kontrolü yapar, geçersizse hata döndürür
     */
    public static function requireCsrfToken(): void
    {
        if (!self::verifyCsrfToken()) {
            http_response_code(403);
            echo json_encode([
                'error' => true,
                'message' => 'Geçersiz CSRF token'
            ]);
            exit;
        }
    }
}
