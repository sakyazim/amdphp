# AMDS Makale Oluşturma Sihirbazı - Uygulama Planı

## JSON vs Doğrudan POST/GET Kullanımı

### JSON Kullanmanın Avantajları:
- Karmaşık veri yapılarını kolayca taşıyabilir (diziler, nesneler)
- Dinamik içerik için ideal (yazarlar, referanslar gibi değişken sayıda veri)
- Frontend-Backend arasında standart veri formatı
- Validasyon ve işleme kolaylığı

### Doğrudan POST Kullanımı:
- Basit form verileri için daha hızlı
- Daha az JavaScript gerektirir
- Geleneksel web uygulamaları için uygun

### Öneri: Karma Yaklaşım
- Basit alanlar için doğrudan POST
- Dinamik içerikler (yazarlar, referanslar) için JSON

## Aşama 1: Temel Altyapı ve Doğrulama

### 1.1. Veritabanı Güncellemeleri

```sql
-- Eksik sütunları ekle
ALTER TABLE `makaleler` 
ADD COLUMN `makale_dili` VARCHAR(10) DEFAULT 'tr' AFTER `makale_kodu`;

-- ORCID formatını genişlet
ALTER TABLE `makale_yazarlari` 
MODIFY `orcid` VARCHAR(100) DEFAULT NULL;
```

### 1.2. Backend Controller Oluşturma

Dosya: controllers/MakalelerController.php

```php
<?php
class MakalelerController {
    public function create() {
        // CSRF doğrulama
        if (!validateCSRFToken($_POST['csrf_token'])) {
            return errorResponse("Geçersiz CSRF token");
        }
        
        // Temel validasyon
        $validation = validateArticleData($_POST);
        if (!$validation['success']) {
            return errorResponse($validation['message']);
        }
        
        // Makale oluştur
        $makaleId = $this->createArticle($_POST);
        
        if ($makaleId) {
            return successResponse("Makale başarıyla oluşturuldu", ['makale_id' => $makaleId]);
        } else {
            return errorResponse("Makale oluşturulurken hata oluştu");
        }
    }
    
    private function createArticle($data) {
        // Makale kodunu oluştur (Örnek: TEST-2025-0001)
        $makaleKodu = generateArticleCode();
        
        $articleData = [
            'makale_kodu' => $makaleKodu,
            'makale_dili' => $data['makale_dili'],
            'baslik_tr' => $data['baslik_tr'],
            'baslik_en' => $data['baslik_en'],
            'ozet_tr' => $data['ozet_tr'],
            'ozet_en' => $data['ozet_en'],
            'anahtar_kelimeler_tr' => $data['anahtar_kelimeler_tr'],
            'anahtar_kelimeler_en' => $data['anahtar_kelimeler_en'],
            'referanslar' => $this->combineReferences($data),
            'makale_turu' => $data['makale_turu'],
            'makale_konusu' => $data['makale_konusu'],
            'durum' => 'gonderildi',
            'gonderi_tarihi' => date('Y-m-d H:i:s')
        ];
        
        return DB::table('makaleler')->insert($articleData);
    }
    
    private function combineReferences($data) {
        // Referansları birleştir
        $references = [];
        
        if (isset($data['referanslar']) && is_array($data['referanslar'])) {
            $references = array_filter($data['referanslar']);
        }
        
        if (isset($data['bulk_references']) && !empty($data['bulk_references'])) {
            $bulkRefs = explode("\n", $data['bulk_references']);
            $references = array_merge($references, array_filter($bulkRefs));
        }
        
        return implode("\n", array_unique($references));
    }
}
```

### 1.3. Frontend Güncellemeleri

Dosya: assets/js/create-wizard.js - Form gönderimini güncelle

```javascript
// Form gönderimini işle
document.getElementById('wizardForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!validateStep(currentStep)) {
        alert('Lütfen tüm gerekli alanları doldurunuz.');
        return;
    }
    
    // Yazarları form verisine ekle
    const authorsJson = JSON.stringify(authors);
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'authors_json';
    hiddenInput.value = authorsJson;
    this.appendChild(hiddenInput);
    
    // Formu gönder
    this.submit();
});
```

## Aşama 2: Yazarlar Yönetimi

### 2.1. Yazarlar için Backend İşlemleri

Dosya: controllers/MakalelerController.php - Yazarları işle

```php
public function createWithAuthors() {
    // Önce makaleyi oluştur
    $makaleId = $this->createArticle($_POST);
    
    if (!$makaleId) {
        return errorResponse("Makale oluşturulamadı");
    }
    
    // Yazarları işle
    if (!empty($_POST['authors_json'])) {
        $authors = json_decode($_POST['authors_json'], true);
        $this->processAuthors($makaleId, $authors);
    }
    
    return successResponse("Makale ve yazarlar başarıyla kaydedildi", [
        'makale_id' => $makaleId,
        'makale_kodu' => getArticleCode($makaleId)
    ]);
}

private function processAuthors($makaleId, $authors) {
    foreach ($authors as $author) {
        $user_id = $this->findUserIdByEmail($author['email1']);
        
        $authorData = [
            'makale_id' => $makaleId,
            'kullanici_id' => $user_id,
            'email' => $author['email1'],
            'ad' => $author['firstName'],
            'soyad' => $author['lastName'],
            'kurum' => $author['institution'],
            'orcid' => $this->normalizeOrcid($author['orcidId']),
            'yazar_sirasi' => $author['order'],
            'sorumlu_yazar_mi' => ($author['type'] === 'corresponding') ? 1 : 0
        ];
        
        DB::table('makale_yazarlari')->insert($authorData);
    }
}

private function normalizeOrcid($orcid) {
    if (empty($orcid)) return null;
    
    // ORCID'i normalize et (URL'den ID'yi çıkar)
    $orcid = trim($orcid);
    if (preg_match('/\d{4}-\d{4}-\d{4}-\d{4}/', $orcid, $matches)) {
        return $matches[0];
    }
    
    return $orcid;
}
```

### 2.2. Frontend Yazarlar Güncellemesi

Dosya: assets/js/authors-management.js - Güncel ORCID validasyonu

```javascript
function validateOrcid(orcid) {
    if (!orcid) return true; // ORCID zorunlu değil
    
    const orcidRegex = /^(https:\/\/orcid\.org\/)?(\d{4}-\d{4}-\d{4}-\d{4})$/;
    return orcidRegex.test(orcid.trim());
}

function normalizeOrcid(orcid) {
    if (!orcid) return '';
    
    // ORCID'den sadece ID kısmını al
    const match = orcid.trim().match(/(\d{4}-\d{4}-\d{4}-\d{4})/);
    return match ? match[1] : orcid;
}
```

## Aşama 3: Dosya Yükleme Sistemi

### 3.1. Dosya Yükleme Backend

Dosya: controllers/DosyalarController.php

```php
class DosyalarController {
    public function upload($makaleId) {
        $allowedTypes = ['pdf', 'doc', 'docx'];
        $maxSize = 10 * 1024 * 1024; // 10MB
        
        $file = $_FILES['file'];
        
        // Validasyon
        if (!$this->validateFile($file, $allowedTypes, $maxSize)) {
            return errorResponse("Dosya geçersiz");
        }
        
        // Dosyayı kaydet
        $fileInfo = $this->saveFile($makaleId, $file);
        
        // Veritabanına kaydet
        $fileId = DB::table('dosyalar')->insert([
            'makale_id' => $makaleId,
            'dosya_turu' => $_POST['file_type'],
            'orijinal_dosya_adi' => $file['name'],
            'kaydedilen_dosya_adi' => $fileInfo['saved_name'],
            'dosya_yolu' => $fileInfo['path'],
            'dosya_boyutu' => $file['size'],
            'mime_tipi' => $file['type'],
            'versiyon' => 1,
            'yukleyen_kullanici_id' => $_SESSION['user_id']
        ]);
        
        return successResponse("Dosya başarıyla yüklendi", ['file_id' => $fileId]);
    }
}
```

## Aşama 4: Adım Adım Kaydetme Sistemi

### 4.1. Session Tabanlı Geçici Kayıt

```php
// Her adımda veriyi session'da sakla
public function saveStep($step) {
    $stepData = $_POST;
    unset($stepData['csrf_token']);
    
    $_SESSION['article_wizard'][$step] = $stepData;
    
    return successResponse("Adım kaydedildi");
}

// Tüm adımları birleştir ve veritabanına kaydet
public function completeWizard() {
    $allData = $_SESSION['article_wizard'] ?? [];
    
    if (empty($allData)) {
        return errorResponse("Kaydedilmiş veri bulunamadı");
    }
    
    // Tüm veriyi birleştir
    $mergedData = [];
    foreach ($allData as $stepData) {
        $mergedData = array_merge($mergedData, $stepData);
    }
    
    // Veritabanına kaydet
    $result = $this->createCompleteArticle($mergedData);
    
    // Session'ı temizle
    unset($_SESSION['article_wizard']);
    
    return $result;
}
```

## Aşama 5: Validasyon ve Hata Yönetimi

### 5.1. Kapsamlı Validasyon Sistemi

Dosya: core/Validation.php

```php
class Validation {
    public static function validateArticle($data) {
        $errors = [];
        
        // Başlık validasyonu
        if (empty($data['baslik_tr']) || strlen($data['baslik_tr']) < 10) {
            $errors[] = "Türkçe başlık en az 10 karakter olmalıdır";
        }
        
        // Özet kelime sayısı
        $trWordCount = str_word_count($data['ozet_tr']);
        if ($trWordCount < 150 || $trWordCount > 250) {
            $errors[] = "Türkçe özet 150-250 kelime arasında olmalıdır";
        }
        
        // Anahtar kelime sayısı
        $trKeywords = explode(',', $data['anahtar_kelimeler_tr']);
        if (count($trKeywords) < 3 || count($trKeywords) > 5) {
            $errors[] = "3-5 arası anahtar kelime girmelisiniz";
        }
        
        return empty($errors) ? true : $errors;
    }
}
```

## Uygulama Sırası - Öncelik Listesi

### ✅ HEMEN YAPILACAKLAR (Bugün)
- Veritabanı güncellemelerini uygula
- Temel makale oluşturma backend'ini yaz
- Adım 0-6 için form gönderimini test et

### 🟡 KISA VADEDE (Bu Hafta)
- Yazarlar yönetimini backend'e bağla
- JSON veri aktarımını implemente et
- Temel validasyonları tamamla

### 🟢 ORTA VADEDE (Önümüzdeki Hafta)
- Dosya yükleme sistemini kur
- Session tabanlı adım kaydetme
- Hata yönetimi ve kullanıcı feedback'i

### 🔵 UZUN VADEDE (Sonraki Adımlar)
- Diğer adımları (hakemler, editör notu, kontrol listesi) implemente et
- Email bildirimleri ekle
- PDF önizleme ve özet oluşturma

## Test Senaryoları

### Test 1: Temel Makale Oluşturma
- Dil seçimi çalışıyor mu?
- Başlık validasyonu doğru mu?
- Özet kelime sayımı doğru mu?
- Veritabanına kayıt başarılı mı?

### Test 2: Yazarlar Yönetimi
- JSON veri aktarımı çalışıyor mu?
- ORCID validasyonu esnek mi?
- Yazarlar doğru sırada kaydediliyor mu?
- Sorumlu yazar ataması çalışıyor mu?

### Test 3: Dosya Yükleme
- Dosya tipi validasyonu çalışıyor mu?
- Boyut limiti denetleniyor mu?
- Dosya veritabanına kaydediliyor mu?

## Önemli Notlar
- Transaction Kullan: Makale ve yazar kayıtlarını transaction içinde yap
- CSRF Koruması: Tüm formlarda CSRF token kullan
- XSS Koruması: Çıktıları htmlspecialchars() ile filtrele
- SQL Injection: Prepared statement kullan
- Dosya Güvenliği: Yüklenen dosyaları virüs taramasından geçir

Bu planı adım adım takip ederek sorunsuz bir makale oluşturma sihirbazı geliştirebilirsin. Her aşamayı tamamladıktan sonra test etmeyi unutma!