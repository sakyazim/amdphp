# FAZ 4: TASLAK KAYIT SİSTEMİ

**Durum**: ⚪ Bekliyor
**Tahmini Süre**: 2 gün
**Öncelik**: 🟡 Orta
**Bağımlılık**: Faz 1 tamamlanmalı

---

## 🎯 AMAÇ

Otomatik ve manuel taslak kayıt sistemi oluşturmak:
- Otomatik kayıt (30 saniye interval)
- Manuel kayıt butonu
- Taslak yükleme
- Taslak listesi (yazar paneli)
- Taslak yönetimi (devam et, sil)

---

## ✅ GÖREVLER

### 4.1 - Veritabanı Tablosunu Kontrol Et

**Süre**: 10 dakika

- [ ] `makale_taslaklari` tablosunun oluşturulduğunu kontrol et
- [ ] Test verisi ekle

**Test SQL:**

```sql
-- Test verisi
INSERT INTO makale_taslaklari (kullanici_id, taslak_adi, son_adim, taslak_verisi, durum) VALUES
(1, 'Test Makale Taslağı', 2, '{"baslik":"Test","tur":"arastirma"}', 'taslak');

-- Test sorgusu
SELECT * FROM makale_taslaklari WHERE kullanici_id = 1;
```

---

### 4.2 - TaslakController.php Oluştur

**Süre**: 2 saat

**Dosya**: `app/Controllers/TaslakController.php`

**Özellikler:**

- Otomatik kayıt API'si
- Manuel kayıt API'si
- Taslak yükleme API'si
- Taslak listeleme
- Taslak silme

**Kod taslağı:**

```php
<?php

namespace App\Controllers;

class TaslakController extends BaseController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Otomatik/Manuel taslak kaydet
     * POST /api/drafts/save
     */
    public function save()
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        // Form verisini al
        $data = [
            'taslak_adi' => $_POST['taslak_adi'] ?? 'İsimsiz Taslak',
            'son_adim' => $_POST['son_adim'] ?? 0,
            'taslak_verisi' => json_encode($_POST['data'] ?? []),
            'toplam_adim' => $_POST['toplam_adim'] ?? 13
        ];

        // Mevcut taslak var mı kontrol et
        $existingDraft = $this->findDraftByUser($userId);

        if ($existingDraft) {
            // Güncelle
            $this->updateDraft($existingDraft['id'], $data);
            return $this->json([
                'success' => true,
                'message' => 'Taslak güncellendi',
                'draft_id' => $existingDraft['id']
            ]);
        } else {
            // Yeni oluştur
            $draftId = $this->createDraft($userId, $data);
            return $this->json([
                'success' => true,
                'message' => 'Taslak oluşturuldu',
                'draft_id' => $draftId
            ]);
        }
    }

    /**
     * Taslak yükle
     * GET /api/drafts/{id}
     */
    public function load($id)
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $draft = $this->findDraft($id, $userId);

        if (!$draft) {
            return $this->json(['error' => 'Taslak bulunamadı'], 404);
        }

        return $this->json([
            'success' => true,
            'draft' => [
                'id' => $draft['id'],
                'taslak_adi' => $draft['taslak_adi'],
                'son_adim' => $draft['son_adim'],
                'data' => json_decode($draft['taslak_verisi'], true),
                'son_guncelleme' => $draft['son_guncelleme']
            ]
        ]);
    }

    /**
     * Kullanıcının taslak listesi
     * GET /api/drafts
     */
    public function listDrafts()
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $drafts = $this->getDraftsByUser($userId);

        return $this->json([
            'success' => true,
            'drafts' => $drafts
        ]);
    }

    /**
     * Taslak sil
     * DELETE /api/drafts/{id}
     */
    public function delete($id)
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $result = $this->deleteDraft($id, $userId);

        if ($result) {
            return $this->json([
                'success' => true,
                'message' => 'Taslak silindi'
            ]);
        } else {
            return $this->json(['error' => 'Taslak silinemedi'], 400);
        }
    }

    // Helper metodlar...
    private function findDraftByUser($userId) { }
    private function findDraft($id, $userId) { }
    private function createDraft($userId, $data) { }
    private function updateDraft($id, $data) { }
    private function getDraftsByUser($userId) { }
    private function deleteDraft($id, $userId) { }
}
```

**Görevler:**

- [ ] Dosyayı oluştur
- [ ] `save()` metodunu yaz
- [ ] `load()` metodunu yaz
- [ ] `listDrafts()` metodunu yaz
- [ ] `delete()` metodunu yaz
- [ ] Helper metodları yaz
- [ ] Routes ekle
- [ ] Test et (Postman)

---

### 4.3 - Otomatik Kayıt API'si Test Et

**Süre**: 30 dakika

**Endpoint**: `POST /api/drafts/save`

**Request:**
```json
{
  "taslak_adi": "Test Makale",
  "son_adim": 2,
  "toplam_adim": 13,
  "data": {
    "baslik": "Makale Başlığı",
    "baslik_en": "Article Title",
    "tur": "arastirma",
    "konu": "bilgisayar",
    "ozet": "Bu bir özet...",
    "authors": [
      {
        "name": "John Doe",
        "email": "john@example.com"
      }
    ]
  }
}
```

**Response:**
```json
{
  "success": true,
  "message": "Taslak oluşturuldu",
  "draft_id": 123
}
```

**Görevler:**

- [ ] Postman'de test et
- [ ] Veritabanında kontrol et
- [ ] JSON parse/encode doğru çalışıyor mu?

---

### 4.4 - Manuel Kayıt API'si Test Et

**Süre**: 15 dakika

Aynı endpoint, fakat kullanıcı manuel "Kaydet" butonuna bastığında çağrılacak.

**Görevler:**

- [ ] Test et
- [ ] Başarı mesajı gösteriliyor mu?

---

### 4.5 - Taslak Yükleme API'si Test Et

**Süre**: 30 dakika

**Endpoint**: `GET /api/drafts/123`

**Response:**
```json
{
  "success": true,
  "draft": {
    "id": 123,
    "taslak_adi": "Test Makale",
    "son_adim": 2,
    "data": {
      "baslik": "Makale Başlığı",
      "baslik_en": "Article Title",
      "tur": "arastirma"
    },
    "son_guncelleme": "2024-12-03 14:30:00"
  }
}
```

**Görevler:**

- [ ] Test et
- [ ] JSON doğru parse ediliyor mu?
- [ ] Sadece kendi taslağını görebiliyor mu? (güvenlik testi)

---

### 4.6 - taslak-sistemi.js Oluştur

**Süre**: 3 saat

**Dosya**: `public/assets/js/taslak-sistemi.js`

**Özellikler:**

- Otomatik kayıt (30 saniye interval)
- Manuel kayıt
- Taslak yükleme
- Form verilerini serialize et
- Son kayıt zamanını göster

**Kod taslağı:**

```javascript
class TaslakSistemi {
    constructor(options) {
        this.apiBaseUrl = options.apiBaseUrl || '/api/drafts';
        this.formSelector = options.formSelector;
        this.autoSaveInterval = options.autoSaveInterval || 30000; // 30 saniye
        this.autoSaveEnabled = options.autoSaveEnabled !== false;
        this.lastSaveTime = null;
        this.draftId = null;
        this.intervalId = null;
    }

    init() {
        if (this.autoSaveEnabled) {
            this.startAutoSave();
        }

        // Manuel kayıt butonu
        const saveBtn = document.getElementById('manual-save-btn');
        if (saveBtn) {
            saveBtn.addEventListener('click', () => this.manualSave());
        }

        // Sayfa yüklendiğinde taslak var mı kontrol et
        this.checkForExistingDraft();
    }

    startAutoSave() {
        console.log('Otomatik kayıt başlatıldı (30 saniye)');

        this.intervalId = setInterval(() => {
            this.autoSave();
        }, this.autoSaveInterval);
    }

    stopAutoSave() {
        if (this.intervalId) {
            clearInterval(this.intervalId);
            console.log('Otomatik kayıt durduruldu');
        }
    }

    async autoSave() {
        console.log('Otomatik kayıt yapılıyor...');
        const data = this.serializeForm();

        const response = await fetch(`${this.apiBaseUrl}/save`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            this.draftId = result.draft_id;
            this.lastSaveTime = new Date();
            this.updateSaveStatus('Otomatik kaydedildi');
        }
    }

    async manualSave() {
        console.log('Manuel kayıt yapılıyor...');
        const data = this.serializeForm();

        const response = await fetch(`${this.apiBaseUrl}/save`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            this.draftId = result.draft_id;
            this.lastSaveTime = new Date();
            this.updateSaveStatus('Taslak kaydedildi');
            alert('Taslak başarıyla kaydedildi!');
        }
    }

    async loadDraft(draftId) {
        console.log('Taslak yükleniyor:', draftId);

        const response = await fetch(`${this.apiBaseUrl}/${draftId}`);
        const result = await response.json();

        if (result.success) {
            this.fillForm(result.draft.data);
            this.draftId = result.draft.id;
            // İstenen adıma git
            if (result.draft.son_adim) {
                this.goToStep(result.draft.son_adim);
            }
        }
    }

    serializeForm() {
        const form = document.querySelector(this.formSelector);
        const formData = new FormData(form);

        const data = {
            taslak_adi: formData.get('baslik') || 'İsimsiz Taslak',
            son_adim: parseInt(formData.get('current_step')) || 1,
            toplam_adim: 13,
            data: {}
        };

        // Tüm form verilerini topla
        for (let [key, value] of formData.entries()) {
            data.data[key] = value;
        }

        return data;
    }

    fillForm(data) {
        // Form alanlarını doldur
        for (let [key, value] of Object.entries(data)) {
            const input = document.querySelector(`[name="${key}"]`);
            if (input) {
                input.value = value;
            }
        }
    }

    updateSaveStatus(message) {
        const statusEl = document.getElementById('save-status');
        if (statusEl) {
            const time = this.lastSaveTime.toLocaleTimeString('tr-TR');
            statusEl.innerHTML = `<i class="fa fa-check text-success"></i> ${message} (${time})`;
        }
    }

    checkForExistingDraft() {
        // URL'de draft_id var mı?
        const urlParams = new URLSearchParams(window.location.search);
        const draftId = urlParams.get('draft_id');

        if (draftId) {
            this.loadDraft(draftId);
        }
    }

    goToStep(step) {
        // Wizard sistemine entegre et
        if (typeof wizardGoToStep === 'function') {
            wizardGoToStep(step);
        }
    }
}

// Sayfa yüklendiğinde başlat
document.addEventListener('DOMContentLoaded', () => {
    window.taslakSistemi = new TaslakSistemi({
        formSelector: '#article-form',
        autoSaveInterval: 30000, // 30 saniye
        autoSaveEnabled: true
    });

    taslakSistemi.init();
});
```

**Görevler:**

- [ ] Dosyayı oluştur
- [ ] `autoSave()` yaz
- [ ] `manualSave()` yaz
- [ ] `loadDraft()` yaz
- [ ] `serializeForm()` yaz
- [ ] `fillForm()` yaz
- [ ] Test et

---

### 4.7 - Otomatik Kayıt (30 saniye interval) Ekle

**Süre**: 30 dakika

Otomatik kayıt sistemi zaten `taslak-sistemi.js` içinde var.

**Görevler:**

- [ ] `create.php` dosyasına JS'i include et
- [ ] Test et (konsola log bakarak)
- [ ] 30 saniye bekle, otomatik kaydediliyor mu?

**HTML (create.php):**

```html
<div class="card-footer">
    <div id="save-status" class="text-muted">
        <i class="fa fa-clock"></i> Otomatik kayıt aktif (30 saniye)
    </div>
</div>

<script src="/assets/js/taslak-sistemi.js"></script>
```

---

### 4.8 - Manuel Kayıt Butonu Ekle

**Süre**: 15 dakika

**HTML:**

```html
<button type="button" id="manual-save-btn" class="btn btn-secondary">
    <i class="fa fa-save"></i> Taslak Kaydet
</button>
```

**Görevler:**

- [ ] Butonu ekle
- [ ] Event listener zaten JS'de var
- [ ] Test et

---

### 4.9 - Taslak Listesi (Yazar Paneli) Ekle

**Süre**: 2 saat

**Dosya**: `views/author/drafts.php` (yeni sayfa)

**UI:**

```html
<div class="container">
    <h2>Taslak Makalelerim</h2>

    <table class="table">
        <thead>
            <tr>
                <th>Taslak Adı</th>
                <th>Son Adım</th>
                <th>Son Güncelleme</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody id="draft-list">
            <!-- Dinamik olarak doldurulacak -->
        </tbody>
    </table>
</div>

<script>
async function loadDrafts() {
    const response = await fetch('/api/drafts');
    const result = await response.json();

    if (result.success) {
        const tbody = document.getElementById('draft-list');
        tbody.innerHTML = '';

        result.drafts.forEach(draft => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${draft.taslak_adi}</td>
                <td>Adım ${draft.son_adim} / ${draft.toplam_adim}</td>
                <td>${new Date(draft.son_guncelleme).toLocaleString('tr-TR')}</td>
                <td>
                    <a href="/articles/create?draft_id=${draft.id}" class="btn btn-sm btn-primary">
                        <i class="fa fa-edit"></i> Devam Et
                    </a>
                    <button class="btn btn-sm btn-danger" onclick="deleteDraft(${draft.id})">
                        <i class="fa fa-trash"></i> Sil
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }
}

async function deleteDraft(id) {
    if (!confirm('Taslağı silmek istediğinize emin misiniz?')) {
        return;
    }

    const response = await fetch(`/api/drafts/${id}`, {
        method: 'DELETE'
    });

    const result = await response.json();

    if (result.success) {
        alert('Taslak silindi');
        loadDrafts(); // Listeyi yenile
    }
}

loadDrafts();
</script>
```

**Görevler:**

- [ ] `drafts.php` oluştur
- [ ] `loadDrafts()` fonksiyonunu yaz
- [ ] `deleteDraft()` fonksiyonunu yaz
- [ ] Menüye link ekle
- [ ] Test et

---

### 4.10 - Test Et

**Süre**: 1 saat

**Test senaryoları:**

**Otomatik kayıt:**
- [ ] Sayfa açıldığında otomatik kayıt başlıyor
- [ ] 30 saniye sonra kayıt yapılıyor
- [ ] Konsola log yazılıyor
- [ ] Veritabanında kayıt oluşuyor
- [ ] İkinci kayıtta update yapılıyor (duplicate oluşmuyor)

**Manuel kayıt:**
- [ ] "Taslak Kaydet" butonu çalışıyor
- [ ] Başarı mesajı gösteriliyor
- [ ] Veritabanına kaydediliyor

**Taslak yükleme:**
- [ ] Taslak listesinde "Devam Et" çalışıyor
- [ ] Form alanları dolduruluyor
- [ ] Doğru adıma gidiyor

**Taslak silme:**
- [ ] Silme butonu çalışıyor
- [ ] Onay soruluyor
- [ ] Veritabanından siliniyor
- [ ] Liste güncelleniyor

---

## 🎉 FAZ 4 TAMAMLANDI MI?

Tüm checkboxlar işaretlendiyse:

- [ ] **Faz 4 tamamlandı!**
- [ ] [CHECKLIST-MASTER.md](CHECKLIST-MASTER.md) dosyasında Faz 4'ü tamamlandı olarak işaretle
- [ ] Faz 5'e geç: [FAZ-5-HAKEM-MODULU.md](FAZ-5-HAKEM-MODULU.md)

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
