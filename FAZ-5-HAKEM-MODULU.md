# FAZ 5: HAKEM MODÜLÜ

**Durum**: ⚪ Bekliyor
**Tahmini Süre**: 2-3 gün
**Öncelik**: 🟢 Düşük
**Bağımlılık**: Faz 2 tamamlanmalı (Yazar modülü template olacak)

---

## 🎯 AMAÇ

Hakem önerme sistemi oluşturmak:
- Email/ORCID ile hakem arama (opsiyonel)
- Hakem ekleme formu
- Minimum hakem kontrolü (en az 3)
- Çıkar çatışması kontrolü (opsiyonel)

---

## ✅ GÖREVLER

### 5.1 - Gereksinimler Belirle

**Süre**: 30 dakika

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
   - [ ] Ad/Soyad ✓
   - [ ] Email ✓
   - [ ] ORCID (zorunlu mu?)
   - [ ] Kurum ✓
   - [ ] Uzmanlık Alanı
   - [ ] Diğer: ___

5. **Çıkar çatışması kontrolü?**
   - [ ] Evet (Hakem-Yazar aynı kurumdan mı kontrol edilsin?)
   - [ ] Hayır

**Kararlar:**

```
[Buraya kararlarınızı yazın]

Örnek:
- Email/ORCID arama: Hayır (basit versiyon)
- Minimum hakem: 3
- Hakem türü: Yok (sadece hakem)
- Zorunlu alanlar: Ad/Soyad, Email, Kurum
- Çıkar çatışması: Hayır (ileri faz)
```

---

### 5.2 - Veritabanı Tablosunu Oluştur

**Süre**: 15 dakika

**Tablo**: `makale_hakem_onerileri`

```sql
CREATE TABLE `makale_hakem_onerileri` (
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

  KEY `idx_makale` (`makale_id`),
  KEY `idx_email` (`email`),

  FOREIGN KEY (`makale_id`) REFERENCES `makaleler` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Görevler:**

- [ ] SQL'i çalıştır
- [ ] Tabloyu phpMyAdmin'de kontrol et
- [ ] Test verisi ekle

**Test SQL:**

```sql
INSERT INTO makale_hakem_onerileri (makale_id, ad, soyad, email, kurum, uzmanlik_alani, sira) VALUES
(1, 'Ali', 'Yılmaz', 'ali@example.com', 'İTÜ', 'Yapay Zeka', 1),
(1, 'Ayşe', 'Demir', 'ayse@example.com', 'ODTÜ', 'Makine Öğrenmesi', 2),
(1, 'Mehmet', 'Kaya', 'mehmet@example.com', 'Hacettepe', 'Veri Madenciliği', 3);

SELECT * FROM makale_hakem_onerileri WHERE makale_id = 1;
```

---

### 5.3 - ReviewerController.php Oluştur

**Süre**: 2 saat

**Dosya**: `app/Controllers/ReviewerController.php`

**Özellikler:**

- Hakem ekleme
- Hakem listesi
- Hakem silme
- Hakem düzenleme
- Minimum hakem kontrolü

**Kod taslağı:**

```php
<?php

namespace App\Controllers;

class ReviewerController extends BaseController
{
    private $db;
    private $minReviewers = 3; // Minimum hakem sayısı

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Hakem ekle
     * POST /api/articles/{articleId}/reviewers
     */
    public function addReviewer($articleId)
    {
        $data = [
            'makale_id' => $articleId,
            'ad' => $_POST['ad'] ?? '',
            'soyad' => $_POST['soyad'] ?? '',
            'email' => $_POST['email'] ?? '',
            'kurum' => $_POST['kurum'] ?? '',
            'uzmanlik_alani' => $_POST['uzmanlik_alani'] ?? '',
            'ulke' => $_POST['ulke'] ?? '',
            'orcid' => $_POST['orcid'] ?? '',
            'hakem_turu' => $_POST['hakem_turu'] ?? 'ana',
            'notlar' => $_POST['notlar'] ?? '',
            'sira' => $this->getNextOrder($articleId)
        ];

        // Validasyon
        if (empty($data['ad']) || empty($data['email']) || empty($data['kurum'])) {
            return $this->json(['error' => 'Gerekli alanları doldurun'], 400);
        }

        // Email format kontrolü
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->json(['error' => 'Geçersiz email'], 400);
        }

        // Aynı hakem daha önce eklendi mi?
        if ($this->isDuplicateReviewer($articleId, $data['email'])) {
            return $this->json(['error' => 'Bu hakem zaten eklenmiş'], 400);
        }

        $reviewerId = $this->insertReviewer($data);

        return $this->json([
            'success' => true,
            'message' => 'Hakem eklendi',
            'reviewer_id' => $reviewerId
        ]);
    }

    /**
     * Hakem listesi
     * GET /api/articles/{articleId}/reviewers
     */
    public function listReviewers($articleId)
    {
        $reviewers = $this->getReviewers($articleId);

        return $this->json([
            'success' => true,
            'reviewers' => $reviewers,
            'count' => count($reviewers),
            'min_required' => $this->minReviewers,
            'is_valid' => count($reviewers) >= $this->minReviewers
        ]);
    }

    /**
     * Hakem sil
     * DELETE /api/reviewers/{id}
     */
    public function deleteReviewer($id)
    {
        $result = $this->deleteReviewerById($id);

        if ($result) {
            return $this->json([
                'success' => true,
                'message' => 'Hakem silindi'
            ]);
        } else {
            return $this->json(['error' => 'Hakem silinemedi'], 400);
        }
    }

    /**
     * Hakem sayısı kontrolü
     * GET /api/articles/{articleId}/reviewers/validate
     */
    public function validate($articleId)
    {
        $reviewers = $this->getReviewers($articleId);
        $count = count($reviewers);

        return $this->json([
            'valid' => $count >= $this->minReviewers,
            'count' => $count,
            'min_required' => $this->minReviewers,
            'message' => $count >= $this->minReviewers
                ? 'Hakem sayısı yeterli'
                : "En az {$this->minReviewers} hakem önermeniz gerekiyor"
        ]);
    }

    // Helper metodlar...
    private function getNextOrder($articleId) { }
    private function isDuplicateReviewer($articleId, $email) { }
    private function insertReviewer($data) { }
    private function getReviewers($articleId) { }
    private function deleteReviewerById($id) { }
}
```

**Görevler:**

- [ ] Dosyayı oluştur
- [ ] `addReviewer()` metodunu yaz
- [ ] `listReviewers()` metodunu yaz
- [ ] `deleteReviewer()` metodunu yaz
- [ ] `validate()` metodunu yaz
- [ ] Helper metodları yaz
- [ ] Routes ekle
- [ ] Test et (Postman)

---

### 5.4 - Email/ORCID Arama API'leri (Opsiyonel)

**Süre**: 2 saat (sadece isterseniz)

Eğer 5.1'de "Evet" dediyseniz:

**Yazar modülündeki arama sistemini kopyalayın:**

- `AuthorController::searchByEmail()` → `ReviewerController::searchByEmail()`
- `AuthorController::searchByOrcid()` → `ReviewerController::searchByOrcid()`
- `author-search.js` → `reviewer-search.js`

**Görevler:**

- [ ] Gerekli mi? (5.1'deki karar)
- [ ] Evet ise, yazar modülünden kopyala
- [ ] Test et

**NOT:** İlk versiyonda bu opsiyoneldir. Daha sonra eklenebilir.

---

### 5.5 - Hakem Ekleme Formu UI

**Süre**: 2 saat

**Dosya**: `views/articles/create.php` (Hakem bölümü)

**UI:**

```html
<div class="card">
    <div class="card-header">
        <h5>Önerilen Hakemler</h5>
        <small class="text-muted">En az 3 hakem önermeniz gerekmektedir</small>
    </div>
    <div class="card-body">
        <!-- Hakem ekleme formu -->
        <form id="reviewer-form">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Ad *</label>
                        <input type="text" name="ad" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Soyad *</label>
                        <input type="text" name="soyad" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Kurum *</label>
                        <input type="text" name="kurum" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Uzmanlık Alanı</label>
                        <input type="text" name="uzmanlik_alani" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>ORCID</label>
                        <input type="text" name="orcid" class="form-control" placeholder="0000-0001-2345-6789">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Notlar (Neden bu hakemi öneriyorsunuz?)</label>
                <textarea name="notlar" class="form-control" rows="2"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fa fa-plus"></i> Hakem Ekle
            </button>
        </form>

        <hr>

        <!-- Hakem listesi -->
        <div id="reviewer-list">
            <h6>Eklenen Hakemler: <span id="reviewer-count">0</span> / 3</h6>
            <div id="reviewers-container"></div>
        </div>
    </div>
</div>

<script src="/assets/js/reviewer-manager.js"></script>
```

**Görevler:**

- [ ] HTML'i ekle
- [ ] CSS stilleri ekle
- [ ] Test et

---

### 5.6 - Hakem Listesi Tablosu

**Süre**: 1 saat

**Dosya**: `public/assets/js/reviewer-manager.js`

**Özellikler:**

- Hakem ekleme
- Hakem listesi gösterme
- Hakem silme
- Hakem sayısı kontrolü

**Kod taslağı:**

```javascript
class ReviewerManager {
    constructor(articleId) {
        this.articleId = articleId;
        this.apiBaseUrl = '/api';
        this.minReviewers = 3;
        this.reviewers = [];
    }

    init() {
        // Form submit
        document.getElementById('reviewer-form').addEventListener('submit', (e) => {
            e.preventDefault();
            this.addReviewer();
        });

        // Mevcut hakemleri yükle
        this.loadReviewers();
    }

    async addReviewer() {
        const form = document.getElementById('reviewer-form');
        const formData = new FormData(form);

        const response = await fetch(`${this.apiBaseUrl}/articles/${this.articleId}/reviewers`, {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert('Hakem eklendi');
            form.reset();
            this.loadReviewers();
        } else {
            alert(result.error || 'Hata oluştu');
        }
    }

    async loadReviewers() {
        const response = await fetch(`${this.apiBaseUrl}/articles/${this.articleId}/reviewers`);
        const result = await response.json();

        if (result.success) {
            this.reviewers = result.reviewers;
            this.renderReviewers();
            this.updateCount();
        }
    }

    renderReviewers() {
        const container = document.getElementById('reviewers-container');

        if (this.reviewers.length === 0) {
            container.innerHTML = '<p class="text-muted">Henüz hakem eklenmedi</p>';
            return;
        }

        let html = '<table class="table table-sm">';
        html += '<thead><tr><th>Ad Soyad</th><th>Email</th><th>Kurum</th><th>İşlem</th></tr></thead>';
        html += '<tbody>';

        this.reviewers.forEach(reviewer => {
            html += `
                <tr>
                    <td>${reviewer.ad} ${reviewer.soyad}</td>
                    <td>${reviewer.email}</td>
                    <td>${reviewer.kurum}</td>
                    <td>
                        <button class="btn btn-sm btn-danger" onclick="reviewerManager.deleteReviewer(${reviewer.id})">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        html += '</tbody></table>';
        container.innerHTML = html;
    }

    async deleteReviewer(id) {
        if (!confirm('Hakemi silmek istediğinize emin misiniz?')) {
            return;
        }

        const response = await fetch(`${this.apiBaseUrl}/reviewers/${id}`, {
            method: 'DELETE'
        });

        const result = await response.json();

        if (result.success) {
            this.loadReviewers();
        }
    }

    updateCount() {
        const countEl = document.getElementById('reviewer-count');
        countEl.textContent = this.reviewers.length;

        if (this.reviewers.length >= this.minReviewers) {
            countEl.classList.add('text-success');
            countEl.classList.remove('text-danger');
        } else {
            countEl.classList.add('text-danger');
            countEl.classList.remove('text-success');
        }
    }

    async validate() {
        const response = await fetch(`${this.apiBaseUrl}/articles/${this.articleId}/reviewers/validate`);
        const result = await response.json();

        return result.valid;
    }
}

// Sayfa yüklendiğinde başlat
document.addEventListener('DOMContentLoaded', () => {
    const articleId = document.querySelector('[name="article_id"]')?.value;

    if (articleId) {
        window.reviewerManager = new ReviewerManager(articleId);
        reviewerManager.init();
    }
});
```

**Görevler:**

- [ ] Dosyayı oluştur
- [ ] `addReviewer()` yaz
- [ ] `loadReviewers()` yaz
- [ ] `renderReviewers()` yaz
- [ ] `deleteReviewer()` yaz
- [ ] `validate()` yaz
- [ ] Test et

---

### 5.7 - Minimum Hakem Kontrolü

**Süre**: 1 saat

**Form submit öncesi validasyon:**

```javascript
// create.php - Form submit event
document.getElementById('article-form').addEventListener('submit', async (e) => {
    // ...diğer validasyonlar

    // Hakem kontrolü
    const isValid = await reviewerManager.validate();

    if (!isValid) {
        e.preventDefault();
        alert('En az 3 hakem önermeniz gerekmektedir!');
        return false;
    }

    // Devam et...
});
```

**Görevler:**

- [ ] Form submit'e kontrol ekle
- [ ] Test et (0, 1, 2, 3 hakem ile)
- [ ] Hata mesajı gösteriliyor mu?

---

### 5.8 - Test Et

**Süre**: 1 saat

**Test senaryoları:**

**Hakem ekleme:**
- [ ] Form çalışıyor
- [ ] Zorunlu alanlar kontrol ediliyor
- [ ] Email validasyonu çalışıyor
- [ ] Hakem listeye ekleniyor
- [ ] Hakem sayısı güncelleniyor

**Hakem silme:**
- [ ] Silme butonu çalışıyor
- [ ] Onay soruluyor
- [ ] Liste güncelleniyor

**Minimum hakem kontrolü:**
- [ ] 0 hakem ile form gönderilemiyor
- [ ] 2 hakem ile form gönderilemiyor
- [ ] 3 hakem ile form gönderilebiliyor

**Duplicate kontrolü:**
- [ ] Aynı email'e sahip hakem tekrar eklenemiyor

---

## 🎉 FAZ 5 TAMAMLANDI MI?

Tüm checkboxlar işaretlendiyse:

- [ ] **Faz 5 tamamlandı!**
- [ ] [CHECKLIST-MASTER.md](CHECKLIST-MASTER.md) dosyasında Faz 5'i tamamlandı olarak işaretle
- [ ] Faz 6'ya geç: [FAZ-6-DOSYA-YUKLEME.md](FAZ-6-DOSYA-YUKLEME.md)

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
