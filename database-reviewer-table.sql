-- ============================================
-- FAZ 5: HAKEM MODÜLÜ - VERİTABANI TABLOSU
-- ============================================
-- Tarih: 2024-12-04
-- Dosya: database-reviewer-table.sql
-- ============================================

-- Hakem önerileri tablosu
CREATE TABLE IF NOT EXISTS `makale_hakem_onerileri` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `makale_id` INT UNSIGNED NOT NULL COMMENT 'Makale ID (Foreign Key)',
  `ad` VARCHAR(100) NOT NULL COMMENT 'Hakem adı',
  `soyad` VARCHAR(100) NOT NULL COMMENT 'Hakem soyadı',
  `email` VARCHAR(255) NOT NULL COMMENT 'Hakem email',
  `kurum` VARCHAR(255) NOT NULL COMMENT 'Hakem kurumu',
  `uzmanlik_alani` VARCHAR(255) DEFAULT NULL COMMENT 'Uzmanlık alanı',
  `ulke` VARCHAR(100) DEFAULT NULL COMMENT 'Ülke',
  `orcid` VARCHAR(100) DEFAULT NULL COMMENT 'ORCID ID',
  `hakem_turu` ENUM('ana','yedek','dis') DEFAULT 'ana' COMMENT 'Hakem türü',
  `sira` TINYINT UNSIGNED DEFAULT 0 COMMENT 'Sıra numarası',
  `notlar` TEXT DEFAULT NULL COMMENT 'Yazar notu (neden bu hakemi önerdi)',
  `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Oluşturma tarihi',

  KEY `idx_makale` (`makale_id`),
  KEY `idx_email` (`email`),

  CONSTRAINT `fk_hakem_makale`
    FOREIGN KEY (`makale_id`)
    REFERENCES `makaleler` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Makale başvurularında önerilen hakemler';

-- ============================================
-- TEST VERİSİ (Opsiyonel - Geliştirme için)
-- ============================================

-- Test verisi eklemek için (makale_id = 1 olduğunu varsayıyoruz)
-- INSERT INTO makale_hakem_onerileri (makale_id, ad, soyad, email, kurum, uzmanlik_alani, ulke, orcid, sira) VALUES
-- (1, 'Ali', 'Yılmaz', 'ali.yilmaz@example.com', 'İstanbul Teknik Üniversitesi', 'Yapay Zeka', 'Türkiye', '0000-0001-2345-6789', 1),
-- (1, 'Ayşe', 'Demir', 'ayse.demir@example.com', 'Orta Doğu Teknik Üniversitesi', 'Makine Öğrenmesi', 'Türkiye', '0000-0002-3456-7890', 2),
-- (1, 'Mehmet', 'Kaya', 'mehmet.kaya@example.com', 'Hacettepe Üniversitesi', 'Veri Madenciliği', 'Türkiye', '0000-0003-4567-8901', 3);

-- ============================================
-- KONTROL SORGUSU
-- ============================================

-- Tablo yapısını kontrol et
-- DESCRIBE makale_hakem_onerileri;

-- Test verilerini görüntüle
-- SELECT
--   id,
--   makale_id,
--   CONCAT(ad, ' ', soyad) as hakem_adi,
--   email,
--   kurum,
--   uzmanlik_alani,
--   sira,
--   olusturma_tarihi
-- FROM makale_hakem_onerileri
-- ORDER BY makale_id, sira;

-- Makaleye göre hakem sayısı
-- SELECT
--   makale_id,
--   COUNT(*) as hakem_sayisi
-- FROM makale_hakem_onerileri
-- GROUP BY makale_id;
