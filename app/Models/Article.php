<?php
namespace App\Models;

use Core\Database;

/**
 * AMDS - Article Model
 * Makale veritabani islemleri
 */
class Article
{
    /**
     * Tum makaleleri getirir (pagination destekli)
     */
    public static function getAll(string $tenantDb, int $limit = 20, int $offset = 0, array $filters = []): array
    {
        $pdo = Database::getTenantConnection($tenantDb);

        // Base query
        $sql = "
            SELECT m.*,
                   (SELECT GROUP_CONCAT(CONCAT(ad, ' ', soyad) SEPARATOR ', ')
                    FROM makale_yazarlari
                    WHERE makale_id = m.id
                    ORDER BY yazar_sirasi) as yazarlar
            FROM makaleler m
            WHERE 1=1
        ";

        $params = [];

        // Durum filtresi
        if (!empty($filters['durum'])) {
            $sql .= " AND m.durum = :durum";
            $params['durum'] = $filters['durum'];
        }

        // Makale turu filtresi
        if (!empty($filters['makale_turu'])) {
            $sql .= " AND m.makale_turu = :makale_turu";
            $params['makale_turu'] = $filters['makale_turu'];
        }

        // Arama (baslik, ozet, anahtar kelimeler)
        if (!empty($filters['search'])) {
            $sql .= " AND (
                m.baslik_tr LIKE :search
                OR m.baslik_en LIKE :search
                OR m.ozet_tr LIKE :search
                OR m.ozet_en LIKE :search
                OR m.anahtar_kelimeler_tr LIKE :search
                OR m.anahtar_kelimeler_en LIKE :search
            )";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        // Siralama
        $sql .= " ORDER BY m.gonderi_tarihi DESC";

        // Pagination
        $sql .= " LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);

        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Toplam makale sayisini getirir (filtrelere gore)
     */
    public static function count(string $tenantDb, array $filters = []): int
    {
        $pdo = Database::getTenantConnection($tenantDb);

        $sql = "SELECT COUNT(*) FROM makaleler m WHERE 1=1";
        $params = [];

        // Durum filtresi
        if (!empty($filters['durum'])) {
            $sql .= " AND m.durum = :durum";
            $params['durum'] = $filters['durum'];
        }

        // Makale turu filtresi
        if (!empty($filters['makale_turu'])) {
            $sql .= " AND m.makale_turu = :makale_turu";
            $params['makale_turu'] = $filters['makale_turu'];
        }

        // Arama
        if (!empty($filters['search'])) {
            $sql .= " AND (
                m.baslik_tr LIKE :search
                OR m.baslik_en LIKE :search
                OR m.ozet_tr LIKE :search
                OR m.ozet_en LIKE :search
                OR m.anahtar_kelimeler_tr LIKE :search
                OR m.anahtar_kelimeler_en LIKE :search
            )";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    /**
     * ID'ye gore makale getirir
     */
    public static function findById(int $id, string $tenantDb): ?array
    {
        $pdo = Database::getTenantConnection($tenantDb);

        $stmt = $pdo->prepare("
            SELECT m.*
            FROM makaleler m
            WHERE m.id = :id
            LIMIT 1
        ");

        $stmt->execute(['id' => $id]);
        $makale = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$makale) {
            return null;
        }

        // Yazarlari getir
        $makale['yazarlar'] = self::getAuthors($id, $tenantDb);

        // Dosyalari getir
        $makale['dosyalar'] = self::getFiles($id, $tenantDb);

        return $makale;
    }

    /**
     * Makale kodu ile makale getirir
     */
    public static function findByCode(string $code, string $tenantDb): ?array
    {
        $pdo = Database::getTenantConnection($tenantDb);

        $stmt = $pdo->prepare("
            SELECT m.*
            FROM makaleler m
            WHERE m.makale_kodu = :code
            LIMIT 1
        ");

        $stmt->execute(['code' => $code]);
        $makale = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$makale) {
            return null;
        }

        // Yazarlari getir
        $makale['yazarlar'] = self::getAuthors($makale['id'], $tenantDb);

        // Dosyalari getir
        $makale['dosyalar'] = self::getFiles($makale['id'], $tenantDb);

        return $makale;
    }

    /**
     * Kullanicinin makalelerini getirir
     */
    public static function getByUser(int $userId, string $tenantDb, int $limit = 20, int $offset = 0): array
    {
        $pdo = Database::getTenantConnection($tenantDb);

        $stmt = $pdo->prepare("
            SELECT m.*,
                   (SELECT GROUP_CONCAT(CONCAT(ad, ' ', soyad) SEPARATOR ', ')
                    FROM makale_yazarlari
                    WHERE makale_id = m.id
                    ORDER BY yazar_sirasi) as yazarlar
            FROM makaleler m
            INNER JOIN makale_yazarlari my ON m.id = my.makale_id
            WHERE my.kullanici_id = :user_id
            ORDER BY m.gonderi_tarihi DESC
            LIMIT :limit OFFSET :offset
        ");

        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Makale yazarlarini getirir
     */
    public static function getAuthors(int $makaleId, string $tenantDb): array
    {
        $pdo = Database::getTenantConnection($tenantDb);

        $stmt = $pdo->prepare("
            SELECT *
            FROM makale_yazarlari
            WHERE makale_id = :makale_id
            ORDER BY yazar_sirasi ASC
        ");

        $stmt->execute(['makale_id' => $makaleId]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Makale dosyalarini getirir
     */
    public static function getFiles(int $makaleId, string $tenantDb): array
    {
        $pdo = Database::getTenantConnection($tenantDb);

        $stmt = $pdo->prepare("
            SELECT *
            FROM dosyalar
            WHERE makale_id = :makale_id
            ORDER BY yuklenme_tarihi DESC
        ");

        $stmt->execute(['makale_id' => $makaleId]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Yeni makale olusturur
     */
    public static function create(array $data, string $tenantDb): int
    {
        $pdo = Database::getTenantConnection($tenantDb);

        // Benzersiz makale kodu olustur
        $makaleKodu = self::generateArticleCode($tenantDb);

        $stmt = $pdo->prepare("
            INSERT INTO makaleler (
                makale_kodu, baslik_tr, baslik_en, ozet_tr, ozet_en,
                anahtar_kelimeler_tr, anahtar_kelimeler_en, makale_turu,
                makale_konusu, referanslar, durum, mevcut_asamasi
            ) VALUES (
                :makale_kodu, :baslik_tr, :baslik_en, :ozet_tr, :ozet_en,
                :anahtar_kelimeler_tr, :anahtar_kelimeler_en, :makale_turu,
                :makale_konusu, :referanslar, 'gonderildi', 'yeni_gonderim'
            )
        ");

        $stmt->execute([
            'makale_kodu' => $makaleKodu,
            'baslik_tr' => $data['baslik_tr'],
            'baslik_en' => $data['baslik_en'],
            'ozet_tr' => $data['ozet_tr'],
            'ozet_en' => $data['ozet_en'],
            'anahtar_kelimeler_tr' => $data['anahtar_kelimeler_tr'],
            'anahtar_kelimeler_en' => $data['anahtar_kelimeler_en'],
            'makale_turu' => $data['makale_turu'],
            'makale_konusu' => $data['makale_konusu'] ?? null,
            'referanslar' => $data['referanslar'] ?? null,
        ]);

        return (int) $pdo->lastInsertId();
    }

    /**
     * Makaleyi gunceller
     */
    public static function update(int $id, array $data, string $tenantDb): bool
    {
        $pdo = Database::getTenantConnection($tenantDb);

        $stmt = $pdo->prepare("
            UPDATE makaleler SET
                baslik_tr = :baslik_tr,
                baslik_en = :baslik_en,
                ozet_tr = :ozet_tr,
                ozet_en = :ozet_en,
                anahtar_kelimeler_tr = :anahtar_kelimeler_tr,
                anahtar_kelimeler_en = :anahtar_kelimeler_en,
                makale_turu = :makale_turu,
                makale_konusu = :makale_konusu,
                referanslar = :referanslar
            WHERE id = :id
        ");

        return $stmt->execute([
            'id' => $id,
            'baslik_tr' => $data['baslik_tr'],
            'baslik_en' => $data['baslik_en'],
            'ozet_tr' => $data['ozet_tr'],
            'ozet_en' => $data['ozet_en'],
            'anahtar_kelimeler_tr' => $data['anahtar_kelimeler_tr'],
            'anahtar_kelimeler_en' => $data['anahtar_kelimeler_en'],
            'makale_turu' => $data['makale_turu'],
            'makale_konusu' => $data['makale_konusu'] ?? null,
            'referanslar' => $data['referanslar'] ?? null,
        ]);
    }

    /**
     * Makale durumunu gunceller
     */
    public static function updateStatus(int $id, string $durum, string $tenantDb): bool
    {
        $pdo = Database::getTenantConnection($tenantDb);

        $stmt = $pdo->prepare("
            UPDATE makaleler SET
                durum = :durum,
                guncelleme_tarihi = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            'id' => $id,
            'durum' => $durum,
        ]);
    }

    /**
     * Makaleyi siler
     */
    public static function delete(int $id, string $tenantDb): bool
    {
        $pdo = Database::getTenantConnection($tenantDb);

        $stmt = $pdo->prepare("DELETE FROM makaleler WHERE id = :id");

        return $stmt->execute(['id' => $id]);
    }

    /**
     * Benzersiz makale kodu olusturur
     */
    private static function generateArticleCode(string $tenantDb): string
    {
        $pdo = Database::getTenantConnection($tenantDb);

        do {
            // Format: AMDS-2025-XXXX
            $year = date('Y');
            $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $code = "AMDS-{$year}-{$random}";

            // Kod mevcut mu kontrol et
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM makaleler WHERE makale_kodu = :code");
            $stmt->execute(['code' => $code]);
            $exists = $stmt->fetchColumn() > 0;

        } while ($exists);

        return $code;
    }

    /**
     * Durumlarin listesini getirir
     */
    public static function getStatusList(): array
    {
        return [
            'gonderildi' => 'Gönderildi',
            'on_kontrol' => 'Ön Kontrol',
            'editore_atandi' => 'Editöre Atandı',
            'hakem_ataniyor' => 'Hakem Atanıyor',
            'hakemde' => 'Hakemde',
            'degerlendirme_tamamlandi' => 'Değerlendirme Tamamlandı',
            'duzeltme_bekleniyor' => 'Düzeltme Bekleniyor',
            'kabul_edildi' => 'Kabul Edildi',
            'reddedildi' => 'Reddedildi',
            'yayinlandi' => 'Yayınlandı',
        ];
    }

    /**
     * Makale turlerinin listesini getirir
     */
    public static function getTypeList(): array
    {
        return [
            'arastirma' => 'Araştırma Makalesi',
            'derleme' => 'Derleme',
            'olgu_sunumu' => 'Olgu Sunumu',
            'editore_mektup' => 'Editöre Mektup',
        ];
    }

    /**
     * Makale konularinin listesini getirir
     */
    public static function getSubjectList(): array
    {
        return [
            'bilgisayar' => 'Bilgisayar Bilimleri',
            'muhendislik' => 'Mühendislik',
            'tip' => 'Tıp Bilimleri',
            'sosyal' => 'Sosyal Bilimler',
            'egitim' => 'Eğitim Bilimleri',
            'sanat' => 'Sanat ve Beşeri Bilimler',
            'tarim' => 'Tarım ve Yaşam Bilimleri',
            'fizik' => 'Fizik Bilimleri',
            'cevre' => 'Çevre Bilimleri',
            'matematik' => 'Matematik',
            'kimya' => 'Kimya',
            'biyoloji' => 'Biyoloji',
        ];
    }
}
