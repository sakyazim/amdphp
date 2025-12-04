# FAZ 0: PLANLAMA VE HAZIRLIK

**Durum**: 🟢 Tamamlandı
**Tahmini Süre**: 2-3 saat
**Öncelik**: 🔥 Kritik

---

## 🎯 AMAÇ

Tüm geliştirme fazları için gerekli altyapıyı hazırlamak:
- Veritabanı tablolarını oluşturmak
- Klasör yapısını düzenlemek
- Gereksinimleri netleştirmek

---

## ✅ GÖREVLER

### 0.1 - Tüm Faz MD Dosyalarını Oluştur

**Süre**: 15 dakika

- [x] FAZ-1-DIL-SISTEMI.md
- [x] FAZ-2-YAZAR-MODULU.md
- [x] FAZ-3-REFERANS-SISTEMI.md
- [x] FAZ-4-TASLAK-SISTEMI.md
- [x] FAZ-5-HAKEM-MODULU.md
- [x] FAZ-6-DOSYA-YUKLEME.md
- [x] FAZ-7-EDITORE-NOT.md
- [x] FAZ-8-KONTROL-LISTESI.md

---

### 0.2 - Veritabanı Tablolarını Oluştur

**Süre**: 30 dakika

#### 0.2.1 - `dil_degiskenleri` Tablosu

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

- [ ] SQL'i çalıştır
- [ ] Tabloyu phpMyAdmin'de kontrol et

---

#### 0.2.2 - `dil_paketleri` Tablosu

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

- [ ] SQL'i çalıştır
- [ ] Tabloyu phpMyAdmin'de kontrol et

---

#### 0.2.3 - `dergi_ayarlari` Tablosu

```sql
CREATE TABLE `dergi_ayarlari` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` INT UNSIGNED NOT NULL,
  `ayar_grubu` VARCHAR(50) NOT NULL COMMENT 'makale_turleri, makale_konulari, makale_dilleri',
  `ayar_anahtari` VARCHAR(100) NOT NULL COMMENT 'arastirma, derleme, olgu_sunumu',
  `ayar_degeri_tr` VARCHAR(255) NOT NULL,
  `ayar_degeri_en` VARCHAR(255),
  `sira` INT DEFAULT 0,
  `aktif` TINYINT(1) DEFAULT 1,
  `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  UNIQUE KEY `unique_ayar` (`tenant_id`, `ayar_grubu`, `ayar_anahtari`),
  KEY `idx_grup` (`ayar_grubu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

- [ ] SQL'i çalıştır
- [ ] Tabloyu phpMyAdmin'de kontrol et

---

#### 0.2.4 - `kullanici_yazar_profilleri` Tablosu

```sql
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

- [ ] SQL'i çalıştır
- [ ] Tabloyu phpMyAdmin'de kontrol et

---

#### 0.2.5 - `makale_taslaklari` Tablosu

```sql
CREATE TABLE `makale_taslaklari` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `kullanici_id` INT UNSIGNED NOT NULL,
  `makale_id` INT UNSIGNED DEFAULT NULL COMMENT 'Tamamlandığında ilişkilendirilecek',
  `taslak_adi` VARCHAR(255) DEFAULT NULL,
  `son_adim` TINYINT UNSIGNED DEFAULT 0,
  `taslak_verisi` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`taslak_verisi`)),
  `durum` ENUM('taslak','tamamlandi','iptal') DEFAULT 'taslak',
  `toplam_adim` TINYINT UNSIGNED DEFAULT 13,
  `son_guncelleme` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `olusturma_tarihi` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  KEY `kullanici_id` (`kullanici_id`),
  KEY `makale_id` (`makale_id`),
  KEY `durum` (`durum`),

  FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`makale_id`) REFERENCES `makaleler` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

- [ ] SQL'i çalıştır
- [ ] Tabloyu phpMyAdmin'de kontrol et

---

#### 0.2.6 - `makale_yazarlari` Tablosunu Güncelle

```sql
ALTER TABLE `makale_yazarlari`
ADD COLUMN `orcid` VARCHAR(100) AFTER `kurum`,
ADD COLUMN `orcid_verified` TINYINT(1) DEFAULT 0 AFTER `orcid`,
ADD COLUMN `orcid_data` JSON AFTER `orcid_verified` COMMENT 'ORCID API response';
```

- [ ] SQL'i çalıştır
- [ ] Tabloyu phpMyAdmin'de kontrol et

---

### 0.3 - Klasör Yapısını Oluştur

**Süre**: 10 dakika

```
amdsphp/
├── config/
│   └── languages/           ← YENİ
│       ├── tr/
│       │   ├── create_article.json
│       │   ├── author.json
│       │   └── common.json
│       └── en/
│           ├── create_article.json
│           ├── author.json
│           └── common.json
├── app/
│   └── Services/            ← YENİ
│       ├── LanguageService.php
│       ├── OrcidService.php
│       └── DergiAyarlariService.php
├── storage/                 ← YENİ
│   ├── manuscripts/
│   ├── forms/
│   ├── supplements/
│   └── temp/
└── public/
    └── assets/
        └── js/
            ├── language-helper.js    ← YENİ
            ├── author-search.js      ← YENİ
            └── taslak-sistemi.js     ← YENİ
```

**Görevler:**

- [x] `config/languages/tr/` klasörünü oluştur
- [x] `config/languages/en/` klasörünü oluştur
- [x] `app/Services/` klasörünü oluştur
- [x] `storage/manuscripts/` klasörünü oluştur
- [x] `storage/forms/` klasörünü oluştur
- [x] `storage/supplements/` klasörünü oluştur
- [x] `storage/temp/` klasörünü oluştur
- [x] Storage klasörlerine `.htaccess` ekle (doğrudan erişimi engelle)

**.htaccess içeriği (storage klasörleri için):**

```apache
# storage/.htaccess
Deny from all
```

- [x] `.htaccess` dosyasını oluştur

---

### 0.4 - Dergi Ayarlarını Tanımla

**Süre**: 30 dakika

#### 0.4.1 - Varsayılan Makale Türlerini Ekle

```sql
INSERT INTO dergi_ayarlari (tenant_id, ayar_grubu, ayar_anahtari, ayar_degeri_tr, ayar_degeri_en, sira, aktif) VALUES
(1, 'makale_turleri', 'arastirma', 'Araştırma Makalesi', 'Research Article', 1, 1),
(1, 'makale_turleri', 'derleme', 'Derleme Makale', 'Review Article', 2, 1),
(1, 'makale_turleri', 'olgu_sunumu', 'Olgu Sunumu', 'Case Report', 3, 1),
(1, 'makale_turleri', 'teknik_not', 'Teknik Not', 'Technical Note', 4, 1),
(1, 'makale_turleri', 'editore_mektup', 'Editöre Mektup', 'Letter to Editor', 5, 1),
(1, 'makale_turleri', 'kisa_bildiri', 'Kısa Bildiri', 'Short Communication', 6, 1);
```

- [ ] SQL'i çalıştır
- [ ] Verileri kontrol et

---

#### 0.4.2 - Varsayılan Makale Konularını Ekle

```sql
INSERT INTO dergi_ayarlari (tenant_id, ayar_grubu, ayar_anahtari, ayar_degeri_tr, ayar_degeri_en, sira, aktif) VALUES
(1, 'makale_konulari', 'bilgisayar', 'Bilgisayar Bilimleri', 'Computer Science', 1, 1),
(1, 'makale_konulari', 'muhendislik', 'Mühendislik', 'Engineering', 2, 1),
(1, 'makale_konulari', 'tip', 'Tıp Bilimleri', 'Medical Sciences', 3, 1),
(1, 'makale_konulari', 'sosyal', 'Sosyal Bilimler', 'Social Sciences', 4, 1),
(1, 'makale_konulari', 'egitim', 'Eğitim Bilimleri', 'Educational Sciences', 5, 1),
(1, 'makale_konulari', 'sanat', 'Sanat ve Beşeri Bilimler', 'Arts and Humanities', 6, 1);
```

- [ ] SQL'i çalıştır
- [ ] Verileri kontrol et

---

#### 0.4.3 - Varsayılan Makale Dillerini Ekle

```sql
INSERT INTO dergi_ayarlari (tenant_id, ayar_grubu, ayar_anahtari, ayar_degeri_tr, ayar_degeri_en, sira, aktif) VALUES
(1, 'makale_dilleri', 'tr', 'Türkçe', 'Turkish', 1, 1),
(1, 'makale_dilleri', 'en', 'İngilizce', 'English', 2, 1),
(1, 'makale_dilleri', 'de', 'Almanca', 'German', 3, 1),
(1, 'makale_dilleri', 'fr', 'Fransızca', 'French', 4, 1);
```

- [ ] SQL'i çalıştır
- [ ] Verileri kontrol et

---

### 0.5 - Hakem Formu Gereksinimlerini Belirle

**Süre**: 15 dakika

**Lütfen aşağıdaki soruları cevaplayın:**

#### Sorular:

1. **Email/ORCID arama olsun mu?**
   - [ ] Evet (Yazar modülü gibi)
   - [ ] Hayır (Sadece manuel giriş)

2. **Minimum kaç hakem zorunlu?**
   - [ ] 3 hakem
   - [ ] 5 hakem
   - [ ] Diğer: ___ hakem

3. **Hakem türleri:**
   - [ ] Ana Hakem (Primary Reviewer)
   - [ ] Yedek Hakem (Alternate Reviewer)
   - [ ] Dış Hakem (External Reviewer)
   - [ ] Diğer: ___

4. **Hakem için zorunlu alanlar:**
   - [ ] Ad/Soyad
   - [ ] Email
   - [ ] ORCID (zorunlu mu?)
   - [ ] Kurum
   - [ ] Uzmanlık Alanı
   - [ ] Diğer: ___

5. **Çıkar çatışması kontrolü?**
   - [ ] Evet (Hakem-Yazar aynı kurumdan mı kontrol edilsin?)
   - [ ] Hayır

**Notlarınız:**

```
[Buraya hakem formu için özel isteklerinizi yazın]
```

---

### 0.6 - Dosya Yükleme Gereksinimlerini Belirle

**Süre**: 15 dakika

**Lütfen aşağıdaki soruları cevaplayın:**

#### Sorular:

1. **Zorunlu dosyalar:**
   - [ ] Tam Metin (PDF)
   - [ ] Yayın Hakkı Devir Formu
   - [ ] Etik Kurul Onay Belgesi
   - [ ] Diğer: ___

2. **Opsiyonel dosyalar:**
   - [ ] Yazar Katkı Formu
   - [ ] ICMJE COI Form
   - [ ] İThenticate Raporu
   - [ ] Ek Dosyalar (Veri setleri, grafikler)
   - [ ] Şekiller/Görseller (ayrı yükleme)
   - [ ] Diğer: ___

3. **Dosya boyutu limitleri:**
   - [ ] Tam Metin: ___ MB
   - [ ] Formlar: ___ MB
   - [ ] Ek Dosyalar: ___ MB
   - [ ] Görseller: ___ MB

4. **İzin verilen formatlar:**
   - **Tam Metin:**
     - [ ] PDF
     - [ ] DOC/DOCX
   - **Görseller:**
     - [ ] JPG/JPEG
     - [ ] PNG
     - [ ] TIFF
   - **Veri Setleri:**
     - [ ] CSV
     - [ ] XLSX
     - [ ] ZIP

5. **Dosya adlandırma:**
   - [ ] Otomatik (sistem oluşturur: `makale-123-tam-metin.pdf`)
   - [ ] Orijinal dosya adını koru
   - [ ] Karma (orijinal adı kaydet ama güvenli ad ile sakla)

**Notlarınız:**

```
[Buraya dosya yükleme için özel isteklerinizi yazın]
```

---

## 🎉 FAZ 0 TAMAMLANDI MI?

Tüm checkboxlar işaretlendiyse:

- [ ] **Faz 0 tamamlandı!**
- [ ] [CHECKLIST-MASTER.md](CHECKLIST-MASTER.md) dosyasında Faz 0'ı tamamlandı olarak işaretle
- [ ] Faz 1'e geç: [FAZ-1-DIL-SISTEMI.md](FAZ-1-DIL-SISTEMI.md)

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
**Durum**: 🟢 Tamamlandı

---

## 📝 TAMAMLANAN İŞLER

### ✅ Oluşturulan Dosyalar
- FAZ-1-DIL-SISTEMI.md
- FAZ-2-YAZAR-MODULU.md
- FAZ-3-REFERANS-SISTEMI.md
- FAZ-4-TASLAK-SISTEMI.md
- FAZ-5-HAKEM-MODULU.md
- FAZ-6-DOSYA-YUKLEME.md
- FAZ-7-EDITORE-NOT.md
- FAZ-8-KONTROL-LISTESI.md
- database-setup.sql
- KURULUM-REHBERI.md

### ✅ Oluşturulan Klasörler
- config/languages/tr/
- config/languages/en/
- app/Services/
- storage/manuscripts/
- storage/forms/
- storage/supplements/
- storage/temp/
- storage/.htaccess (güvenlik)

### ✅ Veritabanı Script Hazır
- Tüm tablolar tanımlandı
- Varsayılan veriler eklendi
- Kullanıma hazır

### ⏳ Sıradaki Adım
1. **Manuel**: database-setup.sql dosyasını phpMyAdmin'de çalıştır
2. **Claude**: "Faz 1'i başlat" komutunu ver
