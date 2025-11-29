<?php
namespace Core;

use PDO;
use PDOException;

/**
 * AMDS - Database Sınıfı
 * Multi-tenant veritabanı bağlantı yöneticisi
 */
class Database
{
    private static $coreConnection = null;
    private static $tenantConnections = [];
    private $config;

    public function __construct(array $config = [])
    {
        $this->config = $config ?: require __DIR__ . '/../config/database.php';
    }

    /**
     * Core database bağlantısını al
     */
    public static function getCoreConnection(): PDO
    {
        if (self::$coreConnection === null) {
            $config = require __DIR__ . '/../config/database.php';
            $dbConfig = $config['connections']['mysql'];

            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $dbConfig['host'],
                $dbConfig['port'],
                $dbConfig['database'],
                $dbConfig['charset']
            );

            try {
                self::$coreConnection = new PDO(
                    $dsn,
                    $dbConfig['username'],
                    $dbConfig['password'],
                    $config['options'] ?? []
                );
            } catch (PDOException $e) {
                throw new \Exception('Core database baglantisi basarisiz: ' . $e->getMessage());
            }
        }

        return self::$coreConnection;
    }

    /**
     * Tenant database bağlantısını al
     */
    public static function getTenantConnection(string $databaseName): PDO
    {
        if (!isset(self::$tenantConnections[$databaseName])) {
            $config = require __DIR__ . '/../config/database.php';
            $dbConfig = $config['connections']['mysql'];

            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $dbConfig['host'],
                $dbConfig['port'],
                $databaseName,
                $dbConfig['charset']
            );

            try {
                self::$tenantConnections[$databaseName] = new PDO(
                    $dsn,
                    $dbConfig['username'],
                    $dbConfig['password'],
                    $config['options'] ?? []
                );
            } catch (PDOException $e) {
                throw new \Exception("Tenant database baglantisi basarisiz ($databaseName): " . $e->getMessage());
            }
        }

        return self::$tenantConnections[$databaseName];
    }

    /**
     * Query çalıştır (Core database'de)
     */
    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $pdo = self::getCoreConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Tenant database'de query çalıştır
     */
    public static function tenantQuery(string $databaseName, string $sql, array $params = []): \PDOStatement
    {
        $pdo = self::getTenantConnection($databaseName);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Insert işlemi
     */
    public static function insert(string $table, array $data): int
    {
        $pdo = self::getCoreConnection();

        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($data));

        return (int)$pdo->lastInsertId();
    }

    /**
     * Update işlemi
     */
    public static function update(string $table, int $id, array $data): bool
    {
        $pdo = self::getCoreConnection();

        $set = [];
        foreach (array_keys($data) as $column) {
            $set[] = "{$column} = ?";
        }
        $setClause = implode(', ', $set);

        $sql = "UPDATE {$table} SET {$setClause} WHERE id = ?";

        $params = array_values($data);
        $params[] = $id;

        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete işlemi
     */
    public static function delete(string $table, int $id): bool
    {
        $pdo = self::getCoreConnection();
        $stmt = $pdo->prepare("DELETE FROM {$table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Tek satır getir
     */
    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = self::query($sql, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Tüm satırları getir
     */
    public static function fetchAll(string $sql, array $params = []): array
    {
        $stmt = self::query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Transaction başlat
     */
    public static function beginTransaction(): void
    {
        self::getCoreConnection()->beginTransaction();
    }

    /**
     * Transaction commit
     */
    public static function commit(): void
    {
        self::getCoreConnection()->commit();
    }

    /**
     * Transaction rollback
     */
    public static function rollback(): void
    {
        self::getCoreConnection()->rollBack();
    }

    /**
     * Yeni database oluştur (Multi-tenant için)
     */
    public static function createDatabase(string $databaseName): bool
    {
        $pdo = self::getCoreConnection();
        $stmt = $pdo->prepare("CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        return $stmt->execute();
    }
}
