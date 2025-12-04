# FAZ 2: YAZAR MODÜLÜ

**Durum**: ⚪ Bekliyor
**Tahmini Süre**: 2-3 gün
**Öncelik**: 🔥 Kritik
**Bağımlılık**: Faz 1 tamamlanmalı

---

## 🎯 AMAÇ

Gelişmiş yazar yönetim sistemi oluşturmak:
- Email ile yazar arama
- ORCID ile yazar arama
- Otomatik form doldurma
- Yazar profili yönetimi
- Co-author ekleme sistemi

---

## ✅ GÖREVLER

### 2.1 - Veritabanı Tablolarını Kontrol Et

**Süre**: 10 dakika

- [ ] `kullanici_yazar_profilleri` tablosunun oluşturulduğunu kontrol et
- [ ] `makale_yazarlari` tablosunun güncellendiğini kontrol et
- [ ] Test verisi ekle

**Test SQL:**

```sql
-- Test verisi
INSERT INTO kullanici_yazar_profilleri (kullanici_id, unvan, departman, kurum, ulke, orcid) VALUES
(1, 'Prof. Dr.', 'Bilgisayar Mühendisliği', 'Ankara Üniversitesi', 'Türkiye', '0000-0001-2345-6789');

-- Test sorgusu
SELECT * FROM kullanici_yazar_profilleri WHERE kullanici_id = 1;
```

---

### 2.2 - AuthorController.php Oluştur

**Süre**: 2 saat

**Dosya**: `app/Controllers/AuthorController.php`

**Özellikler:**

- Email ile yazar arama
- ORCID ile yazar arama
- Yazar profili oluşturma/güncelleme
- Co-author ekleme/çıkarma

**Kod taslağı:**

```php
<?php

namespace App\Controllers;

class AuthorController extends BaseController
{
    private $db;
    private $orcidService;

    public function __construct($db)
    {
        $this->db = $db;
        $this->orcidService = new \App\Services\OrcidService();
    }

    /**
     * Email ile yazar ara
     * GET /api/authors/search-by-email?email=xxx
     */
    public function searchByEmail()
    {
        $email = $_GET['email'] ?? '';

        // Önce kendi sistemimizde ara
        $user = $this->findUserByEmail($email);

        if ($user) {
            return $this->json([
                'found' => true,
                'source' => 'internal',
                'author' => $user
            ]);
        }

        return $this->json([
            'found' => false,
            'message' => 'Yazar bulunamadı'
        ]);
    }

    /**
     * ORCID ile yazar ara
     * GET /api/authors/search-by-orcid?orcid=0000-0001-2345-6789
     */
    public function searchByOrcid()
    {
        $orcid = $_GET['orcid'] ?? '';

        // Önce kendi sistemimizde ara
        $user = $this->findUserByOrcid($orcid);

        if ($user) {
            return $this->json([
                'found' => true,
                'source' => 'internal',
                'author' => $user
            ]);
        }

        // ORCID API'sinde ara
        $orcidData = $this->orcidService->getAuthorInfo($orcid);

        if ($orcidData) {
            return $this->json([
                'found' => true,
                'source' => 'orcid',
                'author' => $orcidData
            ]);
        }

        return $this->json([
            'found' => false,
            'message' => 'ORCID bulunamadı'
        ]);
    }

    /**
     * Yazar profili oluştur/güncelle
     * POST /api/authors/profile
     */
    public function updateProfile()
    {
        // Implementasyon...
    }

    /**
     * Makaleye co-author ekle
     * POST /api/articles/{id}/authors
     */
    public function addCoAuthor($articleId)
    {
        // Implementasyon...
    }
}
```

**Görevler:**

- [ ] Dosyayı oluştur
- [ ] `searchByEmail()` metodunu yaz
- [ ] `searchByOrcid()` metodunu yaz
- [ ] `updateProfile()` metodunu yaz
- [ ] `addCoAuthor()` metodunu yaz
- [ ] Routes ekle
- [ ] Test et

---

### 2.3 - OrcidService.php Oluştur

**Süre**: 2 saat

**Dosya**: `app/Services/OrcidService.php`

**Özellikler:**

- ORCID Public API entegrasyonu
- Yazar bilgilerini getir
- Cache mekanizması
- Rate limiting

**ORCID API Endpoints:**

- Public API: `https://pub.orcid.org/v3.0/`
- Örnek: `https://pub.orcid.org/v3.0/0000-0001-2345-6789`

**Kod taslağı:**

```php
<?php

namespace App\Services;

class OrcidService
{
    private $apiBaseUrl = 'https://pub.orcid.org/v3.0/';
    private $cache = [];

    /**
     * ORCID'den yazar bilgilerini getir
     * @param string $orcid ORCID ID (örn: 0000-0001-2345-6789)
     * @return array|null
     */
    public function getAuthorInfo($orcid)
    {
        // ORCID formatını validate et
        if (!$this->validateOrcid($orcid)) {
            return null;
        }

        // Cache kontrol et
        if (isset($this->cache[$orcid])) {
            return $this->cache[$orcid];
        }

        // API'ye istek at
        $url = $this->apiBaseUrl . $orcid;
        $headers = [
            'Accept: application/json'
        ];

        $response = $this->makeRequest($url, $headers);

        if ($response) {
            $data = $this->parseOrcidResponse($response);
            $this->cache[$orcid] = $data;
            return $data;
        }

        return null;
    }

    /**
     * ORCID formatını validate et
     */
    private function validateOrcid($orcid)
    {
        // Format: 0000-0001-2345-6789
        return preg_match('/^\d{4}-\d{4}-\d{4}-\d{3}[0-9X]$/', $orcid);
    }

    /**
     * ORCID response'unu parse et
     */
    private function parseOrcidResponse($response)
    {
        // JSON parse ve normalize et
        // Döndürülecek format:
        // [
        //   'orcid' => '0000-0001-2345-6789',
        //   'name' => 'John Doe',
        //   'email' => 'john@example.com',
        //   'affiliation' => 'University of ABC',
        //   'country' => 'USA'
        // ]
    }

    private function makeRequest($url, $headers)
    {
        // cURL ile istek at
    }
}
```

**Görevler:**

- [ ] Dosyayı oluştur
- [ ] `getAuthorInfo()` metodunu yaz
- [ ] `validateOrcid()` metodunu yaz
- [ ] `parseOrcidResponse()` metodunu yaz
- [ ] Cache mekanizması ekle
- [ ] Test et (gerçek ORCID ile)

---

### 2.4 - Email Arama API'si Yaz

**Süre**: 30 dakika

**Endpoint**: `GET /api/authors/search-by-email`

**Request:**
```
GET /api/authors/search-by-email?email=john@example.com
```

**Response:**
```json
{
  "found": true,
  "source": "internal",
  "author": {
    "id": 123,
    "name": "John Doe",
    "email": "john@example.com",
    "title": "Prof. Dr.",
    "department": "Computer Science",
    "institution": "ABC University",
    "country": "USA",
    "orcid": "0000-0001-2345-6789"
  }
}
```

**Görevler:**

- [ ] Route ekle
- [ ] Controller metodunu yaz
- [ ] Test et (Postman)

---

### 2.5 - ORCID Arama API'si Yaz

**Süre**: 30 dakika

**Endpoint**: `GET /api/authors/search-by-orcid`

**Request:**
```
GET /api/authors/search-by-orcid?orcid=0000-0001-2345-6789
```

**Response:**
```json
{
  "found": true,
  "source": "orcid",
  "author": {
    "orcid": "0000-0001-2345-6789",
    "name": "John Doe",
    "email": "john@example.com",
    "affiliation": "ABC University",
    "country": "USA"
  }
}
```

**Görevler:**

- [ ] Route ekle
- [ ] Controller metodunu yaz
- [ ] Test et (Postman)

---

### 2.6 - author-search.js Oluştur

**Süre**: 2 saat

**Dosya**: `public/assets/js/author-search.js`

**Özellikler:**

- Email arama UI
- ORCID arama UI
- Debounce ile arama
- Sonuçları göster
- Form otomatik doldurma

**Kod taslağı:**

```javascript
class AuthorSearch {
    constructor(options) {
        this.apiBaseUrl = options.apiBaseUrl || '/api/authors';
        this.emailInput = options.emailInput;
        this.orcidInput = options.orcidInput;
        this.resultContainer = options.resultContainer;
        this.onSelect = options.onSelect || null;
    }

    init() {
        // Email input'a event listener ekle
        this.emailInput.addEventListener('input',
            this.debounce(() => this.searchByEmail(), 500)
        );

        // ORCID input'a event listener ekle
        this.orcidInput.addEventListener('input',
            this.debounce(() => this.searchByOrcid(), 500)
        );
    }

    async searchByEmail() {
        const email = this.emailInput.value.trim();
        if (email.length < 3) return;

        const response = await fetch(`${this.apiBaseUrl}/search-by-email?email=${email}`);
        const data = await response.json();

        this.displayResults(data);
    }

    async searchByOrcid() {
        const orcid = this.orcidInput.value.trim();
        if (!this.validateOrcid(orcid)) return;

        const response = await fetch(`${this.apiBaseUrl}/search-by-orcid?orcid=${orcid}`);
        const data = await response.json();

        this.displayResults(data);
    }

    displayResults(data) {
        if (data.found) {
            // Sonuçları göster
            this.resultContainer.innerHTML = `
                <div class="author-result">
                    <strong>${data.author.name}</strong>
                    <p>${data.author.institution}</p>
                    <button onclick="authorSearch.fillForm(${JSON.stringify(data.author)})">
                        Bu Yazarı Kullan
                    </button>
                </div>
            `;
        } else {
            this.resultContainer.innerHTML = '<p>Yazar bulunamadı</p>';
        }
    }

    fillForm(author) {
        // Formu otomatik doldur
        if (this.onSelect) {
            this.onSelect(author);
        }
    }

    validateOrcid(orcid) {
        return /^\d{4}-\d{4}-\d{4}-\d{3}[0-9X]$/.test(orcid);
    }

    debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }
}
```

**Görevler:**

- [ ] Dosyayı oluştur
- [ ] `searchByEmail()` yaz
- [ ] `searchByOrcid()` yaz
- [ ] `displayResults()` yaz
- [ ] `fillForm()` yaz
- [ ] Debounce ekle
- [ ] Test et

---

### 2.7 - Email Arama UI'ını Ekle

**Süre**: 1 saat

**Dosya**: `views/articles/create.php` (Yazar bölümü)

**UI:**

```html
<div class="form-group">
    <label>Yazar Email</label>
    <input type="email" id="author-email" class="form-control" placeholder="Email girin...">
    <small class="form-text text-muted">Email girerken sistem otomatik arama yapacak</small>
    <div id="email-search-results" class="author-search-results"></div>
</div>
```

**Görevler:**

- [ ] HTML'i ekle
- [ ] CSS stilleri ekle
- [ ] JS'i include et
- [ ] Test et

---

### 2.8 - ORCID Arama UI'ını Ekle

**Süre**: 1 saat

**UI:**

```html
<div class="form-group">
    <label>ORCID</label>
    <input type="text" id="author-orcid" class="form-control" placeholder="0000-0001-2345-6789">
    <small class="form-text text-muted">ORCID girerken sistem otomatik arama yapacak</small>
    <div id="orcid-search-results" class="author-search-results"></div>
</div>
```

**Görevler:**

- [ ] HTML'i ekle
- [ ] ORCID format validasyonu ekle
- [ ] Test et

---

### 2.9 - Otomatik Form Doldurma Ekle

**Süre**: 1 saat

**Özellik:**

Yazar arama sonucunda "Bu Yazarı Kullan" butonuna tıklandığında:
- Ad/Soyad
- Email
- Unvan
- Departman
- Kurum
- Ülke
- ORCID

alanları otomatik doldurulmalı.

**Görevler:**

- [ ] `fillForm()` fonksiyonunu yaz
- [ ] Tüm form alanlarını map et
- [ ] Test et

---

### 2.10 - Test Et

**Süre**: 1 saat

**Test senaryoları:**

- [ ] Email arama çalışıyor
- [ ] ORCID arama çalışıyor
- [ ] Debounce çalışıyor (gereksiz API çağrısı yok)
- [ ] Sonuçlar doğru gösteriliyor
- [ ] "Bu Yazarı Kullan" formu dolduruyor
- [ ] ORCID validation çalışıyor
- [ ] Cache çalışıyor (aynı ORCID tekrar aranmıyor)
- [ ] Co-author ekleme çalışıyor

---

## 🎉 FAZ 2 TAMAMLANDI MI?

Tüm checkboxlar işaretlendiyse:

- [ ] **Faz 2 tamamlandı!**
- [ ] [CHECKLIST-MASTER.md](CHECKLIST-MASTER.md) dosyasında Faz 2'yi tamamlandı olarak işaretle
- [ ] Faz 3'e geç: [FAZ-3-REFERANS-SISTEMI.md](FAZ-3-REFERANS-SISTEMI.md)

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
