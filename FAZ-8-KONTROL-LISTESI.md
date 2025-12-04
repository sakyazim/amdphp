# FAZ 8: KONTROL LİSTESİ

**Durum**: ⚪ Bekliyor
**Tahmini Süre**: 1 gün
**Öncelik**: 🟢 Düşük
**Bağımlılık**: Faz 1 tamamlanmalı

---

## 🎯 AMAÇ

Makale gönderimi öncesi kontrol listesi sistemi oluşturmak:
- 3 kategori (Makale Bilgileri, Yazarlar, Etik/Telif)
- Progress bar
- "Tümünü İşaretle" butonu
- Frontend validasyon
- Kullanıcı deneyimini iyileştirme

---

## ✅ GÖREVLER

### 8.1 - Kontrol Listesi Maddelerini Belirle

**Süre**: 30 dakika

**Lütfen aşağıdaki kategoriler için kontrol listesi maddelerini belirleyin:**

#### Kategori 1: Makale Bilgileri

- [ ] Makale başlığı Türkçe ve İngilizce olarak girildi
- [ ] Özet Türkçe ve İngilizce olarak girildi
- [ ] Anahtar kelimeler Türkçe ve İngilizce olarak girildi (en az 3)
- [ ] Makale türü seçildi
- [ ] Makale konusu seçildi
- [ ] Tam metin (PDF) yüklendi
- [ ] Referanslar eklendi (en az 10)

#### Kategori 2: Yazar Bilgileri

- [ ] En az bir yazar eklendi
- [ ] Tüm yazarlar için email adresi girildi
- [ ] Tüm yazarlar için kurum bilgisi girildi
- [ ] Sorumlu yazar (corresponding author) belirlendi
- [ ] ORCID bilgileri girildi (önerilen)

#### Kategori 3: Etik ve Telif Hakları

- [ ] Yayın hakkı devir formu yüklendi
- [ ] Etik kurul onayı alındı (gerekiyorsa)
- [ ] Çıkar çatışması beyanı okundu ve kabul edildi
- [ ] Yazarlık kriterleri okundu ve kabul edildi
- [ ] Makale daha önce başka bir yerde yayınlanmadı
- [ ] Tüm yazarlar makalenin gönderilmesini onayladı

**Özelleştirin:**

```
[Buraya kendi kontrol listesi maddelerinizi yazın]

Örnek:
- Kategori 1: 7 madde
- Kategori 2: 5 madde
- Kategori 3: 6 madde
Toplam: 18 madde
```

---

### 8.2 - UI Oluştur (3 Kategori)

**Süre**: 2 saat

**Dosya**: `views/articles/create.php` (Son adım: Kontrol Listesi ve Gönder)

**UI:**

```html
<div class="card">
    <div class="card-header">
        <h5>Gönderim Öncesi Kontrol Listesi</h5>
        <small class="text-muted">Lütfen göndermeden önce aşağıdaki kontrolleri yapın</small>
    </div>
    <div class="card-body">
        <!-- Progress bar -->
        <div class="checklist-progress mb-4">
            <div class="d-flex justify-content-between mb-2">
                <span>İlerleme</span>
                <span id="checklist-progress-text">0 / 18</span>
            </div>
            <div class="progress">
                <div id="checklist-progress-bar" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
            </div>
        </div>

        <!-- Tümünü İşaretle Butonu -->
        <div class="text-right mb-3">
            <button type="button" class="btn btn-sm btn-outline-primary" id="check-all-btn">
                <i class="fa fa-check-square"></i> Tümünü İşaretle
            </button>
        </div>

        <!-- Kategori 1: Makale Bilgileri -->
        <div class="checklist-category">
            <h6 class="font-weight-bold">
                <i class="fa fa-file-alt"></i> Makale Bilgileri
                <small class="text-muted">(<span class="category-progress" data-category="1">0</span> / 7)</small>
            </h6>
            <div class="checklist-items">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input checklist-item" data-category="1" id="check-1-1" name="checklist[1][1]">
                    <label class="custom-control-label" for="check-1-1">Makale başlığı Türkçe ve İngilizce olarak girildi</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input checklist-item" data-category="1" id="check-1-2" name="checklist[1][2]">
                    <label class="custom-control-label" for="check-1-2">Özet Türkçe ve İngilizce olarak girildi</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input checklist-item" data-category="1" id="check-1-3" name="checklist[1][3]">
                    <label class="custom-control-label" for="check-1-3">Anahtar kelimeler Türkçe ve İngilizce olarak girildi (en az 3)</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input checklist-item" data-category="1" id="check-1-4" name="checklist[1][4]">
                    <label class="custom-control-label" for="check-1-4">Makale türü seçildi</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input checklist-item" data-category="1" id="check-1-5" name="checklist[1][5]">
                    <label class="custom-control-label" for="check-1-5">Makale konusu seçildi</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input checklist-item" data-category="1" id="check-1-6" name="checklist[1][6]">
                    <label class="custom-control-label" for="check-1-6">Tam metin (PDF) yüklendi</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input checklist-item" data-category="1" id="check-1-7" name="checklist[1][7]">
                    <label class="custom-control-label" for="check-1-7">Referanslar eklendi (en az 10)</label>
                </div>
            </div>
        </div>

        <hr>

        <!-- Kategori 2: Yazar Bilgileri -->
        <div class="checklist-category">
            <h6 class="font-weight-bold">
                <i class="fa fa-users"></i> Yazar Bilgileri
                <small class="text-muted">(<span class="category-progress" data-category="2">0</span> / 5)</small>
            </h6>
            <div class="checklist-items">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input checklist-item" data-category="2" id="check-2-1" name="checklist[2][1]">
                    <label class="custom-control-label" for="check-2-1">En az bir yazar eklendi</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input checklist-item" data-category="2" id="check-2-2" name="checklist[2][2]">
                    <label class="custom-control-label" for="check-2-2">Tüm yazarlar için email adresi girildi</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input checklist-item" data-category="2" id="check-2-3" name="checklist[2][3]">
                    <label class="custom-control-label" for="check-2-3">Tüm yazarlar için kurum bilgisi girildi</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input checklist-item" data-category="2" id="check-2-4" name="checklist[2][4]">
                    <label class="custom-control-label" for="check-2-4">Sorumlu yazar (corresponding author) belirlendi</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input checklist-item" data-category="2" id="check-2-5" name="checklist[2][5]">
                    <label class="custom-control-label" for="check-2-5">ORCID bilgileri girildi (önerilen)</label>
                </div>
            </div>
        </div>

        <hr>

        <!-- Kategori 3: Etik ve Telif Hakları -->
        <div class="checklist-category">
            <h6 class="font-weight-bold">
                <i class="fa fa-shield-alt"></i> Etik ve Telif Hakları
                <small class="text-muted">(<span class="category-progress" data-category="3">0</span> / 6)</small>
            </h6>
            <div class="checklist-items">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input checklist-item" data-category="3" id="check-3-1" name="checklist[3][1]">
                    <label class="custom-control-label" for="check-3-1">Yayın hakkı devir formu yüklendi</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input checklist-item" data-category="3" id="check-3-2" name="checklist[3][2]">
                    <label class="custom-control-label" for="check-3-2">Etik kurul onayı alındı (gerekiyorsa)</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input checklist-item" data-category="3" id="check-3-3" name="checklist[3][3]">
                    <label class="custom-control-label" for="check-3-3">Çıkar çatışması beyanı okundu ve kabul edildi</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input checklist-item" data-category="3" id="check-3-4" name="checklist[3][4]">
                    <label class="custom-control-label" for="check-3-4">Yazarlık kriterleri okundu ve kabul edildi</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input checklist-item" data-category="3" id="check-3-5" name="checklist[3][5]">
                    <label class="custom-control-label" for="check-3-5">Makale daha önce başka bir yerde yayınlanmadı</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input checklist-item" data-category="3" id="check-3-6" name="checklist[3][6]">
                    <label class="custom-control-label" for="check-3-6">Tüm yazarlar makalenin gönderilmesini onayladı</label>
                </div>
            </div>
        </div>

    </div>
</div>
```

**CSS:**

```css
.checklist-category {
    margin-bottom: 20px;
}

.checklist-items {
    margin-left: 20px;
}

.checklist-items .custom-control {
    margin-bottom: 10px;
}

.category-progress {
    font-weight: bold;
}

#checklist-progress-bar {
    transition: width 0.3s ease;
}
```

**Görevler:**

- [ ] HTML'i ekle
- [ ] CSS'i ekle
- [ ] Test et (görünüm)

---

### 8.3 - Progress Bar Ekle

**Süre**: 1 saat

**Dosya**: `public/assets/js/checklist-manager.js`

**Özellikler:**

- Checkbox işaretlendiğinde progress güncelle
- Kategori bazlı sayaç
- Genel progress bar

**Kod taslağı:**

```javascript
class ChecklistManager {
    constructor() {
        this.totalItems = 18;
        this.categoryTotals = {
            '1': 7,
            '2': 5,
            '3': 6
        };
    }

    init() {
        // Tüm checkbox'lara event listener ekle
        const checkboxes = document.querySelectorAll('.checklist-item');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                this.updateProgress();
            });
        });

        // Tümünü işaretle butonu
        document.getElementById('check-all-btn').addEventListener('click', () => {
            this.checkAll();
        });

        // İlk yükleme
        this.updateProgress();
    }

    updateProgress() {
        // Genel progress
        const checkedItems = document.querySelectorAll('.checklist-item:checked').length;
        const percentage = (checkedItems / this.totalItems) * 100;

        document.getElementById('checklist-progress-bar').style.width = percentage + '%';
        document.getElementById('checklist-progress-text').textContent = `${checkedItems} / ${this.totalItems}`;

        // Kategori progress'leri
        for (let category in this.categoryTotals) {
            const categoryChecked = document.querySelectorAll(`.checklist-item[data-category="${category}"]:checked`).length;
            const categorySpan = document.querySelector(`.category-progress[data-category="${category}"]`);

            if (categorySpan) {
                categorySpan.textContent = categoryChecked;

                // Kategori tamamlandıysa yeşil yap
                if (categoryChecked === this.categoryTotals[category]) {
                    categorySpan.classList.add('text-success');
                } else {
                    categorySpan.classList.remove('text-success');
                }
            }
        }
    }

    checkAll() {
        const checkboxes = document.querySelectorAll('.checklist-item');

        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });

        this.updateProgress();
    }

    validate() {
        const checkedItems = document.querySelectorAll('.checklist-item:checked').length;

        if (checkedItems < this.totalItems) {
            alert(`Lütfen tüm kontrol listesi maddelerini işaretleyin (${checkedItems} / ${this.totalItems})`);
            return false;
        }

        return true;
    }
}

// Sayfa yüklendiğinde başlat
document.addEventListener('DOMContentLoaded', () => {
    window.checklistManager = new ChecklistManager();
    checklistManager.init();
});
```

**Görevler:**

- [ ] Dosyayı oluştur
- [ ] `updateProgress()` yaz
- [ ] `checkAll()` yaz
- [ ] `validate()` yaz
- [ ] Test et

---

### 8.4 - "Tümünü İşaretle" Butonu

**Süre**: 15 dakika

Zaten `checklist-manager.js` içinde `checkAll()` metodu var.

**Görevler:**

- [ ] Test et
- [ ] Tüm checkbox'lar işaretleniyor mu?
- [ ] Progress güncelleniyor mu?

---

### 8.5 - Frontend Validasyon

**Süre**: 30 dakika

Form submit öncesi kontrol listesi doğrulaması:

```javascript
// create.php - Form submit event
document.getElementById('article-form').addEventListener('submit', (e) => {
    // ...diğer validasyonlar

    // Kontrol listesi validasyonu
    if (!checklistManager.validate()) {
        e.preventDefault();
        return false;
    }

    // Devam et...
});
```

**Görevler:**

- [ ] Form submit'e kontrol ekle
- [ ] Test et (eksik checkbox ile)
- [ ] Test et (tamamlanmış liste ile)

---

### 8.6 - Test Et

**Süre**: 1 saat

**Test senaryoları:**

**Checkbox işaretleme:**
- [ ] Checkbox işaretlenebiliyor
- [ ] Progress bar güncelleniyor
- [ ] Kategori sayacı güncelleniyor
- [ ] Genel sayaç güncelleniyor

**Tümünü işaretle:**
- [ ] Buton çalışıyor
- [ ] Tüm checkbox'lar işaretleniyor
- [ ] Progress %100 oluyor

**Validasyon:**
- [ ] Eksik checkbox varsa form gönderilemiyor
- [ ] Hata mesajı gösteriliyor
- [ ] Tamamlandığında form gönderilebiliyor

**UI/UX:**
- [ ] Kategori tamamlandığında yeşile dönüyor
- [ ] Progress bar smooth geçiş yapıyor
- [ ] Responsive (mobil uyumlu)

---

## 🎉 FAZ 8 TAMAMLANDI MI?

Tüm checkboxlar işaretlendiyse:

- [ ] **Faz 8 tamamlandı!**
- [ ] [CHECKLIST-MASTER.md](CHECKLIST-MASTER.md) dosyasında Faz 8'i tamamlandı olarak işaretle
- [ ] Final Kontrol'e geç!

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
