# 🧪 FAZ 5: HAKEM MODÜLÜ - TEST REHBERİ

**Tarih**: 2024-12-04

---

## ⚙️ KURULUM (Önce Yapılmalı)

### 1. Veritabanı Tablosunu Oluştur

**Yöntem 1: phpMyAdmin**
```
1. phpMyAdmin'i aç (http://localhost/phpmyadmin)
2. Sol menüden 'amds' veritabanını seç
3. SQL sekmesine tıkla
4. 'database-reviewer-table.sql' dosyasının içeriğini yapıştır
5. 'Git' butonuna tıkla
```

**Yöntem 2: MySQL Command Line**
```bash
mysql -u root -p
USE amds;
source c:/xampp/htdocs/amdsphp/database-reviewer-table.sql;
```

### 2. Tabloyu Kontrol Et

```sql
-- Tablo oluşturuldu mu?
SHOW TABLES LIKE 'makale_hakem_onerileri';

-- Tablo yapısını gör
DESCRIBE makale_hakem_onerileri;
```

---

## 🧪 TEST SENARYOLARI

### TEST 1: Hakem Ekleme ✅

**Adımlar**:
1. http://localhost/amdsphp/yazar/yeni-makale adresine git
2. Step 1-8'i doldur (hızlı geçmek için minimum bilgi gir)
3. Step 9 (Hakemler) bölümüne gel
4. Formu doldur:
   - Ad: Ali
   - Soyad: Yılmaz
   - Email: ali.yilmaz@example.com
   - Kurum: İstanbul Teknik Üniversitesi
   - Uzmanlık Alanı: Yapay Zeka
   - Ülke: Türkiye
   - ORCID: 0000-0001-2345-6789 (opsiyonel)
   - Notlar: AI alanında uzman (opsiyonel)
5. "Hakem Ekle" butonuna bas

**Beklenen Sonuç**:
- ✅ "Hakem başarıyla eklendi" mesajı görünmeli
- ✅ Form temizlenmeli
- ✅ Hakem listesinde görünmeli
- ✅ Sayaç 1/3 olmalı
- ✅ Durum "yetersiz" göstermeli (sarı uyarı)

---

### TEST 2: Email Validasyonu ❌

**Adımlar**:
1. Formu doldur
2. Email: "geçersiz-email" (@ işareti yok)
3. "Hakem Ekle" butonuna bas

**Beklenen Sonuç**:
- ❌ "Geçersiz email formatı" hatası vermeli
- ❌ Hakem eklenmemeli

---

### TEST 3: ORCID Validasyonu ❌

**Adımlar**:
1. Formu doldur
2. ORCID: "123456" (geçersiz format)
3. "Hakem Ekle" butonuna bas

**Beklenen Sonuç**:
- ❌ "Geçersiz ORCID formatı" hatası vermeli
- ❌ Hakem eklenmemeli

---

### TEST 4: Duplicate Email Kontrolü ❌

**Adımlar**:
1. Bir hakem ekle (email: test@example.com)
2. Aynı email ile başka bir hakem eklemeye çalış

**Beklenen Sonuç**:
- ❌ "Bu email adresine sahip hakem zaten eklenmiş" hatası vermeli
- ❌ İkinci hakem eklenmemeli

---

### TEST 5: 3 Hakem Ekleme ✅

**Adımlar**:
1. İlk hakemi ekle (Test 1'deki gibi)
2. İkinci hakemi ekle:
   - Ad: Ayşe
   - Soyad: Demir
   - Email: ayse.demir@example.com
   - Kurum: ODTÜ
3. Üçüncü hakemi ekle:
   - Ad: Mehmet
   - Soyad: Kaya
   - Email: mehmet.kaya@example.com
   - Kurum: Hacettepe Üniversitesi

**Beklenen Sonuç**:
- ✅ 3 hakem listede görünmeli
- ✅ Sayaç 3/3 olmalı (yeşil)
- ✅ Durum "yeterli" göstermeli (yeşil onay)

---

### TEST 6: Hakem Silme 🗑️

**Adımlar**:
1. Listede bir hakemin "Sil" butonuna tıkla
2. Onay penceresinde "Tamam"a bas

**Beklenen Sonuç**:
- ✅ Hakem listeden silinmeli
- ✅ Sayaç güncellenmeli (3/3 → 2/3)
- ✅ Durum "yetersiz"e dönmeli (sarı uyarı)

---

### TEST 7: Hakem Silme İptal ❌

**Adımlar**:
1. "Sil" butonuna tıkla
2. Onay penceresinde "İptal"e bas

**Beklenen Sonuç**:
- ❌ Hakem silinmemeli
- ✅ Liste değişmemeli

---

### TEST 8: Minimum Hakem Kontrolü (0 Hakem) ❌

**Adımlar**:
1. Hiç hakem ekleme
2. "İleri" butonuna bas (Step 10'a geçmeye çalış)

**Beklenen Sonuç**:
- ❌ Step 10'a geçilememeli
- ❌ Hata mesajı göstermeli: "En az 3 hakem önermeniz gerekiyor (şu anda: 0)"
- ❌ Sayfa en üste scroll etmeli

---

### TEST 9: Minimum Hakem Kontrolü (2 Hakem) ❌

**Adımlar**:
1. Sadece 2 hakem ekle
2. "İleri" butonuna bas

**Beklenen Sonuç**:
- ❌ Step 10'a geçilememeli
- ❌ Hata mesajı: "En az 3 hakem önermeniz gerekiyor (şu anda: 2)"

---

### TEST 10: Minimum Hakem Kontrolü (3 Hakem) ✅

**Adımlar**:
1. 3 hakem ekle
2. "İleri" butonuna bas

**Beklenen Sonuç**:
- ✅ Step 10'a geçilmeli
- ✅ Hata mesajı görünmemeli

---

### TEST 11: Sayfayı Yenileme 🔄

**Adımlar**:
1. 2 hakem ekle
2. Sayfayı yenile (F5)
3. Step 9'a dön

**Beklenen Sonuç**:
- ✅ Hakemler listede görünmeli (taslak sisteminden yüklenmeli)
- ✅ Sayaç doğru göstermeli

---

### TEST 12: Uzun Notlar 📝

**Adımlar**:
1. Hakem eklerken "Notlar" alanına çok uzun bir metin gir (200+ karakter)
2. Hakem ekle

**Beklenen Sonuç**:
- ✅ Not kaydedilmeli
- ✅ Listede not görünmeli (küçültülmüş satırda)

---

### TEST 13: Responsive Tasarım 📱

**Adımlar**:
1. Tarayıcı geliştirici araçlarını aç (F12)
2. Mobil görünüme geç (375px genişlik)
3. Form ve listeyi kontrol et

**Beklenen Sonuç**:
- ✅ Form alanları responsive olmalı
- ✅ Tablo kaydırılabilir olmalı
- ✅ Butonlar tıklanabilir olmalı

---

## 🔍 API TESTLERI (Postman/cURL)

### Test 1: Hakem Ekle

```bash
curl -X POST "http://localhost/amdsphp/api/articles/1/reviewers" \
  -F "ad=Ali" \
  -F "soyad=Yılmaz" \
  -F "email=ali@example.com" \
  -F "kurum=İTÜ"
```

**Beklenen Sonuç**:
```json
{
  "success": true,
  "message": "Hakem başarıyla eklendi",
  "reviewer_id": 1,
  "reviewer": {
    "id": 1,
    "makale_id": 1,
    "ad": "Ali",
    "soyad": "Yılmaz",
    ...
  }
}
```

---

### Test 2: Hakem Listesi

```bash
curl "http://localhost/amdsphp/api/articles/1/reviewers"
```

**Beklenen Sonuç**:
```json
{
  "success": true,
  "reviewers": [...],
  "count": 3,
  "min_required": 3,
  "is_valid": true,
  "message": "Hakem sayısı yeterli"
}
```

---

### Test 3: Hakem Validasyonu

```bash
curl "http://localhost/amdsphp/api/articles/1/reviewers/validate"
```

**Beklenen Sonuç**:
```json
{
  "success": true,
  "valid": true,
  "count": 3,
  "min_required": 3,
  "message": "Hakem sayısı yeterli"
}
```

---

### Test 4: Hakem Sil

```bash
curl -X DELETE "http://localhost/amdsphp/api/reviewers/1"
```

**Beklenen Sonuç**:
```json
{
  "success": true,
  "message": "Hakem başarıyla silindi"
}
```

---

## 🐛 HATA AYIKLAMA

### Hakem Eklenmiyor

**Kontrol Et**:
1. Tarayıcı Console (F12) hatalarını kontrol et
2. Network sekmesinden API yanıtını gör
3. PHP error log'larını kontrol et

**Olası Sorunlar**:
- Veritabanı tablosu oluşturulmamış
- ReviewerController yüklenmiyor
- Routing tanımlı değil
- JavaScript dosyası yüklenmiyor

---

### JavaScript Çalışmıyor

**Kontrol Et**:
```javascript
// Console'da çalıştır
console.log(typeof reviewerManager);
// "object" dönmeli
```

**Düzeltme**:
```html
<!-- create.php'de script tag'i var mı? -->
<script src="<?= base_url('assets/js/reviewer-manager.js') ?>"></script>
```

---

### Validasyon Çalışmıyor

**Kontrol Et**:
```javascript
// Console'da çalıştır
validateReviewers();
```

**Düzeltme**:
- create-wizard.js'de `validateReviewers()` fonksiyonu tanımlı mı?
- `validateStep()` fonksiyonu async mi?

---

## ✅ TEST CHECKLIST

### Frontend UI
- [ ] Hakem formu görünüyor
- [ ] Tüm alanlar çalışıyor
- [ ] "Hakem Ekle" butonu çalışıyor
- [ ] Hakem listesi görünüyor
- [ ] "Sil" butonu çalışıyor
- [ ] Sayaç güncelleniyor
- [ ] Durum göstergesi değişiyor
- [ ] Responsive tasarım çalışıyor

### Validasyon
- [ ] Email formatı kontrol ediliyor
- [ ] ORCID formatı kontrol ediliyor
- [ ] Duplicate email engelleniyor
- [ ] Minimum 3 hakem kontrolü çalışıyor
- [ ] Hata mesajları gösteriliyor

### Backend API
- [ ] POST /api/articles/{id}/reviewers çalışıyor
- [ ] GET /api/articles/{id}/reviewers çalışıyor
- [ ] DELETE /api/reviewers/{id} çalışıyor
- [ ] GET /api/articles/{id}/reviewers/validate çalışıyor

### Veritabanı
- [ ] Hakemler kaydediliyor
- [ ] Hakem bilgileri doğru
- [ ] Foreign key çalışıyor
- [ ] Silme işlemi çalışıyor

---

## 📊 TEST SONUÇLARI (Doldurulacak)

| Test | Durum | Notlar |
|------|-------|--------|
| Hakem Ekleme | ⏳ | |
| Email Validasyonu | ⏳ | |
| ORCID Validasyonu | ⏳ | |
| Duplicate Kontrolü | ⏳ | |
| 3 Hakem Ekleme | ⏳ | |
| Hakem Silme | ⏳ | |
| Min. Hakem Kontrolü (0) | ⏳ | |
| Min. Hakem Kontrolü (2) | ⏳ | |
| Min. Hakem Kontrolü (3) | ⏳ | |
| API Testleri | ⏳ | |

**Durum**:
- ✅ Başarılı
- ❌ Başarısız
- ⏳ Test Edilmedi

---

**Test Tamamlanınca**: [FAZ-5-TAMAMLANDI.md](FAZ-5-TAMAMLANDI.md) dosyasını güncelle
