<?php
namespace Core;

/**
 * AMDS - Tenant Çözümleyici
 * URL'den veya subdomain'den tenant'ı belirler
 */
class TenantResolver
{
    private static $currentTenant = null;

    /**
     * Mevcut tenant'ı çöz
     * Örnek: dergi1.amds.com -> dergi1
     * Örnek: localhost/dergi1 -> dergi1
     */
    public static function resolve(): ?object
    {
        if (self::$currentTenant !== null) {
            return self::$currentTenant;
        }

        $slug = null;

        // 1. Önce .env'den TENANT_SLUG kontrol et (geliştirme için)
        if (isset($_ENV['TENANT_SLUG']) && !empty($_ENV['TENANT_SLUG'])) {
            $slug = $_ENV['TENANT_SLUG'];
        }
        // 2. Subdomain kontrolü
        else {
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $parts = explode('.', $host);

            if (count($parts) > 2) {
                // Subdomain var: dergi1.amds.com
                $slug = $parts[0];
            } else {
                // Subdomain yok, path kontrolü: localhost/dergi1
                $uri = $_SERVER['REQUEST_URI'] ?? '/';
                $pathParts = explode('/', trim($uri, '/'));
                $slug = $pathParts[0] ?? null;
            }
        }

        if (empty($slug) || $slug === 'localhost' || is_numeric($slug)) {
            return null;
        }

        // Tenant'ı veritabanından bul
        $tenant = self::getTenantBySlug($slug);

        if ($tenant && $tenant['status'] === 'active') {
            self::$currentTenant = (object)$tenant;
            return self::$currentTenant;
        }

        return null;
    }

    /**
     * Slug'a göre tenant bilgisini getir
     */
    private static function getTenantBySlug(string $slug): ?array
    {
        try {
            $stmt = Database::query(
                "SELECT * FROM tenants WHERE slug = ? AND status = 'active' LIMIT 1",
                [$slug]
            );

            $tenant = $stmt->fetch();
            return $tenant ?: null;
        } catch (\Exception $e) {
            // Core database henüz oluşmamış olabilir (kurulum aşamasında)
            return null;
        }
    }

    /**
     * Mevcut tenant'ı al
     */
    public static function current(): ?object
    {
        return self::$currentTenant ?: self::resolve();
    }

    /**
     * Mevcut tenant'ın database adını al
     */
    public static function getCurrentDatabase(): ?string
    {
        $tenant = self::current();
        return $tenant ? $tenant->database_name : null;
    }

    /**
     * Manuel olarak tenant ayarla (test için)
     */
    public static function setTenant(object $tenant): void
    {
        self::$currentTenant = $tenant;
    }

    /**
     * Tenant'ı sıfırla
     */
    public static function reset(): void
    {
        self::$currentTenant = null;
    }
}
