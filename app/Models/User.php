<?php
namespace App\Models;

use Core\Database;

/**
 * AMDS - User Model
 * Kullanıcı veritabanı işlemleri
 */
class User
{
    /**
     * Email'e göre kullanıcı bulur
     */
    public static function findByEmail(string $email, string $tenantDb): ?array
    {
        $pdo = Database::getTenantConnection($tenantDb);
        $stmt = $pdo->prepare("
            SELECT * FROM kullanicilar
            WHERE email = :email AND durum = 'active'
            LIMIT 1
        ");
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    /**
     * ID'ye göre kullanıcı bulur
     */
    public static function findById(int $id, string $tenantDb): ?array
    {
        $pdo = Database::getTenantConnection($tenantDb);
        $stmt = $pdo->prepare("
            SELECT * FROM kullanicilar
            WHERE id = :id AND durum = 'active'
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);

        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    /**
     * Kullanıcının rollerini getirir
     */
    public static function getRoles(int $userId, string $tenantDb): array
    {
        $pdo = Database::getTenantConnection($tenantDb);
        $stmt = $pdo->prepare("
            SELECT r.rol_adi_en, r.rol_adi_tr
            FROM kullanici_roller kr
            JOIN roller r ON kr.rol_id = r.id
            WHERE kr.kullanici_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Yeni kullanıcı oluşturur
     */
    public static function create(array $data, string $tenantDb): int
    {
        $pdo = Database::getTenantConnection($tenantDb);

        // Şifreyi hash'le
        $hashedPassword = password_hash($data['sifre'], PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("
            INSERT INTO kullanicilar (
                email, sifre_hash, ad, soyad, kurum, unvan,
                telefon, durum
            ) VALUES (
                :email, :sifre_hash, :ad, :soyad, :kurum, :unvan,
                :telefon, 'active'
            )
        ");

        $stmt->execute([
            'email' => $data['email'],
            'sifre_hash' => $hashedPassword,
            'ad' => $data['ad'],
            'soyad' => $data['soyad'],
            'kurum' => $data['kurum'] ?? '',
            'unvan' => $data['unvan'] ?? '',
            'telefon' => $data['telefon'] ?? '',
        ]);

        return (int) $pdo->lastInsertId();
    }

    /**
     * Kullanıcıya rol atar
     */
    public static function assignRole(int $userId, int $roleId, string $tenantDb): void
    {
        $pdo = Database::getTenantConnection($tenantDb);
        $stmt = $pdo->prepare("
            INSERT INTO kullanici_roller (kullanici_id, rol_id, atanma_tarihi)
            VALUES (:user_id, :role_id, NOW())
            ON DUPLICATE KEY UPDATE rol_id = :role_id
        ");
        $stmt->execute([
            'user_id' => $userId,
            'role_id' => $roleId,
        ]);
    }

    /**
     * Şifre doğrulama
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Email'in kullanımda olup olmadığını kontrol eder
     */
    public static function emailExists(string $email, string $tenantDb): bool
    {
        $pdo = Database::getTenantConnection($tenantDb);
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM kullanicilar WHERE email = :email
        ");
        $stmt->execute(['email' => $email]);

        return $stmt->fetchColumn() > 0;
    }

    /**
     * Kullanıcı son giriş zamanını günceller
     */
    public static function updateLastLogin(int $userId, string $tenantDb): void
    {
        $pdo = Database::getTenantConnection($tenantDb);
        $stmt = $pdo->prepare("
            UPDATE kullanicilar
            SET son_giris_tarihi = NOW()
            WHERE id = :id
        ");
        $stmt->execute(['id' => $userId]);
    }

    /**
     * Varsayılan rol ID'sini getirir (Yazar)
     */
    public static function getDefaultRoleId(string $tenantDb): int
    {
        $pdo = Database::getTenantConnection($tenantDb);
        $stmt = $pdo->query("
            SELECT id FROM roller
            WHERE rol_adi_en = 'Author'
            LIMIT 1
        ");

        $roleId = $stmt->fetchColumn();
        return $roleId ?: 5; // Varsayılan olarak 5 (Yazar rolü)
    }
}
