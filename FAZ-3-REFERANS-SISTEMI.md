# FAZ 3: REFERANS SİSTEMİ

**Durum**: ⚪ Bekliyor
**Tahmini Süre**: 1 gün
**Öncelik**: 🟡 Orta
**Bağımlılık**: Faz 1 tamamlanmalı

---

## 🎯 AMAÇ

Esnek referans ekleme sistemi oluşturmak:
- Tek tek referans ekleme (mevcut)
- Toplu referans ekleme
- İki mod arası geçiş
- Referans validasyonu

---

## ✅ GÖREVLER

### 3.1 - Mevcut Sistemi Test Et

**Süre**: 30 dakika

Mevcut tek tek ekleme modu zaten var. Test edelim:

**Test senaryoları:**

- [ ] Referans ekleme formu açılıyor
- [ ] Yeni referans eklenebiliyor
- [ ] Referans düzenlenebiliyor
- [ ] Referans silinebiliyor
- [ ] Referanslar sıralanabiliyor
- [ ] Form validasyonu çalışıyor

**Sorun varsa not al:**

```
[Buraya notlarınızı yazın]
```

---

### 3.2 - Toplu Ekleme Modu UI'ını Ekle

**Süre**: 2 saat

**Dosya**: `views/articles/create.php` (Referanslar bölümü)

**UI tasarımı:**

```html
<div class="reference-mode-switcher">
    <button type="button" class="btn btn-sm" id="mode-single" onclick="switchMode('single')">
        <i class="fa fa-plus"></i> Tek Tek Ekle
    </button>
    <button type="button" class="btn btn-sm" id="mode-bulk" onclick="switchMode('bulk')">
        <i class="fa fa-list"></i> Toplu Ekle
    </button>
</div>

<!-- Tek tek ekleme modu (mevcut) -->
<div id="single-mode" class="reference-mode active">
    <!-- Mevcut form -->
</div>

<!-- Toplu ekleme modu (yeni) -->
<div id="bulk-mode" class="reference-mode" style="display:none;">
    <div class="form-group">
        <label>Referansları Yapıştırın (Her satır bir referans)</label>
        <textarea id="bulk-references" class="form-control" rows="15"
            placeholder="Örnek:&#10;1. Smith J, Doe A. Title of article. Journal Name. 2020;15(3):123-45.&#10;2. Brown K. Another article title. Another Journal. 2019;10(2):67-89."></textarea>
        <small class="form-text text-muted">
            Her satıra bir referans yazın. Sistem otomatik olarak ayrıştıracak.
        </small>
    </div>
    <button type="button" class="btn btn-primary" onclick="parseBulkReferences()">
        <i class="fa fa-check"></i> Referansları İşle
    </button>

    <div id="bulk-preview" class="mt-3" style="display:none;">
        <h6>Bulunan Referanslar: <span id="ref-count"></span></h6>
        <div id="parsed-references"></div>
    </div>
</div>
```

**CSS:**

```css
.reference-mode-switcher {
    margin-bottom: 20px;
    border-bottom: 2px solid #e0e0e0;
    padding-bottom: 10px;
}

.reference-mode-switcher .btn {
    margin-right: 10px;
}

.reference-mode-switcher .btn.active {
    background-color: #007bff;
    color: white;
}

#bulk-references {
    font-family: 'Courier New', monospace;
    font-size: 14px;
}

#bulk-preview {
    border: 1px solid #ddd;
    padding: 15px;
    border-radius: 5px;
    background-color: #f9f9f9;
}

.parsed-reference-item {
    padding: 10px;
    border: 1px solid #ccc;
    margin-bottom: 10px;
    background-color: white;
}

.parsed-reference-item.valid {
    border-left: 4px solid #28a745;
}

.parsed-reference-item.invalid {
    border-left: 4px solid #dc3545;
}
```

**Görevler:**

- [ ] HTML'i ekle
- [ ] CSS'i ekle
- [ ] Mod değiştirme butonlarını yap
- [ ] Test et

---

### 3.3 - Backend Array Parse Ekle

**Süre**: 3 saat

**Dosya**: `app/Services/ReferenceParser.php`

**Özellikler:**

- Çok satırlı metni parse et
- Her satırı referans olarak tanımla
- Basit validasyon (en az 20 karakter, nokta içermeli)
- Numaralandırmayı otomatik temizle

**Kod taslağı:**

```php
<?php

namespace App\Services;

class ReferenceParser
{
    /**
     * Toplu referans metnini parse et
     * @param string $text Çok satırlı referans metni
     * @return array Parsed references
     */
    public function parseBulkReferences($text)
    {
        $lines = explode("\n", $text);
        $references = [];

        foreach ($lines as $index => $line) {
            $line = trim($line);

            if (empty($line)) {
                continue;
            }

            // Başındaki numaralandırmayı temizle (1., 2), [3], vb.)
            $cleanLine = $this->removeNumbering($line);

            // Validasyon
            if ($this->isValidReference($cleanLine)) {
                $references[] = [
                    'original' => $line,
                    'cleaned' => $cleanLine,
                    'order' => count($references) + 1,
                    'valid' => true
                ];
            } else {
                $references[] = [
                    'original' => $line,
                    'cleaned' => $cleanLine,
                    'order' => count($references) + 1,
                    'valid' => false,
                    'error' => 'Geçersiz referans formatı'
                ];
            }
        }

        return $references;
    }

    /**
     * Başındaki numaralandırmayı temizle
     */
    private function removeNumbering($text)
    {
        // Örnek formatlar:
        // "1. Smith..."
        // "1) Smith..."
        // "[1] Smith..."
        // "(1) Smith..."

        $patterns = [
            '/^\d+\.\s+/',    // 1.
            '/^\d+\)\s+/',    // 1)
            '/^\[\d+\]\s+/',  // [1]
            '/^\(\d+\)\s+/'   // (1)
        ];

        foreach ($patterns as $pattern) {
            $text = preg_replace($pattern, '', $text);
        }

        return trim($text);
    }

    /**
     * Basit referans validasyonu
     */
    private function isValidReference($text)
    {
        // En az 20 karakter
        if (strlen($text) < 20) {
            return false;
        }

        // En az bir nokta içermeli (cümle yapısı)
        if (strpos($text, '.') === false) {
            return false;
        }

        // Bazı harfler içermeli (sadece noktalama değil)
        if (!preg_match('/[a-zA-Z]/', $text)) {
            return false;
        }

        return true;
    }
}
```

**API Endpoint:**

```php
// POST /api/references/parse-bulk
public function parseBulk()
{
    $text = $_POST['text'] ?? '';

    $parser = new ReferenceParser();
    $references = $parser->parseBulkReferences($text);

    return $this->json([
        'success' => true,
        'count' => count($references),
        'references' => $references
    ]);
}
```

**Görevler:**

- [ ] `ReferenceParser.php` oluştur
- [ ] `parseBulkReferences()` yaz
- [ ] `removeNumbering()` yaz
- [ ] `isValidReference()` yaz
- [ ] API endpoint ekle
- [ ] Test et (Postman)

---

### 3.4 - İki Mod Arası Geçiş Ekle

**Süre**: 1 saat

**Dosya**: `public/assets/js/reference-manager.js` (yeni veya mevcut)

**Özellikler:**

- Tek tek mod ↔ Toplu mod geçişi
- Mevcut referansları koru
- Aktif modu göster

**Kod taslağı:**

```javascript
let currentMode = 'single';

function switchMode(mode) {
    currentMode = mode;

    // Butonları güncelle
    document.getElementById('mode-single').classList.toggle('active', mode === 'single');
    document.getElementById('mode-bulk').classList.toggle('active', mode === 'bulk');

    // Bölümleri göster/gizle
    document.getElementById('single-mode').style.display = mode === 'single' ? 'block' : 'none';
    document.getElementById('bulk-mode').style.display = mode === 'bulk' ? 'block' : 'none';
}

async function parseBulkReferences() {
    const text = document.getElementById('bulk-references').value;

    if (!text.trim()) {
        alert('Lütfen referans girin');
        return;
    }

    // API'ye gönder
    const response = await fetch('/api/references/parse-bulk', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `text=${encodeURIComponent(text)}`
    });

    const data = await response.json();

    if (data.success) {
        displayParsedReferences(data.references);
    }
}

function displayParsedReferences(references) {
    const container = document.getElementById('parsed-references');
    const count = document.getElementById('ref-count');

    count.textContent = references.length;

    let html = '';
    references.forEach((ref, index) => {
        const statusClass = ref.valid ? 'valid' : 'invalid';
        const statusIcon = ref.valid ? '✓' : '✗';

        html += `
            <div class="parsed-reference-item ${statusClass}">
                <div class="d-flex justify-content-between">
                    <strong>${statusIcon} Referans ${ref.order}</strong>
                    ${ref.valid ? '<button class="btn btn-sm btn-primary" onclick="addParsedReference('+index+')">Ekle</button>' : ''}
                </div>
                <p class="mb-0 mt-2">${ref.cleaned}</p>
                ${!ref.valid ? '<small class="text-danger">'+ref.error+'</small>' : ''}
            </div>
        `;
    });

    container.innerHTML = html;
    document.getElementById('bulk-preview').style.display = 'block';
}

function addParsedReference(index) {
    // Referansı listeye ekle (mevcut sisteme entegre et)
}
```

**Görevler:**

- [ ] `switchMode()` fonksiyonunu yaz
- [ ] `parseBulkReferences()` fonksiyonunu yaz
- [ ] `displayParsedReferences()` fonksiyonunu yaz
- [ ] `addParsedReference()` fonksiyonunu yaz
- [ ] Test et

---

### 3.5 - Test Et

**Süre**: 1 saat

**Test senaryoları:**

**Tek tek mod:**
- [ ] Referans eklenebiliyor
- [ ] Referans düzenlenebiliyor
- [ ] Referans silinebiliyor

**Toplu mod:**
- [ ] Çok satırlı metin yapıştırılabiliyor
- [ ] Parse butonu çalışıyor
- [ ] Referanslar ayrıştırılıyor
- [ ] Geçerli/geçersiz referanslar gösteriliyor
- [ ] Numaralandırma otomatik temizleniyor

**Geçiş:**
- [ ] Tek tek → Toplu geçiş çalışıyor
- [ ] Toplu → Tek tek geçiş çalışıyor
- [ ] Mevcut referanslar korunuyor

**Örnekler:**

Test metni:
```
1. Smith J, Doe A. Title of article. Journal Name. 2020;15(3):123-45.
2. Brown K. Another article title. Another Journal. 2019;10(2):67-89.
[3] Johnson M. Third article. Science Journal. 2018;5(1):10-20.
(4) Wilson L, Taylor R. Fourth article. Medical Review. 2021;20(4):200-215.
```

---

## 🎉 FAZ 3 TAMAMLANDI MI?

Tüm checkboxlar işaretlendiyse:

- [ ] **Faz 3 tamamlandı!**
- [ ] [CHECKLIST-MASTER.md](CHECKLIST-MASTER.md) dosyasında Faz 3'ü tamamlandı olarak işaretle
- [ ] Faz 4'e geç: [FAZ-4-TASLAK-SISTEMI.md](FAZ-4-TASLAK-SISTEMI.md)

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
