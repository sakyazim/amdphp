# Yeni Makale Formu - Eksik Kalan Özellikler

## 📋 Genel Durum

✅ **Tamamlanan:**
- Temel makale bilgileri (dil, tür, konu, başlık, özet, anahtar kelimeler)
- Türkçe + İngilizce zorunlu alanlar (başlık, özet, anahtar kelimeler)
- Referanslar (dinamik ekleme/çıkarma sistemi)

⏳ **Eksik:** Yazarlar, Dosyalar, Hakemler, Editöre Not, Kontrol Listesi

---

## ✅ VERİTABANI YAPISI - MEVCUT DURUM

### `makaleler` Tablosu - Zorunlu Alanlar

| Alan | Tür | Zorunlu | Durum |
|------|-----|---------|-------|
| `makale_turu` | ENUM | ✅ ZORUNLU | ✅ Mevcut |
| `makale_konusu` | VARCHAR(100) | ⚠️ Opsiyonel | ✅ Mevcut |
| `baslik_tr` | VARCHAR(500) | ✅ NOT NULL | ✅ Mevcut |
| `baslik_en` | VARCHAR(500) | ✅ NOT NULL | ✅ Mevcut |
| `ozet_tr` | TEXT | ✅ NOT NULL | ✅ Mevcut |
| `ozet_en` | TEXT | ✅ NOT NULL | ✅ Mevcut |
| `anahtar_kelimeler_tr` | TEXT | ✅ NOT NULL | ✅ Mevcut |
| `anahtar_kelimeler_en` | TEXT | ✅ NOT NULL | ✅ Mevcut |
| `referanslar` | TEXT | ⚠️ Opsiyonel | ✅ Mevcut |

**📝 Sonuç:** Veritabanı yapısı istediğiniz tüm zorunlu alanları destekliyor!
- ✅ Makale türü ve konusu mevcut
- ✅ Başlık, özet, anahtar kelimeler Türkçe+İngilizce zorunlu
- ✅ Referanslar alanı mevcut

**🎯 İSTENEN ÖZELLİKLER:**
1. ✅ **Makale türü** beraberinde **makale konusu** - TAMAMLANDI
2. ✅ **Başlık** (Türkçe + İngilizce) zorunlu - TAMAMLANDI
3. ✅ **Anahtar kelimeler** (Türkçe + İngilizce) zorunlu - TAMAMLANDI
4. ✅ **Özet** (Türkçe + İngilizce) zorunlu - TAMAMLANDI
5. ✅ **Referanslar** - TAMAMLANDI (Dinamik ekleme/çıkarma sistemi)

---

## 🔴 Eksik Kalan Wizard Adımları (Old HTML'den)

### **Adım 6: Referanslar** (step6)
**Durum:** ✅ TAMAMLANDI

**✅ Eklenen Özellikler:**
- ✅ Dinamik referans ekleme/çıkarma sistemi
- ✅ Her referans için ayrı textarea
- ✅ "Yeni Referans Ekle" butonu
- ✅ "Referansı Sil" butonu (çöp kutusu ikonu)
- ✅ APA formatı uyarısı
- ✅ İlk referans silinemez (en az 1 referans alanı)
- ✅ Referans numaraları otomatik güncellenir
- ✅ Özet sayfasında referanslar listelenmiş halde gösterilir

**✅ Backend Durum:**
- `makaleler` tablosunda `referanslar` TEXT alanı **MEVCUT**
- Frontend'den `referanslar[]` array olarak gelecek
- Backend'de array'i JSON veya newline ile birleştirip kaydedecek

**Örnek Kod (HTML):**
```html
<div id="referencesContainer">
    <div class="reference-item mb-3">
        <div class="input-group">
            <textarea class="form-control" rows="3"
                placeholder="Örnek: Smith, J. (2023). Makale başlığı..."></textarea>
            <button class="btn btn-danger" onclick="removeReference(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
</div>
<button class="btn btn-success" onclick="addNewReference()">
    <i class="fas fa-plus me-2"></i>Yeni Referans Ekle
</button>
```

---

### **Adım 7: Yazarlar** (step7)
**Durum:** ❌ Eksik - ÖNEMLİ!

**Özellikler:**
- ⚠️ **En başta yazar arama kutuları** (Email ve ORCID ile arama)
- Yazar ekleme formu (detaylı):
  - Kişisel Bilgiler: Ad, İkinci Ad, Soyad, Ünvan
  - İletişim: Telefon, Email 1, Email 2
  - Kurum: Departman, Kurum, Ülke
  - Makale Bilgileri: ORCID ID, Yazar Sırası, Yazar Tipi
- Yazarlar tablosu (sıralı liste)
- Düzenleme/Silme butonları
- Yazar sayacı (badge)

**Gerekli Backend İşler:**
1. `makale_yazarlari` tablosu **MEVCUT** ✅
   ```sql
   - id
   - makale_id
   - kullanici_id (opsiyonel - kayıtlı kullanıcı ise)
   - email, ad, soyad, kurum, orcid
   - yazar_sirasi
   - sorumlu_yazar_mi
   - katkı_orani
   ```

2. Yazar CRUD operasyonları:
   - `addAuthor()` - Yazar ekle
   - `editAuthor()` - Yazar düzenle
   - `deleteAuthor()` - Yazar sil
   - `searchAuthorByEmail()` - Email ile ara
   - `searchAuthorByOrcid()` - ORCID ile ara

3. JavaScript işlevleri:
   - Yazar formu göster/gizle
   - Tabloya yazar ekle
   - Yazar düzenle (formu doldur)
   - Yazar sil (tablodan çıkar)

**Örnek Kod (HTML):**
```html
<!-- YAZAR ARAMA (ÖNEMLİ!) -->
<div class="alert alert-info mb-4">
    <h6>Mevcut Yazarları Ara</h6>
    <div class="row">
        <div class="col-md-6">
            <input type="email" class="form-control"
                placeholder="Email ile ara..." id="searchByEmail">
        </div>
        <div class="col-md-6">
            <input type="text" class="form-control"
                placeholder="ORCID ID ile ara..." id="searchByOrcid">
        </div>
    </div>
</div>

<!-- YAZAR EKLEME FORMU -->
<form id="authorForm">
    <!-- Detaylı form alanları... -->
</form>

<!-- YAZARLAR TABLOSU -->
<table id="authorsTable">
    <thead>
        <tr>
            <th>Sıra</th>
            <th>Yazar Bilgileri</th>
            <th>İşlem</th>
        </tr>
    </thead>
    <tbody>
        <!-- Yazarlar JavaScript ile eklenecek -->
    </tbody>
</table>
```

---

### **Adım 8: Dosyalar** (step8)
**Durum:** ❌ Eksik - ÖNEMLİ!

**Özellikler:**
- Dosya türü seçimi:
  - Tam Metin
  - Yayın Hakkı Devir Formu
  - Yazar Katkı Formu
  - ICMJE COI Form
  - iThenticate Formu
  - Ek Dosya
  - Şekiller/Görseller
  - Benzerlik Raporu
- Dosya yükleme (max 25MB)
- Progress bar (yükleme ilerlemesi)
- Dosyalar tablosu (yüklenen dosyaların listesi)
- Dosya silme/indirme

**Gerekli Backend İşler:**
1. `dosyalar` tablosu **MEVCUT** ✅
   ```sql
   - id
   - makale_id
   - dosya_turu
   - orijinal_dosya_adi
   - kaydedilen_dosya_adi
   - dosya_yolu
   - dosya_boyutu
   - mime_tipi
   - versiyon
   - yukleyen_kullanici_id
   ```

2. Dosya işlemleri:
   - `uploadFile()` - Dosya yükle
   - `deleteFile()` - Dosya sil
   - `downloadFile()` - Dosya indir
   - `validateFile()` - Dosya validasyonu (boyut, tip)

3. Storage klasör yapısı:
   ```
   storage/
   ├── manuscripts/
   ├── forms/
   ├── supplements/
   └── temp/
   ```

**Örnek Kod (HTML):**
```html
<form id="fileUploadForm" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-4">
            <select class="form-select" id="fileType" required>
                <option value="">Seçiniz</option>
                <option value="fullText">Tam Metin</option>
                <option value="copyright">Yayın Hakkı Devir Formu</option>
                <!-- ... diğer türler ... -->
            </select>
        </div>
        <div class="col-md-8">
            <div class="input-group">
                <input type="file" class="form-control" id="fileInput" required>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload me-2"></i>Yükle
                </button>
            </div>
        </div>
    </div>
</form>

<!-- Progress Bar -->
<div id="fileUploadProgress" class="progress d-none">
    <div class="progress-bar progress-bar-striped progress-bar-animated"></div>
</div>

<!-- Dosyalar Tablosu -->
<table id="filesTable">
    <thead>
        <tr>
            <th>Dosya Türü</th>
            <th>Dosya Adı</th>
            <th>Boyut</th>
            <th>Format</th>
            <th>Yükleme Tarihi</th>
            <th>İşlemler</th>
        </tr>
    </thead>
    <tbody>
        <!-- Dosyalar dinamik eklenecek -->
    </tbody>
</table>
```

---

### **Adım 9: Hakemler** (step9)
**Durum:** ❌ Eksik

**Özellikler:**
- ⚠️ **En başta hakem arama kutuları** (Email ve ORCID ile arama)
- En az 3 hakem ekleme zorunluluğu
- Hakem ekleme formu:
  - Sıra ve Hakem Tipi (Ana/Yedek/Dış)
  - Kişisel Bilgiler: Ünvan, Ad, İkinci Ad, Soyad
  - İletişim: Email 1, Email 2, Telefon
  - Kurum: Departman, Kurum, Ülke
  - ORCID ID
- Hakemler tablosu
- Hakem sayacı ve uyarı mesajı

**Gerekli Backend İşler:**
1. `hakem_onerileri` veya `makale_hakem_onerileri` tablosu oluşturma
   ```sql
   CREATE TABLE makale_hakem_onerileri (
       id INT PRIMARY KEY AUTO_INCREMENT,
       makale_id INT,
       hakem_tipi ENUM('main', 'alternate', 'external'),
       sira INT,
       unvan VARCHAR(50),
       ad VARCHAR(100),
       soyad VARCHAR(100),
       email VARCHAR(255),
       telefon VARCHAR(50),
       kurum VARCHAR(255),
       departman VARCHAR(255),
       ulke VARCHAR(100),
       orcid VARCHAR(50),
       olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );
   ```

2. CRUD operasyonları:
   - `addReviewer()` - Hakem ekle
   - `editReviewer()` - Hakem düzenle
   - `deleteReviewer()` - Hakem sil
   - `searchReviewerByEmail()` - Email ile ara
   - `searchReviewerByOrcid()` - ORCID ile ara

**Örnek Kod (HTML):**
```html
<!-- HAKEM ARAMA -->
<div class="alert alert-info mb-4">
    <h6>Mevcut Hakemleri Ara</h6>
    <div class="row">
        <div class="col-md-6">
            <input type="email" class="form-control"
                placeholder="Email ile ara..." id="reviewerSearchEmail">
        </div>
        <div class="col-md-6">
            <input type="text" class="form-control"
                placeholder="ORCID ID ile ara..." id="reviewerSearchOrcid">
        </div>
    </div>
</div>

<!-- En az 3 hakem uyarısı -->
<div class="alert alert-warning mb-4" id="reviewerWarning">
    <i class="fas fa-exclamation-triangle me-2"></i> En az 3 hakem eklemelisiniz.
</div>

<!-- HAKEM FORMU VE TABLO -->
<!-- ... form alanları ... -->
```

---

### **Adım 10: Editöre Not** (step10)
**Durum:** ❌ Eksik

**Özellikler:**
- Rich text editor (basit toolbar)
- Karakter sayacı
- Kaydet butonu
- Bold, Italic, Underline, Clear format butonları

**Gerekli Backend İşler:**
- `makaleler` tablosuna `editore_notu` TEXT alanı eklemek

**Örnek Kod (HTML):**
```html
<div class="editor-toolbar mb-2">
    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('bold')">
        <i class="fas fa-bold"></i>
    </button>
    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('italic')">
        <i class="fas fa-italic"></i>
    </button>
    <!-- ... diğer butonlar ... -->
</div>

<textarea id="editorNote" class="form-control" rows="10"
        name="editore_notu"
        placeholder="Editöre notunuzu buraya yazın..."></textarea>
<div class="form-text mt-2">
    <span id="characterCount">0</span> karakter
</div>
```

---

### **Adım 11: Kontrol Listesi** (step11)
**Durum:** ❌ Eksik

**Özellikler:**
- 9 adet checkbox (3 kategori):
  - **Makale İçerik Kontrolleri** (3 madde)
  - **Yazar ve Hakem Kontrolleri** (3 madde)
  - **Dosya Kontrolleri** (3 madde)
- İlerleme göstergesi (X/9 madde tamamlandı)
- "Tümünü İşaretle" butonu
- "Tümünü Temizle" butonu
- Progress bar

**Gerekli Backend İşler:**
- Sadece frontend validasyon yeterli
- Form gönderilmeden önce tüm checkboxların işaretli olması kontrolü

**Örnek Kod (HTML):**
```html
<div class="checklist-container">
    <!-- Makale İçerik Kontrolleri -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5>Makale İçerik Kontrolleri</h5>
        </div>
        <div class="card-body">
            <div class="form-check mb-3">
                <input class="form-check-input checklist-item" type="checkbox" id="check1">
                <label class="form-check-label" for="check1">
                    Makalenin başlığı, özeti ve anahtar kelimeleri hem Türkçe hem de İngilizce olarak eklenmiştir.
                </label>
            </div>
            <!-- ... diğer checkboxlar ... -->
        </div>
    </div>
    <!-- ... diğer kategoriler ... -->
</div>

<div class="progress mb-3">
    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
</div>
<div class="d-flex justify-content-between">
    <button class="btn btn-sm btn-outline-success" id="checkAllBtn">
        <i class="fas fa-check-double me-1"></i> Tümünü İşaretle
    </button>
    <span class="text-muted">
        <span id="checkProgress">0/9</span> madde tamamlandı
    </span>
</div>
```

---

### **Adım 12: Makaleyi Gönder** (step12)
**Durum:** ✅ Mevcut (Basit versiyon)

**Özellikler:**
- Tüm girilen bilgilerin özeti
- Her bölüm için düzenleme butonları
- Final onay checkbox'ı
- "Makaleyi Gönder" butonu

**İyileştirmeler:**
- Yazarlar listesi özeti
- Dosyalar listesi özeti
- Hakemler listesi özeti
- Referanslar listesi özeti
- Editöre not özeti

---

## 📊 Veritabanı Değişiklikleri

### ✅ Mevcut Tablolar (Kullanılabilir)
```sql
- makaleler (ana tablo) ✅
  - makale_turu (ENUM) ✅
  - makale_konusu (VARCHAR 100) ✅
  - baslik_tr, baslik_en (VARCHAR 500, NOT NULL) ✅
  - ozet_tr, ozet_en (TEXT, NOT NULL) ✅
  - anahtar_kelimeler_tr, anahtar_kelimeler_en (TEXT, NOT NULL) ✅
  - referanslar (TEXT) ✅

- makale_yazarlari (yazar ilişkileri) ✅
- dosyalar (dosya yönetimi) ✅
```

### 🔴 Eklenecek Alanlar

#### `makaleler` tablosuna:
```sql
-- Editöre notu alanı (opsiyonel)
ALTER TABLE makaleler ADD COLUMN editore_notu TEXT AFTER ret_nedeni;

-- Makale konusu zorunlu yapmak isterseniz:
-- ALTER TABLE makaleler MODIFY COLUMN makale_konusu VARCHAR(100) NOT NULL;
```

**Not:** `referanslar` alanı zaten mevcut, eklemeye gerek yok!

#### Yeni Tablo (Hakem Önerileri):
```sql
CREATE TABLE makale_hakem_onerileri (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    makale_id INT UNSIGNED NOT NULL,
    hakem_tipi ENUM('main', 'alternate', 'external') NOT NULL,
    sira TINYINT UNSIGNED NOT NULL,
    unvan VARCHAR(50),
    ad VARCHAR(100) NOT NULL,
    ikinci_ad VARCHAR(100),
    soyad VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    email2 VARCHAR(255),
    telefon VARCHAR(50),
    departman VARCHAR(255),
    kurum VARCHAR(255),
    ulke VARCHAR(100),
    orcid VARCHAR(50),
    olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (makale_id) REFERENCES makaleler(id) ON DELETE CASCADE,
    INDEX idx_makale_id (makale_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 🎯 Öncelik Sırası

### 🔥 Yüksek Öncelik (Mutlaka Olmalı)
1. **Yazarlar Sistemi** (Adım 7)
   - En az 1 yazar olmalı
   - Sorumlu yazar seçimi önemli
   - ORCID zorunlu

2. **Dosya Yükleme** (Adım 8)
   - Tam metin dosyası zorunlu
   - Diğer formlar opsiyonel

### ⚠️ Orta Öncelik
3. **Hakemler** (Adım 9)
   - En az 3 hakem önerisi
   - Çıkar çatışması kontrolü için önemli

4. **Referanslar** (Adım 6)
   - Akademik dergi için önemli

### 💡 Düşük Öncelik
5. **Editöre Not** (Adım 10)
   - Opsiyonel alan

6. **Kontrol Listesi** (Adım 11)
   - Son kontrol için faydalı

---

## 🛠️ Gerekli Backend Geliştirmeler

### 1. Article Model Güncellemeleri
```php
// app/Models/Article.php'ye eklenecek metodlar:

// Yazarlar
public static function addAuthor($makaleId, $authorData, $tenantDb);
public static function updateAuthor($authorId, $authorData, $tenantDb);
public static function deleteAuthor($authorId, $tenantDb);
public static function searchAuthorByEmail($email, $tenantDb);
public static function searchAuthorByOrcid($orcid, $tenantDb);

// Dosyalar
public static function uploadFile($makaleId, $fileData, $tenantDb);
public static function deleteFile($fileId, $tenantDb);

// Hakemler
public static function addReviewer($makaleId, $reviewerData, $tenantDb);
public static function updateReviewer($reviewerId, $reviewerData, $tenantDb);
public static function deleteReviewer($reviewerId, $tenantDb);

// Referanslar (JSON olarak saklanabilir)
public static function saveReferences($makaleId, $references, $tenantDb);
```

### 2. Yeni Controller Metodları
```php
// app/Controllers/ArticleController.php'ye eklenecek:

// Yazar işlemleri (AJAX)
public function addAuthor(int $id);
public function updateAuthor(int $id, int $authorId);
public function deleteAuthor(int $id, int $authorId);

// Dosya işlemleri (AJAX)
public function uploadFile(int $id);
public function deleteFile(int $id, int $fileId);
public function downloadFile(int $id, int $fileId);

// Hakem işlemleri (AJAX)
public function addReviewer(int $id);
public function updateReviewer(int $id, int $reviewerId);
public function deleteReviewer(int $id, int $reviewerId);
```

### 3. Routes Eklemeleri
```php
// AJAX endpoints
$router->post('/makaleler/{id}/yazar', 'ArticleController@addAuthor');
$router->post('/makaleler/{id}/yazar/{authorId}', 'ArticleController@updateAuthor');
$router->delete('/makaleler/{id}/yazar/{authorId}', 'ArticleController@deleteAuthor');

$router->post('/makaleler/{id}/dosya', 'ArticleController@uploadFile');
$router->delete('/makaleler/{id}/dosya/{fileId}', 'ArticleController@deleteFile');
$router->get('/makaleler/{id}/dosya/{fileId}', 'ArticleController@downloadFile');

$router->post('/makaleler/{id}/hakem', 'ArticleController@addReviewer');
$router->post('/makaleler/{id}/hakem/{reviewerId}', 'ArticleController@updateReviewer');
$router->delete('/makaleler/{id}/hakem/{reviewerId}', 'ArticleController@deleteReviewer');
```

---

## 📝 JavaScript Gereksinimleri

### 1. Yazar Yönetimi JS
```javascript
// public/assets/js/author-manager.js
- Form validasyonu
- Dinamik tablo ekleme/çıkarma
- AJAX ile yazar ekleme/düzenleme/silme
- Email ve ORCID arama
```

### 2. Dosya Yükleme JS
```javascript
// public/assets/js/file-upload.js
- Drag & drop desteği
- Progress bar güncellemesi
- Dosya boyutu kontrolü
- Mime type validasyonu
- Çoklu dosya yükleme
```

### 3. Hakem Yönetimi JS
```javascript
// public/assets/js/reviewer-manager.js
- Form validasyonu
- Dinamik tablo ekleme/çıkarma
- AJAX ile hakem ekleme/düzenleme/silme
- Email ve ORCID arama
- Minimum 3 hakem kontrolü
```

### 4. Referans Yönetimi JS
```javascript
// public/assets/js/reference-manager.js
- Dinamik referans ekleme/çıkarma
- APA format validasyonu (opsiyonel)
```

---

## 🎨 UI/UX İyileştirmeleri

1. **Loading Spinners** - AJAX işlemlerinde
2. **Toast Notifications** - Başarı/Hata mesajları için
3. **Confirmation Modals** - Silme işlemlerinde
4. **Auto-save** - Form verilerini periyodik kaydetme
5. **Taslak Sistemi** - Yarım kalan formları kaydetme

---

## 🔒 Güvenlik Kontrolleri

1. **Dosya Yükleme:**
   - Maksimum boyut kontrolü (25MB)
   - İzin verilen dosya tipleri (PDF, DOCX, vb.)
   - Dosya adı sanitizasyonu
   - Virus taraması (opsiyonel)

2. **CSRF Koruması:**
   - Tüm AJAX isteklerinde token kontrolü

3. **Yetkilendirme:**
   - Sadece makale sahibi düzenleyebilir
   - Rol kontrolü

---

## 📅 Tahmini Geliştirme Süresi

| Özellik | Süre | Zorluk |
|---------|------|--------|
| Yazarlar Sistemi | 4-6 saat | Orta |
| Dosya Yükleme | 3-4 saat | Orta |
| Hakemler Sistemi | 3-4 saat | Kolay-Orta |
| Referanslar | 1-2 saat | Kolay |
| Editöre Not | 30 dk | Çok Kolay |
| Kontrol Listesi | 1 saat | Kolay |
| **TOPLAM** | **12-17 saat** | |

---

## 🎯 Sonuç

**Mevcut Durum:**
- ✅ Temel makale bilgileri sistemi %100 tamamlandı
- ✅ Wizard yapısı kuruldu
- ✅ **Veritabanı yapısı istediğiniz zorunlu alanları destekliyor!**
  - ✅ Makale türü + konusu mevcut
  - ✅ Başlık, özet, anahtar kelimeler (TR + EN) zorunlu
  - ✅ Referanslar alanı mevcut
- ⏳ İlişkili sistemler (yazarlar, dosyalar, hakemler) eksik

**Yapılacaklar:**
1. Veritabanı güncellemeleri (1 alan + 1 tablo)
   - ✅ `referanslar` alanı zaten mevcut - atlandı!
   - ⏳ `editore_notu` TEXT alanı eklenecek
   - ⏳ `makale_hakem_onerileri` tablosu oluşturulacak
2. Backend CRUD metodları (yazarlar, dosyalar, hakemler)
3. Frontend JavaScript (AJAX işlemleri, dinamik tablolar)
4. UI iyileştirmeleri (loading, toast, modals)

**Önerilen Sıralama:**
1. Önce **Yazarlar** (en kritik)
2. Sonra **Dosya Yükleme** (zorunlu)
3. Ardından **Hakemler** (önemli)
4. En son **Referanslar + Editöre Not + Kontrol Listesi** (opsiyonel)

---

## 📝 İSTEDİĞİNİZ ÖZELLİKLER - DURUM RAPORU

### ✅ Makale Türü + Makale Konusu
- **Durum:** Veritabanında mevcut
- `makale_turu`: ENUM (arastirma, derleme, olgu_sunumu, editore_mektup) - ZORUNLU
- `makale_konusu`: VARCHAR(100) - Mevcut (isteğe bağlı zorunlu yapılabilir)

### ✅ Başlık (Türkçe + İngilizce - Zorunlu)
- **Durum:** Veritabanında mevcut ve zorunlu
- `baslik_tr`: VARCHAR(500) NOT NULL
- `baslik_en`: VARCHAR(500) NOT NULL

### ✅ Özet (Türkçe + İngilizce - Zorunlu)
- **Durum:** Veritabanında mevcut ve zorunlu
- `ozet_tr`: TEXT NOT NULL
- `ozet_en`: TEXT NOT NULL

### ✅ Anahtar Kelimeler (Türkçe + İngilizce - Zorunlu)
- **Durum:** Veritabanında mevcut ve zorunlu
- `anahtar_kelimeler_tr`: TEXT NOT NULL
- `anahtar_kelimeler_en`: TEXT NOT NULL

### ✅ Referanslar
- **Durum:** ✅ Frontend tamamlandı
- `referanslar`: TEXT (NULL olabilir)
- ✅ Dinamik ekleme/çıkarma sistemi aktif
- ✅ Array olarak frontend'den gönderilecek (`referanslar[]`)

---

## 📝 BUGÜN YAPILAN DEĞİŞİKLİKLER (Son Güncelleme)

### ✅ Frontend'de Eklenen Özellikler:

**1. Step 2 - Tür ve Konu:**
- ✅ Makale konusu alanı eklendi (`makale_konusu`)
- ✅ 100 karakter limiti
- ✅ Karakter sayacı eklendi
- ✅ Zorunlu alan (required)

**2. Step 3 - Başlık:**
- ✅ İngilizce başlık zorunlu yapıldı (`baslik_en` - required)
- ✅ Minimum 10, maksimum 500 karakter validasyonu
- ✅ Validasyon mesajları eklendi

**3. Step 4 - Özet:**
- ✅ İngilizce özet zorunlu yapıldı (`ozet_en` - required)
- ✅ 150-250 kelime validasyonu eklendi
- ✅ Kelime sayacı her iki dil için aktif

**4. Step 5 - Anahtar Kelimeler:**
- ✅ İngilizce anahtar kelimeler zorunlu yapıldı (`anahtar_kelimeler_en` - required)
- ✅ 3-5 anahtar kelime validasyonu her iki dil için
- ✅ Anahtar kelime sayacı eklendi

**5. Step 6 - Referanslar (YENİ ADIM):**
- ✅ Yeni wizard adımı eklendi
- ✅ Dinamik referans ekleme/çıkarma sistemi
- ✅ "Yeni Referans Ekle" butonu
- ✅ Her referansın yanında "Sil" butonu (çöp kutusu ikonu)
- ✅ İlk referans silinemez (minimum 1 alan)
- ✅ Referans numaraları otomatik güncellenir
- ✅ Array olarak gönderilecek: `referanslar[]`

**6. Step 7 - Özet ve Gönderim (Eski Step 6):**
- ✅ Makale konusu özette gösteriliyor
- ✅ Referanslar numaralı liste olarak gösteriliyor
- ✅ Tüm bilgiler özet kartlarında mevcut

**7. JavaScript Güncellemeleri:**
- ✅ totalSteps: 7 → 8 olarak güncellendi
- ✅ Tüm validasyon fonksiyonları İngilizce alanları kontrol ediyor
- ✅ Karakter/kelime sayaçları çalışıyor
- ✅ `addNewReference()` fonksiyonu eklendi
- ✅ `removeReference()` fonksiyonu eklendi
- ✅ `updateReferenceNumbers()` fonksiyonu eklendi
- ✅ `updateSummary()` referansları listeliyor

### 📊 Form Yapısı Özet:

| Adım | Başlık | Durum | Zorunlu Alanlar |
|------|--------|-------|-----------------|
| Step 0 | Dil Seçimi | ✅ Var | makale_dili |
| Step 1 | Ön Bilgi | ✅ Var | Onay checkbox |
| Step 2 | Tür-Konu | ✅ Güncellendi | makale_turu, **makale_konusu** |
| Step 3 | Başlık | ✅ Güncellendi | baslik_tr, **baslik_en** |
| Step 4 | Özet | ✅ Güncellendi | ozet_tr, **ozet_en** |
| Step 5 | Anahtar Kelimeler | ✅ Güncellendi | anahtar_kelimeler_tr, **anahtar_kelimeler_en** |
| Step 6 | **Referanslar** | ✅ **YENİ EKLENDI** | referanslar[] (opsiyonel) |
| Step 7 | Makaleyi Gönder | ✅ Var | submitConfirmation |

### 🔧 Backend İçin Gerekli Değişiklikler:

**ArticleController.php** veya ilgili controller'da:
```php
// POST isteğinde referanslar[] array olarak gelecek
if (isset($_POST['referanslar']) && is_array($_POST['referanslar'])) {
    // Boş referansları filtrele
    $references = array_filter($_POST['referanslar'], function($ref) {
        return !empty(trim($ref));
    });

    // JSON olarak kaydet (önerilen)
    $referanslarJSON = json_encode($references, JSON_UNESCAPED_UNICODE);

    // VEYA satır satır kaydet
    $referanslarText = implode("\n", $references);

    // Database'e kaydet
    $data['referanslar'] = $referanslarJSON; // veya $referanslarText
}
```

---

**Not:** Old klasöründeki `yeni-makale.html` dosyası tam bir referans olarak kullanılabilir. Tüm JavaScript kodları ve HTML yapısı orada mevcut!
