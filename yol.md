# AMDS - Akademik Makale Değerlendirme Sistemi
## Analiz Raporu ve Yeniden Yazım Yol Haritası

---

## ⚠️ ÖNEMLİ: KOD YAZIM STANDARTLARI

### Türkçe İsimlendirme Kuralı
**TÜM KOD ELEMANLARI TÜRKÇE OLACAK AMA İNGİLİZCE KARAKTER KULLANILACAK**

Bu proje Türk geliştiriciler tarafından geliştirildiği için tüm kod elemanları (değişkenler, fonksiyonlar, tablolar, sütunlar, class'lar vb.) **Türkçe** isimlendirilecek ancak **İngilizce karakterler** kullanılacaktır.

#### Karakter Dönüşüm Tablosu
| Türkçe Karakter | Kullanılacak Karakter |
|-----------------|----------------------|
| ı | i |
| ş | s |
| ğ | g |
| ü | u |
| ö | o |
| ç | c |
| İ | I |
| Ş | S |
| Ğ | G |
| Ü | U |
| Ö | O |
| Ç | C |

#### Örnekler:

**✅ DOĞRU:**
```php
// Değişkenler
$kullanici_adi = "Ahmet";
$makale_basligi = "Yapay Zeka";
$degerlendirme_notu = 85;

// Fonksiyonlar
function makale_gonder($veri) { }
function hakem_ata($makale_id, $hakem_id) { }
function degerlendirme_kaydet() { }

// Class'lar
class MakaleYoneticisi { }
class HakemDegerlendirme { }
class KullaniciYetkilendirme { }

// Veritabanı tabloları
CREATE TABLE kullanicilar (...);
CREATE TABLE makale_gonderimleri (...);
CREATE TABLE hakem_degerlendirmeleri (...);

// Sütunlar
CREATE TABLE makaleler (
    id INT PRIMARY KEY,
    makale_kodu VARCHAR(50),
    baslik_tr VARCHAR(500),
    ozet_tr TEXT,
    anahtar_kelimeler TEXT,
    gonderi_tarihi TIMESTAMP
);
```

**❌ YANLIŞ:**
```php
// Türkçe karakterler kullanılmış
$kullanıcı_adı = "Ahmet";  // YANLIŞ
function makale_gönder() { }  // YANLIŞ
class MakaleYöneticisi { }  // YANLIŞ

// İngilizce isimler
$userName = "Ahmet";  // YANLIŞ
function submitArticle() { }  // YANLIŞ
CREATE TABLE users (...);  // YANLIŞ
```

#### PHP Standart Fonksiyonları
Sadece PHP'nin kendi standart fonksiyonları ve anahtar kelimeleri İngilizce kalacak:
```php
// Bunlar standart PHP - değiştirilmeyecek
if, else, while, foreach, function, class, public, private
echo, print, return, isset, empty, array, etc.
```

#### Naming Convention
- **Değişkenler**: `snake_case` → `$kullanici_adi`, `$makale_sayisi`
- **Fonksiyonlar**: `snake_case` → `makale_gonder()`, `hakem_ata()`
- **Class'lar**: `PascalCase` → `MakaleYoneticisi`, `HakemDegerlendirme`
- **Sabitler**: `UPPER_SNAKE_CASE` → `MAKSIMUM_DOSYA_BOYUTU`, `VARSAYILAN_DIL`
- **Veritabanı**: `snake_case` → `kullanicilar`, `makale_gonderimleri`

#### Neden Bu Yaklaşım?
1. ✅ Türk geliştiriciler için daha okunabilir ve anlaşılır
2. ✅ Kod ve iş mantığı arasında doğrudan bağlantı
3. ✅ Teknik hatalar önlenir (encoding sorunları yok)
4. ✅ Tüm sistemlerde (Windows, Linux, Mac) sorunsuz çalışır
5. ✅ IDE'ler ve editörler için uyumlu

**NOT:** Bu dokümandaki tüm kod örnekleri açıklama amaçlı İngilizce yazılmıştır. Gerçek geliştirmede yukarıdaki kurallara göre Türkçeleştirilecektir.

---

## 📋 İÇİNDEKİLER
1. [Sistem Analizi](#sistem-analizi)
2. [Mevcut Özellikler](#mevcut-özellikler)
3. [Yeni Kritik Gereksinimler](#yeni-kritik-gereksinimler)
4. [WordPress Benzeri Mimari Tasarım](#wordpress-benzeri-mimari-tasarım)
5. [Çok Dilli Sistem (Multi-Language)](#çok-dilli-sistem-multi-language)
6. [Dinamik Form Yapılandırma](#dinamik-form-yapılandırma)
7. [WordPress Benzeri Kurulum Sistemi](#wordpress-benzeri-kurulum-sistemi)
8. [Güncelleme Sistemi](#güncelleme-sistemi)
9. [Süper Admin Paneli ve Kaynak Yönetimi](#süper-admin-paneli-ve-kaynak-yönetimi)
10. [Teknik Yol Haritası](#teknik-yol-haritası)
11. [Veritabanı Mimarisi](#veritabanı-mimarisi)
12. [Geliştirme Aşamaları](#geliştirme-aşamaları)

---

## 🔍 SİSTEM ANALİZİ

### Mevcut Durum
Sistem şu anda tamamen **statik HTML** dosyalarından oluşmaktadır. Backend entegrasyonu bulunmamaktadır.

### Tespit Edilen Dosya Yapısı

```
amdsphp/
├── giris/                    # Giriş sayfaları
│   ├── giris.html
│   ├── estyles.css
│   └── main.js
├── kayit/                    # Kayıt sayfaları
│   ├── kayit.html
│   ├── kayit.css
│   └── kayit.js
├── yazar/                    # Yazar paneli
│   ├── yazar-panel.html
│   ├── yazar-makaleler.html
│   ├── yeni-makale.html
│   ├── makale-detay.html
│   ├── ortak-yazarliklar.html
│   ├── yazar-profil.html
│   ├── yazar-ayarlar.html
│   └── assets/
├── hakem/                    # Hakem paneli
│   ├── hakem-panel.html
│   ├── hakem-davetler.html
│   ├── hakem-bekleyenler.html
│   ├── hakem-devam-eden.html
│   ├── hakem-makale-detay.html
│   ├── tamamlanan-degerlendirmeler.html
│   ├── hakem-uzmanlik-alanlarim.html
│   ├── hakem-ayarlar.html
│   ├── hakem-rehberi.html
│   └── assets/
├── alan-editoru/             # Alan Editörü paneli
│   ├── alan-editor-panel.html
│   ├── alan-editor-bekleyen-makaleler.html
│   ├── alan-editor-inceleme-sureci.html
│   ├── alan-editor-hakem-atamalari.html
│   ├── alan-editor-tamamlanan.html
│   └── alan-editor-makale-detay.html
├── sekreter/                 # Sekreter paneli
│   ├── sekreter-panel.html
│   ├── sekreter-yeni-gonderimler.html
│   ├── sekreter-makaleler.html
│   ├── sekreter-yazisma.html
│   ├── sekreter-hakem-havuzu.html
│   ├── sekreter-sablonlar.html
│   ├── sekreter-yayinlar.html
│   └── sekreter-makale-detay.html
├── yönetici/                 # Dergi Yöneticisi paneli
│   ├── dergi-yonetici-panel.html
│   ├── dergi-yonetici-kullanicilar.html
│   ├── dergi-yonetici-roller.html
│   ├── dergi-yonetici-is-akislari.html
│   ├── dergi-yonetici-raporlar.html
│   ├── dergi-yonetici-istatistikler.html
│   ├── dergi-yonetici-tum-makaleler.html
│   ├── dergi-yoneticisi-makale-detay.html
│   ├── dergi-yapilandirmasi (10).html
│   ├── dergi-sayi-yonetimi.html
│   └── makale-durum-raporu.html
└── dergi/                    # Public dergi sayfaları
    ├── onsayfa.html
    ├── styles.css
    └── script.js
```

---

## 🎯 MEVCUT ÖZELLİKLER

### 1. Kullanıcı Rolleri
- **Yazar**: Makale gönderimi, takip, revizyon
- **Hakem**: Makale değerlendirme, rapor yazma
- **Alan Editörü**: Hakem atama, değerlendirme yönetimi
- **Sekreter**: İdari işlemler, yazışma yönetimi
- **Dergi Yöneticisi**: Sistem yönetimi, istatistikler, yapılandırma

### 2. Ana Fonksiyonlar

#### Yazar Paneli
- Makale gönderimi (wizard/sihirbaz formatı)
- Makale takibi ve durum görüntüleme
- Ortak yazar yönetimi
- Taslak yönetimi
- Değerlendirme sonuçlarını görüntüleme
- Revizyon yükleme

#### Hakem Paneli
- Değerlendirme davetlerini görüntüleme/kabul etme
- Makale değerlendirme formları
- Tamamlanan değerlendirmeler
- Uzmanlık alanları yönetimi
- Sertifika sistemi

#### Alan Editörü Paneli
- Bekleyen makaleler listesi
- Hakem önerisi/atama
- İnceleme süreci takibi
- Değerlendirme kontrolü
- Karar verme (kabul/ret/revizyon)

#### Sekreter Paneli
- Yeni gönderimleri kontrol
- Ön değerlendirme
- Hakem havuzu yönetimi
- E-posta şablonları
- Yayın programı takibi
- Yazışma yönetimi

#### Dergi Yöneticisi Paneli
- Kullanıcı yönetimi
- Rol yönetimi
- İş akışı yapılandırması
- Dergi yapılandırması
- İstatistik ve raporlar
- Sayı yönetimi
- Sistem ayarları

### 3. Güvenlik Özellikleri (Tasarımda Mevcut)
- CSRF koruması
- reCAPTCHA entegrasyonu
- KVKK aydınlatma metni onayı
- ORCID entegrasyonu
- Çoklu dil desteği (TR/EN)

### 4. UI/UX Özellikleri
- Responsive tasarım (Bootstrap 5.3)
- Modern ve temiz arayüz
- Sidebar navigasyon
- Rol bildirimleri
- Progress bar'lar
- İstatistik kartları
- Modal'lar ve dropdown'lar
- Animasyonlar (Animate.css)
- Chart.js ile grafikler

---

## 🎯 YENİ KRİTİK GEREKSİNİMLER

### Özet
Sistem, WordPress benzeri bir SaaS platformu olarak yeniden tasarlanacaktır. Her dergi bağımsız bir "tenant" olarak çalışacak ve aşağıdaki kritik özelliklere sahip olacaktır:

#### 1. **Tam Çok Dilli Destek**
- Dergi yöneticisi tüm sistemi istediği dile çevirebilecek
- Sadece içerik değil, arayüz, form alanları, bildirimler de çevrilebilir
- Sınırsız dil desteği (TR, EN, JP, DE, FR, AR, vb.)

#### 2. **Dinamik Form Yapılandırma**
- Form alanı kurallarını özelleştirme (başlık uzunluğu, anahtar kelime sayısı, vb.)
- Yeni form alanları ekleme/çıkarma
- Dosya yükleme kurallarını yapılandırma (tür, boyut, isimlendirme)
- Alan türlerini değiştirme (text, textarea, select, vb.)

#### 3. **WordPress Benzeri Kurulum**
- `install/` klasörü ile kolay kurulum
- Subdomain'e otomatik kurulum
- Kurulum sonrası otomatik yapılandırma
- Genel ve özel güncellemeler

#### 4. **Süper Admin Paneli**
- Tüm dergileri merkezi yönetim
- Kaynak kullanım takibi (CPU, RAM, Storage, Bandwidth)
- Sorun ve destek talepleri yönetimi
- Paket/seviye yükseltme uyarıları
- İletişim yönetimi (Email, WhatsApp, Telefon)

#### 5. **Özel Güncelleme Talep Sistemi**
- Dergi yöneticisi form/bildirim ile güncelleme talebi
- Süper admin onayı ile özel güncellemeler
- Güncelleme takip sistemi

---

## 🏗️ WORDPRESS BENZERİ MİMARİ TASARIM

### Multi-Tenant (Çok Kiracılı) Yapı

#### 1. Merkezi Core Sistem
```
core/
├── config/
│   ├── database.php
│   ├── app.php
│   └── constants.php
├── framework/
│   ├── Router.php
│   ├── Controller.php
│   ├── Model.php
│   ├── View.php
│   └── Middleware.php
├── libraries/
│   ├── Authentication.php
│   ├── Authorization.php
│   ├── Mailer.php
│   └── FileUpload.php
└── updates/
    ├── UpdateManager.php
    ├── VersionControl.php
    └── migrations/
```

#### 2. Tenant (Dergi) Yapısı
```
tenants/
├── x-dergisi/
│   ├── config/
│   │   ├── tenant.php
│   │   └── custom.php
│   ├── uploads/
│   ├── themes/
│   │   └── active-theme/
│   ├── plugins/
│   └── backups/
├── y-dergisi/
│   └── ...
└── z-dergisi/
    └── ...
```

### Veritabanı Stratejisi

#### Seçenek 1: Shared Database + Tenant ID (Önerilen)
Her tablo `tenant_id` alanı içerir, tüm dergiler aynı veritabanını kullanır.

**Avantajlar:**
- Daha kolay yönetim
- Merkezi güncelleme
- Daha az kaynak tüketimi

**Dezavantajlar:**
- Veri izolasyonu zayıf
- Performans sorunları olabilir

#### Seçenek 2: Database Per Tenant
Her dergi için ayrı veritabanı.

**Avantajlar:**
- Tam veri izolasyonu
- Daha güvenli
- Bağımsız yedekleme

**Dezavantajlar:**
- Güncelleme zorluğu
- Daha fazla kaynak

#### Önerilen Hibrit Çözüm
```sql
-- Merkezi database
amds_core
  ├── tenants (dergi listesi)
  ├── core_users (süper admin)
  ├── updates
  └── update_logs

-- Tenant database (her dergi için)
amds_tenant_x
  ├── users
  ├── articles
  ├── reviews
  ├── settings
  └── ...
```

---

## 🌍 ÇOK DİLLİ SİSTEM (MULTI-LANGUAGE)

### Mimari Yaklaşım

#### 1. Dil Dosyaları Yapısı
```
languages/
├── core/                     # Core sistem çevirileri
│   ├── en.json
│   ├── tr.json
│   ├── ja.json              # Japanese
│   ├── de.json              # German
│   ├── fr.json              # French
│   ├── ar.json              # Arabic
│   └── ...
└── tenants/                  # Tenant özel çeviriler
    ├── x-dergisi/
    │   ├── custom_en.json
    │   └── custom_tr.json
    └── y-dergisi/
        └── custom_ja.json
```

#### 2. Çeviri Dosyası Formatı
```json
{
  "common": {
    "save": "Save",
    "cancel": "Cancel",
    "delete": "Delete",
    "edit": "Edit"
  },
  "auth": {
    "login": "Login",
    "logout": "Logout",
    "email": "Email Address",
    "password": "Password"
  },
  "article": {
    "submit": "Submit Article",
    "title": "Article Title",
    "abstract": "Abstract",
    "keywords": "Keywords",
    "manuscript": "Manuscript File",
    "title_length_error": "Title must be between {min} and {max} characters"
  },
  "validation": {
    "required": "This field is required",
    "email": "Please enter a valid email address",
    "min_length": "Minimum {count} characters required",
    "max_length": "Maximum {count} characters allowed"
  }
}
```

#### 3. Language Manager Sınıfı
```php
<?php
class LanguageManager {
    private static $instance = null;
    private $currentLang = 'en';
    private $fallbackLang = 'en';
    private $translations = [];
    private $tenantId = null;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function setLanguage($lang) {
        $this->currentLang = $lang;
        $this->loadTranslations();
    }

    public function setTenant($tenantId) {
        $this->tenantId = $tenantId;
        $this->loadTranslations();
    }

    private function loadTranslations() {
        // Core çevirileri yükle
        $corePath = __DIR__ . "/languages/core/{$this->currentLang}.json";
        if (file_exists($corePath)) {
            $this->translations = json_decode(file_get_contents($corePath), true);
        }

        // Tenant özel çevirileri yükle ve birleştir
        if ($this->tenantId) {
            $tenant = Tenant::find($this->tenantId);
            $customPath = __DIR__ . "/languages/tenants/{$tenant->slug}/custom_{$this->currentLang}.json";

            if (file_exists($customPath)) {
                $customTranslations = json_decode(file_get_contents($customPath), true);
                $this->translations = array_merge_recursive($this->translations, $customTranslations);
            }
        }

        // Fallback yükle
        if (empty($this->translations) && $this->currentLang !== $this->fallbackLang) {
            $fallbackPath = __DIR__ . "/languages/core/{$this->fallbackLang}.json";
            if (file_exists($fallbackPath)) {
                $this->translations = json_decode(file_get_contents($fallbackPath), true);
            }
        }
    }

    public function get($key, $params = []) {
        $keys = explode('.', $key);
        $value = $this->translations;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                // Fallback dilden dene
                return $this->getFallback($key, $params);
            }
            $value = $value[$k];
        }

        // Parametreleri değiştir
        if (!empty($params)) {
            foreach ($params as $param => $replacement) {
                $value = str_replace('{' . $param . '}', $replacement, $value);
            }
        }

        return $value;
    }

    private function getFallback($key, $params = []) {
        if ($this->currentLang === $this->fallbackLang) {
            return $key; // Key'i göster
        }

        // Fallback dilden al
        $originalLang = $this->currentLang;
        $this->currentLang = $this->fallbackLang;
        $this->loadTranslations();
        $value = $this->get($key, $params);
        $this->currentLang = $originalLang;
        $this->loadTranslations();

        return $value;
    }

    // Dergi yöneticisi için çeviri ekleme/düzenleme
    public function saveCustomTranslation($tenantId, $lang, $key, $value) {
        $tenant = Tenant::find($tenantId);
        $customPath = __DIR__ . "/languages/tenants/{$tenant->slug}/custom_{$lang}.json";

        // Mevcut çevirileri yükle
        $translations = [];
        if (file_exists($customPath)) {
            $translations = json_decode(file_get_contents($customPath), true);
        }

        // Yeni çeviriyi ekle
        $keys = explode('.', $key);
        $current = &$translations;

        foreach ($keys as $k) {
            if (!isset($current[$k])) {
                $current[$k] = [];
            }
            $current = &$current[$k];
        }

        $current = $value;

        // Dosyaya kaydet
        if (!is_dir(dirname($customPath))) {
            mkdir(dirname($customPath), 0755, true);
        }

        file_put_contents($customPath, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    // Tüm mevcut dilleri listele
    public function getAvailableLanguages() {
        $languages = [];
        $coreDir = __DIR__ . '/languages/core/';

        foreach (glob($coreDir . '*.json') as $file) {
            $lang = basename($file, '.json');
            $languages[$lang] = $this->getLanguageName($lang);
        }

        return $languages;
    }

    private function getLanguageName($code) {
        $names = [
            'en' => 'English',
            'tr' => 'Türkçe',
            'ja' => '日本語',
            'de' => 'Deutsch',
            'fr' => 'Français',
            'ar' => 'العربية',
            'es' => 'Español',
            'zh' => '中文',
            'ru' => 'Русский'
        ];

        return $names[$code] ?? $code;
    }
}

// Helper function
function __($key, $params = []) {
    return LanguageManager::getInstance()->get($key, $params);
}

// Kullanım
echo __('article.submit'); // "Submit Article"
echo __('article.title_length_error', ['min' => 10, 'max' => 200]);
// "Title must be between 10 and 200 characters"
```

#### 4. Veritabanı Yapısı
```sql
-- Tenant settings tablosuna dil tercihi
ALTER TABLE settings ADD COLUMN IF NOT EXISTS language VARCHAR(10) DEFAULT 'en';

-- Çoklu dil desteği için çeviri tablosu
CREATE TABLE tenant_translations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tenant_id INT,
    language_code VARCHAR(10),
    translation_key VARCHAR(255),
    translation_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id),
    UNIQUE KEY unique_translation (tenant_id, language_code, translation_key)
);

-- Desteklenen diller
CREATE TABLE languages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(10) UNIQUE,
    name VARCHAR(100),
    native_name VARCHAR(100),
    direction ENUM('ltr', 'rtl') DEFAULT 'ltr',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Varsayılan dilleri ekle
INSERT INTO languages (code, name, native_name, direction) VALUES
('en', 'English', 'English', 'ltr'),
('tr', 'Turkish', 'Türkçe', 'ltr'),
('ja', 'Japanese', '日本語', 'ltr'),
('de', 'German', 'Deutsch', 'ltr'),
('fr', 'French', 'Français', 'ltr'),
('ar', 'Arabic', 'العربية', 'rtl'),
('es', 'Spanish', 'Español', 'ltr'),
('zh', 'Chinese', '中文', 'ltr'),
('ru', 'Russian', 'Русский', 'ltr');
```

#### 5. Dergi Yöneticisi için Çeviri Arayüzü
```php
class TranslationController extends Controller {
    // Çeviri düzenleme sayfası
    public function editTranslations() {
        if (!Auth::hasPermission('manage_translations')) {
            return $this->error('Yetkiniz yok', 403);
        }

        $tenantId = Auth::tenant()->id;
        $currentLang = Settings::get('language', 'en');
        $languages = LanguageManager::getInstance()->getAvailableLanguages();

        // Tüm çeviri anahtarlarını al
        $coreTranslations = $this->getCoreTranslations($currentLang);
        $customTranslations = $this->getCustomTranslations($tenantId, $currentLang);

        return $this->view('admin/translations', [
            'languages' => $languages,
            'currentLang' => $currentLang,
            'coreTranslations' => $coreTranslations,
            'customTranslations' => $customTranslations
        ]);
    }

    // Çeviri kaydetme
    public function saveTranslation() {
        $data = $this->validate($_POST, [
            'language' => 'required',
            'key' => 'required',
            'value' => 'required'
        ]);

        $tenantId = Auth::tenant()->id;

        LanguageManager::getInstance()->saveCustomTranslation(
            $tenantId,
            $data['language'],
            $data['key'],
            $data['value']
        );

        // Veritabanına da kaydet
        DB::query(
            "INSERT INTO tenant_translations (tenant_id, language_code, translation_key, translation_value)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE translation_value = ?",
            [$tenantId, $data['language'], $data['key'], $data['value'], $data['value']]
        );

        return $this->success(['message' => 'Çeviri kaydedildi']);
    }

    // Dil değiştirme
    public function changeLanguage() {
        $data = $this->validate($_POST, [
            'language' => 'required'
        ]);

        $tenantId = Auth::tenant()->id;

        // Tenant ayarlarını güncelle
        Settings::set('language', $data['language']);

        // Session'ı güncelle
        $_SESSION['language'] = $data['language'];
        LanguageManager::getInstance()->setLanguage($data['language']);

        return $this->success(['message' => 'Dil değiştirildi']);
    }

    // Toplu çeviri içe aktarma
    public function importTranslations() {
        if (!isset($_FILES['translation_file'])) {
            return $this->error('Dosya yüklenmedi', 400);
        }

        $file = $_FILES['translation_file'];
        $content = file_get_contents($file['tmp_name']);
        $translations = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->error('Geçersiz JSON dosyası', 400);
        }

        $tenantId = Auth::tenant()->id;
        $language = $_POST['language'] ?? 'en';

        foreach ($translations as $section => $items) {
            foreach ($items as $key => $value) {
                $fullKey = $section . '.' . $key;
                LanguageManager::getInstance()->saveCustomTranslation(
                    $tenantId,
                    $language,
                    $fullKey,
                    $value
                );
            }
        }

        return $this->success(['message' => 'Çeviriler içe aktarıldı']);
    }
}
```

#### 6. Frontend Entegrasyonu
```html
<!-- Dil seçici -->
<div class="language-selector">
    <select id="languageSelect" class="form-select">
        <?php foreach ($languages as $code => $name): ?>
            <option value="<?= $code ?>" <?= $currentLang === $code ? 'selected' : '' ?>>
                <?= $name ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<script>
document.getElementById('languageSelect').addEventListener('change', function() {
    const selectedLang = this.value;

    fetch('/api/settings/change-language', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ language: selectedLang })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
});
</script>
```

#### 7. RTL (Right-to-Left) Desteği
```php
// Layout'ta RTL kontrolü
<?php
$lang = LanguageManager::getInstance();
$direction = $lang->getDirection(); // 'ltr' veya 'rtl'
?>

<!DOCTYPE html>
<html lang="<?= $currentLang ?>" dir="<?= $direction ?>">
<head>
    <?php if ($direction === 'rtl'): ?>
        <link rel="stylesheet" href="/assets/css/rtl.css">
    <?php endif; ?>
</head>
```

---

## 📝 DİNAMİK FORM YAPILANDIRMA

### Mimari Yaklaşım

#### 1. Form Konfigürasyon Yapısı
```sql
-- Form şemaları tablosu
CREATE TABLE form_schemas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tenant_id INT,
    form_type VARCHAR(50), -- 'article_submission', 'review_form', vb.
    schema_name VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);

-- Form alanları tablosu
CREATE TABLE form_fields (
    id INT PRIMARY KEY AUTO_INCREMENT,
    schema_id INT,
    field_name VARCHAR(100),
    field_label VARCHAR(255),
    field_type ENUM('text', 'textarea', 'number', 'email', 'select', 'checkbox', 'radio', 'file', 'date', 'url'),
    field_order INT,
    is_required BOOLEAN DEFAULT FALSE,
    is_visible BOOLEAN DEFAULT TRUE,
    placeholder TEXT,
    help_text TEXT,
    default_value TEXT,
    validation_rules JSON, -- {"min": 10, "max": 200, "pattern": "regex"}
    options JSON, -- For select, radio, checkbox: [{"value": "opt1", "label": "Option 1"}]
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (schema_id) REFERENCES form_schemas(id) ON DELETE CASCADE
);

-- Dosya yükleme kuralları
CREATE TABLE file_upload_rules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    field_id INT,
    allowed_types JSON, -- ["pdf", "doc", "docx"]
    max_size_mb DECIMAL(10,2), -- MB cinsinden
    min_size_kb DECIMAL(10,2), -- KB cinsinden
    naming_pattern VARCHAR(255), -- "{article_code}_{type}_{timestamp}"
    max_files INT DEFAULT 1,
    FOREIGN KEY (field_id) REFERENCES form_fields(id) ON DELETE CASCADE
);

-- Form validasyon kuralları
CREATE TABLE validation_rules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    field_id INT,
    rule_type VARCHAR(50), -- 'min_length', 'max_length', 'regex', 'custom'
    rule_value TEXT,
    error_message TEXT,
    FOREIGN KEY (field_id) REFERENCES form_fields(id) ON DELETE CASCADE
);
```

#### 2. Form Schema Manager
```php
<?php
class FormSchemaManager {
    private $tenantId;
    private $db;

    public function __construct($tenantId) {
        $this->tenantId = $tenantId;
        $this->db = Database::getTenantConnection($tenantId);
    }

    // Form şeması oluştur
    public function createSchema($formType, $schemaName) {
        $schemaId = DB::insert('form_schemas', [
            'tenant_id' => $this->tenantId,
            'form_type' => $formType,
            'schema_name' => $schemaName,
            'is_active' => true
        ]);

        return $schemaId;
    }

    // Alan ekle
    public function addField($schemaId, $fieldData) {
        $fieldId = DB::insert('form_fields', [
            'schema_id' => $schemaId,
            'field_name' => $fieldData['name'],
            'field_label' => $fieldData['label'],
            'field_type' => $fieldData['type'],
            'field_order' => $fieldData['order'] ?? 0,
            'is_required' => $fieldData['required'] ?? false,
            'is_visible' => $fieldData['visible'] ?? true,
            'placeholder' => $fieldData['placeholder'] ?? '',
            'help_text' => $fieldData['help_text'] ?? '',
            'default_value' => $fieldData['default_value'] ?? '',
            'validation_rules' => json_encode($fieldData['validation'] ?? []),
            'options' => json_encode($fieldData['options'] ?? [])
        ]);

        // Dosya yükleme kuralları varsa ekle
        if ($fieldData['type'] === 'file' && isset($fieldData['file_rules'])) {
            $this->addFileRules($fieldId, $fieldData['file_rules']);
        }

        return $fieldId;
    }

    // Dosya kuralları ekle
    private function addFileRules($fieldId, $rules) {
        DB::insert('file_upload_rules', [
            'field_id' => $fieldId,
            'allowed_types' => json_encode($rules['allowed_types'] ?? ['pdf']),
            'max_size_mb' => $rules['max_size_mb'] ?? 10,
            'min_size_kb' => $rules['min_size_kb'] ?? 0,
            'naming_pattern' => $rules['naming_pattern'] ?? '{article_code}_{timestamp}',
            'max_files' => $rules['max_files'] ?? 1
        ]);
    }

    // Şemayı getir
    public function getSchema($formType) {
        $schema = DB::query(
            "SELECT * FROM form_schemas
             WHERE tenant_id = ? AND form_type = ? AND is_active = 1
             LIMIT 1",
            [$this->tenantId, $formType]
        )->fetch();

        if (!$schema) {
            // Varsayılan şemayı oluştur
            return $this->createDefaultSchema($formType);
        }

        // Alanları getir
        $fields = DB::query(
            "SELECT f.*, r.allowed_types, r.max_size_mb, r.naming_pattern
             FROM form_fields f
             LEFT JOIN file_upload_rules r ON f.id = r.field_id
             WHERE f.schema_id = ?
             ORDER BY f.field_order ASC",
            [$schema['id']]
        )->fetchAll();

        $schema['fields'] = $fields;

        return $schema;
    }

    // Varsayılan makale gönderim şeması
    private function createDefaultSchema($formType) {
        if ($formType === 'article_submission') {
            $schemaId = $this->createSchema($formType, 'Default Article Submission');

            // Başlık alanı
            $this->addField($schemaId, [
                'name' => 'title',
                'label' => 'Article Title',
                'type' => 'text',
                'order' => 1,
                'required' => true,
                'validation' => [
                    'min_length' => 10,
                    'max_length' => 200
                ]
            ]);

            // Özet alanı
            $this->addField($schemaId, [
                'name' => 'abstract',
                'label' => 'Abstract',
                'type' => 'textarea',
                'order' => 2,
                'required' => true,
                'validation' => [
                    'min_length' => 100,
                    'max_length' => 3000
                ]
            ]);

            // Anahtar kelimeler
            $this->addField($schemaId, [
                'name' => 'keywords',
                'label' => 'Keywords',
                'type' => 'text',
                'order' => 3,
                'required' => true,
                'help_text' => 'Separate keywords with commas',
                'validation' => [
                    'min_keywords' => 3,
                    'max_keywords' => 6
                ]
            ]);

            // Makale dosyası
            $this->addField($schemaId, [
                'name' => 'manuscript',
                'label' => 'Manuscript File',
                'type' => 'file',
                'order' => 4,
                'required' => true,
                'file_rules' => [
                    'allowed_types' => ['pdf', 'doc', 'docx'],
                    'max_size_mb' => 10,
                    'naming_pattern' => '{article_code}_manuscript_{timestamp}'
                ]
            ]);

            return $this->getSchema($formType);
        }

        return null;
    }

    // Alan güncelle
    public function updateField($fieldId, $updates) {
        $allowedUpdates = ['field_label', 'is_required', 'validation_rules', 'options', 'help_text', 'placeholder'];
        $updateData = [];

        foreach ($allowedUpdates as $key) {
            if (isset($updates[$key])) {
                if (in_array($key, ['validation_rules', 'options'])) {
                    $updateData[$key] = json_encode($updates[$key]);
                } else {
                    $updateData[$key] = $updates[$key];
                }
            }
        }

        if (!empty($updateData)) {
            DB::update('form_fields', $fieldId, $updateData);
        }

        return true;
    }

    // Alan sil
    public function deleteField($fieldId) {
        DB::delete('form_fields', $fieldId);
        return true;
    }

    // Form render et
    public function renderForm($formType, $data = []) {
        $schema = $this->getSchema($formType);
        $html = '<form id="' . $formType . '" class="dynamic-form">';

        foreach ($schema['fields'] as $field) {
            if (!$field['is_visible']) continue;

            $html .= $this->renderField($field, $data[$field['field_name']] ?? '');
        }

        $html .= '</form>';
        return $html;
    }

    // Tek alan render et
    private function renderField($field, $value = '') {
        $required = $field['is_required'] ? 'required' : '';
        $validation = json_decode($field['validation_rules'], true) ?? [];
        $options = json_decode($field['options'], true) ?? [];

        $html = '<div class="form-group mb-3">';
        $html .= '<label for="' . $field['field_name'] . '" class="form-label">';
        $html .= __($field['field_label']);
        if ($field['is_required']) $html .= ' <span class="text-danger">*</span>';
        $html .= '</label>';

        switch ($field['field_type']) {
            case 'text':
            case 'email':
            case 'url':
            case 'date':
                $html .= '<input type="' . $field['field_type'] . '" ';
                $html .= 'name="' . $field['field_name'] . '" ';
                $html .= 'id="' . $field['field_name'] . '" ';
                $html .= 'class="form-control" ';
                $html .= 'placeholder="' . __($field['placeholder']) . '" ';
                $html .= 'value="' . htmlspecialchars($value) . '" ';
                if (isset($validation['min_length'])) $html .= 'minlength="' . $validation['min_length'] . '" ';
                if (isset($validation['max_length'])) $html .= 'maxlength="' . $validation['max_length'] . '" ';
                $html .= $required . '>';
                break;

            case 'textarea':
                $html .= '<textarea ';
                $html .= 'name="' . $field['field_name'] . '" ';
                $html .= 'id="' . $field['field_name'] . '" ';
                $html .= 'class="form-control" ';
                $html .= 'placeholder="' . __($field['placeholder']) . '" ';
                $html .= 'rows="5" ';
                if (isset($validation['min_length'])) $html .= 'minlength="' . $validation['min_length'] . '" ';
                if (isset($validation['max_length'])) $html .= 'maxlength="' . $validation['max_length'] . '" ';
                $html .= $required . '>';
                $html .= htmlspecialchars($value);
                $html .= '</textarea>';
                break;

            case 'select':
                $html .= '<select name="' . $field['field_name'] . '" ';
                $html .= 'id="' . $field['field_name'] . '" ';
                $html .= 'class="form-select" ' . $required . '>';
                $html .= '<option value="">Select...</option>';
                foreach ($options as $option) {
                    $selected = ($value === $option['value']) ? 'selected' : '';
                    $html .= '<option value="' . $option['value'] . '" ' . $selected . '>';
                    $html .= __($option['label']);
                    $html .= '</option>';
                }
                $html .= '</select>';
                break;

            case 'file':
                $fileRules = DB::query(
                    "SELECT * FROM file_upload_rules WHERE field_id = ?",
                    [$field['id']]
                )->fetch();

                $allowedTypes = json_decode($fileRules['allowed_types'] ?? '[]', true);
                $accept = !empty($allowedTypes) ? '.' . implode(',.', $allowedTypes) : '';

                $html .= '<input type="file" ';
                $html .= 'name="' . $field['field_name'] . '" ';
                $html .= 'id="' . $field['field_name'] . '" ';
                $html .= 'class="form-control" ';
                $html .= 'accept="' . $accept . '" ';
                $html .= 'data-max-size="' . ($fileRules['max_size_mb'] * 1024 * 1024) . '" ';
                $html .= $required . '>';

                if ($fileRules) {
                    $html .= '<small class="form-text text-muted">';
                    $html .= 'Allowed types: ' . implode(', ', $allowedTypes) . ' | ';
                    $html .= 'Max size: ' . $fileRules['max_size_mb'] . 'MB';
                    $html .= '</small>';
                }
                break;
        }

        if ($field['help_text']) {
            $html .= '<small class="form-text text-muted">' . __($field['help_text']) . '</small>';
        }

        $html .= '</div>';

        return $html;
    }

    // Form validasyonu
    public function validateForm($formType, $data, $files = []) {
        $schema = $this->getSchema($formType);
        $errors = [];

        foreach ($schema['fields'] as $field) {
            $fieldName = $field['field_name'];
            $value = $data[$fieldName] ?? '';
            $validation = json_decode($field['validation_rules'], true) ?? [];

            // Required kontrolü
            if ($field['is_required'] && empty($value) && $field['field_type'] !== 'file') {
                $errors[$fieldName][] = __('validation.required');
                continue;
            }

            // Dosya validasyonu
            if ($field['field_type'] === 'file' && $field['is_required']) {
                if (!isset($files[$fieldName]) || $files[$fieldName]['error'] !== UPLOAD_ERR_OK) {
                    $errors[$fieldName][] = __('validation.required');
                    continue;
                }

                $fileErrors = $this->validateFile($field['id'], $files[$fieldName]);
                if (!empty($fileErrors)) {
                    $errors[$fieldName] = array_merge($errors[$fieldName] ?? [], $fileErrors);
                }
            }

            // Uzunluk kontrolü
            if (isset($validation['min_length']) && mb_strlen($value) < $validation['min_length']) {
                $errors[$fieldName][] = __('validation.min_length', ['count' => $validation['min_length']]);
            }

            if (isset($validation['max_length']) && mb_strlen($value) > $validation['max_length']) {
                $errors[$fieldName][] = __('validation.max_length', ['count' => $validation['max_length']]);
            }

            // Email kontrolü
            if ($field['field_type'] === 'email' && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$fieldName][] = __('validation.email');
            }

            // Anahtar kelime kontrolü
            if (isset($validation['min_keywords']) || isset($validation['max_keywords'])) {
                $keywords = array_filter(array_map('trim', explode(',', $value)));
                $count = count($keywords);

                if (isset($validation['min_keywords']) && $count < $validation['min_keywords']) {
                    $errors[$fieldName][] = "Minimum {$validation['min_keywords']} keywords required";
                }

                if (isset($validation['max_keywords']) && $count > $validation['max_keywords']) {
                    $errors[$fieldName][] = "Maximum {$validation['max_keywords']} keywords allowed";
                }
            }
        }

        return $errors;
    }

    // Dosya validasyonu
    private function validateFile($fieldId, $file) {
        $errors = [];
        $rules = DB::query(
            "SELECT * FROM file_upload_rules WHERE field_id = ?",
            [$fieldId]
        )->fetch();

        if (!$rules) return $errors;

        // Dosya türü kontrolü
        $allowedTypes = json_decode($rules['allowed_types'], true);
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedTypes)) {
            $errors[] = "File type must be one of: " . implode(', ', $allowedTypes);
        }

        // Boyut kontrolü
        $sizeMB = $file['size'] / (1024 * 1024);
        if ($sizeMB > $rules['max_size_mb']) {
            $errors[] = "File size must not exceed {$rules['max_size_mb']}MB";
        }

        if (isset($rules['min_size_kb'])) {
            $sizeKB = $file['size'] / 1024;
            if ($sizeKB < $rules['min_size_kb']) {
                $errors[] = "File size must be at least {$rules['min_size_kb']}KB";
            }
        }

        return $errors;
    }
}
```

#### 3. Dergi Yöneticisi için Form Yapılandırma Arayüzü
```php
class FormBuilderController extends Controller {
    public function index() {
        if (!Auth::hasPermission('manage_forms')) {
            return $this->error('Yetkiniz yok', 403);
        }

        $tenantId = Auth::tenant()->id;
        $formManager = new FormSchemaManager($tenantId);

        // Mevcut form şemalarını listele
        $schemas = DB::query(
            "SELECT * FROM form_schemas WHERE tenant_id = ?",
            [$tenantId]
        )->fetchAll();

        return $this->view('admin/form-builder/index', [
            'schemas' => $schemas
        ]);
    }

    public function editSchema($schemaId) {
        $tenantId = Auth::tenant()->id;
        $formManager = new FormSchemaManager($tenantId);

        $schema = DB::query(
            "SELECT * FROM form_schemas WHERE id = ? AND tenant_id = ?",
            [$schemaId, $tenantId]
        )->fetch();

        if (!$schema) {
            return $this->error('Schema not found', 404);
        }

        $fields = DB::query(
            "SELECT f.*, r.allowed_types, r.max_size_mb, r.naming_pattern, r.max_files
             FROM form_fields f
             LEFT JOIN file_upload_rules r ON f.id = r.field_id
             WHERE f.schema_id = ?
             ORDER BY f.field_order ASC",
            [$schemaId]
        )->fetchAll();

        return $this->view('admin/form-builder/edit', [
            'schema' => $schema,
            'fields' => $fields,
            'fieldTypes' => [
                'text' => 'Text Input',
                'textarea' => 'Text Area',
                'number' => 'Number',
                'email' => 'Email',
                'select' => 'Dropdown',
                'checkbox' => 'Checkbox',
                'radio' => 'Radio Button',
                'file' => 'File Upload',
                'date' => 'Date',
                'url' => 'URL'
            ]
        ]);
    }

    public function addField() {
        $data = $this->validate($_POST, [
            'schema_id' => 'required|integer',
            'field_name' => 'required',
            'field_label' => 'required',
            'field_type' => 'required'
        ]);

        $tenantId = Auth::tenant()->id;
        $formManager = new FormSchemaManager($tenantId);

        $fieldId = $formManager->addField($data['schema_id'], $data);

        return $this->success([
            'message' => 'Field added successfully',
            'field_id' => $fieldId
        ]);
    }

    public function updateField($fieldId) {
        $data = $_POST;

        $tenantId = Auth::tenant()->id;
        $formManager = new FormSchemaManager($tenantId);

        $formManager->updateField($fieldId, $data);

        return $this->success(['message' => 'Field updated successfully']);
    }

    public function deleteField($fieldId) {
        $tenantId = Auth::tenant()->id;
        $formManager = new FormSchemaManager($tenantId);

        $formManager->deleteField($fieldId);

        return $this->success(['message' => 'Field deleted successfully']);
    }
}
```

---

## 🚀 WORDPRESS BENZERİ KURULUM SİSTEMİ

### Kurulum Akışı

#### 1. Dizin Yapısı
```
amds/
├── install/                     # Kurulum dosyaları
│   ├── index.php               # Kurulum başlangıcı
│   ├── steps/
│   │   ├── 1-requirements.php  # Sistem gereksinimleri kontrolü
│   │   ├── 2-database.php      # Veritabanı yapılandırması
│   │   ├── 3-admin.php         # Süper admin oluşturma
│   │   ├── 4-journal.php       # Dergi bilgileri
│   │   ├── 5-settings.php      # Genel ayarlar
│   │   └── 6-complete.php      # Kurulum tamamlandı
│   ├── assets/
│   │   ├── css/
│   │   └── js/
│   └── Installer.php           # Kurulum sınıfı
├── core/
├── config/
│   ├── config.sample.php       # Örnek config
│   └── config.php              # Kurulum sonrası oluşur
└── ...
```

#### 2. Installer Sınıfı
```php
<?php
// install/Installer.php
class Installer {
    private $db = null;
    private $errors = [];
    private $currentStep = 1;
    private $totalSteps = 6;

    public function __construct() {
        session_start();
        $this->currentStep = $_SESSION['install_step'] ?? 1;
    }

    // Sistem gereksinimlerini kontrol et
    public function checkRequirements() {
        $requirements = [
            'php_version' => [
                'required' => '8.0',
                'current' => PHP_VERSION,
                'status' => version_compare(PHP_VERSION, '8.0', '>=')
            ],
            'pdo_mysql' => [
                'required' => 'Enabled',
                'current' => extension_loaded('pdo_mysql') ? 'Enabled' : 'Disabled',
                'status' => extension_loaded('pdo_mysql')
            ],
            'mbstring' => [
                'required' => 'Enabled',
                'current' => extension_loaded('mbstring') ? 'Enabled' : 'Disabled',
                'status' => extension_loaded('mbstring')
            ],
            'fileinfo' => [
                'required' => 'Enabled',
                'current' => extension_loaded('fileinfo') ? 'Enabled' : 'Disabled',
                'status' => extension_loaded('fileinfo')
            ],
            'gd' => [
                'required' => 'Enabled',
                'current' => extension_loaded('gd') ? 'Enabled' : 'Disabled',
                'status' => extension_loaded('gd')
            ],
            'writable_config' => [
                'required' => 'Writable',
                'current' => is_writable('../config') ? 'Writable' : 'Not writable',
                'status' => is_writable('../config')
            ],
            'writable_uploads' => [
                'required' => 'Writable',
                'current' => is_writable('../uploads') ? 'Writable' : 'Not writable',
                'status' => is_writable('../uploads')
            ]
        ];

        return $requirements;
    }

    // Veritabanı bağlantısını test et
    public function testDatabase($host, $port, $dbname, $username, $password) {
        try {
            $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            // Veritabanını oluştur
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `$dbname`");

            $this->db = $pdo;

            return [
                'success' => true,
                'message' => 'Database connection successful'
            ];

        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Database connection failed: ' . $e->getMessage()
            ];
        }
    }

    // Config dosyası oluştur
    public function createConfig($dbConfig) {
        $template = file_get_contents('../config/config.sample.php');

        $replacements = [
            '{{DB_HOST}}' => $dbConfig['host'],
            '{{DB_PORT}}' => $dbConfig['port'],
            '{{DB_NAME}}' => $dbConfig['database'],
            '{{DB_USER}}' => $dbConfig['username'],
            '{{DB_PASS}}' => $dbConfig['password'],
            '{{APP_KEY}}' => bin2hex(random_bytes(32)),
            '{{INSTALL_DATE}}' => date('Y-m-d H:i:s')
        ];

        $config = str_replace(array_keys($replacements), array_values($replacements), $template);

        if (file_put_contents('../config/config.php', $config)) {
            return true;
        }

        $this->errors[] = 'Could not write config file';
        return false;
    }

    // Veritabanı tablolarını oluştur
    public function createTables() {
        try {
            // Core database schema
            $schema = file_get_contents('sql/core_schema.sql');
            $this->db->exec($schema);

            // Tenant database schema template
            $tenantSchema = file_get_contents('sql/tenant_schema.sql');
            $_SESSION['tenant_schema'] = $tenantSchema;

            return true;

        } catch (PDOException $e) {
            $this->errors[] = 'Failed to create tables: ' . $e->getMessage();
            return false;
        }
    }

    // Süper admin oluştur
    public function createSuperAdmin($data) {
        try {
            $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);

            $stmt = $this->db->prepare(
                "INSERT INTO core_users (email, password_hash, first_name, last_name, role, created_at)
                 VALUES (?, ?, ?, ?, 'super_admin', NOW())"
            );

            $stmt->execute([
                $data['email'],
                $passwordHash,
                $data['first_name'],
                $data['last_name']
            ]);

            $_SESSION['super_admin_id'] = $this->db->lastInsertId();

            return true;

        } catch (PDOException $e) {
            $this->errors[] = 'Failed to create super admin: ' . $e->getMessage();
            return false;
        }
    }

    // İlk dergiyi oluştur
    public function createJournal($data) {
        try {
            // Dergi slug'ı oluştur
            $slug = $this->createSlug($data['name']);

            // Tenant database adı
            $tenantDb = 'amds_tenant_' . $slug;

            // Tenant veritabanını oluştur
            $this->db->exec("CREATE DATABASE `$tenantDb` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // Tenant bilgilerini kaydet
            $stmt = $this->db->prepare(
                "INSERT INTO tenants (slug, name, database_name, domain, status, core_version, created_at)
                 VALUES (?, ?, ?, ?, 'active', '1.0.0', NOW())"
            );

            $stmt->execute([
                $slug,
                $data['name'],
                $tenantDb,
                $data['domain']
            ]);

            $tenantId = $this->db->lastInsertId();

            // Tenant veritabanını yapılandır
            $this->db->exec("USE `$tenantDb`");
            $this->db->exec($_SESSION['tenant_schema']);

            // Dergi yöneticisi oluştur
            $passwordHash = password_hash($data['admin_password'], PASSWORD_BCRYPT);

            $this->db->exec("USE `$tenantDb`");
            $stmt = $this->db->prepare(
                "INSERT INTO users (email, password_hash, first_name, last_name, role_id, created_at)
                 VALUES (?, ?, ?, ?, 1, NOW())"
            );

            $stmt->execute([
                $data['admin_email'],
                $passwordHash,
                $data['admin_first_name'],
                $data['admin_last_name']
            ]);

            // Varsayılan rolleri oluştur
            $this->createDefaultRoles($tenantDb);

            // Varsayılan ayarları oluştur
            $this->createDefaultSettings($tenantDb, $data);

            $_SESSION['tenant_id'] = $tenantId;

            return [
                'success' => true,
                'tenant_id' => $tenantId,
                'slug' => $slug
            ];

        } catch (PDOException $e) {
            $this->errors[] = 'Failed to create journal: ' . $e->getMessage();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // Slug oluştur
    private function createSlug($name) {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return $slug;
    }

    // Varsayılan rolleri oluştur
    private function createDefaultRoles($tenantDb) {
        $this->db->exec("USE `$tenantDb`");

        $roles = [
            ['name' => 'Journal Manager', 'permissions' => json_encode(['manage_all'])],
            ['name' => 'Author', 'permissions' => json_encode(['submit_article', 'view_own_articles'])],
            ['name' => 'Reviewer', 'permissions' => json_encode(['review_articles'])],
            ['name' => 'Editor', 'permissions' => json_encode(['manage_articles', 'assign_reviewers'])],
            ['name' => 'Secretary', 'permissions' => json_encode(['manage_submissions', 'manage_correspondence'])]
        ];

        $stmt = $this->db->prepare(
            "INSERT INTO roles (name, permissions, created_at) VALUES (?, ?, NOW())"
        );

        foreach ($roles as $role) {
            $stmt->execute([$role['name'], $role['permissions']]);
        }
    }

    // Varsayılan ayarları oluştur
    private function createDefaultSettings($tenantDb, $journalData) {
        $this->db->exec("USE `$tenantDb`");

        $settings = [
            ['key' => 'journal_name', 'value' => $journalData['name'], 'type' => 'string'],
            ['key' => 'journal_email', 'value' => $journalData['admin_email'], 'type' => 'string'],
            ['key' => 'language', 'value' => $journalData['language'] ?? 'en', 'type' => 'string'],
            ['key' => 'timezone', 'value' => $journalData['timezone'] ?? 'UTC', 'type' => 'string'],
            ['key' => 'date_format', 'value' => 'Y-m-d', 'type' => 'string'],
            ['key' => 'submissions_enabled', 'value' => '1', 'type' => 'boolean']
        ];

        $stmt = $this->db->prepare(
            "INSERT INTO settings (setting_key, setting_value, setting_type, updated_at)
             VALUES (?, ?, ?, NOW())"
        );

        foreach ($settings as $setting) {
            $stmt->execute([$setting['key'], $setting['value'], $setting['type']]);
        }
    }

    // Kurulumu tamamla
    public function complete() {
        // install klasörünü kilitle
        $lockFile = __DIR__ . '/.installed';
        file_put_contents($lockFile, date('Y-m-d H:i:s'));

        // Session temizle
        session_destroy();

        return true;
    }

    // İlerlemeyi kaydet
    public function setStep($step) {
        $_SESSION['install_step'] = $step;
        $this->currentStep = $step;
    }

    public function getCurrentStep() {
        return $this->currentStep;
    }

    public function getErrors() {
        return $this->errors;
    }

    // Kurulumun yapılıp yapılmadığını kontrol et
    public static function isInstalled() {
        $configExists = file_exists(__DIR__ . '/../config/config.php');
        $lockExists = file_exists(__DIR__ . '/.installed');

        return $configExists && $lockExists;
    }
}
```

#### 3. Kurulum Adımları

**Step 1: Sistem Gereksinimleri** (install/steps/1-requirements.php)
```php
<?php
require_once '../Installer.php';
$installer = new Installer();
$requirements = $installer->checkRequirements();
$allPassed = !in_array(false, array_column($requirements, 'status'));
?>

<div class="install-step">
    <h2>System Requirements</h2>

    <table class="table">
        <thead>
            <tr>
                <th>Requirement</th>
                <th>Required</th>
                <th>Current</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requirements as $name => $req): ?>
            <tr>
                <td><?= ucwords(str_replace('_', ' ', $name)) ?></td>
                <td><?= $req['required'] ?></td>
                <td><?= $req['current'] ?></td>
                <td>
                    <?php if ($req['status']): ?>
                        <span class="badge bg-success">✓ Pass</span>
                    <?php else: ?>
                        <span class="badge bg-danger">✗ Fail</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($allPassed): ?>
        <button onclick="nextStep()" class="btn btn-primary">Continue</button>
    <?php else: ?>
        <div class="alert alert-danger">
            Please fix the failed requirements before continuing.
        </div>
    <?php endif; ?>
</div>
```

**Step 2: Veritabanı Yapılandırması** (install/steps/2-database.php)
```php
<div class="install-step">
    <h2>Database Configuration</h2>

    <form id="databaseForm">
        <div class="mb-3">
            <label class="form-label">Database Host</label>
            <input type="text" name="host" class="form-control" value="localhost" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Database Port</label>
            <input type="number" name="port" class="form-control" value="3306" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Database Name</label>
            <input type="text" name="database" class="form-control" value="amds_core" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control">
        </div>

        <div id="dbTestResult"></div>

        <button type="button" onclick="testDatabase()" class="btn btn-secondary">Test Connection</button>
        <button type="submit" class="btn btn-primary" id="continueBtn" disabled>Continue</button>
    </form>
</div>

<script>
function testDatabase() {
    const formData = new FormData(document.getElementById('databaseForm'));

    fetch('ajax/test-database.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const resultDiv = document.getElementById('dbTestResult');

        if (data.success) {
            resultDiv.innerHTML = '<div class="alert alert-success">✓ Connection successful!</div>';
            document.getElementById('continueBtn').disabled = false;
        } else {
            resultDiv.innerHTML = `<div class="alert alert-danger">✗ ${data.message}</div>`;
        }
    });
}
</script>
```

**Step 4: Dergi Bilgileri** (install/steps/4-journal.php)
```php
<div class="install-step">
    <h2>Journal Information</h2>

    <form id="journalForm" onsubmit="submitJournal(event)">
        <div class="mb-3">
            <label class="form-label">Journal Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Domain/Subdomain</label>
            <input type="text" name="domain" class="form-control" placeholder="journal.example.com" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Default Language</label>
            <select name="language" class="form-select">
                <option value="en">English</option>
                <option value="tr">Türkçe</option>
                <option value="ja">日本語</option>
                <option value="de">Deutsch</option>
            </select>
        </div>

        <h4>Journal Manager Account</h4>

        <div class="mb-3">
            <label class="form-label">First Name</label>
            <input type="text" name="admin_first_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Last Name</label>
            <input type="text" name="admin_last_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="admin_email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="admin_password" class="form-control" required minlength="8">
        </div>

        <button type="submit" class="btn btn-primary">Create Journal</button>
    </form>
</div>
```

---

## 🔄 GÜNCELLEME SİSTEMİ

### 1. Versiyon Yapısı

```
Versiyonlama: MAJOR.MINOR.PATCH
Örnek: 2.5.3

MAJOR: Büyük değişiklikler
MINOR: Yeni özellikler
PATCH: Hata düzeltmeleri
```

### 2. Güncelleme Türleri

#### A. Genel Güncellemeler
Tüm dergilere otomatik olarak dağıtılır.

```php
// UpdateManager.php
class UpdateManager {
    public function checkForUpdates() {
        // Merkezi sunucudan güncelleme kontrolü
        $latestVersion = $this->fetchLatestVersion();
        $currentVersion = $this->getCurrentVersion();

        if (version_compare($latestVersion, $currentVersion, '>')) {
            return [
                'available' => true,
                'version' => $latestVersion,
                'type' => 'general',
                'changelog' => $this->getChangelog($latestVersion)
            ];
        }

        return ['available' => false];
    }

    public function applyUpdate($version, $tenantId = null) {
        // Güncelleme uygulama
        $this->backupDatabase($tenantId);
        $this->downloadUpdate($version);
        $this->runMigrations($version, $tenantId);
        $this->updateFiles($version);
        $this->updateVersion($version, $tenantId);
    }
}
```

#### B. Özel Güncellemeler (Tenant Specific)
Sadece belirli bir dergiye özel.

```php
class TenantUpdateManager extends UpdateManager {
    public function createCustomUpdate($tenantId, $updateData) {
        // Özel güncelleme oluşturma
        $customUpdate = [
            'tenant_id' => $tenantId,
            'version' => $this->generateCustomVersion(),
            'files' => $updateData['files'],
            'migrations' => $updateData['migrations'],
            'rollback_available' => true
        ];

        $this->saveCustomUpdate($customUpdate);
        return $customUpdate;
    }

    public function applyCustomUpdate($tenantId, $updateId) {
        // Özel güncellemeyi uygula
        $update = $this->getCustomUpdate($updateId);

        $this->switchToTenant($tenantId);
        $this->backupTenantData($tenantId);
        $this->applyCustomFiles($update);
        $this->runCustomMigrations($update);
    }
}
```

### 3. Yönetim Panelinde Güncelleme Arayüzü

```php
// Admin panel update view
if ($this->updateManager->hasUpdate()) {
    $updateInfo = $this->updateManager->getUpdateInfo();

    echo '
    <div class="update-notification">
        <h4>Yeni Güncelleme Mevcut</h4>
        <p>Versiyon: ' . $updateInfo['version'] . '</p>
        <button onclick="installUpdate()">Şimdi Güncelle</button>
    </div>';
}
```

### 4. Güncelleme Akışı

```
1. Merkezi Sunucu ← Güncelleme Yayınla
                 ↓
2. Tüm Tenant'lar ← Bildirim Gönder
                 ↓
3. Admin Panel   → Güncelleme Görüntüle
                 ↓
4. Admin Onayı   → Güncellemeyi Başlat
                 ↓
5. Backup        → Veritabanı + Dosyalar
                 ↓
6. Download      → Güncelleme Dosyaları
                 ↓
7. Apply         → Migration + File Replace
                 ↓
8. Verify        → Başarı Kontrolü
                 ↓
9. Complete      → Güncelleme Tamamlandı
```

---

## 👑 SÜPER ADMIN PANELİ VE KAYNAK YÖNETİMİ

### Mimari Yaklaşım

Süper admin, tüm dergileri görebilen ve yöneten, hiçbir derginin erişemeyeceği merkezi bir panel. Bu panel ile kaynak kullanımı, sorunlar, destek talepleri ve faturalama yönetilir.

#### 1. Veritabanı Yapısı

```sql
-- Süper admin kullanıcıları (core database)
CREATE TABLE core_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE,
    password_hash VARCHAR(255),
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    role ENUM('super_admin', 'support', 'developer') DEFAULT 'super_admin',
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Kaynak kullanımı izleme
CREATE TABLE resource_usage (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tenant_id INT,
    date DATE,
    cpu_usage DECIMAL(5,2), -- CPU kullanımı yüzdesi
    ram_usage_mb DECIMAL(10,2), -- RAM kullanımı MB
    storage_used_mb DECIMAL(10,2), -- Depolama kullanımı MB
    bandwidth_used_mb DECIMAL(10,2), -- Bant genişliği MB
    active_users INT, -- Aktif kullanıcı sayısı
    total_articles INT, -- Toplam makale sayısı
    total_reviews INT, -- Toplam değerlendirme sayısı
    api_calls INT, -- API çağrı sayısı
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);

-- Paket/plan tanımları
CREATE TABLE subscription_plans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    description TEXT,
    max_articles_per_month INT, -- Aylık max makale
    max_storage_gb DECIMAL(10,2), -- Max depolama GB
    max_users INT, -- Max kullanıcı sayısı
    max_bandwidth_gb DECIMAL(10,2), -- Max bant genişliği GB
    price_monthly DECIMAL(10,2),
    price_yearly DECIMAL(10,2),
    features JSON, -- Ek özellikler
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tenant abonelikleri
CREATE TABLE tenant_subscriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tenant_id INT,
    plan_id INT,
    status ENUM('active', 'suspended', 'cancelled', 'expired') DEFAULT 'active',
    started_at TIMESTAMP,
    expires_at TIMESTAMP,
    auto_renew BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id),
    FOREIGN KEY (plan_id) REFERENCES subscription_plans(id)
);

-- Destek talepleri
CREATE TABLE support_tickets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tenant_id INT,
    ticket_number VARCHAR(50) UNIQUE,
    subject VARCHAR(255),
    description TEXT,
    type ENUM('bug', 'feature_request', 'question', 'custom_update', 'billing', 'other'),
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('open', 'in_progress', 'waiting_response', 'resolved', 'closed') DEFAULT 'open',
    assigned_to INT, -- core_users.id
    created_by INT, -- tenant user id
    contact_method ENUM('email', 'phone', 'whatsapp') DEFAULT 'email',
    contact_value VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id),
    FOREIGN KEY (assigned_to) REFERENCES core_users(id)
);

-- Destek talep mesajları
CREATE TABLE ticket_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ticket_id INT,
    sender_type ENUM('tenant', 'admin'),
    sender_id INT,
    message TEXT,
    attachments JSON,
    is_internal BOOLEAN DEFAULT FALSE, -- Sadece adminler görebilir
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES support_tickets(id)
);

-- Özel güncelleme talepleri
CREATE TABLE custom_update_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tenant_id INT,
    ticket_id INT,
    title VARCHAR(255),
    description TEXT,
    requirements JSON, -- Detaylı gereksinimler
    status ENUM('pending', 'approved', 'in_development', 'testing', 'completed', 'rejected') DEFAULT 'pending',
    estimated_hours DECIMAL(10,2),
    actual_hours DECIMAL(10,2),
    estimated_cost DECIMAL(10,2),
    actual_cost DECIMAL(10,2),
    developer_id INT, -- Atanan developer
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id),
    FOREIGN KEY (ticket_id) REFERENCES support_tickets(id),
    FOREIGN KEY (developer_id) REFERENCES core_users(id)
);

-- Sistem sorunları/logları
CREATE TABLE system_issues (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tenant_id INT,
    issue_type ENUM('error', 'warning', 'performance', 'security'),
    severity ENUM('low', 'medium', 'high', 'critical'),
    title VARCHAR(255),
    description TEXT,
    error_log TEXT,
    stack_trace TEXT,
    affected_users INT,
    status ENUM('open', 'investigating', 'resolved', 'ignored') DEFAULT 'open',
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);

-- Faturalar
CREATE TABLE invoices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tenant_id INT,
    subscription_id INT,
    invoice_number VARCHAR(50) UNIQUE,
    amount DECIMAL(10,2),
    currency VARCHAR(3) DEFAULT 'USD',
    status ENUM('draft', 'sent', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',
    issued_at TIMESTAMP,
    due_at TIMESTAMP,
    paid_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id),
    FOREIGN KEY (subscription_id) REFERENCES tenant_subscriptions(id)
);

-- Varsayılan paketleri ekle
INSERT INTO subscription_plans (name, description, max_articles_per_month, max_storage_gb, max_users, max_bandwidth_gb, price_monthly, price_yearly, features) VALUES
('Starter', 'Small journals getting started', 50, 10, 50, 50, 49.99, 499.99, '["email_support", "basic_statistics"]'),
('Professional', 'Growing journals with more needs', 200, 50, 200, 200, 149.99, 1499.99, '["priority_support", "advanced_statistics", "custom_themes"]'),
('Enterprise', 'Large journals with high volume', -1, 500, -1, 1000, 499.99, 4999.99, '["24/7_support", "custom_development", "dedicated_server", "api_access"]');
```

#### 2. Süper Admin Controller

```php
<?php
class SuperAdminController extends Controller {

    public function __construct() {
        // Süper admin kontrolü
        if (!$this->isSuperAdmin()) {
            http_response_code(403);
            die('Access denied');
        }
    }

    private function isSuperAdmin() {
        return isset($_SESSION['core_user_role']) && $_SESSION['core_user_role'] === 'super_admin';
    }

    // Dashboard - Tüm dergilerin özeti
    public function dashboard() {
        $stats = [
            'total_tenants' => $this->getTotalTenants(),
            'active_tenants' => $this->getActiveTenants(),
            'total_revenue' => $this->getTotalRevenue(),
            'open_tickets' => $this->getOpenTickets(),
            'critical_issues' => $this->getCriticalIssues(),
            'resource_alerts' => $this->getResourceAlerts()
        ];

        $recentTenants = $this->getRecentTenants(10);
        $resourceUsage = $this->getResourceUsageSummary();

        return $this->view('super-admin/dashboard', [
            'stats' => $stats,
            'recent_tenants' => $recentTenants,
            'resource_usage' => $resourceUsage
        ]);
    }

    // Tüm dergileri listele
    public function listTenants() {
        $tenants = DB::query("
            SELECT
                t.*,
                ts.status as subscription_status,
                sp.name as plan_name,
                COUNT(DISTINCT st.id) as open_tickets,
                AVG(ru.cpu_usage) as avg_cpu,
                AVG(ru.ram_usage_mb) as avg_ram,
                SUM(ru.storage_used_mb) as total_storage
            FROM tenants t
            LEFT JOIN tenant_subscriptions ts ON t.id = ts.tenant_id AND ts.status = 'active'
            LEFT JOIN subscription_plans sp ON ts.plan_id = sp.id
            LEFT JOIN support_tickets st ON t.id = st.tenant_id AND st.status IN ('open', 'in_progress')
            LEFT JOIN resource_usage ru ON t.id = ru.tenant_id AND ru.date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY t.id
            ORDER BY t.created_at DESC
        ")->fetchAll();

        return $this->view('super-admin/tenants/list', [
            'tenants' => $tenants
        ]);
    }

    // Belirli bir dergiyi görüntüle
    public function viewTenant($tenantId) {
        $tenant = $this->getTenantDetails($tenantId);
        $subscription = $this->getTenantSubscription($tenantId);
        $resourceUsage = $this->getTenantResourceUsage($tenantId, 30); // Son 30 gün
        $tickets = $this->getTenantTickets($tenantId);
        $issues = $this->getTenantIssues($tenantId);

        return $this->view('super-admin/tenants/view', [
            'tenant' => $tenant,
            'subscription' => $subscription,
            'resource_usage' => $resourceUsage,
            'tickets' => $tickets,
            'issues' => $issues
        ]);
    }

    // Kaynak kullanımı izleme
    public function resourceMonitoring() {
        $tenants = DB::query("
            SELECT
                t.id,
                t.name,
                t.slug,
                ru.cpu_usage,
                ru.ram_usage_mb,
                ru.storage_used_mb,
                ru.bandwidth_used_mb,
                sp.max_storage_gb,
                sp.max_bandwidth_gb,
                (ru.storage_used_mb / (sp.max_storage_gb * 1024)) * 100 as storage_percentage,
                (ru.bandwidth_used_mb / (sp.max_bandwidth_gb * 1024)) * 100 as bandwidth_percentage
            FROM tenants t
            LEFT JOIN tenant_subscriptions ts ON t.id = ts.tenant_id
            LEFT JOIN subscription_plans sp ON ts.plan_id = sp.id
            LEFT JOIN resource_usage ru ON t.id = ru.tenant_id AND ru.date = CURDATE()
            WHERE t.status = 'active'
            ORDER BY storage_percentage DESC
        ")->fetchAll();

        // Uyarılar - %80 üzerindeki kullanımlar
        $alerts = array_filter($tenants, function($t) {
            return $t['storage_percentage'] > 80 || $t['bandwidth_percentage'] > 80;
        });

        return $this->view('super-admin/monitoring/resources', [
            'tenants' => $tenants,
            'alerts' => $alerts
        ]);
    }

    // Destek talepleri
    public function supportTickets() {
        $tickets = DB::query("
            SELECT
                st.*,
                t.name as tenant_name,
                t.slug as tenant_slug,
                cu.first_name as assigned_to_name,
                (SELECT COUNT(*) FROM ticket_messages WHERE ticket_id = st.id) as message_count
            FROM support_tickets st
            JOIN tenants t ON st.tenant_id = t.id
            LEFT JOIN core_users cu ON st.assigned_to = cu.id
            ORDER BY
                FIELD(st.priority, 'urgent', 'high', 'medium', 'low'),
                st.created_at DESC
        ")->fetchAll();

        return $this->view('super-admin/support/tickets', [
            'tickets' => $tickets
        ]);
    }

    // Destek talebi detayı
    public function viewTicket($ticketId) {
        $ticket = DB::query("
            SELECT
                st.*,
                t.name as tenant_name,
                t.slug as tenant_slug
            FROM support_tickets st
            JOIN tenants t ON st.tenant_id = t.id
            WHERE st.id = ?
        ", [$ticketId])->fetch();

        $messages = DB::query("
            SELECT * FROM ticket_messages
            WHERE ticket_id = ?
            ORDER BY created_at ASC
        ", [$ticketId])->fetchAll();

        return $this->view('super-admin/support/ticket-detail', [
            'ticket' => $ticket,
            'messages' => $messages
        ]);
    }

    // Destek talebine cevap
    public function replyTicket() {
        $data = $this->validate($_POST, [
            'ticket_id' => 'required|integer',
            'message' => 'required'
        ]);

        $ticketId = $data['ticket_id'];
        $message = $data['message'];
        $isInternal = $_POST['is_internal'] ?? false;

        // Mesajı ekle
        DB::insert('ticket_messages', [
            'ticket_id' => $ticketId,
            'sender_type' => 'admin',
            'sender_id' => $_SESSION['core_user_id'],
            'message' => $message,
            'is_internal' => $isInternal
        ]);

        // Ticket durumunu güncelle
        if (isset($_POST['status'])) {
            DB::update('support_tickets', $ticketId, [
                'status' => $_POST['status']
            ]);
        }

        // Tenant'a email gönder (internal değilse)
        if (!$isInternal) {
            $this->sendTicketReplyEmail($ticketId, $message);
        }

        return $this->success(['message' => 'Reply sent']);
    }

    // Özel güncelleme talepleri
    public function customUpdateRequests() {
        $requests = DB::query("
            SELECT
                cur.*,
                t.name as tenant_name,
                st.ticket_number,
                cu.first_name as developer_name
            FROM custom_update_requests cur
            JOIN tenants t ON cur.tenant_id = t.id
            LEFT JOIN support_tickets st ON cur.ticket_id = st.id
            LEFT JOIN core_users cu ON cur.developer_id = cu.id
            ORDER BY
                FIELD(cur.status, 'pending', 'approved', 'in_development', 'testing', 'completed', 'rejected'),
                cur.created_at DESC
        ")->fetchAll();

        return $this->view('super-admin/updates/custom-requests', [
            'requests' => $requests
        ]);
    }

    // Özel güncelleme talebi onayla
    public function approveCustomUpdate() {
        $data = $this->validate($_POST, [
            'request_id' => 'required|integer',
            'estimated_hours' => 'required|numeric',
            'estimated_cost' => 'required|numeric',
            'developer_id' => 'required|integer'
        ]);

        DB::update('custom_update_requests', $data['request_id'], [
            'status' => 'approved',
            'estimated_hours' => $data['estimated_hours'],
            'estimated_cost' => $data['estimated_cost'],
            'developer_id' => $data['developer_id']
        ]);

        // Tenant'a bildirim gönder
        $request = DB::query("
            SELECT * FROM custom_update_requests WHERE id = ?
        ", [$data['request_id']])->fetch();

        $this->sendUpdateApprovalEmail($request['tenant_id'], $data);

        return $this->success(['message' => 'Update request approved']);
    }

    // Sistem sorunları
    public function systemIssues() {
        $issues = DB::query("
            SELECT
                si.*,
                t.name as tenant_name
            FROM system_issues si
            JOIN tenants t ON si.tenant_id = t.id
            WHERE si.status != 'ignored'
            ORDER BY
                FIELD(si.severity, 'critical', 'high', 'medium', 'low'),
                si.created_at DESC
        ")->fetchAll();

        return $this->view('super-admin/monitoring/issues', [
            'issues' => $issues
        ]);
    }

    // Paket yönetimi
    public function managePlans() {
        $plans = DB::query("SELECT * FROM subscription_plans ORDER BY price_monthly ASC")->fetchAll();

        return $this->view('super-admin/billing/plans', [
            'plans' => $plans
        ]);
    }

    // Faturalar
    public function invoices() {
        $invoices = DB::query("
            SELECT
                i.*,
                t.name as tenant_name
            FROM invoices i
            JOIN tenants t ON i.tenant_id = t.id
            ORDER BY i.created_at DESC
            LIMIT 100
        ")->fetchAll();

        return $this->view('super-admin/billing/invoices', [
            'invoices' => $invoices
        ]);
    }

    // Kaynak uyarısı gönder
    public function sendResourceAlert($tenantId, $alertType, $usage) {
        $tenant = $this->getTenantDetails($tenantId);
        $subscription = $this->getTenantSubscription($tenantId);

        // Email gönder
        $emailService = new EmailService();
        $emailService->sendToTenant($tenantId, 'resource_alert', [
            'tenant_name' => $tenant['name'],
            'alert_type' => $alertType,
            'current_usage' => $usage['current'],
            'limit' => $usage['limit'],
            'percentage' => $usage['percentage'],
            'plan_name' => $subscription['plan_name'],
            'upgrade_url' => url("/upgrade-plan")
        ]);

        // Ticket oluştur
        $ticketNumber = 'AUTO-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        DB::insert('support_tickets', [
            'tenant_id' => $tenantId,
            'ticket_number' => $ticketNumber,
            'subject' => "Resource Alert: $alertType usage at {$usage['percentage']}%",
            'description' => "Your $alertType usage has reached {$usage['percentage']}% of your plan limit. Consider upgrading your plan.",
            'type' => 'billing',
            'priority' => 'medium',
            'status' => 'open',
            'contact_method' => 'email',
            'contact_value' => $tenant['email']
        ]);
    }

    // İstatistikler
    public function statistics() {
        $stats = [
            'revenue_by_month' => $this->getRevenueByMonth(12),
            'new_tenants_by_month' => $this->getNewTenantsByMonth(12),
            'churn_rate' => $this->getChurnRate(),
            'popular_plans' => $this->getPopularPlans(),
            'ticket_stats' => $this->getTicketStatistics(),
            'average_response_time' => $this->getAverageResponseTime()
        ];

        return $this->view('super-admin/statistics', [
            'stats' => $stats
        ]);
    }

    // Helper methodları
    private function getTenantDetails($tenantId) {
        return DB::query("SELECT * FROM tenants WHERE id = ?", [$tenantId])->fetch();
    }

    private function getTenantSubscription($tenantId) {
        return DB::query("
            SELECT ts.*, sp.name as plan_name, sp.*
            FROM tenant_subscriptions ts
            JOIN subscription_plans sp ON ts.plan_id = sp.id
            WHERE ts.tenant_id = ? AND ts.status = 'active'
            ORDER BY ts.created_at DESC
            LIMIT 1
        ", [$tenantId])->fetch();
    }

    private function getTenantResourceUsage($tenantId, $days = 30) {
        return DB::query("
            SELECT * FROM resource_usage
            WHERE tenant_id = ? AND date >= DATE_SUB(NOW(), INTERVAL ? DAY)
            ORDER BY date DESC
        ", [$tenantId, $days])->fetchAll();
    }

    private function getTenantTickets($tenantId) {
        return DB::query("
            SELECT * FROM support_tickets
            WHERE tenant_id = ?
            ORDER BY created_at DESC
            LIMIT 20
        ", [$tenantId])->fetchAll();
    }

    private function getTenantIssues($tenantId) {
        return DB::query("
            SELECT * FROM system_issues
            WHERE tenant_id = ? AND status != 'ignored'
            ORDER BY created_at DESC
            LIMIT 20
        ", [$tenantId])->fetchAll();
    }
}
```

#### 3. Kaynak Kullanımı İzleme (Cron Job)

```php
<?php
// cron/monitor_resources.php
// Her saat başı çalışır

class ResourceMonitor {
    public function collectUsageData() {
        $tenants = DB::query("SELECT * FROM tenants WHERE status = 'active'")->fetchAll();

        foreach ($tenants as $tenant) {
            $usage = $this->getTenantUsage($tenant['id']);

            // Kullanım verilerini kaydet
            DB::insert('resource_usage', [
                'tenant_id' => $tenant['id'],
                'date' => date('Y-m-d'),
                'cpu_usage' => $usage['cpu'],
                'ram_usage_mb' => $usage['ram'],
                'storage_used_mb' => $usage['storage'],
                'bandwidth_used_mb' => $usage['bandwidth'],
                'active_users' => $usage['active_users'],
                'total_articles' => $usage['total_articles'],
                'total_reviews' => $usage['total_reviews'],
                'api_calls' => $usage['api_calls']
            ]);

            // Eşik kontrolü ve uyarı
            $this->checkThresholds($tenant['id'], $usage);
        }
    }

    private function getTenantUsage($tenantId) {
        $tenant = DB::query("SELECT * FROM tenants WHERE id = ?", [$tenantId])->fetch();
        $tenantDb = $tenant['database_name'];

        // Storage kullanımı
        $storage = DB::query("
            SELECT
                SUM(data_length + index_length) / 1024 / 1024 AS size_mb
            FROM information_schema.TABLES
            WHERE table_schema = ?
        ", [$tenantDb])->fetch();

        // Uploads klasörü boyutu
        $uploadsPath = __DIR__ . "/../tenants/{$tenant['slug']}/uploads";
        $uploadsSize = $this->getFolderSize($uploadsPath) / 1024 / 1024; // MB

        // Tenant veritabanına bağlan
        $tenantConn = Database::getTenantConnection($tenantId);

        // Aktif kullanıcılar (son 24 saat)
        $activeUsers = $tenantConn->query("
            SELECT COUNT(DISTINCT user_id) FROM user_sessions
            WHERE last_activity >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ")->fetchColumn();

        // Toplam makaleler
        $totalArticles = $tenantConn->query("SELECT COUNT(*) FROM articles")->fetchColumn();

        // Toplam değerlendirmeler
        $totalReviews = $tenantConn->query("SELECT COUNT(*) FROM reviews")->fetchColumn();

        return [
            'cpu' => $this->getCurrentCPU(),
            'ram' => $this->getCurrentRAM(),
            'storage' => $storage['size_mb'] + $uploadsSize,
            'bandwidth' => $this->getBandwidthUsage($tenantId),
            'active_users' => $activeUsers,
            'total_articles' => $totalArticles,
            'total_reviews' => $totalReviews,
            'api_calls' => $this->getAPICallCount($tenantId)
        ];
    }

    private function checkThresholds($tenantId, $usage) {
        $subscription = DB::query("
            SELECT ts.*, sp.*
            FROM tenant_subscriptions ts
            JOIN subscription_plans sp ON ts.plan_id = sp.id
            WHERE ts.tenant_id = ? AND ts.status = 'active'
        ", [$tenantId])->fetch();

        if (!$subscription) return;

        $alerts = [];

        // Storage kontrolü
        $storageLimit = $subscription['max_storage_gb'] * 1024; // MB'ye çevir
        $storagePercentage = ($usage['storage'] / $storageLimit) * 100;

        if ($storagePercentage >= 80) {
            $alerts[] = [
                'type' => 'storage',
                'current' => $usage['storage'],
                'limit' => $storageLimit,
                'percentage' => $storagePercentage
            ];
        }

        // Bandwidth kontrolü
        if ($subscription['max_bandwidth_gb'] > 0) {
            $bandwidthLimit = $subscription['max_bandwidth_gb'] * 1024;
            $bandwidthPercentage = ($usage['bandwidth'] / $bandwidthLimit) * 100;

            if ($bandwidthPercentage >= 80) {
                $alerts[] = [
                    'type' => 'bandwidth',
                    'current' => $usage['bandwidth'],
                    'limit' => $bandwidthLimit,
                    'percentage' => $bandwidthPercentage
                ];
            }
        }

        // Uyarıları gönder
        foreach ($alerts as $alert) {
            $controller = new SuperAdminController();
            $controller->sendResourceAlert($tenantId, $alert['type'], $alert);
        }
    }

    private function getFolderSize($path) {
        $size = 0;
        if (is_dir($path)) {
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $file) {
                $size += $file->getSize();
            }
        }
        return $size;
    }

    private function getCurrentCPU() {
        // CPU kullanımını sys_getloadavg() ile al
        $load = sys_getloadavg();
        return $load[0]; // 1 dakika ortalaması
    }

    private function getCurrentRAM() {
        // RAM kullanımını al (Linux için)
        $free = shell_exec('free -m');
        $free = (string)trim($free);
        $free_arr = explode("\n", $free);
        $mem = explode(" ", $free_arr[1]);
        $mem = array_filter($mem);
        $mem = array_merge($mem);
        return $mem[2]; // Kullanılan RAM (MB)
    }

    private function getBandwidthUsage($tenantId) {
        // Apache/Nginx loglarından bant genişliği hesapla
        // Alternatif: Veritabanında bandwidth logging
        return DB::query("
            SELECT COALESCE(SUM(bandwidth_used_mb), 0)
            FROM resource_usage
            WHERE tenant_id = ? AND date = CURDATE()
        ", [$tenantId])->fetchColumn();
    }

    private function getAPICallCount($tenantId) {
        // API çağrı sayısını loglardan al
        return DB::query("
            SELECT COUNT(*) FROM api_logs
            WHERE tenant_id = ? AND DATE(created_at) = CURDATE()
        ", [$tenantId])->fetchColumn();
    }
}

// Çalıştır
$monitor = new ResourceMonitor();
$monitor->collectUsageData();
```

#### 4. Dergi Yöneticisi için Destek Talep Formu

```php
<?php
// Tenant side - Support request
class TenantSupportController extends Controller {

    public function submitTicket() {
        if (!Auth::hasPermission('contact_support')) {
            return $this->error('Yetkiniz yok', 403);
        }

        $data = $this->validate($_POST, [
            'subject' => 'required',
            'description' => 'required',
            'type' => 'required',
            'contact_method' => 'required'
        ]);

        $tenantId = Auth::tenant()->id;
        $ticketNumber = $this->generateTicketNumber();

        // Core veritabanına ticket ekle
        $coreDb = Database::getCoreConnection();

        $stmt = $coreDb->prepare("
            INSERT INTO support_tickets
            (tenant_id, ticket_number, subject, description, type, priority, created_by, contact_method, contact_value)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $tenantId,
            $ticketNumber,
            $data['subject'],
            $data['description'],
            $data['type'],
            $data['priority'] ?? 'medium',
            Auth::user()->id,
            $data['contact_method'],
            $data['contact_value'] ?? Auth::user()->email
        ]);

        $ticketId = $coreDb->lastInsertId();

        // İlk mesajı ekle
        $stmt = $coreDb->prepare("
            INSERT INTO ticket_messages (ticket_id, sender_type, sender_id, message)
            VALUES (?, 'tenant', ?, ?)
        ");

        $stmt->execute([$ticketId, Auth::user()->id, $data['description']]);

        // Süper admin'e email gönder
        $this->notifySuperAdmin($ticketId, $data);

        return $this->success([
            'message' => 'Support ticket created',
            'ticket_number' => $ticketNumber
        ]);
    }

    public function requestCustomUpdate() {
        $data = $this->validate($_POST, [
            'title' => 'required',
            'description' => 'required',
            'requirements' => 'required'
        ]);

        $tenantId = Auth::tenant()->id;

        // Önce ticket oluştur
        $ticketNumber = $this->generateTicketNumber();
        $coreDb = Database::getCoreConnection();

        $stmt = $coreDb->prepare("
            INSERT INTO support_tickets
            (tenant_id, ticket_number, subject, description, type, priority, created_by, contact_method, contact_value)
            VALUES (?, ?, ?, ?, 'custom_update', 'high', ?, 'email', ?)
        ");

        $stmt->execute([
            $tenantId,
            $ticketNumber,
            "Custom Update Request: " . $data['title'],
            $data['description'],
            Auth::user()->id,
            Auth::user()->email
        ]);

        $ticketId = $coreDb->lastInsertId();

        // Custom update request ekle
        $stmt = $coreDb->prepare("
            INSERT INTO custom_update_requests
            (tenant_id, ticket_id, title, description, requirements)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $tenantId,
            $ticketId,
            $data['title'],
            $data['description'],
            json_encode($data['requirements'])
        ]);

        // Bildirim gönder
        $this->notifyCustomUpdateRequest($ticketId, $data);

        return $this->success([
            'message' => 'Custom update request submitted',
            'ticket_number' => $ticketNumber
        ]);
    }

    private function generateTicketNumber() {
        $prefix = Auth::tenant()->slug;
        $date = date('Ymd');
        $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        return strtoupper($prefix) . '-' . $date . '-' . $random;
    }
}
```

---

## 🗺️ TEKNİK YOL HARİTASI

### Faz 1: Temel Altyapı (2-3 Hafta)

#### 1.1 Database Tasarımı
```sql
-- Merkezi Core Database
CREATE DATABASE amds_core;

-- Core tabloları
CREATE TABLE tenants (
    id INT PRIMARY KEY AUTO_INCREMENT,
    slug VARCHAR(50) UNIQUE,
    name VARCHAR(255),
    database_name VARCHAR(100),
    domain VARCHAR(255),
    status ENUM('active', 'suspended', 'pending'),
    core_version VARCHAR(20),
    custom_version VARCHAR(20),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE updates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    version VARCHAR(20),
    type ENUM('general', 'security', 'feature'),
    description TEXT,
    changelog TEXT,
    file_path VARCHAR(255),
    release_date TIMESTAMP,
    mandatory BOOLEAN DEFAULT FALSE
);

CREATE TABLE tenant_updates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tenant_id INT,
    update_id INT,
    status ENUM('pending', 'installed', 'failed', 'skipped'),
    installed_at TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id),
    FOREIGN KEY (update_id) REFERENCES updates(id)
);

-- Her tenant için ayrı database
CREATE DATABASE amds_tenant_x;
```

#### 1.2 Tenant Database Şeması
```sql
-- Her tenant database'inde olacak tablolar
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE,
    password_hash VARCHAR(255),
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    role_id INT,
    orcid VARCHAR(50),
    status ENUM('active', 'inactive', 'suspended'),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50),
    permissions JSON,
    created_at TIMESTAMP
);

CREATE TABLE articles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    article_code VARCHAR(50) UNIQUE,
    title_tr VARCHAR(500),
    title_en VARCHAR(500),
    abstract_tr TEXT,
    abstract_en TEXT,
    keywords_tr TEXT,
    keywords_en TEXT,
    type ENUM('research', 'review', 'case'),
    status VARCHAR(50),
    submitted_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE article_authors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    article_id INT,
    user_id INT,
    author_order INT,
    is_corresponding BOOLEAN,
    contribution_rate DECIMAL(5,2),
    FOREIGN KEY (article_id) REFERENCES articles(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    article_id INT,
    reviewer_id INT,
    invitation_status ENUM('pending', 'accepted', 'declined'),
    review_status ENUM('not_started', 'in_progress', 'completed'),
    recommendation ENUM('accept', 'minor_revision', 'major_revision', 'reject'),
    comments_to_author TEXT,
    comments_to_editor TEXT,
    quality_score INT,
    originality_score INT,
    submitted_at TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id),
    FOREIGN KEY (reviewer_id) REFERENCES users(id)
);

CREATE TABLE workflow_stages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    article_id INT,
    stage VARCHAR(100),
    status VARCHAR(50),
    assigned_to INT,
    started_at TIMESTAMP,
    completed_at TIMESTAMP,
    notes TEXT,
    FOREIGN KEY (article_id) REFERENCES articles(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id)
);

CREATE TABLE files (
    id INT PRIMARY KEY AUTO_INCREMENT,
    article_id INT,
    file_type ENUM('manuscript', 'supplement', 'revision', 'final'),
    file_name VARCHAR(255),
    file_path VARCHAR(500),
    file_size INT,
    mime_type VARCHAR(100),
    version INT DEFAULT 1,
    uploaded_by INT,
    uploaded_at TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id),
    FOREIGN KEY (uploaded_by) REFERENCES users(id)
);

CREATE TABLE email_templates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(100) UNIQUE,
    name VARCHAR(255),
    subject_tr VARCHAR(500),
    subject_en VARCHAR(500),
    body_tr TEXT,
    body_en TEXT,
    variables JSON,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE issues (
    id INT PRIMARY KEY AUTO_INCREMENT,
    volume INT,
    number INT,
    year INT,
    publish_date DATE,
    status ENUM('planning', 'in_progress', 'published'),
    created_at TIMESTAMP
);

CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE,
    setting_value TEXT,
    setting_type VARCHAR(50),
    updated_at TIMESTAMP
);
```

#### 1.3 Framework Core Sınıfları

**Router.php**
```php
<?php
class Router {
    private $routes = [];
    private $tenant;

    public function __construct($tenant) {
        $this->tenant = $tenant;
    }

    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }

    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Tenant prefix'i kaldır
        $path = str_replace('/' . $this->tenant->slug, '', $path);

        if (isset($this->routes[$method][$path])) {
            return call_user_func($this->routes[$method][$path]);
        }

        http_response_code(404);
        echo "404 Not Found";
    }
}
```

**Database.php**
```php
<?php
class Database {
    private static $instances = [];
    private $connection;

    public static function getTenantConnection($tenantId) {
        if (!isset(self::$instances[$tenantId])) {
            $tenant = self::getTenantInfo($tenantId);

            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . $tenant['database_name'],
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            self::$instances[$tenantId] = $pdo;
        }

        return self::$instances[$tenantId];
    }

    private static function getTenantInfo($tenantId) {
        $coreDb = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=amds_core",
            DB_USER,
            DB_PASS
        );

        $stmt = $coreDb->prepare("SELECT * FROM tenants WHERE id = ?");
        $stmt->execute([$tenantId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
```

### Faz 2: Kimlik Doğrulama ve Yetkilendirme (1-2 Hafta)

```php
class Auth {
    private $db;
    private $tenant;

    public function login($email, $password) {
        $user = $this->db->query(
            "SELECT * FROM users WHERE email = ? AND status = 'active'",
            [$email]
        )->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['tenant_id'] = $this->tenant->id;
            $_SESSION['role_id'] = $user['role_id'];

            return true;
        }

        return false;
    }

    public function hasPermission($permission) {
        $roleId = $_SESSION['role_id'] ?? null;

        if (!$roleId) return false;

        $role = $this->db->query(
            "SELECT permissions FROM roles WHERE id = ?",
            [$roleId]
        )->fetch();

        $permissions = json_decode($role['permissions'], true);

        return in_array($permission, $permissions);
    }
}
```

### Faz 3: Makale Yönetim Sistemi (3-4 Hafta)

**ArticleController.php**
```php
class ArticleController extends Controller {
    public function submit() {
        if (!Auth::hasPermission('submit_article')) {
            return $this->error('Yetkiniz yok', 403);
        }

        $data = $this->validateSubmission($_POST);

        DB::beginTransaction();

        try {
            // Makale kodunu oluştur
            $articleCode = $this->generateArticleCode();

            // Makaleyi kaydet
            $articleId = DB::insert('articles', [
                'article_code' => $articleCode,
                'title_tr' => $data['title_tr'],
                'title_en' => $data['title_en'],
                'abstract_tr' => $data['abstract_tr'],
                'abstract_en' => $data['abstract_en'],
                'keywords_tr' => $data['keywords_tr'],
                'keywords_en' => $data['keywords_en'],
                'type' => $data['type'],
                'status' => 'submitted',
                'submitted_at' => date('Y-m-d H:i:s')
            ]);

            // Yazarları kaydet
            foreach ($data['authors'] as $index => $author) {
                DB::insert('article_authors', [
                    'article_id' => $articleId,
                    'user_id' => $author['user_id'],
                    'author_order' => $index + 1,
                    'is_corresponding' => $author['is_corresponding'] ?? false
                ]);
            }

            // Dosyaları kaydet
            if (isset($_FILES['manuscript'])) {
                $this->uploadManuscript($articleId, $_FILES['manuscript']);
            }

            // İş akışını başlat
            $this->startWorkflow($articleId);

            // E-posta bildirimi gönder
            $this->sendSubmissionConfirmation($articleId);

            DB::commit();

            return $this->success([
                'message' => 'Makale başarıyla gönderildi',
                'article_code' => $articleCode
            ]);

        } catch (Exception $e) {
            DB::rollback();
            return $this->error('Gönderim başarısız: ' . $e->getMessage());
        }
    }

    private function generateArticleCode() {
        $year = date('Y');
        $tenant = Tenant::current();

        // Son makale numarasını al
        $lastCode = DB::query(
            "SELECT article_code FROM articles
             WHERE article_code LIKE ?
             ORDER BY id DESC LIMIT 1",
            [$tenant->code . '-' . $year . '-%']
        )->fetch();

        if ($lastCode) {
            $number = intval(substr($lastCode['article_code'], -4)) + 1;
        } else {
            $number = 1;
        }

        return sprintf('%s-%s-%04d', $tenant->code, $year, $number);
    }
}
```

### Faz 4: Hakem Değerlendirme Sistemi (2-3 Hafta)

**ReviewController.php**
```php
class ReviewController extends Controller {
    public function inviteReviewer($articleId, $reviewerId) {
        // Alan editörü kontrolü
        if (!Auth::hasPermission('invite_reviewer')) {
            return $this->error('Yetkiniz yok', 403);
        }

        // Hakem davetiyesi oluştur
        $invitationId = DB::insert('reviews', [
            'article_id' => $articleId,
            'reviewer_id' => $reviewerId,
            'invitation_status' => 'pending',
            'review_status' => 'not_started'
        ]);

        // Davet e-postası gönder
        $token = $this->generateInvitationToken($invitationId);
        $this->sendInvitationEmail($reviewerId, $articleId, $token);

        return $this->success([
            'message' => 'Hakem davet edildi',
            'invitation_id' => $invitationId
        ]);
    }

    public function submitReview($reviewId, $data) {
        // Hakem kontrolü
        $review = DB::query(
            "SELECT * FROM reviews WHERE id = ? AND reviewer_id = ?",
            [$reviewId, Auth::user()->id]
        )->fetch();

        if (!$review) {
            return $this->error('Değerlendirme bulunamadı', 404);
        }

        if ($review['review_status'] === 'completed') {
            return $this->error('Değerlendirme zaten tamamlanmış', 400);
        }

        // Değerlendirmeyi güncelle
        DB::update('reviews', $reviewId, [
            'review_status' => 'completed',
            'recommendation' => $data['recommendation'],
            'comments_to_author' => $data['comments_to_author'],
            'comments_to_editor' => $data['comments_to_editor'],
            'quality_score' => $data['quality_score'],
            'originality_score' => $data['originality_score'],
            'submitted_at' => date('Y-m-d H:i:s')
        ]);

        // Alan editörüne bildirim gönder
        $this->notifyEditor($review['article_id']);

        return $this->success(['message' => 'Değerlendirme gönderildi']);
    }
}
```

### Faz 5: Güncelleme Sistemi (2 Hafta)

**UpdateManager.php**
```php
class UpdateManager {
    private $updateServer = 'https://updates.amds.com/api';

    public function checkUpdates($tenantId) {
        $tenant = Tenant::find($tenantId);
        $currentVersion = $tenant->core_version;

        // Merkezi sunucudan güncellemeleri kontrol et
        $response = $this->apiCall('/check-updates', [
            'current_version' => $currentVersion,
            'tenant_id' => $tenantId
        ]);

        if ($response['updates_available']) {
            return [
                'available' => true,
                'updates' => $response['updates'],
                'changelog' => $response['changelog']
            ];
        }

        return ['available' => false];
    }

    public function installUpdate($updateId, $tenantId) {
        $update = $this->getUpdateDetails($updateId);

        try {
            // 1. Backup oluştur
            $backupId = $this->createBackup($tenantId);

            // 2. Güncelleme dosyalarını indir
            $downloadPath = $this->downloadUpdate($update);

            // 3. Güncellemeyi doğrula
            if (!$this->verifyUpdate($downloadPath, $update['checksum'])) {
                throw new Exception('Güncelleme dosyası doğrulanamadı');
            }

            // 4. Bakım modunu aç
            $this->enableMaintenanceMode($tenantId);

            // 5. Database migration'ları çalıştır
            if (isset($update['migrations'])) {
                $this->runMigrations($update['migrations'], $tenantId);
            }

            // 6. Dosyaları güncelle
            $this->updateFiles($downloadPath, $tenantId);

            // 7. Versiyon numarasını güncelle
            $this->updateVersion($tenantId, $update['version']);

            // 8. Cache'i temizle
            $this->clearCache($tenantId);

            // 9. Bakım modunu kapat
            $this->disableMaintenanceMode($tenantId);

            // 10. Log kaydet
            $this->logUpdate($tenantId, $updateId, 'success');

            return [
                'success' => true,
                'message' => 'Güncelleme başarıyla yüklendi',
                'new_version' => $update['version']
            ];

        } catch (Exception $e) {
            // Hata durumunda geri al
            $this->rollbackUpdate($backupId, $tenantId);
            $this->logUpdate($tenantId, $updateId, 'failed', $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function createCustomUpdate($tenantId, $data) {
        // Tenant'a özel güncelleme oluştur
        $customUpdateId = DB::insert('custom_updates', [
            'tenant_id' => $tenantId,
            'version' => $this->generateCustomVersion($tenantId),
            'description' => $data['description'],
            'files' => json_encode($data['files']),
            'migrations' => json_encode($data['migrations'] ?? []),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $customUpdateId;
    }
}
```

### Faz 6: Admin Paneli ve Raporlama (2 Hafta)

**DashboardController.php**
```php
class DashboardController extends Controller {
    public function index() {
        $stats = [
            'total_articles' => $this->getTotalArticles(),
            'pending_reviews' => $this->getPendingReviews(),
            'active_reviewers' => $this->getActiveReviewers(),
            'acceptance_rate' => $this->getAcceptanceRate(),
            'average_review_time' => $this->getAverageReviewTime()
        ];

        $recentArticles = $this->getRecentArticles(10);
        $recentActivities = $this->getRecentActivities(20);

        return $this->view('dashboard', [
            'stats' => $stats,
            'articles' => $recentArticles,
            'activities' => $recentActivities
        ]);
    }

    public function statistics() {
        // İstatistikler
        $data = [
            'submissions_by_month' => $this->getSubmissionsByMonth(),
            'acceptance_by_type' => $this->getAcceptanceByType(),
            'reviewer_performance' => $this->getReviewerPerformance(),
            'turnaround_times' => $this->getTurnaroundTimes()
        ];

        return $this->view('statistics', $data);
    }
}
```

---

## 📊 VERİTABANI MİMARİSİ

### ER Diyagramı Özeti

```
USERS (1) ──────< (N) ARTICLE_AUTHORS (N) ────> (1) ARTICLES
                                                         │
                                                         │
                            ┌────────────────────────────┼─────────────────┐
                            │                            │                 │
                            ▼                            ▼                 ▼
                       (N) REVIEWS               (N) FILES         (N) WORKFLOW_STAGES
                            │
                            │
                            ▼
                    (1) USERS (reviewer)
```

### Indeksler ve Optimizasyonlar

```sql
-- Performans için indeksler
CREATE INDEX idx_articles_status ON articles(status);
CREATE INDEX idx_articles_submitted ON articles(submitted_at);
CREATE INDEX idx_reviews_reviewer ON reviews(reviewer_id);
CREATE INDEX idx_reviews_article ON reviews(article_id);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role_id);
CREATE INDEX idx_workflow_article ON workflow_stages(article_id);
CREATE INDEX idx_files_article ON files(article_id);
```

---

## 🚀 GELİŞTİRME AŞAMALARI

### Hafta 1-2: Temel Altyapı
- [ ] Database tasarımı (Core + Tenant şemaları)
- [ ] Core framework yapısı
- [ ] Routing sistemi
- [ ] Tenant yönetim sistemi
- [ ] Multi-database bağlantı yönetimi
- [ ] **WordPress benzeri kurulum sistemi**
- [ ] **Installer sınıfı ve adımları**

### Hafta 3-4: Kimlik Doğrulama ve Multi-Tenant
- [ ] Kullanıcı kayıt sistemi
- [ ] Login/Logout işlemleri (Tenant + Super Admin)
- [ ] ORCID entegrasyonu
- [ ] Rol ve yetki sistemi
- [ ] Session yönetimi
- [ ] CSRF koruması
- [ ] **Tenant izolasyonu**

### Hafta 5-6: Çok Dilli Sistem
- [ ] **Language Manager sınıfı**
- [ ] **Core dil dosyaları (EN, TR, JA, DE, FR, AR, vb.)**
- [ ] **Tenant özel çeviri sistemi**
- [ ] **Çeviri düzenleme arayüzü**
- [ ] **RTL dil desteği**
- [ ] **Dil içe/dışa aktarma**

### Hafta 7-9: Dinamik Form Yapılandırma
- [ ] **Form Schema Manager**
- [ ] **Form builder arayüzü (drag-drop)**
- [ ] **Dinamik validasyon kuralları**
- [ ] **Dosya yükleme kuralları yapılandırması**
- [ ] **Form preview ve test**
- [ ] **Varsayılan form şablonları**

### Hafta 10-12: Makale Yönetimi
- [ ] Dinamik makale gönderim formu
- [ ] Dosya yükleme sistemi (yapılandırılabilir)
- [ ] Ortak yazar yönetimi
- [ ] Taslak sistemi
- [ ] Makale durum takibi
- [ ] Revizyon yükleme

### Hafta 13-15: Hakem Sistemi
- [ ] Hakem davet sistemi
- [ ] Dinamik değerlendirme formları
- [ ] Hakem havuzu yönetimi
- [ ] Uzmanlık alanı eşleştirme
- [ ] Sertifika sistemi

### Hafta 16-17: İş Akışı Yönetimi
- [ ] Workflow engine
- [ ] Durum geçişleri
- [ ] Otomatik bildirimler (çok dilli)
- [ ] E-posta şablonları (çok dilli)
- [ ] Hatırlatma sistemi

### Hafta 18-19: Güncelleme Sistemi
- [ ] Update Manager (genel güncellemeler)
- [ ] Tenant Update Manager (özel güncellemeler)
- [ ] Version control
- [ ] Backup/restore sistemi
- [ ] Migration runner
- [ ] Rollback mekanizması
- [ ] **Özel güncelleme talep sistemi**

### Hafta 20-22: Süper Admin Paneli
- [ ] **Süper admin dashboard**
- [ ] **Tenant listesi ve detay sayfaları**
- [ ] **Kaynak kullanımı izleme (CPU, RAM, Storage, Bandwidth)**
- [ ] **Kaynak izleme cron job**
- [ ] **Destek talep sistemi (ticket system)**
- [ ] **Özel güncelleme talep yönetimi**
- [ ] **Paket/plan yönetimi**
- [ ] **Faturalama sistemi**
- [ ] **Sistem sorun takibi**
- [ ] **Otomatik uyarılar (%80 kullanım)**

### Hafta 23-24: Tenant Admin Paneli
- [ ] Dashboard ve istatistikler
- [ ] Kullanıcı yönetimi
- [ ] Dergi yapılandırması
- [ ] **Form yapılandırma arayüzü**
- [ ] **Çeviri yönetimi**
- [ ] Sayı yönetimi
- [ ] Raporlama
- [ ] **Destek talep formu**
- [ ] **Özel güncelleme talep formu**

### Hafta 25-26: Tenant Destek ve İletişim
- [ ] **Ticket oluşturma (Email, Phone, WhatsApp)**
- [ ] **Ticket yanıtlama ve takip**
- [ ] **Güncelleme talep formu**
- [ ] **Kaynak kullanım göstergeleri**
- [ ] **Paket yükseltme arayüzü**

### Hafta 27-28: Optimizasyon ve Test
- [ ] Performance tuning
- [ ] Security audit
- [ ] Unit testler
- [ ] Integration testler
- [ ] UI/UX iyileştirmeleri
- [ ] **Çok dil testi**
- [ ] **Multi-tenant izolasyon testi**
- [ ] **Load testing (çoklu tenant)**

### Hafta 29-30: Deployment ve Dokümantasyon
- [ ] Production ortamı hazırlığı
- [ ] **Kurulum dokümantasyonu (WordPress benzeri)**
- [ ] **Süper admin dokümantasyonu**
- [ ] Tenant admin dokümantasyonu
- [ ] Kullanıcı dokümantasyonu (çok dilli)
- [ ] API dokümantasyonu
- [ ] Migration kılavuzu
- [ ] **Form yapılandırma kılavuzu**
- [ ] **Çeviri kılavuzu**

---

## 🔐 GÜVENLİK ÖNLEMLERİ

### 1. Input Validation
```php
class Validator {
    public static function sanitize($data, $rules) {
        $sanitized = [];

        foreach ($rules as $field => $rule) {
            if (!isset($data[$field])) {
                if (strpos($rule, 'required') !== false) {
                    throw new ValidationException("$field is required");
                }
                continue;
            }

            $value = $data[$field];

            // XSS koruması
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

            // SQL Injection koruması (PDO prepared statements ile)
            // Tip kontrolü
            if (strpos($rule, 'email') !== false) {
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    throw new ValidationException("Invalid email format");
                }
            }

            $sanitized[$field] = $value;
        }

        return $sanitized;
    }
}
```

### 2. CSRF Koruması
```php
class CSRF {
    public static function generateToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateToken($token) {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }
}
```

### 3. File Upload Güvenliği
```php
class FileUpload {
    private $allowedTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];

    public function upload($file, $destination) {
        // Dosya türü kontrolü
        if (!in_array($file['type'], $this->allowedTypes)) {
            throw new Exception('Geçersiz dosya türü');
        }

        // Dosya boyutu kontrolü (max 10MB)
        if ($file['size'] > 10 * 1024 * 1024) {
            throw new Exception('Dosya çok büyük');
        }

        // Güvenli dosya adı oluştur
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;

        // Dosyayı taşı
        $path = $destination . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $path)) {
            throw new Exception('Dosya yüklenemedi');
        }

        return $filename;
    }
}
```

---

## 📧 E-POSTA VE BİLDİRİM SİSTEMİ

### E-posta Template Sistemi
```php
class EmailService {
    public function send($to, $templateCode, $variables = []) {
        $template = $this->getTemplate($templateCode);

        // Template variables'ı değiştir
        $subject = $this->replaceVariables($template['subject'], $variables);
        $body = $this->replaceVariables($template['body'], $variables);

        // Mail gönder
        $mail = new PHPMailer();
        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->isHTML(true);

        return $mail->send();
    }

    private function replaceVariables($text, $variables) {
        foreach ($variables as $key => $value) {
            $text = str_replace('{{' . $key . '}}', $value, $text);
        }
        return $text;
    }
}

// Kullanım
$emailService->send(
    'yazar@example.com',
    'submission_confirmation',
    [
        'author_name' => 'Ahmet Yılmaz',
        'article_title' => 'Yapay Zeka ve Eğitim',
        'article_code' => 'ART-2025-0103'
    ]
);
```

---

## 🎨 FRONTEND MİMARİSİ

### Template Sistemi
```php
class View {
    private $layout = 'layouts/main';
    private $data = [];

    public function render($view, $data = []) {
        $this->data = $data;

        // View dosyasını yükle
        $viewPath = __DIR__ . '/views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            throw new Exception("View not found: $view");
        }

        // Layout içinde view'i render et
        extract($this->data);

        ob_start();
        include $viewPath;
        $content = ob_get_clean();

        include __DIR__ . '/views/' . $this->layout . '.php';
    }
}
```

### Modern Frontend Stack (Opsiyonel)
- **Vue.js** veya **Alpine.js** - Reaktif UI
- **Tailwind CSS** - Utility-first CSS (Bootstrap yerine)
- **Chart.js** - Grafikler
- **DataTables** - Tablo yönetimi
- **Axios** - AJAX istekleri

---

## 📝 API TASARIMI

### RESTful API Endpoints

```
# Authentication
POST   /api/auth/login
POST   /api/auth/logout
POST   /api/auth/register
POST   /api/auth/forgot-password
POST   /api/auth/reset-password

# Articles
GET    /api/articles
POST   /api/articles
GET    /api/articles/{id}
PUT    /api/articles/{id}
DELETE /api/articles/{id}
POST   /api/articles/{id}/submit
POST   /api/articles/{id}/upload-file
GET    /api/articles/{id}/files

# Reviews
GET    /api/reviews
POST   /api/reviews
GET    /api/reviews/{id}
PUT    /api/reviews/{id}
POST   /api/reviews/{id}/submit
GET    /api/articles/{articleId}/reviews

# Users
GET    /api/users
POST   /api/users
GET    /api/users/{id}
PUT    /api/users/{id}
DELETE /api/users/{id}

# Dashboard
GET    /api/dashboard/stats
GET    /api/dashboard/recent-articles
GET    /api/dashboard/activities

# Updates
GET    /api/updates/check
POST   /api/updates/install/{id}
GET    /api/updates/history
POST   /api/updates/rollback/{id}
```

### API Response Format
```json
{
  "success": true,
  "data": {
    "id": 123,
    "article_code": "ART-2025-0103",
    "title": "Yapay Zeka ve Eğitim"
  },
  "message": "İşlem başarılı",
  "timestamp": "2025-03-07T10:30:00Z"
}
```

---

## 🧪 TEST STRATEJİSİ

### Unit Tests
```php
use PHPUnit\Framework\TestCase;

class ArticleTest extends TestCase {
    public function testArticleCreation() {
        $article = new Article([
            'title_tr' => 'Test Makale',
            'abstract_tr' => 'Test özet',
            'type' => 'research'
        ]);

        $this->assertEquals('Test Makale', $article->title_tr);
        $this->assertEquals('research', $article->type);
    }

    public function testArticleCodeGeneration() {
        $code = Article::generateCode('TEST', 2025);
        $this->assertMatchesRegularExpression('/^TEST-2025-\d{4}$/', $code);
    }
}
```

### Integration Tests
```php
class ArticleSubmissionTest extends TestCase {
    public function testCompleteSubmissionWorkflow() {
        // 1. Login
        $this->loginAsAuthor();

        // 2. Submit article
        $response = $this->post('/articles/submit', [
            'title_tr' => 'Test Makale',
            'abstract_tr' => 'Test özet'
        ]);

        $this->assertEquals(200, $response->status);

        // 3. Verify database
        $article = Article::where('title_tr', 'Test Makale')->first();
        $this->assertNotNull($article);
        $this->assertEquals('submitted', $article->status);

        // 4. Verify email sent
        $this->assertEmailSent('submission_confirmation');
    }
}
```

---

## 🔧 DEPLOYMENT VE DEVOPS

### Server Gereksinimleri
- **PHP**: 8.0+
- **MySQL**: 8.0+
- **Nginx** veya **Apache**: 2.4+
- **SSL**: Let's Encrypt (ücretsiz)
- **RAM**: Min 4GB (8GB önerilen)
- **Storage**: Min 50GB SSD

### Docker Setup (Opsiyonel)
```dockerfile
# Dockerfile
FROM php:8.1-fpm

# Install extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install

CMD ["php-fpm"]
```

```yaml
# docker-compose.yml
version: '3.8'

services:
  app:
    build: .
    volumes:
      - ./:/var/www/html
    depends_on:
      - db

  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
    volumes:
      - ./:/var/www/html
      - ./nginx.conf:/etc/nginx/nginx.conf
    depends_on:
      - app

  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: secret
      MYSQL_DATABASE: amds_core
    volumes:
      - dbdata:/var/lib/mysql

volumes:
  dbdata:
```

---

## 📋 SONUÇ VE ÖNERİLER

### Öncelikli Özellikler
1. ✅ Temel kimlik doğrulama ve yetkilendirme
2. ✅ Makale gönderimi ve yönetimi
3. ✅ Hakem değerlendirme sistemi
4. ✅ İş akışı yönetimi
5. ✅ Multi-tenant yapı
6. ✅ Güncelleme sistemi

### İkinci Aşama Özellikler
- [ ] Gelişmiş raporlama
- [ ] Export/Import (Excel, CSV)
- [ ] Entegre metin editörü
- [ ] İntihal tarama entegrasyonu
- [ ] DOI oluşturma entegrasyonu
- [ ] CrossRef/ORCID tam entegrasyon
- [ ] Mobil uygulama API'si

### Performans Optimizasyonları
- **Caching**: Redis/Memcached kullanımı
- **CDN**: Statik dosyalar için
- **Database**: Query optimization, indexing
- **Background Jobs**: Queue sistemi (RabbitMQ, Redis Queue)

### Güvenlik Tavsiyeleri
- SSL/TLS şifreleme
- Regular security audits
- Penetration testing
- GDPR/KVKK uyumluluğu
- Regular backups
- Disaster recovery planı

---

## 📞 DESTEK VE DOKÜMANTASYON

### Geliştirici Dokümantasyonu
- API Reference
- Database Schema
- Code Standards
- Contribution Guidelines

### Kullanıcı Dokümantasyonu
- Admin Kılavuzu
- Yazar Kılavuzu
- Hakem Kılavuzu
- Video Eğitimleri

---

**Proje Tahmini Süre**: 16-20 hafta (4-5 ay)
**Tahmini Maliyet**: Geliştirici saatine göre değişkenlik gösterir
**Önerilen Ekip**: 2-3 Full-Stack Developer + 1 UI/UX Designer + 1 QA Tester

---

## 📋 YENİ GEREKSİNİMLER ÖZETİ

### 🌍 1. Tam Çok Dilli Destek
**Amaç**: Dergi yöneticilerinin sistemi herhangi bir dile çevirebilmesi

**Ana Özellikler**:
- Core sistem çevirileri (EN, TR, JA, DE, FR, AR, ES, ZH, RU, vb.)
- Tenant özel çeviri sistemi
- Arayüz, form alanları, bildirimler dahil her şey çevrilebilir
- RTL (Right-to-Left) dil desteği
- Çeviri içe/dışa aktarma
- Fallback dil sistemi

**Teknik Detaylar**:
- JSON tabanlı dil dosyaları
- LanguageManager singleton sınıfı
- Veritabanında tenant_translations tablosu
- `__('key')` helper fonksiyonu

---

### 📝 2. Dinamik Form Yapılandırma
**Amaç**: Dergi yöneticilerinin form alanlarını ve kurallarını özelleştirebilmesi

**Ana Özellikler**:
- Başlık uzunluğu, anahtar kelime sayısı gibi limitleri değiştirme
- Yeni form alanları ekleme/çıkarma
- Dosya yükleme kuralları (tür, boyut, isimlendirme)
- Alan türlerini değiştirme (text, textarea, select, file, vb.)
- Validasyon kuralları yapılandırması
- Form preview ve test

**Teknik Detaylar**:
- form_schemas, form_fields, file_upload_rules tabloları
- FormSchemaManager sınıfı
- Dinamik form render
- Özelleştirilebilir validasyon

---

### 🚀 3. WordPress Benzeri Kurulum
**Amaç**: Kolay kurulum ve subdomain'e otomatik deploy

**Ana Özellikler**:
- `install/` klasörü ile adım adım kurulum
- Sistem gereksinimleri kontrolü
- Veritabanı otomatik oluşturma
- Süper admin hesabı oluşturma
- İlk dergi kurulumu
- Kurulum sonrası kilit dosyası

**Kurulum Adımları**:
1. Sistem gereksinimleri kontrolü
2. Veritabanı yapılandırması
3. Süper admin oluşturma
4. Dergi bilgileri
5. Genel ayarlar
6. Kurulum tamamlandı

**Teknik Detaylar**:
- Installer sınıfı
- Multi-step wizard
- Otomatik config dosyası oluşturma
- Tenant veritabanı otomatik kurulumu

---

### 👑 4. Süper Admin Paneli
**Amaç**: Tüm dergileri merkezi yönetim ve izleme

**Ana Özellikler**:
- **Kaynak İzleme**: CPU, RAM, Storage, Bandwidth kullanımı
- **Dergi Yönetimi**: Tüm tenantları listeleme ve detayları görme
- **Destek Sistemi**: Ticket sistemi (Email, Phone, WhatsApp)
- **Paket Yönetimi**: Starter, Professional, Enterprise planları
- **Faturalama**: Otomatik fatura oluşturma
- **Sistem Sorunları**: Hata ve performans takibi
- **Uyarı Sistemi**: %80 kullanım uyarıları
- **İstatistikler**: Gelir, churn rate, tenant growth

**Otomatik Uyarılar**:
- Storage %80 üzeri → Email + Ticket
- Bandwidth %80 üzeri → Email + Ticket
- Paket yükseltme önerileri

**Teknik Detaylar**:
- core_users tablosu (super_admin rolü)
- resource_usage tablosu (cron job ile güncellenir)
- support_tickets tablosu
- subscription_plans ve tenant_subscriptions
- ResourceMonitor cron job (saatlik)

---

### 🔄 5. Özel Güncelleme Talep Sistemi
**Amaç**: Dergilerin özel geliştirme talebi yapabilmesi

**İki Yönlü İletişim**:

**Yöntem 1: Formdan Talep**
- Dergi yöneticisi form doldurur
- Süper admin panelinde görünür
- Onaylanırsa geliştirme başlar

**Yöntem 2: Doğrudan İletişim**
- Email, Telefon, WhatsApp ile iletişim
- Süper admin manuel ticket oluşturur
- Custom update request'e dönüştürür

**İş Akışı**:
1. Tenant → Custom Update Request oluşturur
2. Super Admin → Talebi görür
3. Super Admin → Maliyet ve süre belirler
4. Tenant → Onay verir
5. Developer → Atanır
6. Status: pending → approved → in_development → testing → completed

**Teknik Detaylar**:
- custom_update_requests tablosu
- support_tickets entegrasyonu
- Maliyet ve süre takibi
- Developer atama sistemi

---

## 📊 SİSTEM MİMARİSİ ÖZETİ

```
┌─────────────────────────────────────────────────────────────┐
│                    SUPER ADMIN PANEL                        │
│  - Tüm Tenant'ları Yönetim                                 │
│  - Kaynak İzleme (CPU, RAM, Storage, Bandwidth)           │
│  - Destek Talepleri                                        │
│  - Özel Güncelleme Yönetimi                               │
│  - Paket & Faturalama                                      │
└─────────────────────────────────────────────────────────────┘
                           │
                           ├─────── Core Database
                           │        - tenants
                           │        - core_users
                           │        - resource_usage
                           │        - support_tickets
                           │        - subscription_plans
                           │        - custom_update_requests
                           │
        ┌──────────────────┼──────────────────┐
        │                  │                  │
  ┌─────▼────┐      ┌─────▼────┐      ┌─────▼────┐
  │ Tenant 1 │      │ Tenant 2 │      │ Tenant N │
  │ (Dergi)  │      │ (Dergi)  │      │ (Dergi)  │
  └──────────┘      └──────────┘      └──────────┘
        │                  │                  │
  DB: amds_t1        DB: amds_t2        DB: amds_tn
  - users            - users            - users
  - articles         - articles         - articles
  - reviews          - reviews          - reviews
  - settings         - settings         - settings
  - form_schemas     - form_schemas     - form_schemas
  - translations     - translations     - translations
```

---

## 🎯 KRİTİK FARKLILIKLAR (Önceki Versiyon vs Yeni Versiyon)

| Özellik | Önceki Tasarım | Yeni Tasarım |
|---------|---------------|--------------|
| **Dil Desteği** | Sadece TR/EN | Sınırsız dil + Tenant özel çeviriler |
| **Form Sistemi** | Statik | Tamamen dinamik ve yapılandırılabilir |
| **Kurulum** | Manuel | WordPress benzeri otomatik kurulum |
| **Yönetim** | Tek dergi odaklı | Multi-tenant SaaS platformu |
| **Destek** | Yok | Ticket sistemi + Özel güncelleme talepleri |
| **Kaynak İzleme** | Yok | Otomatik izleme + Uyarılar |
| **Faturalama** | Yok | Paket sistemi + Otomatik faturalama |
| **Ölçeklenebilirlik** | Düşük | Sınırsız tenant desteği |

---

## 💰 PAKET SİSTEMİ

### Starter Plan - $49.99/ay ($499.99/yıl)
- 50 makale/ay
- 10 GB storage
- 50 aktif kullanıcı
- 50 GB bandwidth
- Email desteği
- Temel istatistikler

### Professional Plan - $149.99/ay ($1,499.99/yıl)
- 200 makale/ay
- 50 GB storage
- 200 aktif kullanıcı
- 200 GB bandwidth
- Öncelikli destek
- Gelişmiş istatistikler
- Özel temalar

### Enterprise Plan - $499.99/ay ($4,999.99/yıl)
- Sınırsız makale
- 500 GB storage
- Sınırsız kullanıcı
- 1 TB bandwidth
- 24/7 destek
- Özel geliştirme
- Dedicated server
- API erişimi

---

## 📈 PROJE TAHMİNLERİ (Güncellenmiş)

**Toplam Süre**: 30 hafta (7.5 ay)

**Önerilen Ekip**:
- 2x Senior Full-Stack Developer
- 1x Frontend Developer (Vue.js/React)
- 1x UI/UX Designer
- 1x DevOps Engineer
- 1x QA Tester
- 1x Technical Writer (Dokümantasyon)

**Teknoloji Stack**:
- **Backend**: PHP 8.1+
- **Database**: MySQL 8.0+
- **Frontend**: Vue.js 3 / Alpine.js + Bootstrap 5 / Tailwind CSS
- **Email**: PHPMailer / SendGrid
- **Cache**: Redis (opsiyonel)
- **Queue**: Laravel Queue / RabbitMQ (opsiyonel)
- **Monitoring**: Custom + (Prometheus/Grafana opsiyonel)

**Sunucu Gereksinimleri** (Production):
- **CPU**: Min 8 cores (16 önerilen)
- **RAM**: Min 16GB (32GB önerilen)
- **Storage**: Min 500GB SSD (1TB önerilen)
- **Bandwidth**: Min 1TB/ay
- **Backup**: Günlük otomatik yedekleme

---

## ✅ SONUÇ VE ÖNERİLER

### Başarı Kriterleri
1. ✅ WordPress benzeri kolay kurulum (5 dakikadan az)
2. ✅ Sınırsız dil desteği ve çeviri esnekliği
3. ✅ Tamamen özelleştirilebilir form sistemi
4. ✅ Gerçek zamanlı kaynak izleme
5. ✅ Etkili destek ve güncelleme talep sistemi
6. ✅ Ölçeklenebilir multi-tenant mimari

### Gelecek Geliştirmeler (v2.0+)
- [ ] AI destekli hakem önerisi
- [ ] Otomatik plagiarism check
- [ ] DOI entegrasyonu (CrossRef)
- [ ] ORCID tam entegrasyon
- [ ] Mobil uygulama
- [ ] Blockchain tabanlı peer review
- [ ] Machine learning ile makale kategorilendirme
- [ ] GraphQL API
- [ ] Microservices mimarisi

### Risk Faktörleri
⚠️ **Yüksek Risk**:
- Multi-tenant veri izolasyonu güvenliği
- Performans (çok sayıda tenant)
- Kaynak izleme doğruluğu

⚠️ **Orta Risk**:
- Çok dilli sistem karmaşıklığı
- Dinamik form validasyonu
- Güncelleme sistemi rollback

⚠️ **Düşük Risk**:
- UI/UX tasarımı
- Email entegrasyonu
- Temel CRUD işlemleri

---

**Proje Durumu**: ✅ Analiz ve Planlama Tamamlandı
**Sonraki Adım**: Geliştirme ekibi oluşturma ve Sprint 1 başlatma

---

*Bu dokümantasyon, AMDS sisteminin modern bir SaaS platformu olarak sıfırdan PHP+MySQL ile yeniden yazılması için kapsamlı bir yol haritası sunmaktadır. Tüm yeni özellikler (çok dilli sistem, dinamik formlar, süper admin paneli, WordPress benzeri kurulum) detaylı olarak planlanmış ve kod örnekleri ile açıklanmıştır.*

**Versiyon**: 2.0
**Son Güncelleme**: 2025-01-28
**Hazırlayan**: Claude (Anthropic) + Proje Sahibi
