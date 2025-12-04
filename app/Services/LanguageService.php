<?php

namespace App\Services;

/**
 * Genişletilebilir Çoklu Dil Yönetim Servisi
 *
 * Özellikler:
 * - TR, EN + sınırsız dil desteği
 * - UTF-8mb4 (Japonca, Arapça, Çince, Kril, Emoji)
 * - RTL (Right-to-Left) dil desteği
 * - Fallback sistemi
 * - Cache mekanizması
 * - Otomatik dil tespiti (tarayıcı, session, cookie)
 */
class LanguageService
{
    private $db;
    private $tenantId;
    private $currentLang;
    private $availableLanguages = [];
    private $fallbackLang = 'en';
    private $cache = [];
    private $cacheFile;

    public function __construct($db, $tenantId = 1, $lang = null)
    {
        $this->db = $db;
        $this->tenantId = $tenantId;
        $this->cacheFile = __DIR__ . '/../../storage/temp/lang_cache_' . $tenantId . '.json';

        // Dil yapılandırmasını yükle
        $this->loadLanguageConfig();

        // Mevcut dili belirle
        $this->currentLang = $this->detectLanguage($lang);

        // Cache'i yükle
        $this->loadCache();
    }

    /**
     * Dil yapılandırmasını yükle
     */
    private function loadLanguageConfig()
    {
        $configPath = __DIR__ . '/../../config/languages/config.json';

        if (file_exists($configPath)) {
            $config = json_decode(file_get_contents($configPath), true);

            // Sadece aktif dilleri al
            foreach ($config['available_languages'] as $lang) {
                if ($lang['enabled']) {
                    $this->availableLanguages[$lang['code']] = $lang;
                }
            }

            $this->fallbackLang = $config['fallback_language'] ?? 'en';
        } else {
            // Varsayılan: TR ve EN
            $this->availableLanguages = [
                'tr' => [
                    'code' => 'tr',
                    'name' => 'Türkçe',
                    'native_name' => 'Türkçe',
                    'direction' => 'ltr',
                    'enabled' => true,
                    'default' => true
                ],
                'en' => [
                    'code' => 'en',
                    'name' => 'English',
                    'native_name' => 'English',
                    'direction' => 'ltr',
                    'enabled' => true,
                    'default' => false
                ]
            ];
        }
    }

    /**
     * Dili tespit et (otomatik veya manuel)
     */
    private function detectLanguage($lang = null)
    {
        // 1. Manuel seçim (parametre)
        if ($lang && isset($this->availableLanguages[$lang])) {
            return $lang;
        }

        // 2. Session'dan
        if (session_status() === PHP_SESSION_ACTIVE) {
            if (isset($_SESSION['language']) && isset($this->availableLanguages[$_SESSION['language']])) {
                return $_SESSION['language'];
            }
        }

        // 3. Cookie'den
        if (isset($_COOKIE['language']) && isset($this->availableLanguages[$_COOKIE['language']])) {
            return $_COOKIE['language'];
        }

        // 4. Tarayıcı dilinden (Accept-Language header)
        $browserLang = $this->detectBrowserLanguage();
        if ($browserLang && isset($this->availableLanguages[$browserLang])) {
            return $browserLang;
        }

        // 5. Varsayılan dil
        foreach ($this->availableLanguages as $code => $lang) {
            if (isset($lang['default']) && $lang['default']) {
                return $code;
            }
        }

        // 6. Fallback
        return $this->fallbackLang;
    }

    /**
     * Tarayıcı dilini tespit et
     */
    private function detectBrowserLanguage()
    {
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return null;
        }

        // Accept-Language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7
        $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

        foreach ($langs as $lang) {
            $code = strtolower(substr(trim($lang), 0, 2));
            if (isset($this->availableLanguages[$code])) {
                return $code;
            }
        }

        return null;
    }

    /**
     * Dil değişkenini getir (fallback destekli)
     * @param string $key Örn: 'form.author.title'
     * @param string|null $lang Dil kodu (null ise mevcut dil)
     * @return string
     */
    public function get($key, $lang = null)
    {
        $lang = $lang ?? $this->currentLang;

        // Cache kontrol
        $cacheKey = "{$this->tenantId}:{$lang}:{$key}";
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        // Veritabanından çek
        try {
            $stmt = $this->db->prepare("
                SELECT deger
                FROM dil_degiskenleri
                WHERE tenant_id = ? AND anahtar = ? AND dil = ?
                LIMIT 1
            ");
            $stmt->execute([$this->tenantId, $key, $lang]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($result) {
                $this->cache[$cacheKey] = $result['deger'];
                $this->saveCache();
                return $result['deger'];
            }
        } catch (\Exception $e) {
            // Log error (opsiyonel)
        }

        // Fallback: Başka dilde var mı?
        if ($lang !== $this->fallbackLang) {
            return $this->get($key, $this->fallbackLang);
        }

        // Hiçbir yerde yok, key'i döndür
        return $key;
    }

    /**
     * Tüm dil değişkenlerini getir (sayfa bazlı)
     * @param string $page Sayfa adı (create_article, author_list, vb.)
     * @param string|null $lang Dil kodu
     * @return array
     */
    public function getAll($page, $lang = null)
    {
        $lang = $lang ?? $this->currentLang;

        try {
            $stmt = $this->db->prepare("
                SELECT anahtar, deger
                FROM dil_degiskenleri
                WHERE tenant_id = ? AND sayfa = ? AND dil = ?
            ");
            $stmt->execute([$this->tenantId, $page, $lang]);
            $results = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);

            return $results;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Dil değişkenini güncelle/ekle
     * @param string $key Anahtar
     * @param string $value Değer
     * @param string|null $lang Dil kodu
     * @param array $meta Metadata (kategori, sayfa, vb.)
     * @return bool
     */
    public function set($key, $value, $lang = null, $meta = [])
    {
        $lang = $lang ?? $this->currentLang;

        try {
            $stmt = $this->db->prepare("
                INSERT INTO dil_degiskenleri
                (tenant_id, anahtar, dil, deger, kategori, sayfa, varsayilan)
                VALUES (?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                deger = VALUES(deger),
                kategori = VALUES(kategori),
                sayfa = VALUES(sayfa)
            ");

            $stmt->execute([
                $this->tenantId,
                $key,
                $lang,
                $value,
                $meta['kategori'] ?? null,
                $meta['sayfa'] ?? null,
                $meta['varsayilan'] ?? null
            ]);

            // Cache'i temizle
            $this->clearCache();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * JSON dil paketini içe aktar
     * @param string $lang Dil kodu
     * @param string $page Sayfa adı
     * @return bool
     */
    public function importFromJson($lang, $page)
    {
        $jsonPath = __DIR__ . "/../../config/languages/{$lang}/{$page}.json";

        if (!file_exists($jsonPath)) {
            return false;
        }

        $data = json_decode(file_get_contents($jsonPath), true);

        if (!$data) {
            return false;
        }

        // Flatten nested array
        $flattened = $this->flattenArray($data);

        // Veritabanına kaydet
        foreach ($flattened as $key => $value) {
            $this->set($key, $value, $lang, [
                'sayfa' => $page,
                'kategori' => explode('.', $key)[0] ?? null
            ]);
        }

        return true;
    }

    /**
     * İç içe array'i flatten et
     * @param array $array
     * @param string $prefix
     * @return array
     */
    private function flattenArray($array, $prefix = '')
    {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = $prefix ? "{$prefix}.{$key}" : $key;

            if (is_array($value)) {
                $result = array_merge($result, $this->flattenArray($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }

    /**
     * Mevcut dili döndür
     */
    public function getCurrentLanguage()
    {
        return $this->currentLang;
    }

    /**
     * Kullanılabilir dilleri döndür
     */
    public function getAvailableLanguages()
    {
        return $this->availableLanguages;
    }

    /**
     * Dil değiştir
     * @param string $lang Dil kodu
     * @return bool
     */
    public function setLanguage($lang)
    {
        if (!isset($this->availableLanguages[$lang])) {
            return false;
        }

        $this->currentLang = $lang;

        // Session'a kaydet
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['language'] = $lang;
        }

        // Cookie'ye kaydet (1 yıl)
        setcookie('language', $lang, time() + (365 * 24 * 60 * 60), '/');

        return true;
    }

    /**
     * RTL (Right-to-Left) dil mi kontrol et
     * @param string|null $lang Dil kodu
     * @return bool
     */
    public function isRTL($lang = null)
    {
        $lang = $lang ?? $this->currentLang;

        return isset($this->availableLanguages[$lang])
            && $this->availableLanguages[$lang]['direction'] === 'rtl';
    }

    /**
     * Dil bilgisini döndür
     * @param string|null $lang Dil kodu
     * @return array|null
     */
    public function getLanguageInfo($lang = null)
    {
        $lang = $lang ?? $this->currentLang;

        return $this->availableLanguages[$lang] ?? null;
    }

    /**
     * Cache'i yükle
     */
    private function loadCache()
    {
        if (file_exists($this->cacheFile)) {
            $cacheData = file_get_contents($this->cacheFile);
            $this->cache = json_decode($cacheData, true) ?? [];
        }
    }

    /**
     * Cache'i kaydet
     */
    private function saveCache()
    {
        $dir = dirname($this->cacheFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($this->cacheFile, json_encode($this->cache));
    }

    /**
     * Cache'i temizle
     */
    public function clearCache()
    {
        $this->cache = [];

        if (file_exists($this->cacheFile)) {
            unlink($this->cacheFile);
        }
    }

    /**
     * Tüm dil değişkenlerini dışa aktar (JSON)
     * @param string $lang Dil kodu
     * @param string $page Sayfa adı
     * @return array
     */
    public function exportToJson($lang, $page)
    {
        $data = $this->getAll($page, $lang);

        // Nested array'e dönüştür
        $nested = [];

        foreach ($data as $key => $value) {
            $keys = explode('.', $key);
            $temp = &$nested;

            foreach ($keys as $k) {
                if (!isset($temp[$k])) {
                    $temp[$k] = [];
                }
                $temp = &$temp[$k];
            }

            $temp = $value;
        }

        return $nested;
    }
}
