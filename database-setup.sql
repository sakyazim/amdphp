-- ============================================
-- AMDS - Veritabanı Kurulum Script
-- Tarih: 2024-12-03
-- Açıklama: Faz 0 - Tüm gerekli tablolar
-- ============================================

-- ============================================
-- 1. DİL DEĞİŞKENLERİ TABLOSU
-- ============================================
CREATE TABLE IF NOT EXISTS `dil_degiskenleri` (
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

-- ============================================
-- 2. DİL PAKETLERİ TABLOSU
-- ============================================
CREATE TABLE IF NOT EXISTS `dil_paketleri` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `paket_adi` VARCHAR(100) NOT NULL COMMENT 'default, academic, medical',
  `dil` VARCHAR(5) NOT NULL,
  `aciklama` TEXT,
  `versiyon` VARCHAR(20) DEFAULT '1.0',
  `dosya_yolu` VARCHAR(255) COMMENT 'JSON dosya yolu',
  `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  UNIQUE KEY `unique_paket_dil` (`paket_adi`, `dil`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. DERGİ AYARLARI TABLOSU
-- ============================================
CREATE TABLE IF NOT EXISTS `dergi_ayarlari` (
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

-- ============================================
-- 4. KULLANICI YAZAR PROFİLLERİ TABLOSU
-- ============================================
CREATE TABLE IF NOT EXISTS `kullanici_yazar_profilleri` (
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
  KEY `idx_email2` (`email2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. MAKALE TASLAKLARI TABLOSU
-- ============================================
CREATE TABLE IF NOT EXISTS `makale_taslaklari` (
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
  KEY `durum` (`durum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. MAKALE YAZARLARI TABLOSUNA ALAN EKLE
-- ============================================
-- Not: Bu ALTER TABLE sadece tablo varsa çalışır
-- Eğer tablo yoksa hata verecektir, bu normaldir

ALTER TABLE `makale_yazarlari`
ADD COLUMN IF NOT EXISTS `orcid` VARCHAR(100) AFTER `kurum`,
ADD COLUMN IF NOT EXISTS `orcid_verified` TINYINT(1) DEFAULT 0 AFTER `orcid`,
ADD COLUMN IF NOT EXISTS `orcid_data` JSON AFTER `orcid_verified` COMMENT 'ORCID API response';

-- ============================================
-- 7. MAKALE HAKEM ÖNERİLERİ TABLOSU
-- ============================================
CREATE TABLE IF NOT EXISTS `makale_hakem_onerileri` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `makale_id` INT UNSIGNED NOT NULL,
  `ad` VARCHAR(100) NOT NULL,
  `soyad` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `kurum` VARCHAR(255) NOT NULL,
  `uzmanlik_alani` VARCHAR(255),
  `ulke` VARCHAR(100),
  `orcid` VARCHAR(100),
  `hakem_turu` ENUM('ana','yedek','dis') DEFAULT 'ana',
  `sira` TINYINT UNSIGNED DEFAULT 0,
  `notlar` TEXT COMMENT 'Yazar notu (neden bu hakemi önerdi)',
  `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  KEY `idx_makale` (`makale_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 8. MAKALE DOSYALARI TABLOSU
-- ============================================
CREATE TABLE IF NOT EXISTS `makale_dosyalari` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `makale_id` INT UNSIGNED NOT NULL,
  `kullanici_id` INT UNSIGNED NOT NULL,
  `dosya_turu` ENUM('manuscript','form','supplement') NOT NULL,
  `orijinal_ad` VARCHAR(255) NOT NULL,
  `guvenli_ad` VARCHAR(255) NOT NULL,
  `dosya_yolu` VARCHAR(500) NOT NULL,
  `dosya_boyutu` INT UNSIGNED NOT NULL COMMENT 'Byte cinsinden',
  `mime_type` VARCHAR(100),
  `aciklama` VARCHAR(500),
  `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  KEY `idx_makale` (`makale_id`),
  KEY `idx_kullanici` (`kullanici_id`),
  KEY `idx_dosya_turu` (`dosya_turu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 9. MAKALELER TABLOSUNA EDITÖRE NOT ALANI EKLE
-- ============================================
-- Not: Bu ALTER TABLE sadece tablo varsa çalışır

ALTER TABLE `makaleler`
ADD COLUMN IF NOT EXISTS `editore_notu` TEXT AFTER `anahtar_kelimeler_en`;

-- ============================================
-- VARSAYILAN VERİLER
-- ============================================

-- Makale Türleri
INSERT INTO dergi_ayarlari (tenant_id, ayar_grubu, ayar_anahtari, ayar_degeri_tr, ayar_degeri_en, sira, aktif) VALUES
(1, 'makale_turleri', 'arastirma', 'Araştırma Makalesi', 'Research Article', 1, 1),
(1, 'makale_turleri', 'derleme', 'Derleme Makale', 'Review Article', 2, 1),
(1, 'makale_turleri', 'olgu_sunumu', 'Olgu Sunumu', 'Case Report', 3, 1),
(1, 'makale_turleri', 'teknik_not', 'Teknik Not', 'Technical Note', 4, 1),
(1, 'makale_turleri', 'editore_mektup', 'Editöre Mektup', 'Letter to Editor', 5, 1),
(1, 'makale_turleri', 'kisa_bildiri', 'Kısa Bildiri', 'Short Communication', 6, 1)
ON DUPLICATE KEY UPDATE ayar_degeri_tr = VALUES(ayar_degeri_tr);

-- Makale Konuları
INSERT INTO dergi_ayarlari (tenant_id, ayar_grubu, ayar_anahtari, ayar_degeri_tr, ayar_degeri_en, sira, aktif) VALUES
(1, 'makale_konulari', 'bilgisayar', 'Bilgisayar Bilimleri', 'Computer Science', 1, 1),
(1, 'makale_konulari', 'muhendislik', 'Mühendislik', 'Engineering', 2, 1),
(1, 'makale_konulari', 'tip', 'Tıp Bilimleri', 'Medical Sciences', 3, 1),
(1, 'makale_konulari', 'sosyal', 'Sosyal Bilimler', 'Social Sciences', 4, 1),
(1, 'makale_konulari', 'egitim', 'Eğitim Bilimleri', 'Educational Sciences', 5, 1),
(1, 'makale_konulari', 'sanat', 'Sanat ve Beşeri Bilimler', 'Arts and Humanities', 6, 1)
ON DUPLICATE KEY UPDATE ayar_degeri_tr = VALUES(ayar_degeri_tr);

-- Makale Dilleri
INSERT INTO dergi_ayarlari (tenant_id, ayar_grubu, ayar_anahtari, ayar_degeri_tr, ayar_degeri_en, sira, aktif) VALUES
(1, 'makale_dilleri', 'tr', 'Türkçe', 'Turkish', 1, 1),
(1, 'makale_dilleri', 'en', 'İngilizce', 'English', 2, 1),
(1, 'makale_dilleri', 'de', 'Almanca', 'German', 3, 1),
(1, 'makale_dilleri', 'fr', 'Fransızca', 'French', 4, 1)
ON DUPLICATE KEY UPDATE ayar_degeri_tr = VALUES(ayar_degeri_tr);

-- ============================================
-- KURULUM TAMAMLANDI
-- ============================================

SELECT 'Veritabanı kurulumu tamamlandı!' AS Sonuc;
