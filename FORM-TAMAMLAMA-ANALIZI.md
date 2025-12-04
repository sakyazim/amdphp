# Makale Gönderim Formu Tamamlama - Kapsamlı Analiz ve Plan

**Proje:** AMDS (Akademik Makale Değerlendirme Sistemi)
**Tarih:** 2024-12-04
**Durum:** Planlama Aşaması

---

## 📋 İÇİNDEKİLER

1. [Mevcut Durum Analizi](#1-mevcut-durum-analizi)
2. [Kullanıcı İstekleri (11 Madde)](#2-kullanıcı-istekleri-11-madde)
3. [Veritabanı Analizi ve Düzenlemeler](#3-veritabanı-analizi-ve-düzenlemeler)
4. [Dergi Yönetici Özelleştirme Sistemi](#4-dergi-yönetici-özelleştirme-sistemi)
5. [Uygulama Planı](#5-uygulama-planı)
6. [Ekstra Öneriler](#6-ekstra-öneriler)

---

## 1. MEVCUT DURUM ANALİZİ

### 1.1 Mevcut Form Yapısı (create.php)
**Dosya:** `views/articles/create.php`

#### ✅ Tamamlanmış Özellikler:
- **13 Adımlı Wizard Sistemi**
- **Dil Sistemi:** Çoklu dil desteği aktif
- **Referans Sistemi:** Tek tek ve toplu ekleme
- **Yazar Arama:** Email ve ORCID ile arama (ama form doldurmada sorun var)
- **Hakem Sistemi:** Temel ekleme formu var (ama eksik alanlar var)
- **Taslak Sistemi:** Otomatik kayıt (30 saniye)
- **İlerleme Takibi:** Progress bar ve adım durumları

#### ❌ Eksik/Sorunlu Özellikler:
1. **Yazar Arama:** Kullan butonunda form doldurma çalışmıyor
2. **Yazar Arama Listesi:** CSS kullanıcı dostu değil, scroll yok, tıklandığında kapanıyor
3. **ORCID Linki:** Tıklanabilir link değil
4. **Yazar Güncelleme:** Güncelle dendiğinde yeni kayıt ekliyor
5. **Yazar Sayısı Badge:** Hakem gibi sayı gösterimi yok
6. **Yazar Tip Renkleri:** Farklı yazar tipleri için renk kodlaması yok
7. **Hakem Formu:** old/yazar/yeni-makale.html'deki ek alanlar yok
8. **Hakem Edit:** Güncelleme butonu yok
9. **Editöre Not:** Adım 10 boş (içerik yok)
10. **Kontrol Listesi:** Adım 11 boş (içerik yok)
11. **Dosya Yükleme:** Adım 8 temel, ama old versiyondaki tüm özellikler yok
12. **Makale Özeti:** Son adımda düzenle butonları var ama adım değiştirme eksik

### 1.2 Referans Form (old/yazar/yeni-makale.html)
**Dosya:** `old/yazar/yeni-makale.html`

#### Bu formda olup yeni formda OLMAYAN özellikler:

**HAKEM FORMU:**
- Hakem Rolü Bilgileri (Sıra, Hakem Tipi)
- Ünvan alanı
- İkinci Ad alanı
- Email 2 alanı
- Telefon alanı
- Departman, Kurum, Ülke alanları
- ORCID ID alanı
- Edit butonu (güncelleme)

**DOSYA YÜKLEME:**
- Dosya türü dropdown'u
- Maksimum boyut kontrolü (25MB)
- Progress bar
- Dosya tablosu (Türü, Adı, Boyut, Format, Tarih, İşlemler)
- Format yardım metni
- 9 farklı dosya türü:
  - Tam Metin (fullText)
  - Yayın Hakkı Devir Formu (copyright)
  - Yazar Katkı Formu (authorContribution)
  - ICMJE COI Form (icmjeCoi)
  - iThenticate Formu (iThenticate)
  - Ek Dosya (supplementary)
  - Şekiller (figures)
  - Görseller (images)
  - Benzerlik Raporu (similarity)

**EDİTÖRE NOT:**
- Rich text toolbar (Bold, Italic, Underline, Clear Format)
- Karakter sayacı
- Kaydet butonu

**KONTROL LİSTESİ:**
- 3 kategori:
  1. Makale İçerik Kontrolleri (3 madde)
  2. Yazar ve Hakem Kontrolleri (3 madde)
  3. Dosya Kontrolleri (3 madde)
- Progress bar (9/9 gösterimi)
- "Tümünü İşaretle" butonu
- "Tümünü Temizle" butonu

**MAKALE ÖZETİ (Son Adım):**
- Her bölüm için "Düzenle" butonu (adıma gidiyor)
- 10 bölüm özeti:
  1. Makale Bilgileri
  2. Başlıklar
  3. Özetler
  4. Anahtar Kelimeler
  5. Referanslar
  6. Yazarlar
  7. Dosyalar
  8. Hakemler
  9. Editör Notu
  10. Onay kutusu

---

## 2. KULLANICI İSTEKLERİ (11 MADDE)

### 📌 Madde 1: Yazar Arama - Form Doldurma Sorunu
**Sorun:** Email ve ORCID ile arama yapıldığında liste geliyor ama "Kullan" denildiğinde forma gerekli bilgiler girilmiyor.

**Çözüm:**
- `author-search.js` dosyasındaki `onSelect` callback'ini düzelt
- `fillAuthorForm()` fonksiyonunda ID eşleştirmelerini kontrol et
- API'den gelen veri formatını doğrula
- Form alanlarına değer atama işlemini test et

**Dosyalar:**
- `public/assets/js/author-search.js`
- `views/articles/create.php` (fillAuthorForm fonksiyonu)

---

### 📌 Madde 2: Yazar Arama - Liste CSS İyileştirmesi
**Sorun:** Liste kullanıcı dostu değil, 3-4 sonuç varsa scroll yok, tıklandığında kapanıyor.

**Çözüm:**
- `.author-search-results` CSS'ini iyileştir
- Max-height ve overflow-y: auto ekle
- Liste öğelerine hover efekti
- Tıklanınca kapanma davranışını değiştir
- Daha görsel kart tabanlı liste

**CSS Özellikleri:**
```css
.author-search-results {
  max-height: 300px;
  overflow-y: auto;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  border-radius: 8px;
}

.author-result-item {
  padding: 12px;
  border-bottom: 1px solid #eee;
  cursor: pointer;
  transition: all 0.3s;
}

.author-result-item:hover {
  background: #f8f9fa;
  transform: translateX(5px);
}
```

**Dosyalar:**
- `public/assets/css/author-search.css`

---

### 📌 Madde 3: ORCID Linki Tıklanabilir
**Sorun:** Yazarlar listesinde ORCID linki siteye gidilebilir link değil.

**Çözüm:**
- Yazar listesinde ORCID ID'yi `<a>` tagı ile sar
- Target="_blank" ile yeni sekmede aç
- İkon ekle (ORCID logo veya external link)

**HTML Örneği:**
```html
<a href="https://orcid.org/0000-0001-2345-6789" target="_blank" class="orcid-link">
  <i class="fab fa-orcid"></i> 0000-0001-2345-6789
</a>
```

**Dosyalar:**
- `public/assets/js/authors-management.js` (renderAuthorsTable fonksiyonu)

---

### 📌 Madde 4: Yazar Sayısı Badge
**Sorun:** Hakemler listesinde gösterilen eklenen hakem sayısı gibi yazarlarda da listenin başında kaç yazar olduğunu gösteren sayı yok.

**Çözüm:**
- Step 7 başlığına badge ekle (zaten var: `<span id="authorCount">0 Yazar</span>`)
- Badge'i dinamik güncelle
- Hakem listesi ile aynı stili kullan

**JavaScript:**
```javascript
function updateAuthorCount() {
  const count = authorsArray.length;
  document.getElementById('authorCount').textContent = `${count} Yazar`;
}
```

**Dosyalar:**
- `public/assets/js/authors-management.js`
- `views/articles/create.php` (badge zaten var, sadece güncelleme gerekli)

---

### 📌 Madde 5: Yazar Tipleri İçin Renkli Badge
**Sorun:** Yazar tiplerine ayrı renkler yok.

**Çözüm:**
- Yazar tipi badge'lerine Bootstrap renk sınıfları ekle:
  - `primary` (Birincil Yazar) → Mavi
  - `corresponding` (Sorumlu Yazar) → Yeşil
  - `contributor` (Katkıda Bulunan) → Turuncu

**HTML Örneği:**
```html
<span class="badge bg-primary">Birincil Yazar</span>
<span class="badge bg-success">Sorumlu Yazar</span>
<span class="badge bg-warning">Katkıda Bulunan</span>
```

**Dosyalar:**
- `public/assets/js/authors-management.js`

---

### 📌 Madde 6: Hakem Formu - Ek Alanlar
**Sorun:** old/yazar/yeni-makale.html'deki hakem formundaki alanlar yok.

**Çözüm:**
Şu alanları ekle:
1. **Hakem Rolü Bilgileri:**
   - Sıra (number input)
   - Hakem Tipi (select: Ana Hakem, Yedek Hakem, Dış Hakem)

2. **Kişisel Bilgiler:**
   - Ünvan (select: Prof. Dr., Doç. Dr., vb.)
   - İkinci Ad (text input)

3. **İletişim Bilgileri:**
   - Email 2 (email input)
   - Telefon (tel input)

4. **Kurum Bilgileri:**
   - Departman (text input)
   - Kurum (text input)
   - Ülke (select)

5. **Akademik Kimlik:**
   - ORCID ID (text input with pattern)

**Dosyalar:**
- `views/articles/create.php` (Step 9)
- `public/assets/js/reviewer-manager.js`

**NOT:** Mevcut formda bazı alanlar VAR (ad, soyad, email, kurum, uzmanlik_alani, ulke, orcid, notlar) ama eksik olanlar: ünvan, ikinci_ad, email2, telefon, departman, sira, hakem_tipi

---

### 📌 Madde 7: Hakem Listesi - Edit Butonu
**Sorun:** Hakem listesinde yazarlar listesi gibi edit butonu yok, güncelleme yapılamıyor.

**Çözüm:**
- Hakem tablosuna "Düzenle" butonu ekle
- Düzenle tıklanınca formu doldur
- Submit butonu "Güncelle" olarak değişsin
- İptal butonu göster

**HTML Örneği:**
```html
<button class="btn btn-sm btn-primary" onclick="editReviewer(${id})">
  <i class="fas fa-edit"></i>
</button>
```

**JavaScript Fonksiyonları:**
```javascript
function editReviewer(id) { ... }
function updateReviewer() { ... }
function cancelReviewerEdit() { ... }
```

**Dosyalar:**
- `public/assets/js/reviewer-manager.js`
- `views/articles/create.php` (Step 9)

---

### 📌 Madde 8: Editöre Not Bölümü
**Sorun:** Step 10 boş, içerik yok.

**Çözüm:**
old/yazar/yeni-makale.html'den kopyala:
1. **Bilgi Alert:** Editöre not hakkında açıklama
2. **Rich Text Toolbar:**
   - Bold butonu
   - Italic butonu
   - Underline butonu
   - Clear Format butonu
3. **Textarea:** 10 satır
4. **Karakter Sayacı:** Dinamik güncelleme
5. **Kaydet Butonu**

**Dosyalar:**
- `views/articles/create.php` (Step 10)
- `public/assets/js/editor-note.js` (yeni dosya)

---

### 📌 Madde 9: Kontrol Listesi
**Sorun:** Step 11 boş, içerik yok.

**Çözüm:**
old/yazar/yeni-makale.html'den kopyala:

**3 Kategori, 9 Madde:**
1. **Makale İçerik Kontrolleri:**
   - Başlık, özet, anahtar kelimeler hem TR hem EN
   - Özet 150-250 kelime
   - 3-5 anahtar kelime

2. **Yazar ve Hakem Kontrolleri:**
   - Tüm yazarların ORCID ID'leri var
   - En az 3 hakem önerisi
   - Yazarların kurum bilgileri tam

3. **Dosya Kontrolleri:**
   - Tam metin yüklendi
   - Yayın hakkı devir formu yüklendi
   - ICMJE COI formları yüklendi

**Özellikler:**
- Progress bar (0/9 → 9/9)
- "Tümünü İşaretle" butonu
- "Tümünü Temizle" butonu
- Her checkbox için event listener

**Dosyalar:**
- `views/articles/create.php` (Step 11)
- `public/assets/js/checklist-manager.js` (yeni dosya)

---

### 📌 Madde 10: Dosya Yükleme Sistemi
**Sorun:** Step 8 temel düzeyde, old versiyondaki tüm özellikler yok.

**Çözüm:**
old/yazar/yeni-makale.html'den modern bir dosya yükleme sistemi oluştur:

**Özellikler:**
1. **Dosya Türü Dropdown:**
   - Tam Metin
   - Yayın Hakkı Devir Formu
   - Yazar Katkı Formu
   - ICMJE COI Form
   - iThenticate Formu
   - Ek Dosya
   - Şekiller
   - Görseller
   - Benzerlik Raporu

2. **Dosya Seçimi ve Yükleme:**
   - File input + Yükle butonu
   - Format yardım metni (dinamik)
   - Maksimum boyut kontrolü (25MB)

3. **Progress Bar:**
   - Yükleme sırasında göster
   - Yüzde gösterimi

4. **Dosya Tablosu:**
   - Dosya Türü
   - Dosya Adı
   - Boyut (formatlanmış)
   - Format (extension)
   - Yükleme Tarihi
   - İşlemler (İndir, Sil)

5. **Validasyonlar:**
   - Dosya türüne göre format kontrolü
   - Boyut kontrolü
   - MIME type kontrolü

**Dosyalar:**
- `views/articles/create.php` (Step 8)
- `public/assets/js/file-uploader.js` (yeni dosya)
- `app/Controllers/ArticleController.php` (uploadFile endpoint)

---

### 📌 Madde 11: Makale Özeti - Düzenle Butonları
**Sorun:** Son adımda girilen tüm bilgiler listelensin, her bir adım için düzenle butonları olsun, düzenle dendiğinde o adıma giderek düzenleme yapılabilsin.

**Çözüm:**
old/yazar/yeni-makale.html'deki gibi:

**10 Bölüm:**
1. Makale Bilgileri (Tür, Konu, Dil) → Düzenle (Adım 2)
2. Başlıklar (TR, EN) → Düzenle (Adım 3)
3. Özetler (TR, EN) → Düzenle (Adım 4)
4. Anahtar Kelimeler (TR, EN) → Düzenle (Adım 5)
5. Referanslar → Düzenle (Adım 6)
6. Yazarlar → Düzenle (Adım 7)
7. Dosyalar → Düzenle (Adım 8)
8. Hakemler → Düzenle (Adım 9)
9. Editör Notu → Düzenle (Adım 10)
10. Onay Kutusu

**Her kart için:**
```html
<div class="card mb-4">
  <div class="card-header d-flex justify-content-between">
    <h5>Başlık</h5>
    <button class="btn btn-sm btn-primary" onclick="goToStep(X)">
      <i class="fas fa-edit"></i> Düzenle
    </button>
  </div>
  <div class="card-body">
    <!-- İçerik -->
  </div>
</div>
```

**JavaScript Fonksiyon:**
```javascript
function goToStep(stepNumber) {
  // Adım değiştirme mantığı
  currentStep = stepNumber;
  showStep(stepNumber);
  updateProgress();
}
```

**Dosyalar:**
- `views/articles/create.php` (Step 12)
- `public/assets/js/create-wizard.js` (goToStep fonksiyonu)

---

## 3. VERİTABANI ANALİZİ VE DÜZENLEMELER

### 3.1 Mevcut Veritabanı Yapısı

#### ✅ Var Olan Tablolar:
1. `dil_degiskenleri` - Dil çevirileri
2. `dil_paketleri` - Dil paketleri
3. `dergi_ayarlari` - Dergi özelleştirmeleri
4. `kullanici_yazar_profilleri` - Yazar profilleri (ORCID var)
5. `makale_taslaklari` - Taslak sistemi (JSON)
6. `makale_hakem_onerileri` - Hakem önerileri (temel alanlar var)
7. `makale_dosyalari` - Dosya yükleme

#### ❌ Eksik/Sorunlu Alanlar:

### 3.2 `makaleler` Tablosu - Eklenecek Alanlar

```sql
ALTER TABLE `makaleler`
ADD COLUMN IF NOT EXISTS `makale_dili` VARCHAR(5) DEFAULT 'tr' COMMENT 'tr, en, de, fr' AFTER `id`,
ADD COLUMN IF NOT EXISTS `makale_turu` VARCHAR(50) AFTER `makale_dili`,
ADD COLUMN IF NOT EXISTS `makale_konusu` VARCHAR(50) AFTER `makale_turu`,
ADD COLUMN IF NOT EXISTS `baslik_tr` VARCHAR(500) AFTER `makale_konusu`,
ADD COLUMN IF NOT EXISTS `baslik_en` VARCHAR(500) AFTER `baslik_tr`,
ADD COLUMN IF NOT EXISTS `ozet_tr` TEXT AFTER `baslik_en`,
ADD COLUMN IF NOT EXISTS `ozet_en` TEXT AFTER `ozet_tr`,
ADD COLUMN IF NOT EXISTS `anahtar_kelimeler_tr` VARCHAR(500) AFTER `ozet_en`,
ADD COLUMN IF NOT EXISTS `anahtar_kelimeler_en` VARCHAR(500) AFTER `anahtar_kelimeler_tr`,
ADD COLUMN IF NOT EXISTS `editore_notu` TEXT AFTER `anahtar_kelimeler_en`,
ADD COLUMN IF NOT EXISTS `kontrol_listesi` JSON AFTER `editore_notu` COMMENT 'Kontrol listesi checkbox durumları',
ADD COLUMN IF NOT EXISTS `durum` ENUM('taslak','gonderildi','hakem_degerlendirmesi','revizyon','kabul','red') DEFAULT 'taslak' AFTER `kontrol_listesi`,
ADD COLUMN IF NOT EXISTS `gonderim_tarihi` TIMESTAMP NULL AFTER `durum`,
ADD COLUMN IF NOT EXISTS `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `gonderim_tarihi`,
ADD COLUMN IF NOT EXISTS `guncelleme_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `olusturma_tarihi`;
```

### 3.3 `makale_referanslar` Tablosu - Yeni Tablo

**Sorun:** Şu an referanslar "array" olarak kaydediliyor, bu yanlış.

**Çözüm:**
```sql
CREATE TABLE IF NOT EXISTS `makale_referanslari` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `makale_id` INT UNSIGNED NOT NULL,
  `sira` TINYINT UNSIGNED NOT NULL,
  `referans_metni` TEXT NOT NULL,
  `referans_turu` ENUM('book','article','web','other') DEFAULT 'article',
  `doi` VARCHAR(255),
  `url` VARCHAR(500),
  `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  KEY `idx_makale` (`makale_id`),
  KEY `idx_sira` (`sira`),
  FOREIGN KEY (`makale_id`) REFERENCES `makaleler`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 3.4 `makale_yazarlari` Tablosu - Eklenecek Alanlar

```sql
ALTER TABLE `makale_yazarlari`
ADD COLUMN IF NOT EXISTS `unvan` VARCHAR(50) AFTER `makale_id`,
ADD COLUMN IF NOT EXISTS `ad` VARCHAR(100) AFTER `unvan`,
ADD COLUMN IF NOT EXISTS `ikinci_ad` VARCHAR(100) AFTER `ad`,
ADD COLUMN IF NOT EXISTS `soyad` VARCHAR(100) AFTER `ikinci_ad`,
ADD COLUMN IF NOT EXISTS `email1` VARCHAR(255) AFTER `soyad`,
ADD COLUMN IF NOT EXISTS `email2` VARCHAR(255) AFTER `email1`,
ADD COLUMN IF NOT EXISTS `telefon` VARCHAR(50) AFTER `email2`,
ADD COLUMN IF NOT EXISTS `departman` VARCHAR(255) AFTER `telefon`,
ADD COLUMN IF NOT EXISTS `kurum` VARCHAR(255) AFTER `departman`,
ADD COLUMN IF NOT EXISTS `ulke` VARCHAR(100) AFTER `kurum`,
ADD COLUMN IF NOT EXISTS `orcid` VARCHAR(100) AFTER `ulke`,
ADD COLUMN IF NOT EXISTS `orcid_verified` TINYINT(1) DEFAULT 0 AFTER `orcid`,
ADD COLUMN IF NOT EXISTS `orcid_data` JSON AFTER `orcid_verified`,
ADD COLUMN IF NOT EXISTS `yazar_sirasi` TINYINT UNSIGNED AFTER `orcid_data`,
ADD COLUMN IF NOT EXISTS `yazar_tipi` ENUM('primary','corresponding','contributor') AFTER `yazar_sirasi`,
ADD COLUMN IF NOT EXISTS `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `yazar_tipi`;

-- Index ekle
CREATE INDEX IF NOT EXISTS `idx_makale_yazar` ON `makale_yazarlari` (`makale_id`, `yazar_sirasi`);
CREATE INDEX IF NOT EXISTS `idx_email1` ON `makale_yazarlari` (`email1`);
CREATE INDEX IF NOT EXISTS `idx_orcid` ON `makale_yazarlari` (`orcid`);
```

### 3.5 `makale_hakem_onerileri` Tablosu - Eklenecek Alanlar

```sql
ALTER TABLE `makale_hakem_onerileri`
ADD COLUMN IF NOT EXISTS `unvan` VARCHAR(50) AFTER `makale_id`,
ADD COLUMN IF NOT EXISTS `ikinci_ad` VARCHAR(100) AFTER `ad`,
ADD COLUMN IF NOT EXISTS `email2` VARCHAR(255) AFTER `email`,
ADD COLUMN IF NOT EXISTS `telefon` VARCHAR(50) AFTER `email2`,
ADD COLUMN IF NOT EXISTS `departman` VARCHAR(255) AFTER `telefon`,
MODIFY COLUMN `hakem_turu` ENUM('main','alternate','external') DEFAULT 'main';

-- Sıra alanını zorunlu yap ve index ekle
ALTER TABLE `makale_hakem_onerileri`
MODIFY COLUMN `sira` TINYINT UNSIGNED NOT NULL;

CREATE INDEX IF NOT EXISTS `idx_makale_sira` ON `makale_hakem_onerileri` (`makale_id`, `sira`);
```

### 3.6 `makale_dosyalari` Tablosu - Güncellemeler

```sql
ALTER TABLE `makale_dosyalari`
MODIFY COLUMN `dosya_turu` ENUM(
  'fullText',
  'copyright',
  'authorContribution',
  'icmjeCoi',
  'iThenticate',
  'supplementary',
  'figures',
  'images',
  'similarity'
) NOT NULL,
ADD COLUMN IF NOT EXISTS `dosya_uzantisi` VARCHAR(10) AFTER `mime_type`,
ADD COLUMN IF NOT EXISTS `yukleme_durumu` ENUM('pending','uploading','completed','failed') DEFAULT 'pending' AFTER `dosya_uzantisi`,
ADD COLUMN IF NOT EXISTS `yukleme_tarihi` TIMESTAMP NULL AFTER `yukleme_durumu`,
ADD COLUMN IF NOT EXISTS `hash` VARCHAR(64) AFTER `yukleme_tarihi` COMMENT 'SHA256 hash for integrity';

CREATE INDEX IF NOT EXISTS `idx_makale_dosya_turu` ON `makale_dosyalari` (`makale_id`, `dosya_turu`);
```

### 3.7 Türkçe Karakter Sorunu - Tüm Tablolar

**Sorun:** Bazı tablo/sütun adlarında Türkçe karakter var.

**Düzeltilecek Alan Adları:**
- `makale_hakem_onerileri.uzmanlik_alani` → `uzmanlik_alani` (doğru)
- `makale_hakem_onerileri.ülke` → `ulke` (düzelt)
- `dergi_ayarlari.ayar_anahtarı` → `ayar_anahtari` (düzelt)

```sql
-- Türkçe karakterleri düzelt
ALTER TABLE `makale_hakem_onerileri`
CHANGE COLUMN `ülke` `ulke` VARCHAR(100);

ALTER TABLE `dergi_ayarlari`
CHANGE COLUMN `ayar_anahtarı` `ayar_anahtari` VARCHAR(100);
```

### 3.8 Veritabanı Düzenleme SQL Dosyası

**Yeni Dosya:** `database-form-completion.sql`

```sql
-- ============================================
-- AMDS - Form Tamamlama Veritabanı Düzenlemeleri
-- Tarih: 2024-12-04
-- Açıklama: Makale gönderim formu için eksik alanlar
-- ============================================

-- [Yukarıdaki tüm ALTER TABLE komutları buraya gelecek]
```

---

## 4. DERGİ YÖNETİCİ ÖZELLEŞTİRME SİSTEMİ

### 4.1 Sorun
Kullanıcı sorusu:
> "Her dergi yöneticisi formalarda bazı alanları kaldırmak, yeni alanlar eklemek, dil değiştirme, zorunlu alanların seçimine vs. karar verebilecek, yazar ve hakem sayısını istediği gibi değiştirebilecek. 3 hakem olayını JS'de zorunlu yaptıysak dergi yöneticisine bunu değiştirmek zor olur sanırım."

### 4.2 Öneri: Form Konfigürasyon Sistemi

#### 4.2.1 Veritabanı Tablosu: `form_konfigurasyonlari`

```sql
CREATE TABLE IF NOT EXISTS `form_konfigurasyonlari` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` INT UNSIGNED NOT NULL,
  `form_adi` VARCHAR(100) NOT NULL COMMENT 'makale_gonderim, yazar_formu, hakem_formu',
  `konfigurasyonlar` JSON NOT NULL,
  `versiyon` VARCHAR(20) DEFAULT '1.0',
  `aktif` TINYINT(1) DEFAULT 1,
  `olusturma_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `guncelleme_tarihi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  UNIQUE KEY `unique_form` (`tenant_id`, `form_adi`),
  KEY `idx_tenant` (`tenant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 4.2.2 JSON Konfigürasyon Örneği

```json
{
  "minimum_yazar_sayisi": 1,
  "maksimum_yazar_sayisi": 10,
  "minimum_hakem_sayisi": 3,
  "maksimum_hakem_sayisi": 5,
  "zorunlu_alanlar": {
    "yazar": ["ad", "soyad", "email1", "orcid", "kurum"],
    "hakem": ["ad", "soyad", "email", "kurum"]
  },
  "opsiyonel_alanlar": {
    "yazar": ["ikinci_ad", "email2", "telefon", "departman", "ulke"],
    "hakem": ["unvan", "ikinci_ad", "email2", "telefon", "departman", "ulke", "orcid"]
  },
  "gizli_alanlar": {
    "yazar": [],
    "hakem": ["telefon"]
  },
  "dosya_turleri": {
    "zorunlu": ["fullText", "copyright", "icmjeCoi"],
    "opsiyonel": ["authorContribution", "iThenticate", "supplementary", "figures", "images", "similarity"]
  },
  "maksimum_dosya_boyutu_mb": 25,
  "ozet_kelime_araligi": {
    "min": 150,
    "max": 250
  },
  "anahtar_kelime_araligi": {
    "min": 3,
    "max": 5
  },
  "kontrol_listesi": {
    "aktif": true,
    "maddeler": [
      "check1", "check2", "check3", "check4", "check5", "check6", "check7", "check8", "check9"
    ]
  },
  "editore_not": {
    "aktif": true,
    "zorunlu": false,
    "maksimum_karakter": 2000
  }
}
```

#### 4.2.3 PHP Servis Sınıfı

**Dosya:** `app/Services/FormConfigService.php`

```php
<?php
namespace App\Services;

class FormConfigService {
    private $db;
    private $tenantId;
    private $config;

    public function __construct($db, $tenantId) {
        $this->db = $db;
        $this->tenantId = $tenantId;
        $this->loadConfig('makale_gonderim');
    }

    private function loadConfig($formName) {
        // Veritabanından JSON config'i çek
        // Önbelleğe al
    }

    public function getMinimumYazarSayisi() {
        return $this->config['minimum_yazar_sayisi'] ?? 1;
    }

    public function getMinimumHakemSayisi() {
        return $this->config['minimum_hakem_sayisi'] ?? 3;
    }

    public function isFieldRequired($formType, $fieldName) {
        return in_array($fieldName, $this->config['zorunlu_alanlar'][$formType] ?? []);
    }

    public function isFieldHidden($formType, $fieldName) {
        return in_array($fieldName, $this->config['gizli_alanlar'][$formType] ?? []);
    }

    public function getZorunluDosyaTurleri() {
        return $this->config['dosya_turleri']['zorunlu'] ?? [];
    }

    // ... diğer metodlar
}
```

#### 4.2.4 JavaScript Dinamik Form

**Dosya:** `public/assets/js/form-config-loader.js`

```javascript
class FormConfigLoader {
  constructor(apiUrl) {
    this.apiUrl = apiUrl;
    this.config = null;
  }

  async loadConfig() {
    const response = await fetch(this.apiUrl);
    this.config = await response.json();
    return this.config;
  }

  getMinimumAuthors() {
    return this.config?.minimum_yazar_sayisi || 1;
  }

  getMinimumReviewers() {
    return this.config?.minimum_hakem_sayisi || 3;
  }

  isFieldRequired(formType, fieldName) {
    const required = this.config?.zorunlu_alanlar?.[formType] || [];
    return required.includes(fieldName);
  }

  isFieldHidden(formType, fieldName) {
    const hidden = this.config?.gizli_alanlar?.[formType] || [];
    return hidden.includes(fieldName);
  }

  applyConfigToForm() {
    // Form alanlarını dinamik olarak ayarla
    // required attribute'lerini ekle/kaldır
    // display: none uygula
  }
}

// Kullanım
const configLoader = new FormConfigLoader('/api/form-config/makale_gonderim');
await configLoader.loadConfig();
configLoader.applyConfigToForm();
```

#### 4.2.5 Admin Panel - Form Yapılandırma Sayfası

**Dosya:** `views/admin/form-yapilandirma.php`

Özellikler:
- Drag & drop alan sıralama
- Checkbox ile zorunlu/opsiyonel/gizli seçimi
- Min/max sayı değerleri için input'lar
- Dosya türleri için checkbox listesi
- Önizleme modu
- JSON import/export

---

## 5. UYGULAMA PLANI

### Faz 1: Kritik Düzeltmeler (1 Gün)
**Öncelik: Yüksek**

#### 1.1 Yazar Arama Form Doldurma Sorunu
- [ ] `author-search.js` - onSelect callback düzelt
- [ ] API response formatını kontrol et
- [ ] Form field ID'lerini doğrula
- [ ] Test et (email ve ORCID ile)

#### 1.2 Yazar Arama Liste CSS
- [ ] `author-search.css` - max-height, overflow ekle
- [ ] Hover efektleri
- [ ] Scroll bar stillendirme
- [ ] Responsive tasarım

#### 1.3 Yazar Güncelleme Sorunu
- [ ] `authors-management.js` - editAuthor fonksiyonu
- [ ] updateAuthor fonksiyonu (array'i güncelle, yeni ekleme)
- [ ] Form reset mantığı

---

### Faz 2: Yazar ve Hakem Geliştirmeleri (1 Gün)
**Öncelik: Yüksek**

#### 2.1 Yazar Modülü
- [ ] ORCID linki tıklanabilir yap
- [ ] Yazar sayısı badge'ini dinamik güncelle
- [ ] Yazar tipi renk kodlaması (primary/corresponding/contributor)
- [ ] Yazar listesi görsel iyileştirme

#### 2.2 Hakem Modülü
- [ ] Hakem formuna ek alanlar ekle (ünvan, ikinci_ad, email2, telefon, departman, sira, hakem_tipi)
- [ ] Edit butonu ekle
- [ ] updateReviewer fonksiyonu
- [ ] cancelReviewerEdit fonksiyonu
- [ ] Hakem listesi görsel iyileştirme

---

### Faz 3: Dosya Yükleme Sistemi (1 Gün)
**Öncelik: Orta**

#### 3.1 Frontend
- [ ] `file-uploader.js` oluştur
- [ ] 9 dosya türü dropdown
- [ ] File input + progress bar
- [ ] Dosya tablosu (dynamic)
- [ ] Validasyonlar (boyut, format, MIME)
- [ ] Önizleme (PDF için)

#### 3.2 Backend
- [ ] ArticleController - uploadFile endpoint
- [ ] Dosya güvenlik kontrolü
- [ ] Dosya adı sanitizasyonu
- [ ] SHA256 hash
- [ ] Veritabanı kaydı
- [ ] API response (JSON)

---

### Faz 4: Editöre Not ve Kontrol Listesi (1 Gün)
**Öncelik: Orta**

#### 4.1 Editöre Not (Step 10)
- [ ] Rich text toolbar (bold, italic, underline, clear)
- [ ] Textarea + karakter sayacı
- [ ] Kaydet butonu
- [ ] localStorage backup

#### 4.2 Kontrol Listesi (Step 11)
- [ ] 3 kategori, 9 madde
- [ ] Progress bar (0/9)
- [ ] "Tümünü İşaretle" butonu
- [ ] "Tümünü Temizle" butonu
- [ ] Event listeners
- [ ] Validasyon (son adıma geçmeden önce)

---

### Faz 5: Makale Özeti ve Gönderim (1 Gün)
**Öncelik: Orta**

#### 5.1 Step 12 - Özet Sayfası
- [ ] 10 bölüm kartları
- [ ] Her kart için "Düzenle" butonu
- [ ] goToStep(X) fonksiyonu
- [ ] Özet verilerini dinamik çek
- [ ] Onay checkbox
- [ ] Gönder butonu (aktif/pasif)

#### 5.2 Form Submission
- [ ] Tüm verileri topla (JSON)
- [ ] Validasyon (tüm adımlar)
- [ ] API request (POST /makaleler)
- [ ] Success/error handling
- [ ] Redirect (makale detay sayfası)

---

### Faz 6: Veritabanı Düzenlemeleri (1 Gün)
**Öncelik: Yüksek**

#### 6.1 SQL Script
- [ ] `database-form-completion.sql` oluştur
- [ ] ALTER TABLE komutları (makaleler, makale_yazarlari, makale_hakem_onerileri, makale_dosyalari)
- [ ] CREATE TABLE (makale_referanslari)
- [ ] Türkçe karakter düzeltmeleri
- [ ] Index'leri ekle
- [ ] Test et (local veritabanında)

#### 6.2 Migration Sistemi
- [ ] Migration sınıfı (PHP)
- [ ] Version tracking
- [ ] Rollback özelliği

---

### Faz 7: Form Konfigürasyon Sistemi (2 Gün)
**Öncelik: Düşük (ileride yapılabilir)**

#### 7.1 Veritabanı
- [ ] `form_konfigurasyonlari` tablosu
- [ ] Varsayılan JSON config'leri ekle

#### 7.2 Backend
- [ ] FormConfigService.php
- [ ] API endpoint (GET /api/form-config/:formName)
- [ ] Cache mekanizması

#### 7.3 Frontend
- [ ] form-config-loader.js
- [ ] Dinamik form rendering
- [ ] Zorunlu/opsiyonel alan yönetimi

#### 7.4 Admin Panel
- [ ] Form yapılandırma sayfası
- [ ] Drag & drop interface
- [ ] Önizleme modu
- [ ] JSON import/export

---

### Faz 8: Test ve Dokümantasyon (1 Gün)
**Öncelik: Orta**

#### 8.1 Test
- [ ] Unit testler (PHP)
- [ ] Integration testler
- [ ] Frontend testler (Jest)
- [ ] E2E testler (Playwright)
- [ ] Browser uyumluluğu
- [ ] Responsive tasarım

#### 8.2 Dokümantasyon
- [ ] API dokümantasyonu
- [ ] Kullanıcı kılavuzu
- [ ] Admin kılavuzu
- [ ] Kod yorumları
- [ ] README güncelle

---

## 6. EKSTRA ÖNERİLER

### 6.1 Performans İyileştirmeleri
1. **Lazy Loading:** Adımlar arası geçişlerde sadece gerekli verileri yükle
2. **Debounce:** Yazar/hakem arama input'larına debounce ekle (300ms)
3. **LocalStorage Cache:** Taslak verisini localStorage'a da yaz (offline destek)
4. **CDN:** Bootstrap, FontAwesome gibi kütüphaneleri CDN'den çek
5. **Minification:** JS ve CSS dosyalarını minify et

### 6.2 Güvenlik İyileştirmeleri
1. **CSRF Token:** Her form'da kontrol et
2. **File Upload Security:**
   - MIME type kontrolü
   - Magic number kontrolü
   - Dosya adı sanitizasyonu
   - Virüs taraması (ClamAV)
3. **Input Sanitization:** XSS koruması (htmlspecialchars)
4. **SQL Injection:** Prepared statements kullan
5. **Rate Limiting:** API endpoint'lerinde rate limit

### 6.3 UX İyileştirmeleri
1. **Tooltip'ler:** Form alanlarına yardımcı tooltip'ler
2. **Auto-save Indicator:** Kaydedildi animasyonu
3. **Error Messages:** Daha açıklayıcı hata mesajları
4. **Progress Animation:** Adım geçişlerinde smooth transition
5. **Keyboard Navigation:** Tab, Enter ile form navigasyonu
6. **Accessibility:** ARIA etiketleri, screen reader desteği

### 6.4 Yeni Özellik Önerileri
1. **PDF Önizleme:** Yüklenen PDF'leri inline göster
2. **ORCID Auto-complete:** ORCID API'den otomatik bilgi çekme
3. **Reference Import:** BibTeX, RIS, EndNote formatlarından import
4. **Collaborative Editing:** Birden fazla yazar aynı anda çalışabilsin
5. **Email Notifications:** Adım tamamlandığında bildirim
6. **Version History:** Taslak versiyonlarını takip et

### 6.5 Teknik Borç Temizliği
1. **Code Refactoring:** Tekrar eden kodları fonksiyonlara çıkar
2. **Naming Conventions:** Türkçe değişken adlarını İngilizce'ye çevir
3. **ES6+ Syntax:** var yerine let/const kullan
4. **Async/Await:** Promise zincirlerini async/await'e çevir
5. **Error Handling:** Try-catch blokları ekle

### 6.6 Veritabanı İyileştirmeleri
1. **Normalization:** `makaleler` tablosunu normalize et (1NF, 2NF, 3NF)
2. **Indexes:** Sık sorgulanan alanlara index ekle
3. **Foreign Keys:** İlişkisel bütünlük için foreign key'ler
4. **Partitioning:** Büyük tabloları partition'la (tenant_id bazında)
5. **Archive Table:** Eski makaleleri arşiv tablosuna taşı

---

## 7. DEVAM KOMUTU

Yarın işe devam edebilmek için kullanılacak komut:

```bash
/form-devam
```

Bu komut çalıştırıldığında:
1. Bu MD dosyasını oku
2. Mevcut ilerlemeyi kontrol et
3. Kaldığı yerden devam et
4. Todo listesini güncelle

---

## 8. ÖNEMLİ NOTLAR

### 8.1 Veritabanı Yedekleme
**UYARI:** Veritabanı değişiklikleri yapmadan önce mutlaka yedek al!

```bash
# Windows (XAMPP)
cd C:\xampp\mysql\bin
.\mysqldump.exe -u root -p amdsphp > C:\xampp\htdocs\amdsphp\backup_$(date +%Y%m%d).sql
```

### 8.2 Git Commit Stratejisi
Her faz sonunda commit at:
```bash
git add .
git commit -m "Faz X tamamlandı: [Açıklama]"
git push origin feature/form-tamamlama
```

### 8.3 Test Stratejisi
Her faz sonunda test et:
1. Localhost'ta manuel test
2. Console error'larını kontrol et
3. Network tab'ını kontrol et
4. Responsive tasarımı kontrol et

---

## 9. ÖZET - YAPILACAKLAR LİSTESİ

### ✅ Anında Yapılması Gerekenler
1. **Yazar arama form doldurma sorunu** (30 dk)
2. **Yazar güncelleme sorunu** (30 dk)
3. **Yazar arama liste CSS** (30 dk)
4. **ORCID linki tıklanabilir** (15 dk)
5. **Yazar sayısı badge** (15 dk)

### 🔨 1-2 Gün İçinde Yapılacaklar
6. **Yazar tip renkleri** (15 dk)
7. **Hakem formu ek alanlar** (2 saat)
8. **Hakem edit butonu** (1 saat)
9. **Editöre not** (1 saat)
10. **Kontrol listesi** (2 saat)

### 📦 3-5 Gün İçinde Yapılacaklar
11. **Dosya yükleme sistemi** (1 gün)
12. **Makale özeti düzenle** (3 saat)
13. **Veritabanı düzenlemeleri** (1 gün)

### 🚀 İleride Yapılabilecekler (Opsiyonel)
14. **Form konfigürasyon sistemi** (2 gün)
15. **Admin panel - form yapılandırma** (1 gün)

---

## 10. SONUÇ

Bu analiz dokümanı, makale gönderim formunun tamamlanması için gereken tüm adımları detaylandırmaktadır.

**Tahmini Toplam Süre:** 8-10 iş günü

**Önerilen Çalışma Sırası:**
1. Faz 1 (Kritik düzeltmeler) → 1 gün
2. Faz 2 (Yazar/Hakem) → 1 gün
3. Faz 6 (Veritabanı) → 1 gün
4. Faz 3 (Dosya yükleme) → 1 gün
5. Faz 4 (Editöre not/Kontrol listesi) → 1 gün
6. Faz 5 (Makale özeti) → 1 gün
7. Faz 8 (Test/Dokümantasyon) → 1 gün
8. Faz 7 (Form konfigürasyon - opsiyonel) → 2 gün

**Form Konfigürasyon Sistemi Hakkında:**
Kullanıcının sorduğu "dergi yöneticisi her şeyi özelleştirebilir" özelliği için Faz 7'deki form konfigürasyon sistemini öneriyorum. Bu sistem sayesinde:
- Zorunlu/opsiyonel alanlar değiştirilebilir
- Min/max sayı limitleri ayarlanabilir
- Alanlar gizlenebilir/gösterilebilir
- Her dergi kendi kurallarını belirleyebilir

Ancak bu sistemi **şimdilik erteleyebiliriz** ve önce formu %100 çalışır hale getirebiliriz. Form konfigürasyon sistemi daha sonra eklenebilir.

---

**Hazırlayan:** Claude (Anthropic)
**Tarih:** 2024-12-04
**Versiyon:** 1.0
