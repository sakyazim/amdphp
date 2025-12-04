# FAZ 6: DOSYA YÜKLEME

**Durum**: ⚪ Bekliyor
**Tahmini Süre**: 2 gün
**Öncelik**: 🟢 Düşük
**Bağımlılık**: Faz 1 tamamlanmalı

---

## 🎯 AMAÇ

Güvenli ve esnek dosya yükleme sistemi oluşturmak:
- Zorunlu dosyalar (Tam Metin, Formlar)
- Opsiyonel dosyalar (Ekler, Görseller)
- Dosya validasyonu (boyut, format)
- Progress bar
- Dosya yönetimi (listeleme, silme, indirme)

---

## ✅ GÖREVLER

### 6.1 - Gereksinimler Belirle

**Süre**: 30 dakika

**Lütfen aşağıdaki soruları cevaplayın:**

#### Sorular:

1. **Zorunlu dosyalar:**
   - [ ] Tam Metin (PDF) ✓
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
   - Tam Metin: ___ MB (önerilen: 10 MB)
   - Formlar: ___ MB (önerilen: 5 MB)
   - Ek Dosyalar: ___ MB (önerilen: 20 MB)
   - Görseller: ___ MB (önerilen: 5 MB)

4. **İzin verilen formatlar:**
   - **Tam Metin:**
     - [ ] PDF ✓
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
   - [x] Karma (orijinal adı kaydet ama güvenli ad ile sakla) **← Önerilen**

**Kararlar:**

```
[Buraya kararlarınızı yazın]

Örnek:
- Zorunlu: Tam Metin (PDF), Yayın Hakkı Devir Formu
- Opsiyonel: Ek Dosyalar, Görseller
- Boyut limiti: 10 MB (tam metin), 5 MB (diğer)
- Formatlar: PDF, DOCX, JPG, PNG, ZIP
- Adlandırma: Karma (güvenli ad + orijinal ad kaydet)
```

---

### 6.2 - Storage Klasör Yapısını Kontrol Et

**Süre**: 10 dakika

Klasörler Faz 0'da oluşturuldu. Kontrol edelim:

```
storage/
├── manuscripts/      (Tam metinler)
├── forms/            (Formlar)
├── supplements/      (Ek dosyalar)
└── temp/             (Geçici yükleme)
```

**Görevler:**

- [ ] Klasörlerin varlığını kontrol et
- [ ] `.htaccess` dosyası var mı kontrol et
- [ ] Klasör izinlerini kontrol et (yazılabilir)

**Linux/Mac komut:**
```bash
chmod -R 755 storage/
```

**Windows:**
Klasör özelliklerinden yazma izni ver.

---

### 6.3 - FileController.php Oluştur

**Süre**: 3 saat

**Dosya**: `app/Controllers/FileController.php`

**Özellikler:**

- Dosya yükleme
- Dosya validasyonu (boyut, format)
- Güvenli dosya adı oluşturma
- Dosya listeleme
- Dosya silme
- Dosya indirme

**Kod taslağı:**

```php
<?php

namespace App\Controllers;

class FileController extends BaseController
{
    private $db;

    // Dosya türleri ve ayarları
    private $fileTypes = [
        'manuscript' => [
            'folder' => 'manuscripts',
            'extensions' => ['pdf', 'doc', 'docx'],
            'max_size' => 10 * 1024 * 1024, // 10 MB
            'required' => true
        ],
        'form' => [
            'folder' => 'forms',
            'extensions' => ['pdf', 'jpg', 'jpeg', 'png'],
            'max_size' => 5 * 1024 * 1024, // 5 MB
            'required' => true
        ],
        'supplement' => [
            'folder' => 'supplements',
            'extensions' => ['pdf', 'jpg', 'jpeg', 'png', 'zip', 'csv', 'xlsx'],
            'max_size' => 20 * 1024 * 1024, // 20 MB
            'required' => false
        ]
    ];

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Dosya yükle
     * POST /api/files/upload
     */
    public function upload()
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        // Dosya var mı?
        if (!isset($_FILES['file'])) {
            return $this->json(['error' => 'Dosya seçilmedi'], 400);
        }

        $file = $_FILES['file'];
        $fileType = $_POST['file_type'] ?? 'manuscript'; // manuscript, form, supplement
        $articleId = $_POST['article_id'] ?? null;

        // Dosya türü geçerli mi?
        if (!isset($this->fileTypes[$fileType])) {
            return $this->json(['error' => 'Geçersiz dosya türü'], 400);
        }

        $config = $this->fileTypes[$fileType];

        // Validasyon
        $validation = $this->validateFile($file, $config);
        if (!$validation['valid']) {
            return $this->json(['error' => $validation['error']], 400);
        }

        // Dosyayı kaydet
        $result = $this->saveFile($file, $fileType, $articleId, $userId);

        if ($result['success']) {
            return $this->json([
                'success' => true,
                'message' => 'Dosya yüklendi',
                'file_id' => $result['file_id'],
                'file_name' => $result['file_name']
            ]);
        } else {
            return $this->json(['error' => $result['error']], 500);
        }
    }

    /**
     * Dosya validasyonu
     */
    private function validateFile($file, $config)
    {
        // Yükleme hatası var mı?
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [
                'valid' => false,
                'error' => 'Dosya yüklenirken hata oluştu'
            ];
        }

        // Boyut kontrolü
        if ($file['size'] > $config['max_size']) {
            $maxMB = $config['max_size'] / (1024 * 1024);
            return [
                'valid' => false,
                'error' => "Dosya boyutu en fazla {$maxMB} MB olabilir"
            ];
        }

        // Uzantı kontrolü
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $config['extensions'])) {
            return [
                'valid' => false,
                'error' => 'Geçersiz dosya formatı. İzin verilen: ' . implode(', ', $config['extensions'])
            ];
        }

        // MIME type kontrolü (güvenlik)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowedMimes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'zip' => 'application/zip'
        ];

        if (isset($allowedMimes[$ext]) && $mimeType !== $allowedMimes[$ext]) {
            return [
                'valid' => false,
                'error' => 'Dosya içeriği format ile uyuşmuyor (güvenlik)'
            ];
        }

        return ['valid' => true];
    }

    /**
     * Dosyayı kaydet
     */
    private function saveFile($file, $fileType, $articleId, $userId)
    {
        $config = $this->fileTypes[$fileType];

        // Güvenli dosya adı oluştur
        $originalName = $file['name'];
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $safeName = $this->generateSafeFileName($articleId, $fileType, $ext);

        // Hedef klasör
        $targetFolder = __DIR__ . '/../../storage/' . $config['folder'];
        $targetPath = $targetFolder . '/' . $safeName;

        // Klasör yoksa oluştur
        if (!is_dir($targetFolder)) {
            mkdir($targetFolder, 0755, true);
        }

        // Dosyayı taşı
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Veritabanına kaydet
            $fileId = $this->saveFileRecord([
                'makale_id' => $articleId,
                'kullanici_id' => $userId,
                'dosya_turu' => $fileType,
                'orijinal_ad' => $originalName,
                'guvenli_ad' => $safeName,
                'dosya_yolu' => $config['folder'] . '/' . $safeName,
                'dosya_boyutu' => $file['size'],
                'mime_type' => $file['type']
            ]);

            return [
                'success' => true,
                'file_id' => $fileId,
                'file_name' => $safeName
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Dosya kaydedilemedi'
            ];
        }
    }

    /**
     * Güvenli dosya adı oluştur
     */
    private function generateSafeFileName($articleId, $fileType, $ext)
    {
        $timestamp = time();
        $random = bin2hex(random_bytes(8));

        return "makale-{$articleId}-{$fileType}-{$timestamp}-{$random}.{$ext}";
    }

    /**
     * Dosya listesi
     * GET /api/articles/{articleId}/files
     */
    public function listFiles($articleId)
    {
        $files = $this->getFilesByArticle($articleId);

        return $this->json([
            'success' => true,
            'files' => $files
        ]);
    }

    /**
     * Dosya sil
     * DELETE /api/files/{id}
     */
    public function delete($id)
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $file = $this->getFile($id);

        if (!$file || $file['kullanici_id'] != $userId) {
            return $this->json(['error' => 'Dosya bulunamadı'], 404);
        }

        // Fiziksel dosyayı sil
        $filePath = __DIR__ . '/../../storage/' . $file['dosya_yolu'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Veritabanından sil
        $this->deleteFileRecord($id);

        return $this->json([
            'success' => true,
            'message' => 'Dosya silindi'
        ]);
    }

    /**
     * Dosya indir
     * GET /api/files/{id}/download
     */
    public function download($id)
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            http_response_code(401);
            die('Unauthorized');
        }

        $file = $this->getFile($id);

        if (!$file || $file['kullanici_id'] != $userId) {
            http_response_code(404);
            die('Dosya bulunamadı');
        }

        $filePath = __DIR__ . '/../../storage/' . $file['dosya_yolu'];

        if (!file_exists($filePath)) {
            http_response_code(404);
            die('Dosya bulunamadı');
        }

        // Download headers
        header('Content-Type: ' . $file['mime_type']);
        header('Content-Disposition: attachment; filename="' . $file['orijinal_ad'] . '"');
        header('Content-Length: ' . filesize($filePath));

        readfile($filePath);
        exit;
    }

    // Helper metodlar...
    private function saveFileRecord($data) { }
    private function getFilesByArticle($articleId) { }
    private function getFile($id) { }
    private function deleteFileRecord($id) { }
}
```

**Görevler:**

- [ ] Dosyayı oluştur
- [ ] `upload()` metodunu yaz
- [ ] `validateFile()` metodunu yaz
- [ ] `saveFile()` metodunu yaz
- [ ] `listFiles()` metodunu yaz
- [ ] `delete()` metodunu yaz
- [ ] `download()` metodunu yaz
- [ ] Helper metodları yaz
- [ ] Routes ekle
- [ ] Test et (Postman)

---

### 6.4 - Veritabanı Tablosu Oluştur

**Süre**: 15 dakika

**Tablo**: `makale_dosyalari`

```sql
CREATE TABLE `makale_dosyalari` (
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
  KEY `idx_dosya_turu` (`dosya_turu`),

  FOREIGN KEY (`makale_id`) REFERENCES `makaleler` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Görevler:**

- [ ] SQL'i çalıştır
- [ ] Tabloyu phpMyAdmin'de kontrol et

---

### 6.5 - Dosya Validasyonu Test Et

**Süre**: 30 dakika

**Test senaryoları:**

- [ ] 11 MB PDF (red edilmeli, limit 10 MB)
- [ ] .exe dosyası (red edilmeli, izin verilen format değil)
- [ ] .pdf uzantılı ama içeriği TXT olan dosya (red edilmeli, MIME type uyuşmuyor)
- [ ] Geçerli PDF (kabul edilmeli)

**Postman test:**

```
POST /api/files/upload
Body: form-data
- file: [dosya seç]
- file_type: manuscript
- article_id: 1
```

---

### 6.6 - Dosya Yükleme UI Ekle

**Süre**: 2 saat

**Dosya**: `views/articles/create.php` (Dosya yükleme bölümü)

**UI:**

```html
<div class="card">
    <div class="card-header">
        <h5>Dosya Yükleme</h5>
    </div>
    <div class="card-body">
        <!-- Tam Metin -->
        <div class="form-group">
            <label>Tam Metin (PDF) *</label>
            <input type="file" id="manuscript-file" class="form-control-file" accept=".pdf">
            <small class="form-text text-muted">En fazla 10 MB</small>
            <div class="progress mt-2" style="display:none;" id="manuscript-progress">
                <div class="progress-bar" role="progressbar"></div>
            </div>
        </div>

        <!-- Formlar -->
        <div class="form-group">
            <label>Yayın Hakkı Devir Formu *</label>
            <input type="file" id="form-file" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png">
            <small class="form-text text-muted">En fazla 5 MB</small>
            <div class="progress mt-2" style="display:none;" id="form-progress">
                <div class="progress-bar" role="progressbar"></div>
            </div>
        </div>

        <!-- Ek Dosyalar -->
        <div class="form-group">
            <label>Ek Dosyalar (Opsiyonel)</label>
            <input type="file" id="supplement-file" class="form-control-file" multiple>
            <small class="form-text text-muted">En fazla 20 MB (her dosya)</small>
            <div class="progress mt-2" style="display:none;" id="supplement-progress">
                <div class="progress-bar" role="progressbar"></div>
            </div>
        </div>

        <hr>

        <!-- Yüklenen dosya listesi -->
        <div id="file-list">
            <h6>Yüklenen Dosyalar</h6>
            <div id="files-container"></div>
        </div>
    </div>
</div>

<script src="/assets/js/file-uploader.js"></script>
```

**Görevler:**

- [ ] HTML'i ekle
- [ ] CSS stilleri ekle
- [ ] Test et

---

### 6.7 - Progress Bar Ekle

**Süre**: 1 saat

**Dosya**: `public/assets/js/file-uploader.js`

**Özellikler:**

- Dosya seçildiğinde otomatik yükle
- Progress bar göster
- Hata durumunda mesaj göster
- Başarı durumunda dosya listesine ekle

**Kod taslağı:**

```javascript
class FileUploader {
    constructor(articleId) {
        this.articleId = articleId;
        this.apiBaseUrl = '/api/files';
    }

    init() {
        // Manuscript file
        document.getElementById('manuscript-file').addEventListener('change', (e) => {
            this.uploadFile(e.target.files[0], 'manuscript', 'manuscript-progress');
        });

        // Form file
        document.getElementById('form-file').addEventListener('change', (e) => {
            this.uploadFile(e.target.files[0], 'form', 'form-progress');
        });

        // Supplement files (multiple)
        document.getElementById('supplement-file').addEventListener('change', (e) => {
            Array.from(e.target.files).forEach(file => {
                this.uploadFile(file, 'supplement', 'supplement-progress');
            });
        });

        // Mevcut dosyaları yükle
        this.loadFiles();
    }

    async uploadFile(file, fileType, progressId) {
        const progressContainer = document.getElementById(progressId);
        const progressBar = progressContainer.querySelector('.progress-bar');

        progressContainer.style.display = 'block';
        progressBar.style.width = '0%';

        const formData = new FormData();
        formData.append('file', file);
        formData.append('file_type', fileType);
        formData.append('article_id', this.articleId);

        try {
            const xhr = new XMLHttpRequest();

            // Progress event
            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const percent = (e.loaded / e.total) * 100;
                    progressBar.style.width = percent + '%';
                    progressBar.textContent = Math.round(percent) + '%';
                }
            });

            // Complete event
            xhr.addEventListener('load', () => {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        progressBar.classList.add('bg-success');
                        setTimeout(() => {
                            progressContainer.style.display = 'none';
                            this.loadFiles(); // Listeyi yenile
                        }, 1000);
                    } else {
                        this.showError(response.error);
                        progressBar.classList.add('bg-danger');
                    }
                } else {
                    this.showError('Yükleme başarısız');
                    progressBar.classList.add('bg-danger');
                }
            });

            // Error event
            xhr.addEventListener('error', () => {
                this.showError('Bağlantı hatası');
                progressBar.classList.add('bg-danger');
            });

            xhr.open('POST', `${this.apiBaseUrl}/upload`);
            xhr.send(formData);

        } catch (error) {
            this.showError(error.message);
        }
    }

    async loadFiles() {
        const response = await fetch(`/api/articles/${this.articleId}/files`);
        const result = await response.json();

        if (result.success) {
            this.renderFiles(result.files);
        }
    }

    renderFiles(files) {
        const container = document.getElementById('files-container');

        if (files.length === 0) {
            container.innerHTML = '<p class="text-muted">Henüz dosya yüklenmedi</p>';
            return;
        }

        let html = '<table class="table table-sm">';
        html += '<thead><tr><th>Dosya Adı</th><th>Tür</th><th>Boyut</th><th>Tarih</th><th>İşlem</th></tr></thead>';
        html += '<tbody>';

        files.forEach(file => {
            const sizeMB = (file.dosya_boyutu / (1024 * 1024)).toFixed(2);

            html += `
                <tr>
                    <td>${file.orijinal_ad}</td>
                    <td>${file.dosya_turu}</td>
                    <td>${sizeMB} MB</td>
                    <td>${new Date(file.olusturma_tarihi).toLocaleString('tr-TR')}</td>
                    <td>
                        <a href="/api/files/${file.id}/download" class="btn btn-sm btn-primary">
                            <i class="fa fa-download"></i>
                        </a>
                        <button class="btn btn-sm btn-danger" onclick="fileUploader.deleteFile(${file.id})">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        html += '</tbody></table>';
        container.innerHTML = html;
    }

    async deleteFile(id) {
        if (!confirm('Dosyayı silmek istediğinize emin misiniz?')) {
            return;
        }

        const response = await fetch(`${this.apiBaseUrl}/${id}`, {
            method: 'DELETE'
        });

        const result = await response.json();

        if (result.success) {
            this.loadFiles();
        } else {
            alert(result.error);
        }
    }

    showError(message) {
        alert('Hata: ' + message);
    }
}

// Sayfa yüklendiğinde başlat
document.addEventListener('DOMContentLoaded', () => {
    const articleId = document.querySelector('[name="article_id"]')?.value;

    if (articleId) {
        window.fileUploader = new FileUploader(articleId);
        fileUploader.init();
    }
});
```

**Görevler:**

- [ ] Dosyayı oluştur
- [ ] `uploadFile()` yaz (XHR ile progress)
- [ ] `loadFiles()` yaz
- [ ] `renderFiles()` yaz
- [ ] `deleteFile()` yaz
- [ ] Test et

---

### 6.8 - Dosya Listesi Tablosu Ekle

**Süre**: 30 dakika

Zaten `file-uploader.js` içinde `renderFiles()` metodu var.

**Görevler:**

- [ ] Test et
- [ ] İndirme butonu çalışıyor mu?
- [ ] Silme butonu çalışıyor mu?

---

### 6.9 - Dosya Silme/İndirme Özellikleri Test Et

**Süre**: 30 dakika

**Test senaryoları:**

- [ ] Dosya yükleniyor
- [ ] Dosya listede görünüyor
- [ ] İndirme butonu dosyayı indiriyor
- [ ] Silme butonu onay soruyor
- [ ] Dosya siliniyor (fiziksel + veritabanı)
- [ ] Liste güncelleniyor

---

### 6.10 - Test Et

**Süre**: 1 saat

**Test senaryoları:**

**Dosya yükleme:**
- [ ] Geçerli PDF yükleniyor
- [ ] Progress bar çalışıyor
- [ ] Başarı mesajı gösteriliyor
- [ ] Dosya listede görünüyor

**Validasyon:**
- [ ] Büyük dosya red ediliyor
- [ ] Geçersiz format red ediliyor
- [ ] Hata mesajı gösteriliyor

**Dosya yönetimi:**
- [ ] İndirme çalışıyor
- [ ] Silme çalışıyor
- [ ] Liste güncelleniyor

**Güvenlik:**
- [ ] Başka kullanıcının dosyasını indiremez
- [ ] Başka kullanıcının dosyasını silemez
- [ ] Direct URL erişimi engelleniyor (storage/.htaccess)

---

## 🎉 FAZ 6 TAMAMLANDI MI?

Tüm checkboxlar işaretlendiyse:

- [ ] **Faz 6 tamamlandı!**
- [ ] [CHECKLIST-MASTER.md](CHECKLIST-MASTER.md) dosyasında Faz 6'yı tamamlandı olarak işaretle
- [ ] Faz 7'ye geç: [FAZ-7-EDITORE-NOT.md](FAZ-7-EDITORE-NOT.md)

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
