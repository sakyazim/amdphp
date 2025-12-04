# FAZ 1: DİL DESTEĞİ SİSTEMİ

**Durum**: ⚪ Bekliyor
**Tahmini Süre**: 2-3 gün
**Öncelik**: 🔥 Kritik
**Bağımlılık**: Faz 0 tamamlanmalı

---

## 🎯 AMAÇ

Çoklu dil desteği altyapısını kurmak:
- Veritabanı tabanlı dil yönetimi
- Dinamik dil değişkenleri
- Dergi yöneticisi için özelleştirme paneli
- TR/EN dil desteği (genişletilebilir)

---

## ✅ GÖREVLER

### 1.1 - Veritabanı Tablolarını Kontrol Et

**Süre**: 10 dakika

- [ ] `dil_degiskenleri` tablosunun oluşturulduğunu kontrol et
- [ ] `dil_paketleri` tablosunun oluşturulduğunu kontrol et
- [ ] Test verisi ekle

**Test SQL:**

```sql
-- Test verisi ekle
INSERT INTO dil_degiskenleri (tenant_id, anahtar, dil, deger, kategori, sayfa) VALUES
(1, 'form.title', 'tr', 'Yeni Makale Başvurusu', 'form', 'create_article'),
(1, 'form.title', 'en', 'New Article Submission', 'form', 'create_article'),
(1, 'form.author.title', 'tr', 'Yazar Bilgileri', 'form', 'create_article'),
(1, 'form.author.title', 'en', 'Author Information', 'form', 'create_article');

-- Test sorgusu
SELECT * FROM dil_degiskenleri WHERE tenant_id = 1 AND dil = 'tr';
```

---

### 1.2 - LanguageService.php Sınıfını Yaz

**Süre**: 1 saat

**Dosya**: `app/Services/LanguageService.php`

**Özellikler:**

- Dil değişkenlerini veritabanından çekme
- Cache mekanizması (dosya tabanlı)
- Fallback sistemi (dil bulunamazsa varsayılan döner)
- Tenant bazlı dil yönetimi

**Kod taslağı:**

```php
<?php

namespace App\Services;

class LanguageService
{
    private $db;
    private $tenantId;
    private $currentLang;
    private $cache = [];

    public function __construct($db, $tenantId = 1, $lang = 'tr')
    {
        $this->db = $db;
        $this->tenantId = $tenantId;
        $this->currentLang = $lang;
    }

    /**
     * Dil değişkenini getir
     * @param string $key Örn: 'form.author.title'
     * @param string|null $lang Dil kodu (null ise mevcut dil)
     * @return string
     */
    public function get($key, $lang = null)
    {
        // Implementasyon...
    }

    /**
     * Tüm dil değişkenlerini getir (sayfa bazlı)
     * @param string $page Sayfa adı
     * @return array
     */
    public function getAll($page)
    {
        // Implementasyon...
    }

    /**
     * Dil değişkenini güncelle/ekle
     */
    public function set($key, $value, $lang = null)
    {
        // Implementasyon...
    }

    /**
     * Cache'i temizle
     */
    public function clearCache()
    {
        // Implementasyon...
    }
}
```

**Görevler:**

- [ ] Dosyayı oluştur
- [ ] `get()` metodunu yaz
- [ ] `getAll()` metodunu yaz
- [ ] `set()` metodunu yaz
- [ ] Cache mekanizmasını ekle
- [ ] Test et

---

### 1.3 - JSON Dil Paketlerini Oluştur (TR)

**Süre**: 2 saat

**Dosyalar:**

- `config/languages/tr/create_article.json`
- `config/languages/tr/author.json`
- `config/languages/tr/common.json`

**create_article.json örneği:**

```json
{
  "page_title": "Yeni Makale Başvurusu",
  "step_labels": {
    "1": "Makale Bilgileri",
    "2": "Yazar Bilgileri",
    "3": "Referanslar",
    "4": "Dosya Yükleme",
    "5": "Önizleme ve Gönder"
  },
  "form": {
    "article_type": "Makale Türü",
    "article_title": "Makale Başlığı",
    "article_title_en": "Makale Başlığı (İngilizce)",
    "abstract": "Özet",
    "abstract_en": "Özet (İngilizce)",
    "keywords": "Anahtar Kelimeler",
    "keywords_en": "Anahtar Kelimeler (İngilizce)"
  },
  "buttons": {
    "next": "İleri",
    "previous": "Geri",
    "save_draft": "Taslak Kaydet",
    "submit": "Gönder"
  },
  "messages": {
    "success": "İşlem başarılı",
    "error": "Bir hata oluştu",
    "draft_saved": "Taslak kaydedildi"
  }
}
```

**Görevler:**

- [ ] `create_article.json` oluştur (tüm form alanları)
- [ ] `author.json` oluştur
- [ ] `common.json` oluştur (genel butonlar, mesajlar)
- [ ] JSON'ları validate et

---

### 1.4 - language-helper.js Yaz

**Süre**: 1 saat

**Dosya**: `public/assets/js/language-helper.js`

**Özellikler:**

- Sayfa yüklendiğinde dil değişkenlerini çek
- DOM elemanlarını otomatik güncelle (`data-lang-key` attribute'u ile)
- Manuel dil değiştirme fonksiyonu
- LocalStorage ile dil tercihini kaydet

**Kod taslağı:**

```javascript
class LanguageHelper {
    constructor(currentLang = 'tr') {
        this.currentLang = currentLang;
        this.translations = {};
    }

    async loadTranslations(page) {
        // JSON dosyalarını yükle
    }

    translate(key, fallback = key) {
        // Çeviriyi döndür
    }

    applyTranslations() {
        // DOM'u güncelle
        document.querySelectorAll('[data-lang-key]').forEach(el => {
            const key = el.getAttribute('data-lang-key');
            el.textContent = this.translate(key);
        });
    }

    switchLanguage(lang) {
        // Dil değiştir ve sayfayı yenile
    }
}
```

**Görevler:**

- [ ] Dosyayı oluştur
- [ ] `loadTranslations()` yaz
- [ ] `translate()` yaz
- [ ] `applyTranslations()` yaz
- [ ] `switchLanguage()` yaz
- [ ] Test et

---

### 1.5 - create.php Dosyasını Dönüştür

**Süre**: 3 saat

**Dosya**: `views/articles/create.php`

**Yapılacaklar:**

Sabit metinleri dil değişkenleri ile değiştir.

**Önce:**
```php
<h1>Yeni Makale Başvurusu</h1>
```

**Sonra:**
```php
<h1 data-lang-key="page_title"><?= $lang->get('form.page_title') ?></h1>
```

**Görevler:**

- [ ] Tüm başlıkları dönüştür
- [ ] Form label'larını dönüştür
- [ ] Butonları dönüştür
- [ ] Placeholder'ları dönüştür
- [ ] Hata mesajlarını dönüştür
- [ ] Test et (TR)

---

### 1.6 - EN Dil Paketini Oluştur

**Süre**: 1 saat

**Dosyalar:**

- `config/languages/en/create_article.json`
- `config/languages/en/author.json`
- `config/languages/en/common.json`

**Görevler:**

- [ ] TR dosyalarını kopyala
- [ ] İngilizce çevirilerini yap
- [ ] Validate et
- [ ] Test et (EN)

---

### 1.7 - Dergi Yöneticisi Özelleştirme Paneli

**Süre**: 2 saat

**Dosya**: `views/admin/language-manager.php`

**Özellikler:**

- Tüm dil değişkenlerini listele
- Değişken düzenle (TR/EN)
- Yeni değişken ekle
- Değişken ara/filtrele

**Basit versiyon** (ileri faz için gelişmiş versiyon):

```php
<form method="POST" action="/admin/language/update">
    <table class="table">
        <thead>
            <tr>
                <th>Anahtar</th>
                <th>Türkçe</th>
                <th>İngilizce</th>
                <th>Kategori</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($variables as $var): ?>
            <tr>
                <td><?= $var['anahtar'] ?></td>
                <td><input type="text" name="tr[<?= $var['anahtar'] ?>]" value="<?= $var['deger_tr'] ?>"></td>
                <td><input type="text" name="en[<?= $var['anahtar'] ?>]" value="<?= $var['deger_en'] ?>"></td>
                <td><?= $var['kategori'] ?></td>
                <td><button type="submit">Kaydet</button></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</form>
```

**Görevler:**

- [ ] `LanguageController.php` oluştur
- [ ] Liste sayfası oluştur
- [ ] Güncelleme fonksiyonu yaz
- [ ] Test et

---

### 1.8 - Test Et (TR/EN Dil Değişimi)

**Süre**: 30 dakika

**Test senaryoları:**

- [ ] Sayfa TR dilinde açılıyor
- [ ] Dil EN'e değiştiriliyor
- [ ] Tüm metinler İngilizce'ye dönüşüyor
- [ ] Dil tercihi LocalStorage'a kaydediliyor
- [ ] Sayfa yenilendiğinde dil tercihi korunuyor
- [ ] Eksik çeviri varsa fallback çalışıyor
- [ ] Dergi yöneticisi özelleştirme paneli çalışıyor

---

## 🎉 FAZ 1 TAMAMLANDI MI?

Tüm checkboxlar işaretlendiyse:

- [ ] **Faz 1 tamamlandı!**
- [ ] [CHECKLIST-MASTER.md](CHECKLIST-MASTER.md) dosyasında Faz 1'i tamamlandı olarak işaretle
- [ ] Faz 2'ye geç: [FAZ-2-YAZAR-MODULU.md](FAZ-2-YAZAR-MODULU.md)

---

## 📝 NOTLAR

**Karşılaşılan Sorunlar:**

```
[Buraya notlarınızı yazın]
```

**Öğrenilen Dersler:**

```
[Buraya notlarınızı yazın]
```

---

**Son Güncelleme**: 2024-12-03
**Durum**: ⚪ Bekliyor
