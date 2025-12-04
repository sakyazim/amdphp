-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 04, 2025 at 01:24 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `amds_tenant_test_dergi`
--

-- --------------------------------------------------------

--
-- Table structure for table `ayarlar`
--

CREATE TABLE `ayarlar` (
  `id` int(10) UNSIGNED NOT NULL,
  `ayar_anahtari` varchar(100) NOT NULL,
  `ayar_degeri` text DEFAULT NULL,
  `ayar_tipi` enum('string','number','boolean','json') DEFAULT 'string',
  `kategori` varchar(50) DEFAULT NULL COMMENT 'genel, eposta, makale, vb.',
  `aciklama` text DEFAULT NULL,
  `guncelleme_tarihi` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tenant ozel ayarlar';

--
-- Dumping data for table `ayarlar`
--

INSERT INTO `ayarlar` (`id`, `ayar_anahtari`, `ayar_degeri`, `ayar_tipi`, `kategori`, `aciklama`, `guncelleme_tarihi`) VALUES
(1, 'dergi_adi_tr', 'Test Akademik Dergi', 'string', 'genel', NULL, '2025-11-28 21:15:32'),
(2, 'dergi_adi_en', 'Test Academic Journal', 'string', 'genel', NULL, '2025-11-28 21:15:32'),
(3, 'makale_basligi_min_uzunluk', '10', 'number', 'makale', NULL, '2025-11-28 21:15:32'),
(4, 'makale_basligi_max_uzunluk', '500', 'number', 'makale', NULL, '2025-11-28 21:15:32'),
(5, 'anahtar_kelime_min', '3', 'number', 'makale', NULL, '2025-11-28 21:15:32'),
(6, 'anahtar_kelime_max', '10', 'number', 'makale', NULL, '2025-11-28 21:15:32'),
(7, 'hakem_degerlendirme_suresi_gun', '14', 'number', 'degerlendirme', NULL, '2025-11-28 21:15:32'),
(8, 'max_dosya_boyutu_mb', '10', 'number', 'dosya', NULL, '2025-11-28 21:15:32');

-- --------------------------------------------------------

--
-- Table structure for table `bildirimler`
--

CREATE TABLE `bildirimler` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kullanici_id` int(10) UNSIGNED NOT NULL,
  `bildirim_tipi` varchar(50) NOT NULL COMMENT 'yeni_makale, degerlendirme_tamamlandi, vb.',
  `baslik` varchar(255) NOT NULL,
  `mesaj` text NOT NULL,
  `link` varchar(500) DEFAULT NULL COMMENT 'Bildirimin yonlendirdigi URL',
  `okundu_mu` tinyint(1) DEFAULT 0,
  `okunma_tarihi` timestamp NULL DEFAULT NULL,
  `olusturma_tarihi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Kullanici bildirimleri';

-- --------------------------------------------------------

--
-- Table structure for table `dergi_ayarlari`
--

CREATE TABLE `dergi_ayarlari` (
  `id` int(10) UNSIGNED NOT NULL,
  `tenant_id` int(10) UNSIGNED NOT NULL,
  `ayar_grubu` varchar(50) NOT NULL COMMENT 'makale_turleri, makale_konulari, makale_dilleri',
  `ayar_anahtari` varchar(100) NOT NULL COMMENT 'arastirma, derleme, olgu_sunumu',
  `ayar_degeri_tr` varchar(255) NOT NULL,
  `ayar_degeri_en` varchar(255) DEFAULT NULL,
  `sira` int(11) DEFAULT 0,
  `aktif` tinyint(1) DEFAULT 1,
  `olusturma_tarihi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dergi_sayilari`
--

CREATE TABLE `dergi_sayilari` (
  `id` int(10) UNSIGNED NOT NULL,
  `cilt` tinyint(3) UNSIGNED NOT NULL COMMENT 'Volume',
  `sayi` tinyint(3) UNSIGNED NOT NULL COMMENT 'Issue number',
  `yil` smallint(5) UNSIGNED NOT NULL,
  `yayin_tarihi` date DEFAULT NULL,
  `durum` enum('planning','in_progress','published') DEFAULT 'planning',
  `kapak_resmi` varchar(255) DEFAULT NULL,
  `olusturma_tarihi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Dergi cilt ve sayilari';

--
-- Dumping data for table `dergi_sayilari`
--

INSERT INTO `dergi_sayilari` (`id`, `cilt`, `sayi`, `yil`, `yayin_tarihi`, `durum`, `kapak_resmi`, `olusturma_tarihi`) VALUES
(1, 1, 1, 2025, NULL, 'in_progress', NULL, '2025-11-28 21:15:32');

-- --------------------------------------------------------

--
-- Table structure for table `dil_degiskenleri`
--

CREATE TABLE `dil_degiskenleri` (
  `id` int(10) UNSIGNED NOT NULL,
  `tenant_id` int(10) UNSIGNED NOT NULL,
  `anahtar` varchar(100) NOT NULL COMMENT 'Örn: form.author.title',
  `dil` varchar(5) NOT NULL COMMENT 'tr, en, de, fr',
  `deger` text NOT NULL COMMENT 'Çevrilmiş değer',
  `kategori` varchar(50) DEFAULT NULL COMMENT 'form, table, button, message',
  `sayfa` varchar(100) DEFAULT NULL COMMENT 'create_article, author_list',
  `varsayilan` text DEFAULT NULL COMMENT 'Sistem varsayılanı',
  `olusturma_tarihi` timestamp NOT NULL DEFAULT current_timestamp(),
  `guncelleme_tarihi` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dil_paketleri`
--

CREATE TABLE `dil_paketleri` (
  `id` int(10) UNSIGNED NOT NULL,
  `paket_adi` varchar(100) NOT NULL COMMENT 'default, academic, medical',
  `dil` varchar(5) NOT NULL,
  `aciklama` text DEFAULT NULL,
  `versiyon` varchar(20) DEFAULT '1.0',
  `dosya_yolu` varchar(255) DEFAULT NULL COMMENT 'JSON dosya yolu',
  `olusturma_tarihi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dosyalar`
--

CREATE TABLE `dosyalar` (
  `id` int(10) UNSIGNED NOT NULL,
  `makale_id` int(10) UNSIGNED NOT NULL,
  `dosya_turu` enum('manuscript','cover_letter','supplement','ethics_form','revision','final') NOT NULL,
  `orijinal_dosya_adi` varchar(255) NOT NULL,
  `kaydedilen_dosya_adi` varchar(255) NOT NULL COMMENT 'Sunucudaki benzersiz isim',
  `dosya_yolu` varchar(500) NOT NULL,
  `dosya_boyutu` int(10) UNSIGNED NOT NULL COMMENT 'Byte cinsinden',
  `mime_tipi` varchar(100) NOT NULL,
  `versiyon` tinyint(3) UNSIGNED DEFAULT 1,
  `yukleyen_kullanici_id` int(10) UNSIGNED NOT NULL,
  `yukleme_tarihi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Makale dosyalari';

-- --------------------------------------------------------

--
-- Table structure for table `eposta_loglari`
--

CREATE TABLE `eposta_loglari` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `alici_email` varchar(255) NOT NULL,
  `alici_kullanici_id` int(10) UNSIGNED DEFAULT NULL,
  `sablon_kodu` varchar(100) DEFAULT NULL,
  `konu` varchar(500) NOT NULL,
  `icerik` text NOT NULL,
  `durum` enum('sent','failed','pending') DEFAULT 'pending',
  `hata_mesaji` text DEFAULT NULL,
  `gonderim_tarihi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Gonderilen e-posta kayitlari';

-- --------------------------------------------------------

--
-- Table structure for table `eposta_sablonlari`
--

CREATE TABLE `eposta_sablonlari` (
  `id` int(10) UNSIGNED NOT NULL,
  `sablon_kodu` varchar(100) NOT NULL COMMENT 'makale_kabul, hakem_davet, vb.',
  `sablon_adi` varchar(255) NOT NULL,
  `konu_tr` varchar(500) NOT NULL,
  `konu_en` varchar(500) DEFAULT NULL,
  `icerik_tr` text NOT NULL,
  `icerik_en` text DEFAULT NULL,
  `degiskenler` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Kullanilabilir degiskenler: {makale_kodu}, {yazar_adi}, vb.' CHECK (json_valid(`degiskenler`)),
  `aktif_mi` tinyint(1) DEFAULT 1,
  `olusturma_tarihi` timestamp NOT NULL DEFAULT current_timestamp(),
  `guncelleme_tarihi` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='E-posta sablonlari';

--
-- Dumping data for table `eposta_sablonlari`
--

INSERT INTO `eposta_sablonlari` (`id`, `sablon_kodu`, `sablon_adi`, `konu_tr`, `konu_en`, `icerik_tr`, `icerik_en`, `degiskenler`, `aktif_mi`, `olusturma_tarihi`, `guncelleme_tarihi`) VALUES
(1, 'makale_alindi', 'Makale AlÄ±ndÄ± OnayÄ±', 'Makaleniz AlÄ±ndÄ± - {makale_kodu}', 'Your Manuscript Received - {makale_kodu}', 'SayÄ±n {yazar_adi},\n\n\"{makale_baslik}\" baÅŸlÄ±klÄ± makaleniz ({makale_kodu}) dergimize baÅŸarÄ±yla gÃ¶nderilmiÅŸtir.\n\nTeÅŸekkÃ¼rler.', 'Dear {yazar_adi},\n\nYour manuscript titled \"{makale_baslik}\" ({makale_kodu}) has been successfully submitted.\n\nBest regards.', '[\"makale_kodu\", \"yazar_adi\", \"makale_baslik\"]', 1, '2025-11-28 21:15:32', '2025-11-28 21:15:32'),
(2, 'hakem_davet', 'Hakem DeÄŸerlendirme Daveti', 'DeÄŸerlendirme Daveti - {makale_kodu}', 'Review Invitation - {makale_kodu}', 'SayÄ±n {hakem_adi},\n\n\"{makale_baslik}\" baÅŸlÄ±klÄ± makaleyi deÄŸerlendirmenizi rica ediyoruz.\n\nSaygÄ±larÄ±mÄ±zla.', 'Dear {hakem_adi},\n\nWe would like to invite you to review the manuscript titled \"{makale_baslik}\".\n\nBest regards.', '[\"hakem_adi\", \"makale_baslik\", \"makale_kodu\"]', 1, '2025-11-28 21:15:32', '2025-11-28 21:15:32');

-- --------------------------------------------------------

--
-- Table structure for table `hakem_degerlendirmeleri`
--

CREATE TABLE `hakem_degerlendirmeleri` (
  `id` int(10) UNSIGNED NOT NULL,
  `makale_id` int(10) UNSIGNED NOT NULL,
  `hakem_kullanici_id` int(10) UNSIGNED NOT NULL,
  `davet_durumu` enum('pending','accepted','declined') DEFAULT 'pending',
  `degerlendirme_durumu` enum('not_started','in_progress','completed') DEFAULT 'not_started',
  `oneri` enum('kabul','kucuk_revizyon','buyuk_revizyon','ret') DEFAULT NULL,
  `yazar_yorumlari` text DEFAULT NULL COMMENT 'Yazara gosterilecek',
  `editor_yorumlari` text DEFAULT NULL COMMENT 'Sadece editore',
  `kalite_puani` tinyint(3) UNSIGNED DEFAULT NULL COMMENT '1-10',
  `ozgunluk_puani` tinyint(3) UNSIGNED DEFAULT NULL COMMENT '1-10',
  `metodoloji_puani` tinyint(3) UNSIGNED DEFAULT NULL COMMENT '1-10',
  `davet_tarihi` timestamp NOT NULL DEFAULT current_timestamp(),
  `davet_cevap_tarihi` timestamp NULL DEFAULT NULL,
  `degerlendirme_teslim_tarihi` timestamp NULL DEFAULT NULL,
  `teslim_suresi_gun` tinyint(3) UNSIGNED DEFAULT 14 COMMENT 'Kac gun icinde teslim edilmeli'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Hakem degerlendirme kayitlari';

-- --------------------------------------------------------

--
-- Table structure for table `is_akisi_asamalari`
--

CREATE TABLE `is_akisi_asamalari` (
  `id` int(10) UNSIGNED NOT NULL,
  `makale_id` int(10) UNSIGNED NOT NULL,
  `asama_kodu` varchar(100) NOT NULL COMMENT 'on_kontrol, hakem_atama, vb.',
  `asama_adi_tr` varchar(255) NOT NULL,
  `asama_adi_en` varchar(255) DEFAULT NULL,
  `durum` enum('waiting','in_progress','completed','skipped') DEFAULT 'waiting',
  `atanan_kullanici_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Bu asamadan sorumlu kisi',
  `baslangic_tarihi` timestamp NULL DEFAULT NULL,
  `bitis_tarihi` timestamp NULL DEFAULT NULL,
  `notlar` text DEFAULT NULL,
  `olusturma_tarihi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Makale is akisi takibi';

-- --------------------------------------------------------

--
-- Table structure for table `kullanicilar`
--

CREATE TABLE `kullanicilar` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `sifre_hash` varchar(255) NOT NULL,
  `ad` varchar(100) NOT NULL,
  `soyad` varchar(100) NOT NULL,
  `orcid` varchar(50) DEFAULT NULL COMMENT 'ORCID ID',
  `telefon` varchar(50) DEFAULT NULL,
  `kurum` varchar(255) DEFAULT NULL COMMENT 'Calistigi kurum',
  `unvan` varchar(100) DEFAULT NULL COMMENT 'Akademik unvan',
  `uzmanlik_alanlari` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Uzmanlik alanlarÄ±' CHECK (json_valid(`uzmanlik_alanlari`)),
  `dil_tercihi` enum('tr','en') DEFAULT 'tr',
  `durum` enum('active','inactive','suspended') DEFAULT 'active',
  `son_giris_tarihi` timestamp NULL DEFAULT NULL,
  `olusturma_tarihi` timestamp NOT NULL DEFAULT current_timestamp(),
  `guncelleme_tarihi` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sistem kullanicilari';

--
-- Dumping data for table `kullanicilar`
--

INSERT INTO `kullanicilar` (`id`, `email`, `sifre_hash`, `ad`, `soyad`, `orcid`, `telefon`, `kurum`, `unvan`, `uzmanlik_alanlari`, `dil_tercihi`, `durum`, `son_giris_tarihi`, `olusturma_tarihi`, `guncelleme_tarihi`) VALUES
(1, 'yazar1@test.com', '$2y$10$nw.Vh0wSM7eKZtoAPlZ6nOeVpkRQ.Ug/Rvi99A8onDGJ4./rvoIhK', 'Ahmet', 'YÄ±lmaz', NULL, NULL, 'Ä°stanbul Ãœniversitesi', 'Prof. Dr.', NULL, 'tr', 'active', '2025-11-28 22:50:52', '2025-11-28 21:15:32', '2025-11-28 22:50:52'),
(2, 'yazar2@test.com', '$2y$10$nw.Vh0wSM7eKZtoAPlZ6nOeVpkRQ.Ug/Rvi99A8onDGJ4./rvoIhK', 'AyÅŸe', 'Kaya', NULL, NULL, 'Ankara Ãœniversitesi', 'DoÃ§. Dr.', NULL, 'tr', 'active', NULL, '2025-11-28 21:15:32', '2025-11-28 22:50:05'),
(3, 'hakem1@test.com', '$2y$10$nw.Vh0wSM7eKZtoAPlZ6nOeVpkRQ.Ug/Rvi99A8onDGJ4./rvoIhK', 'Mehmet', 'Demir', NULL, NULL, 'Hacettepe Ãœniversitesi', 'Prof. Dr.', NULL, 'tr', 'active', NULL, '2025-11-28 21:15:32', '2025-11-28 22:50:05'),
(4, 'hakem2@test.com', '$2y$10$nw.Vh0wSM7eKZtoAPlZ6nOeVpkRQ.Ug/Rvi99A8onDGJ4./rvoIhK', 'Fatma', 'Ã‡elik', NULL, NULL, 'BoÄŸaziÃ§i Ãœniversitesi', 'DoÃ§. Dr.', NULL, 'en', 'active', NULL, '2025-11-28 21:15:32', '2025-11-28 22:50:05'),
(5, 'editor@test.com', '$2y$10$nw.Vh0wSM7eKZtoAPlZ6nOeVpkRQ.Ug/Rvi99A8onDGJ4./rvoIhK', 'Ali', 'Arslan', NULL, NULL, 'ODTÃœ', 'Prof. Dr.', NULL, 'tr', 'active', NULL, '2025-11-28 21:15:32', '2025-11-28 22:50:05'),
(6, 'sekreter@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Zeynep', 'AydÄ±n', NULL, NULL, 'Test Dergi', 'Uzman', NULL, 'tr', 'active', NULL, '2025-11-28 21:15:32', '2025-11-28 21:15:32'),
(7, 'yonetici@test.com', '$2y$10$nw.Vh0wSM7eKZtoAPlZ6nOeVpkRQ.Ug/Rvi99A8onDGJ4./rvoIhK', 'Can', 'Ã–ztÃ¼rk', NULL, NULL, 'Test Dergi', 'EditÃ¶r', NULL, 'tr', 'active', '2025-12-04 11:39:02', '2025-11-28 21:15:32', '2025-12-04 11:39:02');

-- --------------------------------------------------------

--
-- Table structure for table `kullanici_roller`
--

CREATE TABLE `kullanici_roller` (
  `id` int(10) UNSIGNED NOT NULL,
  `kullanici_id` int(10) UNSIGNED NOT NULL,
  `rol_id` int(10) UNSIGNED NOT NULL,
  `atanma_tarihi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kullanici_roller`
--

INSERT INTO `kullanici_roller` (`id`, `kullanici_id`, `rol_id`, `atanma_tarihi`) VALUES
(1, 1, 1, '2025-11-28 21:15:32'),
(2, 2, 1, '2025-11-28 21:15:32'),
(3, 3, 2, '2025-11-28 21:15:32'),
(4, 4, 2, '2025-11-28 21:15:32'),
(5, 5, 3, '2025-11-28 21:15:32'),
(6, 6, 4, '2025-11-28 21:15:32'),
(7, 7, 5, '2025-11-28 21:15:32');

-- --------------------------------------------------------

--
-- Table structure for table `kullanici_yazar_profilleri`
--

CREATE TABLE `kullanici_yazar_profilleri` (
  `id` int(10) UNSIGNED NOT NULL,
  `kullanici_id` int(10) UNSIGNED NOT NULL,
  `unvan` varchar(50) DEFAULT NULL,
  `telefon` varchar(50) DEFAULT NULL,
  `email2` varchar(255) DEFAULT NULL,
  `departman` varchar(255) DEFAULT NULL,
  `kurum` varchar(255) DEFAULT NULL,
  `ulke` varchar(100) DEFAULT NULL,
  `orcid` varchar(100) DEFAULT NULL,
  `orcid_verified` tinyint(1) DEFAULT 0,
  `orcid_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'ORCID API response cached' CHECK (json_valid(`orcid_data`)),
  `bio` text DEFAULT NULL,
  `web_sitesi` varchar(255) DEFAULT NULL,
  `google_scholar` varchar(255) DEFAULT NULL,
  `scopus_author_id` varchar(100) DEFAULT NULL,
  `olusturma_tarihi` timestamp NOT NULL DEFAULT current_timestamp(),
  `guncelleme_tarihi` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `makaleler`
--

CREATE TABLE `makaleler` (
  `id` int(10) UNSIGNED NOT NULL,
  `makale_kodu` varchar(50) NOT NULL COMMENT 'DERGI-2025-0001',
  `makale_dili` varchar(10) DEFAULT 'tr',
  `baslik_tr` varchar(500) NOT NULL,
  `baslik_en` varchar(500) NOT NULL,
  `ozet_tr` text NOT NULL,
  `ozet_en` text NOT NULL,
  `anahtar_kelimeler_tr` text NOT NULL,
  `anahtar_kelimeler_en` text NOT NULL,
  `referanslar` text DEFAULT NULL,
  `makale_turu` enum('arastirma','derleme','olgu_sunumu','editore_mektup') NOT NULL,
  `makale_konusu` varchar(100) DEFAULT NULL,
  `durum` varchar(50) DEFAULT 'gonderildi' COMMENT 'is_akisi_durumlari tablosundan',
  `mevcut_asamasi` varchar(100) DEFAULT NULL COMMENT 'on_kontrol, hakem_degerlendirme, vb.',
  `gonderi_tarihi` timestamp NOT NULL DEFAULT current_timestamp(),
  `kabul_tarihi` timestamp NULL DEFAULT NULL,
  `yayin_tarihi` timestamp NULL DEFAULT NULL,
  `ret_tarihi` timestamp NULL DEFAULT NULL,
  `ret_nedeni` text DEFAULT NULL,
  `olusturma_tarihi` timestamp NOT NULL DEFAULT current_timestamp(),
  `guncelleme_tarihi` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Gonderilen makaleler';

--
-- Dumping data for table `makaleler`
--

INSERT INTO `makaleler` (`id`, `makale_kodu`, `makale_dili`, `baslik_tr`, `baslik_en`, `ozet_tr`, `ozet_en`, `anahtar_kelimeler_tr`, `anahtar_kelimeler_en`, `referanslar`, `makale_turu`, `makale_konusu`, `durum`, `mevcut_asamasi`, `gonderi_tarihi`, `kabul_tarihi`, `yayin_tarihi`, `ret_tarihi`, `ret_nedeni`, `olusturma_tarihi`, `guncelleme_tarihi`) VALUES
(1, 'TEST-2025-0001', 'tr', 'Yapay Zeka ve EÄŸitim: TÃ¼rkiye\'deki Uygulamalar', 'Artificial Intelligence and Education: Applications in Turkey', 'Bu Ã§alÄ±ÅŸmada yapay zeka teknolojilerinin eÄŸitim alanÄ±ndaki kullanÄ±mÄ± incelenmiÅŸtir. TÃ¼rkiye\'deki Ã¼niversitelerde yapÄ±lan uygulamalar deÄŸerlendirilmiÅŸtir.', 'This study examines the use of artificial intelligence technologies in education. Applications in universities in Turkey have been evaluated.', 'yapay zeka, eÄŸitim, teknoloji, TÃ¼rkiye', 'artificial intelligence, education, technology, Turkey', NULL, 'arastirma', NULL, 'gonderildi', NULL, '2025-11-28 21:15:32', NULL, NULL, NULL, NULL, '2025-11-28 21:15:32', '2025-11-28 21:15:32'),
(2, 'AMDS-2025-5028', 'tr', '{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}', '{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}', '{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}', '{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}{\"error\":true,\"message\":\"Ge\\u00e7ersiz CSRF token\",\"debug\":{\"method\":\"POST\",\"has_post_token\":false,\"has_header_token\":false,\"session_token_exists\":true}}', 'd,d,d,d', 'd,d,d,d', 'Array', 'arastirma', 'biyoloji', 'gonderildi', 'yeni_gonderim', '2025-12-04 11:15:22', NULL, NULL, NULL, NULL, '2025-12-04 11:15:22', '2025-12-04 11:15:22'),
(3, 'AMDS-2025-8391', 'tr', 'Test başlık', 'Test article', 'bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,', 'bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,', 'deneme, test, veri, makale,article', 'sample, trying veri, makale', 'Array', 'derleme', 'kimya', 'gonderildi', 'yeni_gonderim', '2025-12-04 12:01:50', NULL, NULL, NULL, NULL, '2025-12-04 12:01:50', '2025-12-04 12:01:50');

-- --------------------------------------------------------

--
-- Table structure for table `makale_hakem_onerileri`
--

CREATE TABLE `makale_hakem_onerileri` (
  `id` int(10) UNSIGNED NOT NULL,
  `makale_id` int(10) UNSIGNED NOT NULL COMMENT 'Makale ID (Foreign Key)',
  `ad` varchar(100) NOT NULL COMMENT 'Hakem adı',
  `soyad` varchar(100) NOT NULL COMMENT 'Hakem soyadı',
  `email` varchar(255) NOT NULL COMMENT 'Hakem email',
  `kurum` varchar(255) NOT NULL COMMENT 'Hakem kurumu',
  `uzmanlik_alani` varchar(255) DEFAULT NULL COMMENT 'Uzmanlık alanı',
  `ulke` varchar(100) DEFAULT NULL COMMENT 'Ülke',
  `orcid` varchar(100) DEFAULT NULL COMMENT 'ORCID ID',
  `hakem_turu` enum('ana','yedek','dis') DEFAULT 'ana' COMMENT 'Hakem türü',
  `sira` tinyint(3) UNSIGNED DEFAULT 0 COMMENT 'Sıra numarası',
  `notlar` text DEFAULT NULL COMMENT 'Yazar notu (neden bu hakemi önerdi)',
  `olusturma_tarihi` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Oluşturma tarihi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Makale başvurularında önerilen hakemler';

-- --------------------------------------------------------

--
-- Table structure for table `makale_sayi_atama`
--

CREATE TABLE `makale_sayi_atama` (
  `id` int(10) UNSIGNED NOT NULL,
  `makale_id` int(10) UNSIGNED NOT NULL,
  `sayi_id` int(10) UNSIGNED NOT NULL,
  `sayfa_baslangic` smallint(5) UNSIGNED DEFAULT NULL,
  `sayfa_bitis` smallint(5) UNSIGNED DEFAULT NULL,
  `doi` varchar(255) DEFAULT NULL COMMENT 'Digital Object Identifier',
  `atanma_tarihi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `makale_taslaklari`
--

CREATE TABLE `makale_taslaklari` (
  `id` int(10) UNSIGNED NOT NULL,
  `kullanici_id` int(10) UNSIGNED NOT NULL,
  `makale_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Tamamlandığında ilişkilendirilecek',
  `taslak_adi` varchar(255) DEFAULT NULL,
  `son_adim` tinyint(3) UNSIGNED DEFAULT 0,
  `taslak_verisi` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`taslak_verisi`)),
  `durum` enum('taslak','tamamlandi','iptal') DEFAULT 'taslak',
  `toplam_adim` tinyint(3) UNSIGNED DEFAULT 13,
  `son_guncelleme` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `olusturma_tarihi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `makale_taslaklari`
--

INSERT INTO `makale_taslaklari` (`id`, `kullanici_id`, `makale_id`, `taslak_adi`, `son_adim`, `taslak_verisi`, `durum`, `toplam_adim`, `son_guncelleme`, `olusturma_tarihi`) VALUES
(1, 7, NULL, 'Test article', 12, '{\"_csrf_token\":\"4d14ebe8ef686899a68bb1e9e9858e7b96eb88b8514858b106fe8570d7d302ba\",\"current_step\":\"12\",\"makale_dili\":\"tr\",\"makale_turu\":\"derleme\",\"makale_konusu\":\"kimya\",\"baslik_tr\":\"Test başlık\",\"baslik_en\":\"Test article\",\"ozet_tr\":\"bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,\",\"ozet_en\":\"bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,bu bir türkçe özettir,\",\"anahtar_kelimeler_tr\":\"deneme, test, veri, makale,article\",\"anahtar_kelimeler_en\":\"sample, trying veri, makale\",\"referenceMethod\":\"bulk\",\"referanslar\":[\"tekli referans ekleme. sisitemi\",\"\",\"\",\"\"],\"bulk_references\":\"burada toplu ekleme yapıldı.toplu ekleme yaprken tekli de eklendi.\",\"ad\":\"\",\"soyad\":\"\",\"email\":\"\",\"kurum\":\"\",\"uzmanlik_alani\":\"\",\"ulke\":\"\",\"orcid\":\"\",\"notlar\":\"\",\"authors[0]\":\"{\\\"id\\\":1764848837984,\\\"title\\\":\\\"prof\\\",\\\"firstName\\\":\\\"Sinan\\\",\\\"middleName\\\":\\\"ota\\\",\\\"lastName\\\":\\\"Akyazı\\\",\\\"phone\\\":\\\"535 445 7250\\\",\\\"email1\\\":\\\"sakyazi@gmail.com\\\",\\\"email2\\\":\\\"sakyazi@anadolu.edu.tr\\\",\\\"department\\\":\\\"Kütüphane\\\",\\\"institution\\\":\\\"anadolu Üniversitesi\\\",\\\"country\\\":\\\"TR\\\",\\\"orcidId\\\":\\\"0000-0003-1497-3017\\\",\\\"order\\\":1,\\\"type\\\":\\\"primary\\\"}\",\"authors[1]\":\"{\\\"id\\\":1764848905425,\\\"title\\\":\\\"prof\\\",\\\"firstName\\\":\\\"Sinan\\\",\\\"middleName\\\":\\\"ota\\\",\\\"lastName\\\":\\\"Akyazı\\\",\\\"phone\\\":\\\"535 445 7250\\\",\\\"email1\\\":\\\"sakyazi@gmail.com\\\",\\\"email2\\\":\\\"sakyazi@anadolu.edu.tr\\\",\\\"department\\\":\\\"Kütüphane\\\",\\\"institution\\\":\\\"anadolu Üniversitesi\\\",\\\"country\\\":\\\"TR\\\",\\\"orcidId\\\":\\\"0000-0003-1497-3017\\\",\\\"order\\\":2,\\\"type\\\":\\\"primary\\\"}\",\"authors[2]\":\"{\\\"id\\\":1764849035851,\\\"title\\\":\\\"prof\\\",\\\"firstName\\\":\\\"Sinan\\\",\\\"middleName\\\":\\\"ota\\\",\\\"lastName\\\":\\\"Akyazı\\\",\\\"phone\\\":\\\"535 445 7250\\\",\\\"email1\\\":\\\"sakyazi@gmail.com\\\",\\\"email2\\\":\\\"sakyazi@anadolu.edu.tr\\\",\\\"department\\\":\\\"Kütüphane\\\",\\\"institution\\\":\\\"anadolu Üniversitesi\\\",\\\"country\\\":\\\"TR\\\",\\\"orcidId\\\":\\\"0000-0003-1497-3017\\\",\\\"order\\\":1,\\\"type\\\":\\\"contributor\\\"}\",\"reviewers[0]\":\"{\\\"id\\\":1764849210865,\\\"ad\\\":\\\"ALI TUGRA\\\",\\\"soyad\\\":\\\"AKYAZI\\\",\\\"email\\\":\\\"bakadevix@gmail.com\\\",\\\"kurum\\\":\\\"hayat üniversitesi\\\",\\\"uzmanlik_alani\\\":\\\"yapay zeka\\\",\\\"ulke\\\":\\\"Türkiye\\\",\\\"orcid\\\":\\\"2156-6484-6598-6954\\\",\\\"notlar\\\":\\\"seni seçtim pikachu\\\"}\",\"reviewers[1]\":\"{\\\"id\\\":1764849240370,\\\"ad\\\":\\\"Mahfuz\\\",\\\"soyad\\\":\\\"Sevinç\\\",\\\"email\\\":\\\"patron@demo.com\\\",\\\"kurum\\\":\\\"vss\\\",\\\"uzmanlik_alani\\\":\\\"müdür\\\",\\\"ulke\\\":\\\"Türkiye\\\",\\\"orcid\\\":\\\"5555-5555-5555-5555\\\",\\\"notlar\\\":\\\"müdür\\\"}\",\"reviewers[2]\":\"{\\\"id\\\":1764849282082,\\\"ad\\\":\\\"recai\\\",\\\"soyad\\\":\\\"Yılmaz\\\",\\\"email\\\":\\\"ryilmaz05@gmail.com\\\",\\\"kurum\\\":\\\"Barbaros\\\",\\\"uzmanlik_alani\\\":\\\"Müdür\\\",\\\"ulke\\\":\\\"\\\",\\\"orcid\\\":\\\"0000-0001-2345-6789\\\",\\\"notlar\\\":\\\"Müdür 2\\\"}\"}', 'taslak', 13, '2025-12-04 12:01:50', '2025-12-03 18:11:01');

-- --------------------------------------------------------

--
-- Table structure for table `makale_yazarlari`
--

CREATE TABLE `makale_yazarlari` (
  `id` int(10) UNSIGNED NOT NULL,
  `makale_id` int(10) UNSIGNED NOT NULL,
  `kullanici_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'NULL ise sisteme kayitli degil',
  `email` varchar(255) NOT NULL COMMENT 'Yazar email (kayitli degilse)',
  `ad` varchar(100) NOT NULL,
  `soyad` varchar(100) NOT NULL,
  `kurum` varchar(255) DEFAULT NULL,
  `orcid` varchar(100) DEFAULT NULL,
  `yazar_sirasi` tinyint(3) UNSIGNED NOT NULL COMMENT '1, 2, 3...',
  `sorumlu_yazar_mi` tinyint(1) DEFAULT 0,
  `katkÄ±_orani` decimal(5,2) DEFAULT NULL COMMENT 'Yuzde olarak',
  `olusturma_tarihi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Makale yazarlari';

--
-- Dumping data for table `makale_yazarlari`
--

INSERT INTO `makale_yazarlari` (`id`, `makale_id`, `kullanici_id`, `email`, `ad`, `soyad`, `kurum`, `orcid`, `yazar_sirasi`, `sorumlu_yazar_mi`, `katkÄ±_orani`, `olusturma_tarihi`) VALUES
(1, 1, 1, 'yazar1@test.com', 'Ahmet', 'YÄ±lmaz', 'Ä°stanbul Ãœniversitesi', NULL, 1, 1, NULL, '2025-11-28 21:15:32'),
(2, 1, 2, 'yazar2@test.com', 'AyÅŸe', 'Kaya', 'Ankara Ãœniversitesi', NULL, 2, 0, NULL, '2025-11-28 21:15:32');

-- --------------------------------------------------------

--
-- Table structure for table `roller`
--

CREATE TABLE `roller` (
  `id` int(10) UNSIGNED NOT NULL,
  `rol_kodu` varchar(50) NOT NULL COMMENT 'yazar, hakem, alan_editoru, vb.',
  `rol_adi_tr` varchar(100) NOT NULL,
  `rol_adi_en` varchar(100) NOT NULL,
  `yetkiler` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Rol yetkileri' CHECK (json_valid(`yetkiler`)),
  `aciklama` text DEFAULT NULL,
  `olusturma_tarihi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Kullanici rolleri';

--
-- Dumping data for table `roller`
--

INSERT INTO `roller` (`id`, `rol_kodu`, `rol_adi_tr`, `rol_adi_en`, `yetkiler`, `aciklama`, `olusturma_tarihi`) VALUES
(1, 'yazar', 'Yazar', 'Author', '[\"makale_gonder\", \"makale_goruntule\", \"revizyon_yukle\"]', 'Makale gonderebilen kullanici', '2025-11-28 21:15:32'),
(2, 'hakem', 'Hakem', 'Reviewer', '[\"degerlendirme_yap\", \"davet_cevapla\"]', 'Makale degerlendiren hakem', '2025-11-28 21:15:32'),
(3, 'alan_editoru', 'Alan EditÃ¶rÃ¼', 'Section Editor', '[\"hakem_ata\", \"degerlendirme_gor\", \"karar_ver\"]', 'Belli bir alanin editoru', '2025-11-28 21:15:32'),
(4, 'sekreter', 'Sekreter', 'Journal Secretary', '[\"on_kontrol\", \"yazisma_yonet\", \"hakem_havuzu\"]', 'Dergi sekreteryasi', '2025-11-28 21:15:32'),
(5, 'dergi_yoneticisi', 'Dergi YÃ¶neticisi', 'Journal Manager', '[\"tam_yetki\", \"ayarlar\", \"kullanici_yonet\"]', 'Dergi yoneticisi', '2025-11-28 21:15:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ayarlar`
--
ALTER TABLE `ayarlar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ayar_anahtari` (`ayar_anahtari`),
  ADD KEY `idx_anahtar` (`ayar_anahtari`),
  ADD KEY `idx_kategori` (`kategori`);

--
-- Indexes for table `bildirimler`
--
ALTER TABLE `bildirimler`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_kullanici` (`kullanici_id`),
  ADD KEY `idx_okundu` (`okundu_mu`),
  ADD KEY `idx_tarih` (`olusturma_tarihi`);

--
-- Indexes for table `dergi_ayarlari`
--
ALTER TABLE `dergi_ayarlari`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_ayar` (`tenant_id`,`ayar_grubu`,`ayar_anahtari`),
  ADD KEY `idx_grup` (`ayar_grubu`);

--
-- Indexes for table `dergi_sayilari`
--
ALTER TABLE `dergi_sayilari`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cilt_sayi` (`cilt`,`sayi`),
  ADD KEY `idx_yil` (`yil`),
  ADD KEY `idx_durum` (`durum`);

--
-- Indexes for table `dil_degiskenleri`
--
ALTER TABLE `dil_degiskenleri`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_dil_anahtar` (`tenant_id`,`anahtar`,`dil`),
  ADD KEY `idx_tenant_dil` (`tenant_id`,`dil`),
  ADD KEY `idx_kategori` (`kategori`),
  ADD KEY `idx_sayfa` (`sayfa`);

--
-- Indexes for table `dil_paketleri`
--
ALTER TABLE `dil_paketleri`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_paket_dil` (`paket_adi`,`dil`);

--
-- Indexes for table `dosyalar`
--
ALTER TABLE `dosyalar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `yukleyen_kullanici_id` (`yukleyen_kullanici_id`),
  ADD KEY `idx_makale` (`makale_id`),
  ADD KEY `idx_tur` (`dosya_turu`);

--
-- Indexes for table `eposta_loglari`
--
ALTER TABLE `eposta_loglari`
  ADD PRIMARY KEY (`id`),
  ADD KEY `alici_kullanici_id` (`alici_kullanici_id`),
  ADD KEY `idx_alici` (`alici_email`),
  ADD KEY `idx_durum` (`durum`),
  ADD KEY `idx_tarih` (`gonderim_tarihi`);

--
-- Indexes for table `eposta_sablonlari`
--
ALTER TABLE `eposta_sablonlari`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sablon_kodu` (`sablon_kodu`),
  ADD KEY `idx_kod` (`sablon_kodu`);

--
-- Indexes for table `hakem_degerlendirmeleri`
--
ALTER TABLE `hakem_degerlendirmeleri`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_makale` (`makale_id`),
  ADD KEY `idx_hakem` (`hakem_kullanici_id`),
  ADD KEY `idx_durum` (`degerlendirme_durumu`);

--
-- Indexes for table `is_akisi_asamalari`
--
ALTER TABLE `is_akisi_asamalari`
  ADD PRIMARY KEY (`id`),
  ADD KEY `atanan_kullanici_id` (`atanan_kullanici_id`),
  ADD KEY `idx_makale` (`makale_id`),
  ADD KEY `idx_durum` (`durum`);

--
-- Indexes for table `kullanicilar`
--
ALTER TABLE `kullanicilar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_durum` (`durum`);

--
-- Indexes for table `kullanici_roller`
--
ALTER TABLE `kullanici_roller`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_kullanici_rol` (`kullanici_id`,`rol_id`),
  ADD KEY `idx_kullanici` (`kullanici_id`),
  ADD KEY `idx_rol` (`rol_id`);

--
-- Indexes for table `kullanici_yazar_profilleri`
--
ALTER TABLE `kullanici_yazar_profilleri`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_kullanici` (`kullanici_id`),
  ADD UNIQUE KEY `unique_orcid` (`orcid`),
  ADD KEY `idx_email2` (`email2`);

--
-- Indexes for table `makaleler`
--
ALTER TABLE `makaleler`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `makale_kodu` (`makale_kodu`),
  ADD KEY `idx_kod` (`makale_kodu`),
  ADD KEY `idx_durum` (`durum`),
  ADD KEY `idx_tarih` (`gonderi_tarihi`);

--
-- Indexes for table `makale_hakem_onerileri`
--
ALTER TABLE `makale_hakem_onerileri`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_makale` (`makale_id`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `makale_sayi_atama`
--
ALTER TABLE `makale_sayi_atama`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_makale_sayi` (`makale_id`,`sayi_id`),
  ADD KEY `sayi_id` (`sayi_id`);

--
-- Indexes for table `makale_taslaklari`
--
ALTER TABLE `makale_taslaklari`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kullanici_id` (`kullanici_id`),
  ADD KEY `makale_id` (`makale_id`),
  ADD KEY `durum` (`durum`);

--
-- Indexes for table `makale_yazarlari`
--
ALTER TABLE `makale_yazarlari`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_makale` (`makale_id`),
  ADD KEY `idx_kullanici` (`kullanici_id`);

--
-- Indexes for table `roller`
--
ALTER TABLE `roller`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rol_kodu` (`rol_kodu`),
  ADD KEY `idx_kod` (`rol_kodu`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ayarlar`
--
ALTER TABLE `ayarlar`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `bildirimler`
--
ALTER TABLE `bildirimler`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dergi_ayarlari`
--
ALTER TABLE `dergi_ayarlari`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dergi_sayilari`
--
ALTER TABLE `dergi_sayilari`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dil_degiskenleri`
--
ALTER TABLE `dil_degiskenleri`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dil_paketleri`
--
ALTER TABLE `dil_paketleri`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dosyalar`
--
ALTER TABLE `dosyalar`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eposta_loglari`
--
ALTER TABLE `eposta_loglari`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eposta_sablonlari`
--
ALTER TABLE `eposta_sablonlari`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `hakem_degerlendirmeleri`
--
ALTER TABLE `hakem_degerlendirmeleri`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `is_akisi_asamalari`
--
ALTER TABLE `is_akisi_asamalari`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kullanicilar`
--
ALTER TABLE `kullanicilar`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `kullanici_roller`
--
ALTER TABLE `kullanici_roller`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `kullanici_yazar_profilleri`
--
ALTER TABLE `kullanici_yazar_profilleri`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `makaleler`
--
ALTER TABLE `makaleler`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `makale_hakem_onerileri`
--
ALTER TABLE `makale_hakem_onerileri`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `makale_sayi_atama`
--
ALTER TABLE `makale_sayi_atama`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `makale_taslaklari`
--
ALTER TABLE `makale_taslaklari`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `makale_yazarlari`
--
ALTER TABLE `makale_yazarlari`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roller`
--
ALTER TABLE `roller`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bildirimler`
--
ALTER TABLE `bildirimler`
  ADD CONSTRAINT `bildirimler_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dosyalar`
--
ALTER TABLE `dosyalar`
  ADD CONSTRAINT `dosyalar_ibfk_1` FOREIGN KEY (`makale_id`) REFERENCES `makaleler` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dosyalar_ibfk_2` FOREIGN KEY (`yukleyen_kullanici_id`) REFERENCES `kullanicilar` (`id`);

--
-- Constraints for table `eposta_loglari`
--
ALTER TABLE `eposta_loglari`
  ADD CONSTRAINT `eposta_loglari_ibfk_1` FOREIGN KEY (`alici_kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hakem_degerlendirmeleri`
--
ALTER TABLE `hakem_degerlendirmeleri`
  ADD CONSTRAINT `hakem_degerlendirmeleri_ibfk_1` FOREIGN KEY (`makale_id`) REFERENCES `makaleler` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hakem_degerlendirmeleri_ibfk_2` FOREIGN KEY (`hakem_kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `is_akisi_asamalari`
--
ALTER TABLE `is_akisi_asamalari`
  ADD CONSTRAINT `is_akisi_asamalari_ibfk_1` FOREIGN KEY (`makale_id`) REFERENCES `makaleler` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `is_akisi_asamalari_ibfk_2` FOREIGN KEY (`atanan_kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `kullanici_roller`
--
ALTER TABLE `kullanici_roller`
  ADD CONSTRAINT `kullanici_roller_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kullanici_roller_ibfk_2` FOREIGN KEY (`rol_id`) REFERENCES `roller` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `makale_hakem_onerileri`
--
ALTER TABLE `makale_hakem_onerileri`
  ADD CONSTRAINT `fk_hakem_makale` FOREIGN KEY (`makale_id`) REFERENCES `makaleler` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `makale_sayi_atama`
--
ALTER TABLE `makale_sayi_atama`
  ADD CONSTRAINT `makale_sayi_atama_ibfk_1` FOREIGN KEY (`makale_id`) REFERENCES `makaleler` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `makale_sayi_atama_ibfk_2` FOREIGN KEY (`sayi_id`) REFERENCES `dergi_sayilari` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `makale_yazarlari`
--
ALTER TABLE `makale_yazarlari`
  ADD CONSTRAINT `makale_yazarlari_ibfk_1` FOREIGN KEY (`makale_id`) REFERENCES `makaleler` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `makale_yazarlari_ibfk_2` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
