# 🌍 ÇOK DİLLİ SİSTEM MİMARİSİ

**Tarih**: 2024-12-03
**Durum**: Genişletilebilir Tasarım

---

## 🎯 TASARIM İLKELERİ

### 1. Genişletilebilirlik
✅ TR ve EN ile başla
✅ 3., 4., 5. dil kolayca eklenebilir
✅ Kod değişikliği minimum

### 2. Unicode Desteği
✅ UTF-8mb4 (tüm karakterler: Çince, Arapça, Japonca, Kril, Emoji)
✅ Veritabanı collation: `utf8mb4_unicode_ci`
✅ PHP: `mb_string` fonksiyonları

### 3. Dil Kodu Standardı
✅ ISO 639-1 (2 harf): tr, en, ja, ar, ru, zh
✅ Bölgesel varyantlar: en-US, en-GB, zh-CN, zh-TW

---

## 📊 VERİTABANI YAPISI

### Mevcut Tasarım (Genişletilebilir)

```sql
CREATE TABLE `dil_degiskenleri` (
  `dil` VARCHAR(5) NOT NULL COMMENT 'tr, en, ja, ar, ru, zh',
  -- 5 karakter = 2 harf dil kodu + '-' + 2 harf bölge kodu
  `deger` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**✅ Desteklenen Diller:**
- `tr` - Türkçe
- `en` - English
- `ja` - 日本語 (Japonca)
- `ar` - العربية (Arapça)
- `ru` - Русский (Rusça)
- `zh` - 中文 (Çince)
- `de` - Deutsch (Almanca)
- `fr` - Français (Fransızca)
- `ko` - 한국어 (Korece)
- `hi` - हिन्दी (Hintçe)

---

## 🏗️ DOSYA YAPISI

### Klasör Yapısı (Genişletilebilir)

```
config/languages/
├── tr/                     # Türkçe
│   ├── create_article.json
│   ├── author.json
│   └── common.json
├── en/                     # English
│   ├── create_article.json
│   ├── author.json
│   └── common.json
├── ja/                     # 日本語 (Gelecek)
│   ├── create_article.json
│   ├── author.json
│   └── common.json
├── ar/                     # العربية (Gelecek)
│   ├── create_article.json
│   ├── author.json
│   └── common.json
└── config.json             # Dil ayarları (YENİ!)
```

### Dil Yapılandırma Dosyası

**config/languages/config.json:**

```json
{
  "available_languages": [
    {
      "code": "tr",
      "name": "Türkçe",
      "native_name": "Türkçe",
      "direction": "ltr",
      "enabled": true,
      "default": true
    },
    {
      "code": "en",
      "name": "English",
      "native_name": "English",
      "direction": "ltr",
      "enabled": true,
      "default": false
    },
    {
      "code": "ar",
      "name": "Arabic",
      "native_name": "العربية",
      "direction": "rtl",
      "enabled": false,
      "default": false
    },
    {
      "code": "ja",
      "name": "Japanese",
      "native_name": "日本語",
      "direction": "ltr",
      "enabled": false,
      "default": false
    }
  ],
  "fallback_language": "en"
}
```

---

## 💻 BACKEND: LanguageService.php

### Genişletilebilir Tasarım

```php
<?php

namespace App\Services;

class LanguageService
{
    private $db;
    private $tenantId;
    private $currentLang;
    private $availableLanguages = [];
    private $fallbackLang = 'en';
    private $cache = [];

    public function __construct($db, $tenantId = 1, $lang = null)
    {
        $this->db = $db;
        $this->tenantId = $tenantId;

        // Dil yapılandırmasını yükle
        $this->loadLanguageConfig();

        // Mevcut dili belirle
        $this->currentLang = $this->detectLanguage($lang);
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
                'tr' => ['code' => 'tr', 'name' => 'Türkçe', 'direction' => 'ltr'],
                'en' => ['code' => 'en', 'name' => 'English', 'direction' => 'ltr']
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
        if (isset($_SESSION['language']) && isset($this->availableLanguages[$_SESSION['language']])) {
            return $_SESSION['language'];
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
            $code = strtolower(substr($lang, 0, 2));
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
            return $result['deger'];
        }

        // Fallback: Başka dilde var mı?
        if ($lang !== $this->fallbackLang) {
            return $this->get($key, $this->fallbackLang);
        }

        // Hiçbir yerde yok, key'i döndür
        return $key;
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
     */
    public function setLanguage($lang)
    {
        if (!isset($this->availableLanguages[$lang])) {
            return false;
        }

        $this->currentLang = $lang;
        $_SESSION['language'] = $lang;
        setcookie('language', $lang, time() + (365 * 24 * 60 * 60), '/'); // 1 yıl

        return true;
    }

    /**
     * RTL (Right-to-Left) dil mi kontrol et
     */
    public function isRTL($lang = null)
    {
        $lang = $lang ?? $this->currentLang;
        return isset($this->availableLanguages[$lang])
            && $this->availableLanguages[$lang]['direction'] === 'rtl';
    }
}
```

---

## 🎨 FRONTEND: Dil Seçici

### HTML (Genişletilebilir Dropdown)

```html
<!-- Navbar'da dil seçici -->
<div class="language-switcher dropdown">
    <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
            type="button"
            id="languageDropdown"
            data-toggle="dropdown">
        <i class="fa fa-globe"></i>
        <span id="current-lang-name">Türkçe</span>
    </button>
    <div class="dropdown-menu" id="language-menu">
        <!-- Dinamik olarak doldurulacak -->
    </div>
</div>
```

### JavaScript (Dinamik Dil Listesi)

```javascript
class LanguageSwitcher {
    constructor() {
        this.currentLang = document.documentElement.lang || 'tr';
        this.availableLanguages = [];
    }

    async init() {
        // Mevcut dilleri API'den al
        await this.loadAvailableLanguages();

        // Dropdown'u doldur
        this.renderLanguageMenu();

        // RTL desteği
        this.applyDirection();
    }

    async loadAvailableLanguages() {
        const response = await fetch('/api/languages/available');
        const data = await response.json();

        if (data.success) {
            this.availableLanguages = data.languages;
        }
    }

    renderLanguageMenu() {
        const menu = document.getElementById('language-menu');

        this.availableLanguages.forEach(lang => {
            const item = document.createElement('a');
            item.className = 'dropdown-item';
            item.href = '#';
            item.dataset.lang = lang.code;
            item.innerHTML = `
                <span class="lang-flag">${this.getFlag(lang.code)}</span>
                ${lang.native_name}
                ${lang.code === this.currentLang ? '<i class="fa fa-check float-right"></i>' : ''}
            `;

            item.addEventListener('click', (e) => {
                e.preventDefault();
                this.switchLanguage(lang.code);
            });

            menu.appendChild(item);
        });
    }

    async switchLanguage(langCode) {
        // API'ye istek gönder
        const response = await fetch('/api/languages/switch', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ language: langCode })
        });

        const data = await response.json();

        if (data.success) {
            // Sayfayı yenile
            location.reload();
        }
    }

    applyDirection() {
        const lang = this.availableLanguages.find(l => l.code === this.currentLang);

        if (lang && lang.direction === 'rtl') {
            document.documentElement.dir = 'rtl';
            document.body.classList.add('rtl');
        } else {
            document.documentElement.dir = 'ltr';
            document.body.classList.remove('rtl');
        }
    }

    getFlag(langCode) {
        const flags = {
            'tr': '🇹🇷',
            'en': '🇬🇧',
            'ja': '🇯🇵',
            'ar': '🇸🇦',
            'ru': '🇷🇺',
            'zh': '🇨🇳',
            'de': '🇩🇪',
            'fr': '🇫🇷',
            'ko': '🇰🇷'
        };
        return flags[langCode] || '🌍';
    }
}

// Sayfa yüklendiğinde başlat
document.addEventListener('DOMContentLoaded', () => {
    const languageSwitcher = new LanguageSwitcher();
    languageSwitcher.init();
});
```

### RTL (Right-to-Left) CSS Desteği

```css
/* Arapça, İbranice, Farsça gibi RTL diller için */
body.rtl {
    direction: rtl;
    text-align: right;
}

body.rtl .navbar {
    flex-direction: row-reverse;
}

body.rtl .form-group label {
    text-align: right;
}

body.rtl .btn {
    float: left;
}
```

---

## 📝 YENİ DİL EKLEME REHBERİ

### Örnek: Japonca (ja) Ekleme

#### Adım 1: Klasör Oluştur

```bash
mkdir config/languages/ja
```

#### Adım 2: JSON Dosyalarını Oluştur

**config/languages/ja/common.json:**

```json
{
  "buttons": {
    "save": "保存",
    "cancel": "キャンセル",
    "submit": "送信",
    "delete": "削除"
  },
  "messages": {
    "success": "成功しました",
    "error": "エラーが発生しました"
  }
}
```

#### Adım 3: config.json Güncelle

```json
{
  "available_languages": [
    {
      "code": "ja",
      "name": "Japanese",
      "native_name": "日本語",
      "direction": "ltr",
      "enabled": true,
      "default": false
    }
  ]
}
```

#### Adım 4: Veritabanına Ekle

```sql
INSERT INTO dil_degiskenleri (tenant_id, anahtar, dil, deger, kategori) VALUES
(1, 'form.title', 'ja', '新しい記事の提出', 'form'),
(1, 'button.save', 'ja', '保存', 'button');
```

#### Adım 5: Test Et

```
http://yoursite.com/?lang=ja
```

---

## 🔧 ÖZEL KARAKTERLER VE ENCODING

### UTF-8mb4 Avantajları

✅ **Tüm Unicode karakterleri desteklenir:**
- Çince: 中文
- Japonca: 日本語
- Arapça: العربية
- Kril: Русский
- Emoji: 😊 🎉 ✅

### PHP Ayarları

**php.ini:**

```ini
default_charset = "UTF-8"
mbstring.internal_encoding = UTF-8
mbstring.http_output = UTF-8
```

**HTML:**

```html
<meta charset="UTF-8">
```

**PHP Kodunda:**

```php
// Her zaman mb_* fonksiyonlarını kullan
mb_strlen($text);
mb_substr($text, 0, 10);
mb_strtoupper($text);
```

---

## ✅ SİSTEM HAZIR MI?

### Mevcut Durum

| Özellik | Durum | Açıklama |
|---------|-------|----------|
| UTF-8mb4 Desteği | ✅ | Veritabanı charset doğru |
| Dil Kodu Alanı | ✅ | VARCHAR(5) - 2 harfli kodlar |
| Genişletilebilir Klasör | ✅ | `config/languages/` yapısı |
| Dinamik Dil Yükleme | 🔨 | LanguageService yazılacak |
| RTL Desteği | 🔨 | CSS eklenecek |
| Dil Seçici UI | 🔨 | Dropdown yapılacak |

### 3. Dil Eklemek

**Kolay Adımlar:**

1. Klasör oluştur: `config/languages/ja/`
2. JSON dosyalarını çevir
3. `config.json` güncelle
4. Test et

**Kod değişikliği**: ❌ GEREK YOK!

---

## 🎯 FAZ 1 HEDEF

1. ✅ Genişletilebilir mimari tasarla
2. 🔨 TR ve EN dillerini implement et
3. 🔨 Sistem 3., 4., 5. dile hazır olsun
4. 🔨 Dokümantasyon: "Yeni dil nasıl eklenir?"

---

**Sonuç**: Sistem **tamamen genişletilebilir**! 🚀

Japonca, Arapça, Rusça, Çince... istediğiniz dili ekleyebilirsiniz.

**Son Güncelleme**: 2024-12-03
