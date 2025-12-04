# AMDS - SİSTEM ANALİZ VE GELİŞTİRME PLANI

## 📊 GİRİŞ ve AMAÇ

Bu belge, AMDS (Akademik Makale Değerlendirme Sistemi) için **dil desteği sistemi**, **yazar modülü** ve **referans sistemi** geliştirmesi için kapsamlı bir analiz ve planlama belgesidir.

### 🎯 Hedefler

1. **Çok Dilli Destek Sistemi**: Her dergi kendi terimlerini tanımlayabilsin
2. **Gelişmiş Yazar Modülü**: Email/ORCID ile arama, otomatik form doldurma
3. **Referans Yönetimi**: Manuel ve otomatik kayıt seçenekleri
4. **Taslak Sistemi**: Otomatik ve manuel kayıt özellikleri
5. **HTML İçinde Sabit Metinleri Kaldırma**: Tüm metinler dil değişkenlerinden gelsin

---

## 📁 MEVCUT DURUM ANALİZİ

### ✅ Tamamlanan Özellikler

#### 1. Temel Makale Formu (create.php)
- **Durum**: Kısmen tamamlandı
- **Mevcut Adımlar**:
  - Step 0: Dil Seçimi ✅
  - Step 1: Ön Bilgi ✅
  - Step 2: Tür-Konu ✅
  - Step 3: Başlık (TR + EN) ✅
  - Step 4: Özet (TR + EN) ✅
  - Step 5: Anahtar Kelimeler (TR + EN) ✅
  - Step 6: Referanslar ✅
  - Step 7: Yazarlar ⚠️ (Frontend hazır, backend eksik)
  - Step 8-12: ❌ Eksik

#### 2. CSS ve JS Ayrımı
- **Durum**: Yapılmış
- `public/assets/css/create-wizard.css` ✅
- `public/assets/js/create-wizard.js` ✅
- `public/assets/js/authors-management.js` ✅

#### 3. Veritabanı Yapısı
- **makaleler** tablosu: Temel alanlar mevcut ✅
- **makale_yazarlari** tablosu: Mevcut ✅
- **dosyalar** tablosu: Mevcut ✅
- **dil_degiskenleri** tablosu: ❌ MEVCUT DEĞİL

### ❌ Eksik Özellikler

1. **Dil Değişkenleri Sistemi**: Henüz oluşturulmamış
2. **Yazar Arama (Email/ORCID)**: Backend yok
3. **Otomatik Form Doldurma**: Backend API yok
4. **Taslak Kayıt Sistemi**: Henüz oluşturulmamış
5. **Hakem Sistemi**: Tamamen eksik
6. **Dosya Yükleme**: Frontend/Backend eksik

---

## 🌍 1. ÇOK DİLLİ DESTEK SİSTEMİ

### 🎯 Hedef

Her dergi/tenant kendi terimlerini tanımlayabilmeli. Örneğin:
- Dergi A: "Yazar" → "Author"
- Dergi B: "Yazar" → "Contributor"
- Dergi C: "Makale Türü" → "Article Type" veya "Manuscript Type"

### 📐 Veritabanı Yapısı

#### Tablo 1: `dil_degiskenleri` (Tenant veritabanında)

```sql
CREATE TABLE `dil_degiskenleri` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` INT UNSIGNED NOT NULL,
  `anahtar` VARCHAR(100) NOT NULL COMMENT 'Örn: form.author.title',
  `dil` VARCHAR(5) NOT NULL COMMENT 'tr, en, de, fr',
  `deger` TEXT NOT NULL COMMENT 'Çevrilmiş değer',
  `kategori` VARCHAR(50) DEFAULT NULL COMMENT 'form, table, button, message',
  `sayfa` VARCHAR(100) DEFAULT NULL COMMENT 'create_article, author_list',
  `varsayilan` TEXT DEFAULT NULL COMMENT 'Sistem varsayılanı',
  `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `guncelleme_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  UNIQUE KEY `unique_dil_anahtar` (`tenant_id`, `anahtar`, `dil`),
  KEY `idx_tenant_dil` (`tenant_id`, `dil`),
  KEY `idx_kategori` (`kategori`),
  KEY `idx_sayfa` (`sayfa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### Tablo 2: `dil_paketleri` (Core veritabanında)

```sql
CREATE TABLE `dil_paketleri` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `paket_adi` VARCHAR(100) NOT NULL COMMENT 'default, academic, medical',
  `dil` VARCHAR(5) NOT NULL,
  `aciklama` TEXT,
  `versiyon` VARCHAR(20) DEFAULT '1.0',
  `dosya_yolu` VARCHAR(255) COMMENT 'JSON dosya yolu',
  `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  UNIQUE KEY `unique_paket_dil` (`paket_adi`, `dil`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 🔧 Dil Sistemi Yapısı

#### Anahtar Yapısı (Key Structure)

```
{kategori}.{sayfa}.{öğe}.{alt_öğe}

Örnekler:
- form.create_article.author.title → "Yazar Başlığı"
- form.create_article.author.first_name → "Ad"
- form.create_article.author.orcid_search → "ORCID ile Ara"
- button.save → "Kaydet"
- button.cancel → "İptal"
- message.success.article_saved → "Makale başarıyla kaydedildi"
- validation.required → "Bu alan zorunludur"
- validation.email_invalid → "Geçerli bir e-posta adresi girin"
```

### 📦 Varsayılan Dil Paketi Yapısı

#### `config/languages/tr/create_article.json`

```json
{
  "form": {
    "create_article": {
      "title": "Yeni Makale Oluştur",
      "language_selection": {
        "title": "Dil Seçimi",
        "label": "Makale Dili",
        "placeholder": "Lütfen bir makale dili seçiniz"
      },
      "article_type": {
        "title": "Makale Türü",
        "label": "Makale Türü",
        "placeholder": "Makale türü seçin",
        "research": "Araştırma Makalesi",
        "review": "Derleme Makale",
        "case": "Olgu Sunumu"
      },
      "author": {
        "title": "Yazarlar",
        "add_new": "Yeni Yazar Ekle",
        "search_email": "Email ile Ara",
        "search_orcid": "ORCID ile Ara",
        "first_name": "Ad",
        "last_name": "Soyad",
        "email": "E-posta",
        "orcid": "ORCID ID",
        "institution": "Kurum",
        "department": "Bölüm",
        "country": "Ülke",
        "author_order": "Yazar Sırası",
        "author_type": "Yazar Tipi",
        "corresponding_author": "Sorumlu Yazar",
        "use_this_data": "Bu Bilgileri Kullan"
      },
      "reference": {
        "title": "Referanslar",
        "add_new": "Yeni Referans Ekle",
        "remove": "Referansı Sil",
        "format_info": "Referanslar APA formatında olmalıdır",
        "placeholder": "Örnek: Smith, J. (2023). Makale başlığı. Dergi Adı, 10(2), 100-120."
      }
    }
  },
  "button": {
    "save": "Kaydet",
    "cancel": "İptal",
    "next": "Sonraki",
    "previous": "Önceki",
    "submit": "Gönder",
    "add": "Ekle",
    "edit": "Düzenle",
    "delete": "Sil",
    "search": "Ara"
  },
  "validation": {
    "required": "Bu alan zorunludur",
    "email_invalid": "Geçerli bir e-posta adresi girin",
    "min_length": "En az {min} karakter girmelisiniz",
    "max_length": "En fazla {max} karakter girebilirsiniz",
    "word_count": "{min} ile {max} kelime arasında olmalıdır",
    "keyword_count": "{min} ile {max} anahtar kelime girmelisiniz"
  },
  "message": {
    "success": {
      "saved": "Başarıyla kaydedildi",
      "updated": "Başarıyla güncellendi",
      "deleted": "Başarıyla silindi"
    },
    "error": {
      "save_failed": "Kaydetme işlemi başarısız",
      "load_failed": "Yükleme işlemi başarısız",
      "invalid_data": "Geçersiz veri"
    }
  }
}
```

### 🔌 Backend - Dil Sistemi API

#### `app/Services/LanguageService.php`

```php
<?php
namespace App\Services;

class LanguageService {
    private $tenantDb;
    private $currentLang;
    private $cache = [];

    public function __construct($tenantDb, $lang = 'tr') {
        $this->tenantDb = $tenantDb;
        $this->currentLang = $lang;
    }

    /**
     * Dil değişkenini getir
     * @param string $key Örn: form.create_article.author.title
     * @param array $params Yer tutucular için değerler
     * @return string
     */
    public function get($key, $params = []) {
        // Cache kontrolü
        $cacheKey = $this->currentLang . '.' . $key;
        if (isset($this->cache[$cacheKey])) {
            return $this->replacePlaceholders($this->cache[$cacheKey], $params);
        }

        // Veritabanından çek
        $stmt = $this->tenantDb->prepare("
            SELECT deger, varsayilan
            FROM dil_degiskenleri
            WHERE anahtar = ? AND dil = ?
            LIMIT 1
        ");
        $stmt->execute([$key, $this->currentLang]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($result) {
            $value = $result['deger'] ?: $result['varsayilan'];
        } else {
            // Varsayılan dil paketinden yükle
            $value = $this->loadFromDefaultPackage($key);
        }

        // Cache'e ekle
        $this->cache[$cacheKey] = $value;

        return $this->replacePlaceholders($value, $params);
    }

    /**
     * Varsayılan paketten yükle
     */
    private function loadFromDefaultPackage($key) {
        $parts = explode('.', $key);
        $category = $parts[0] ?? '';
        $page = $parts[1] ?? '';

        $filePath = __DIR__ . "/../../config/languages/{$this->currentLang}/{$page}.json";

        if (!file_exists($filePath)) {
            return $key; // Anahtar bulunamadı
        }

        $json = json_decode(file_get_contents($filePath), true);

        // İç içe değere ulaş
        $value = $json;
        foreach ($parts as $part) {
            if (isset($value[$part])) {
                $value = $value[$part];
            } else {
                return $key;
            }
        }

        return is_string($value) ? $value : $key;
    }

    /**
     * Yer tutucuları değiştir
     * Örn: "En az {min} karakter" → "En az 5 karakter"
     */
    private function replacePlaceholders($text, $params) {
        if (empty($params)) {
            return $text;
        }

        foreach ($params as $key => $value) {
            $text = str_replace("{{$key}}", $value, $text);
        }

        return $text;
    }

    /**
     * Sayfa için tüm dil değişkenlerini getir
     * @param string $page Örn: create_article
     * @return array
     */
    public function getPageTranslations($page) {
        $stmt = $this->tenantDb->prepare("
            SELECT anahtar, deger, varsayilan
            FROM dil_degiskenleri
            WHERE sayfa = ? AND dil = ?
        ");
        $stmt->execute([$page, $this->currentLang]);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $translations = [];
        foreach ($results as $row) {
            $translations[$row['anahtar']] = $row['deger'] ?: $row['varsayilan'];
        }

        // Varsayılan paketten de yükle
        $defaultTranslations = $this->loadPageFromDefaultPackage($page);

        return array_merge($defaultTranslations, $translations);
    }

    /**
     * Varsayılan paketten sayfa yükle
     */
    private function loadPageFromDefaultPackage($page) {
        $filePath = __DIR__ . "/../../config/languages/{$this->currentLang}/{$page}.json";

        if (!file_exists($filePath)) {
            return [];
        }

        $json = json_decode(file_get_contents($filePath), true);
        return $this->flattenArray($json);
    }

    /**
     * Çok boyutlu diziyi düzleştir
     * ['form' => ['title' => 'X']] → ['form.title' => 'X']
     */
    private function flattenArray($array, $prefix = '') {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = $prefix === '' ? $key : $prefix . '.' . $key;

            if (is_array($value)) {
                $result = array_merge($result, $this->flattenArray($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }

    /**
     * Dil değiş tir
     */
    public function setLanguage($lang) {
        $this->currentLang = $lang;
        $this->cache = []; // Cache temizle
    }
}
```

### 🎨 Frontend Kullanımı

#### View'da Kullanım (create.php)

```php
<?php
// Controller'da LanguageService başlat
$lang = new \App\Services\LanguageService($tenantDb, $_SESSION['user_lang'] ?? 'tr');

// Tüm çevirileri view'a gönder
$translations = $lang->getPageTranslations('create_article');
?>

<form>
    <!-- Dil değişkeni kullanımı -->
    <label><?= $lang->get('form.create_article.author.first_name') ?></label>

    <!-- Yer tutucu ile -->
    <div class="error"><?= $lang->get('validation.min_length', ['min' => 5]) ?></div>

    <!-- JavaScript için JSON olarak gönder -->
    <script>
        window.translations = <?= json_encode($translations) ?>;
    </script>
</form>
```

#### JavaScript'te Kullanım

```javascript
// assets/js/language-helper.js
class LanguageHelper {
    constructor(translations) {
        this.translations = translations;
    }

    get(key, params = {}) {
        let value = this.translations[key] || key;

        // Yer tutucuları değiştir
        Object.keys(params).forEach(paramKey => {
            value = value.replace(`{${paramKey}}`, params[paramKey]);
        });

        return value;
    }
}

// Global instance
const lang = new LanguageHelper(window.translations || {});

// Kullanım
alert(lang.get('message.success.saved'));
document.querySelector('#btn').textContent = lang.get('button.save');
```

---

## 👥 2. GELİŞMİŞ YAZAR MODÜLÜ

### 🎯 Hedefler

1. **Email ile Arama**: Yazarın email'ini gir → sistem veritabanında ara → bulursa bilgileri getir
2. **ORCID ile Arama**: ORCID ID'si gir → ORCID API'den çek → formu otomatik doldur
3. **Otomatik Form Doldurma**: Bulunan bilgiler form alanlarına otomatik yerleşsin
4. **Manuel Giriş**: Kullanıcı isterse manuel de girebilsin

### 📐 Veritabanı Yapısı

#### Mevcut: `makale_yazarlari` Tablosu

```sql
-- Zaten mevcut, güncelleme gerekebilir
ALTER TABLE `makale_yazarlari`
ADD COLUMN `orcid` VARCHAR(100) AFTER `kurum`,
ADD COLUMN `orcid_verified` TINYINT(1) DEFAULT 0 AFTER `orcid`,
ADD COLUMN `orcid_data` JSON AFTER `orcid_verified` COMMENT 'ORCID API response';
```

#### Yeni: `kullanici_yazar_profilleri` Tablosu

```sql
-- Kayıtlı kullanıcıların yazar profili bilgileri
CREATE TABLE `kullanici_yazar_profilleri` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `kullanici_id` INT UNSIGNED NOT NULL,
  `unvan` VARCHAR(50),
  `telefon` VARCHAR(50),
  `email2` VARCHAR(255),
  `departman` VARCHAR(255),
  `kurum` VARCHAR(255),
  `ulke` VARCHAR(100),
  `orcid` VARCHAR(100),
  `orcid_verified` TINYINT(1) DEFAULT 0,
  `orcid_data` JSON COMMENT 'ORCID API response cached',
  `bio` TEXT,
  `web_sitesi` VARCHAR(255),
  `google_scholar` VARCHAR(255),
  `scopus_author_id` VARCHAR(100),
  `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `guncelleme_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  UNIQUE KEY `unique_kullanici` (`kullanici_id`),
  UNIQUE KEY `unique_orcid` (`orcid`),
  KEY `idx_email2` (`email2`),

  FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 🔌 Backend API

#### `app/Controllers/AuthorController.php`

```php
<?php
namespace App\Controllers;

use App\Services\OrcidService;

class AuthorController {
    private $tenantDb;
    private $orcidService;

    public function __construct($tenantDb) {
        $this->tenantDb = $tenantDb;
        $this->orcidService = new OrcidService();
    }

    /**
     * Email ile yazar ara
     * GET /api/authors/search-by-email?email=test@example.com
     */
    public function searchByEmail() {
        $email = $_GET['email'] ?? '';

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->jsonResponse(false, 'Geçersiz email adresi');
        }

        // Önce kullanıcı tablosunda ara
        $stmt = $this->tenantDb->prepare("
            SELECT k.id, k.ad, k.soyad, k.email,
                   yp.unvan, yp.telefon, yp.email2, yp.departman,
                   yp.kurum, yp.ulke, yp.orcid
            FROM kullanicilar k
            LEFT JOIN kullanici_yazar_profilleri yp ON k.id = yp.kullanici_id
            WHERE k.email = ? OR yp.email2 = ?
            LIMIT 1
        ");
        $stmt->execute([$email, $email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($user) {
            return $this->jsonResponse(true, 'Yazar bulundu', [
                'source' => 'database',
                'author' => $user
            ]);
        }

        // Bulunamazsa makale yazarları tablosunda ara (geçmiş yazarlar)
        $stmt = $this->tenantDb->prepare("
            SELECT ad, soyad, email, kurum, orcid
            FROM makale_yazarlari
            WHERE email = ?
            ORDER BY olusturma_tarihi DESC
            LIMIT 1
        ");
        $stmt->execute([$email]);
        $author = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($author) {
            return $this->jsonResponse(true, 'Geçmiş kayıtlarda yazar bulundu', [
                'source' => 'history',
                'author' => $author
            ]);
        }

        return $this->jsonResponse(false, 'Yazar bulunamadı');
    }

    /**
     * ORCID ile yazar ara
     * GET /api/authors/search-by-orcid?orcid=0000-0001-2345-6789
     */
    public function searchByOrcid() {
        $orcid = $_GET['orcid'] ?? '';

        // ORCID formatını normalize et
        $orcid = $this->normalizeOrcid($orcid);

        if (empty($orcid) || !$this->validateOrcid($orcid)) {
            return $this->jsonResponse(false, 'Geçersiz ORCID ID');
        }

        // Önce veritabanında ara (cache)
        $stmt = $this->tenantDb->prepare("
            SELECT kullanici_id, orcid_data
            FROM kullanici_yazar_profilleri
            WHERE orcid = ?
            LIMIT 1
        ");
        $stmt->execute([$orcid]);
        $cached = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($cached && !empty($cached['orcid_data'])) {
            $orcidData = json_decode($cached['orcid_data'], true);

            // Cache 30 günden yeniyse kullan
            $cacheTime = strtotime($orcidData['cached_at'] ?? '1970-01-01');
            if ((time() - $cacheTime) < (30 * 24 * 60 * 60)) {
                return $this->jsonResponse(true, 'ORCID bilgileri bulundu (cache)', [
                    'source' => 'cache',
                    'author' => $this->formatOrcidData($orcidData)
                ]);
            }
        }

        // ORCID API'den çek
        try {
            $orcidData = $this->orcidService->getAuthorInfo($orcid);

            if ($orcidData) {
                // Cache'e kaydet
                $this->cacheOrcidData($orcid, $orcidData);

                return $this->jsonResponse(true, 'ORCID bilgileri başarıyla alındı', [
                    'source' => 'orcid_api',
                    'author' => $this->formatOrcidData($orcidData)
                ]);
            }
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'ORCID API hatası: ' . $e->getMessage());
        }

        return $this->jsonResponse(false, 'ORCID bilgileri alınamadı');
    }

    /**
     * ORCID verisini formatla (form için)
     */
    private function formatOrcidData($orcidData) {
        return [
            'firstName' => $orcidData['person']['name']['given-names']['value'] ?? '',
            'lastName' => $orcidData['person']['name']['family-name']['value'] ?? '',
            'email' => $orcidData['person']['emails']['email'][0]['email'] ?? '',
            'institution' => $orcidData['activities-summary']['employments']['employment-summary'][0]['organization']['name'] ?? '',
            'country' => $orcidData['person']['addresses']['address'][0]['country']['value'] ?? '',
            'orcid' => $orcidData['orcid-identifier']['path'] ?? ''
        ];
    }

    /**
     * ORCID verisini cache'e kaydet
     */
    private function cacheOrcidData($orcid, $orcidData) {
        $orcidData['cached_at'] = date('Y-m-d H:i:s');

        $stmt = $this->tenantDb->prepare("
            INSERT INTO kullanici_yazar_profilleri (kullanici_id, orcid, orcid_data, orcid_verified)
            VALUES (0, ?, ?, 1)
            ON DUPLICATE KEY UPDATE
                orcid_data = VALUES(orcid_data),
                guncelleme_tarihi = CURRENT_TIMESTAMP
        ");
        $stmt->execute([$orcid, json_encode($orcidData)]);
    }

    /**
     * ORCID normalize et
     */
    private function normalizeOrcid($orcid) {
        // URL'den ID'yi çıkar
        $orcid = str_replace('https://orcid.org/', '', $orcid);
        $orcid = str_replace('http://orcid.org/', '', $orcid);

        // Sadece rakam ve tire
        $orcid = preg_replace('/[^0-9-]/', '', $orcid);

        return $orcid;
    }

    /**
     * ORCID doğrula
     */
    private function validateOrcid($orcid) {
        return preg_match('/^\d{4}-\d{4}-\d{4}-\d{4}$/', $orcid);
    }

    /**
     * JSON response
     */
    private function jsonResponse($success, $message, $data = []) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }
}
```

#### `app/Services/OrcidService.php`

```php
<?php
namespace App\Services;

class OrcidService {
    private $apiUrl = 'https://pub.orcid.org/v3.0/';

    /**
     * ORCID API'den yazar bilgilerini çek
     */
    public function getAuthorInfo($orcid) {
        $url = $this->apiUrl . $orcid;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \Exception("ORCID API HTTP $httpCode");
        }

        return json_decode($response, true);
    }
}
```

### 🎨 Frontend - Yazar Arama ve Otomatik Doldurma

#### JavaScript: `assets/js/author-search.js`

```javascript
class AuthorSearch {
    constructor() {
        this.init();
    }

    init() {
        // Email arama butonu
        document.getElementById('searchByEmail')?.addEventListener('click', () => {
            this.searchByEmail();
        });

        // ORCID arama butonu
        document.getElementById('searchByOrcid')?.addEventListener('click', () => {
            this.searchByOrcid();
        });
    }

    async searchByEmail() {
        const email = document.getElementById('emailSearch').value.trim();

        if (!email) {
            alert(lang.get('validation.email_invalid'));
            return;
        }

        this.showLoading();

        try {
            const response = await fetch(`/api/authors/search-by-email?email=${encodeURIComponent(email)}`);
            const result = await response.json();

            if (result.success) {
                this.displaySearchResult(result.data.author, result.data.source);
            } else {
                this.showError(result.message);
            }
        } catch (error) {
            this.showError('Arama sırasında hata oluştu: ' + error.message);
        } finally {
            this.hideLoading();
        }
    }

    async searchByOrcid() {
        const orcid = document.getElementById('orcidSearch').value.trim();

        if (!orcid) {
            alert('Lütfen bir ORCID ID girin');
            return;
        }

        this.showLoading('ORCID API\'den bilgiler alınıyor...');

        try {
            const response = await fetch(`/api/authors/search-by-orcid?orcid=${encodeURIComponent(orcid)}`);
            const result = await response.json();

            if (result.success) {
                this.displaySearchResult(result.data.author, result.data.source);
            } else {
                this.showError(result.message);
            }
        } catch (error) {
            this.showError('ORCID arama hatası: ' + error.message);
        } finally {
            this.hideLoading();
        }
    }

    displaySearchResult(author, source) {
        const resultDiv = document.getElementById('searchResults');
        const resultContent = document.getElementById('resultContent');

        let sourceText = '';
        if (source === 'database') {
            sourceText = '<span class="badge bg-success">Sistemde Kayıtlı</span>';
        } else if (source === 'orcid_api') {
            sourceText = '<span class="badge bg-info">ORCID\'den Alındı</span>';
        } else if (source === 'history') {
            sourceText = '<span class="badge bg-warning">Geçmiş Kayıt</span>';
        }

        resultContent.innerHTML = `
            ${sourceText}
            <div class="mt-3">
                <p><strong>Ad Soyad:</strong> ${author.firstName || author.ad || ''} ${author.lastName || author.soyad || ''}</p>
                <p><strong>Email:</strong> ${author.email || ''}</p>
                <p><strong>Kurum:</strong> ${author.institution || author.kurum || ''}</p>
                <p><strong>ORCID:</strong> ${author.orcid || 'Belirtilmemiş'}</p>
            </div>
        `;

        resultDiv.classList.remove('d-none');

        // "Bu Bilgileri Kullan" butonuna tıklayınca formu doldur
        document.getElementById('useAuthorData').onclick = () => {
            this.fillAuthorForm(author);
        };
    }

    fillAuthorForm(author) {
        // Formu doldur
        document.getElementById('authorFirstName').value = author.firstName || author.ad || '';
        document.getElementById('authorLastName').value = author.lastName || author.soyad || '';
        document.getElementById('authorEmail1').value = author.email || '';
        document.getElementById('authorInstitution').value = author.institution || author.kurum || '';
        document.getElementById('authorOrcidId').value = author.orcid || '';
        document.getElementById('authorCountry').value = author.country || author.ulke || '';

        // Ünvan varsa
        if (author.unvan) {
            document.getElementById('authorTitle').value = author.unvan;
        }

        // Telefon varsa
        if (author.telefon) {
            document.getElementById('authorPhone').value = author.telefon;
        }

        // Başarı mesajı
        this.showSuccess('Yazar bilgileri form alanlarına yerleştirildi');

        // Arama sonuçlarını gizle
        document.getElementById('searchResults').classList.add('d-none');
    }

    showLoading(message = 'Aranıyor...') {
        const loader = document.createElement('div');
        loader.id = 'authorSearchLoader';
        loader.className = 'alert alert-info';
        loader.innerHTML = `
            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
            ${message}
        `;

        document.getElementById('searchResults')?.insertAdjacentElement('beforebegin', loader);
    }

    hideLoading() {
        document.getElementById('authorSearchLoader')?.remove();
    }

    showError(message) {
        alert('Hata: ' + message);
    }

    showSuccess(message) {
        const toast = document.createElement('div');
        toast.className = 'alert alert-success alert-dismissible fade show position-fixed';
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
        toast.innerHTML = `
            <i class="fas fa-check-circle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    window.authorSearch = new AuthorSearch();
});
```

---

## 💾 3. TASLAK KAYIT SİSTEMİ

### 🎯 Özellikler

1. **Otomatik Kayıt**: Her 30 saniyede veya input değişikliğinde otomatik kaydet
2. **Manuel Kayıt**: Kullanıcı "Kaydet" butonuna basarak manuel kaydet
3. **Taslak Yönetimi**: Kullanıcı panelinde taslakları listele
4. **Kaldığı Yerden Devam**: Taslağı açınca kaldığı adıma git

Daha önce `taslak-ayarla.md` dosyasında detaylı planlama yapılmıştı. O sistemi kullanacağız.

---

## 📊 4. GELİŞTİRME PLANI ve ÖNCELİK SIRASI

### 🔥 Faz 1: Dil Desteği Sistemi (Öncelik: Yüksek)

**Süre**: 2-3 gün

#### Adımlar:

1. **Veritabanı**:
   - `dil_degiskenleri` tablosunu oluştur
   - `dil_paketleri` tablosunu oluştur
   - Varsayılan TR dil paketini ekle

2. **Backend**:
   - `LanguageService.php` sınıfını oluştur
   - Controller'lara dil desteği entegre et
   - Helper fonksiyonları ekle (`__()` kısayolu)

3. **Frontend**:
   - `language-helper.js` oluştur
   - `create.php` dosyasındaki tüm sabit metinleri dil değişkenleriyle değiştir
   - JSON dil dosyalarını oluştur (`config/languages/tr/create_article.json`)

4. **Test**:
   - Türkçe çeviri test et
   - İngilizce çeviri test et
   - Dergi özelleştirmesi test et

**Teslim Kriterleri**:
- ✅ Tüm form alanları dil değişkenlerinden geliyor
- ✅ Dergi yöneticisi terim özelleştirmesi yapabiliyor
- ✅ Çoklu dil desteği çalışıyor

---

### 🔥 Faz 2: Yazar Modülü - Email/ORCID Arama (Öncelik: Yüksek)

**Süre**: 2-3 gün

#### Adımlar:

1. **Veritabanı**:
   - `kullanici_yazar_profilleri` tablosunu oluştur
   - `makale_yazarlari` tablosuna `orcid` alanı ekle

2. **Backend**:
   - `AuthorController.php` oluştur
   - `OrcidService.php` oluştur
   - API endpoint'leri ekle (`/api/authors/search-by-email`, `/api/authors/search-by-orcid`)

3. **Frontend**:
   - `author-search.js` oluştur
   - Email arama UI'ı ekle
   - ORCID arama UI'ı ekle
   - Otomatik form doldurma ekle

4. **Test**:
   - Email araması test et
   - ORCID API entegrasyonu test et
   - Otomatik form doldurma test et

**Teslim Kriterleri**:
- ✅ Email ile yazar arama çalışıyor
- ✅ ORCID ile yazar arama ve API çekme çalışıyor
- ✅ Form otomatik dolduruluyor
- ✅ Cache sistemi çalışıyor

---

### 🟡 Faz 3: Referans Sistemi İyileştirmesi (Öncelik: Orta)

**Süre**: 1 gün

#### Adımlar:

1. **Frontend**:
   - Tek tek ekleme modu ✅ (Zaten mevcut)
   - Toplu ekleme modu ekle
   - APA format validasyonu ekle (opsiyonel)

2. **Backend**:
   - Referansları JSON olarak kaydet
   - Array'i parse et ve doğrula

**Teslim Kriterleri**:
- ✅ Tek tek ve toplu ekleme çalışıyor
- ✅ Referanslar doğru kaydediliyor

---

### 🟡 Faz 4: Taslak Sistemi (Öncelik: Orta)

**Süre**: 2 gün

`taslak-ayarla.md` dosyasındaki planı takip ederek:

1. **Veritabanı**:
   - `makale_taslaklari` tablosunu oluştur

2. **Backend**:
   - `TaslakController.php` oluştur
   - Otomatik kayıt API'si

3. **Frontend**:
   - `taslak-sistemi.js` oluştur
   - Otomatik kayıt (30 saniye interval)
   - Manuel kayıt butonu
   - Taslak listesi (yazar paneli)

**Teslim Kriterleri**:
- ✅ Otomatik kayıt çalışıyor
- ✅ Taslak yükleme çalışıyor
- ✅ Kullanıcı kaldığı yerden devam edebiliyor

---

### 🟢 Faz 5: Dosya Yükleme Sistemi (Öncelik: Düşük)

**Süre**: 2 gün

#### Adımlar:

1. **Backend**:
   - `FileController.php` oluştur
   - Dosya validasyonu
   - Storage klasör yapısı oluştur

2. **Frontend**:
   - Dosya yükleme UI'ı
   - Progress bar
   - Dosya listesi

---

### 🟢 Faz 6: Hakem ve Diğer Adımlar (Öncelik: Düşük)

**Süre**: 3-4 gün

`yenimakale.md` dosyasındaki planı takip ederek:

- Hakemler (Step 9)
- Editöre Not (Step 10)
- Kontrol Listesi (Step 11)

---

## 📝 5. ÖRNEK KULLANIM SENARYOLARı

### Senaryo 1: Yazar Formu - Email ile Arama

1. Kullanıcı Step 7'ye gelir
2. Email arama kutusuna "ahmet@universite.edu.tr" yazar
3. "Ara" butonuna tıklar
4. Sistem şu sırayla arar:
   - Önce `kullanicilar` ve `kullanici_yazar_profilleri` tablosunda
   - Sonra `makale_yazarlari` tablosunda (geçmiş kayıtlar)
5. Bulursa sonuçları gösterir
6. "Bu Bilgileri Kullan" butonuna tıklar
7. Form otomatik doldurulur

### Senaryo 2: Yazar Formu - ORCID ile Arama

1. Kullanıcı ORCID arama kutusuna "0000-0001-2345-6789" yazar
2. "Ara" butonuna tıklar
3. Sistem önce cache'e bakar
4. Cache yoksa ORCID Public API'den çeker
5. ORCID verisini parse eder ve formatlar
6. Sonuçları gösterir
7. "Bu Bilgileri Kullan" butonuna tıklar
8. Form otomatik doldurulur

### Senaryo 3: Dil Değiştirme

1. Kullanıcı header'dan dili "English" olarak değiştirir
2. Sistem `$_SESSION['user_lang'] = 'en'` olarak ayarlar
3. Sayfa yenilenir
4. `LanguageService` İngilizce çevirileri yükler
5. Tüm form alanları İngilizce görünür

### Senaryo 4: Dergi Yöneticisi Terim Özelleştirmesi

1. Dergi yöneticisi admin paneline girer
2. "Dil Ayarları" → "Terim Özelleştirmesi" menüsüne gider
3. "Yazar" terimini "Katkıda Bulunan" olarak değiştirir
4. Kaydet butonuna basar
5. Sistem `dil_degiskenleri` tablosuna kaydeder
6. Artık tüm yazar formlarında "Katkıda Bulunan" görünür

---

## 🚀 6. UYGULAMA NOTLARI

### Önemli Hususlar

1. **Performans**:
   - Dil çevirilerini cache'le (Redis veya APCu)
   - ORCID API sonuçlarını 30 gün cache'le
   - Taslak kayıtlarında debounce kullan (2 saniye)

2. **Güvenlik**:
   - CSRF token tüm formlarda olmalı
   - ORCID API rate limiting uygula
   - Dosya yüklemede virus taraması

3. **Kullanıcı Deneyimi**:
   - Loading spinners göster
   - Toast notifications kullan
   - Validation mesajları açık olsun

4. **Bakım**:
   - Varsayılan dil paketlerini JSON'da tut (kolay güncelleme)
   - ORCID cache'ini temizleme scripti yaz
   - Taslak temizleme (30 gün eski taslakları sil)

---

## 📚 7. KAYNAKLAR

### Dökümantasyon

- ORCID Public API: https://info.orcid.org/documentation/api-tutorials/api-tutorial-read-data-on-a-record/
- Bootstrap 5: https://getbootstrap.com/docs/5.3/
- PHP i18n Best Practices: https://www.php.net/manual/en/book.intl.php

### Örnek Projeler

- Laravel Localization: https://github.com/mcamara/laravel-localization
- Symfony Translation: https://symfony.com/doc/current/translation.html

---

## ✅ 8. KONTROL LİSTESİ

### Dil Desteği Sistemi

- [ ] `dil_degiskenleri` tablosu oluşturuldu
- [ ] `dil_paketleri` tablosu oluşturuldu
- [ ] `LanguageService.php` yazıldı
- [ ] `language-helper.js` yazıldı
- [ ] TR dil paketi oluşturuldu
- [ ] EN dil paketi oluşturuldu
- [ ] `create.php` dil değişkenlerine dönüştürüldü
- [ ] Dergi yöneticisi özelleştirme paneli yapıldı

### Yazar Modülü

- [ ] `kullanici_yazar_profilleri` tablosu oluşturuldu
- [ ] `makale_yazarlari` tablosuna `orcid` alanı eklendi
- [ ] `AuthorController.php` yazıldı
- [ ] `OrcidService.php` yazıldı
- [ ] Email arama API'si test edildi
- [ ] ORCID arama API'si test edildi
- [ ] `author-search.js` yazıldı
- [ ] Otomatik form doldurma test edildi

### Referans Sistemi

- [ ] Tek tek ekleme çalışıyor
- [ ] Toplu ekleme modu eklendi
- [ ] Backend array parse çalışıyor

### Taslak Sistemi

- [ ] `makale_taslaklari` tablosu oluşturuldu
- [ ] `TaslakController.php` yazıldı
- [ ] Otomatik kayıt (30 saniye) çalışıyor
- [ ] Manuel kayıt butonu eklendi
- [ ] Taslak listesi (yazar paneli) yapıldı
- [ ] Kaldığı yerden devam özelliği test edildi

---

## 🎉 SONUÇ

Bu belge, AMDS sisteminin **dil desteği**, **yazar modülü** ve **taslak sistemi** geliştirmesi için kapsamlı bir yol haritası sunmaktadır.

**Geliştirme süreci:**
1. Önce **Dil Desteği** (en temelden başla)
2. Sonra **Yazar Modülü** (kritik özellik)
3. Ardından **Taslak Sistemi** (kullanıcı deneyimi)
4. En son **Diğer Adımlar** (hakem, dosya, vb.)

**Tahmini Toplam Süre**: 10-12 iş günü

Her fazı tamamladıktan sonra test et ve bir sonraki faza geç!
