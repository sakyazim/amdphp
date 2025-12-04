# Makale Gönderim Formu - Analiz ve Eksikler

## 📋 Genel Bakış
Bu dokümanda eski `yazar-makaleler.html` ve mevcut `views/articles/create.php` karşılaştırılmış ve eksik özellikler listelenmiştir.

---

## ✅ MEVCUT FORM ÖZELLİKLERİ (Tamamlanmış)

### Form Adımları
1. ✅ **Dil Seçimi** - Makale dili seçimi (TR/EN/DE/FR)
2. ✅ **Ön Bilgi** - Kullanıcı bilgilendirme ve onay
3. ✅ **Tür ve Konu** - Makale türü ve konusu seçimi
4. ✅ **Başlık** - TR ve EN başlıklar (karakter sayacı ile)
5. ✅ **Özet** - TR ve EN özetler (kelime sayacı ile, 150-250 kelime kontrolü)
6. ✅ **Anahtar Kelimeler** - TR ve EN anahtar kelimeler (3-5 adet kontrolü)
7. ✅ **Referanslar** - Tek tek veya toplu ekleme, APA format desteği
8. ✅ **Yazarlar** - Yazar ekleme, ORCID ile arama, email ile arama
9. ⚠️ **Dosyalar** - Boş (içerik yakında eklenecek)
10. ✅ **Hakemler** - 3+ hakem önerme sistemi
11. ⚠️ **Editöre Not** - Boş (içerik yakında eklenecek)
12. ⚠️ **Kontrol Listesi** - Boş (içerik yakında eklenecek)
13. ✅ **Makaleyi Gönder** - Özet görüntüleme ve onay

### Teknik Özellikler
- ✅ CSRF Token Koruması (Düzeltildi)
- ✅ Taslak Kaydetme Sistemi (Otomatik 30sn)
- ✅ Progress Bar (Adım ilerlemesi)
- ✅ Validasyon (Client-side)
- ✅ Form Data Handling (Array desteği ile düzeltildi)

---

## ❌ EKSİK ÖZELLİKLER

### 1. 🎯 **Form Gönderimi Sonrası İşlemler**

#### A. Başarı Mesajı ve Bilgilendirme
**Durum:** Eksik
**Öncelik:** 🔴 Yüksek

**Gerekli Özellikler:**
```php
// Form başarıyla gönderildikten sonra:
- ✅ Makale ID'si (Örn: ART-2025-0103)
- ✅ Gönderim tarihi
- ✅ Durum bilgisi (Gönderildi/Beklemede)
- ✅ Sonraki adımlar hakkında bilgilendirme
- ✅ "Makalelerim" sayfasına yönlendirme linki
- ✅ PDF çıktısı alma seçeneği (opsiyonel)
```

**Örnek Çıktı:**
```
🎉 Makaleniz Başarıyla Gönderildi!

Makale ID: ART-2025-0103
Gönderim Tarihi: 04.12.2025 14:30
Durum: Editör İncelemesinde

📋 Sonraki Adımlar:
1. Editör makalenizi inceleyecek (2-3 gün)
2. Hakem ataması yapılacak
3. Değerlendirme süreci başlayacak

➡️ [Makalelerim Sayfasına Git]
📄 [Gönderim Özetini İndir]
```

---

### 2. 📊 **Makalelerim Listesi Özellikleri**

#### A. Filtreleme ve Arama
**Durum:** Eksik
**Öncelik:** 🔴 Yüksek

**Gerekli Özellikler:**
```javascript
// Filtreleme seçenekleri:
- [ ] Durum filtresi (Taslak, Gönderildi, Değerlendirmede, vs.)
- [ ] Tarih aralığı filtresi (Son 1/3/6 ay, 1 yıl)
- [ ] Arama kutusu (Başlık, ID, anahtar kelime)
- [ ] Sıralama (Tarih, Durum, Alfabetik)
```

**Eski HTML'den Alınacak:**
- `statusFilter` - Durum seçim dropdown'u
- `dateFilter` - Tarih aralığı dropdown'u
- `searchArticle` - Arama input'u
- `sortDropdown` - Sıralama dropdown'u

---

#### B. Genişletilebilir Tablo (Expandable Rows)
**Durum:** Eksik
**Öncelik:** 🟡 Orta

**Gerekli Özellikler:**
```javascript
// Her makale satırı için:
- [ ] Chevron butonu (▼/▲ açılır/kapanır)
- [ ] Collapse özelliği (Bootstrap collapse)
- [ ] Detay kartı (Genişletildiğinde gösterilecek)
```

**Detay Kartında Gösterilecek Bilgiler:**
```
1. Makale Detayları:
   - Makale Tipi (Özgün Araştırma, Derleme, vs.)
   - Yazarlar (Sıralı liste)
   - Anahtar Kelimeler

2. Süreç Bilgileri:
   - Gönderim Tarihi
   - Son İşlem (Tarih ve açıklama)
   - Hakem Sayısı
   - İlerleme Bar (%0-100)

3. Dosyalar:
   - Makale PDF
   - Ek dosyalar (tablolar, şekiller)
   - İndirme linkleri
```

---

#### C. Duruma Özel Bilgiler
**Durum:** Eksik
**Öncelik:** 🔴 Yüksek

**Durum: Değerlendirmede**
```html
- [ ] İlerleme çubuğu (%)
- [ ] Hakem sayısı
- [ ] Tahmini sonuç tarihi (opsiyonel)
```

**Durum: Düzeltme İstendi**
```html
- [ ] Hakem yorumları kartları
- [ ] Düzeltme son tarihi
- [ ] Kalan süre uyarısı (countdown)
- [ ] Düzenle butonu (aktif)
```

**Durum: Kabul Edildi / Yayınlandı**
```html
- [ ] Kabul tarihi
- [ ] Yayın tarihi
- [ ] Cilt/Sayı bilgisi
- [ ] Sayfa numaraları
- [ ] DOI numarası
- [ ] Atıf sayısı (varsa)
- [ ] Yayın sertifikası PDF
```

**Durum: Reddedildi**
```html
- [ ] Ret tarihi
- [ ] Editör notu/açıklaması
- [ ] Ret mektubu PDF
```

**Durum: Taslak**
```html
- [ ] Oluşturma tarihi
- [ ] Son düzenleme tarihi
- [ ] Tamamlanma oranı (%)
- [ ] İlerleme çubuğu
- [ ] Eksik bölümler listesi
- [ ] "Düzenlemeye Devam Et" butonu
- [ ] "Tamamla ve Gönder" butonu
```

---

#### D. İşlem Butonları
**Durum:** Kısmen Eksik
**Öncelik:** 🟡 Orta

**Her Makale için:**
```html
Durum Bazlı Butonlar:
- [ ] 👁️ Görüntüle (Tüm durumlar)
- [ ] ✏️ Düzenle (Taslak, Düzeltme İstendi)
- [ ] 📄 PDF İndir (Kabul Edildi, Yayınlandı)
- [ ] 🔄 Revizyon Gönder (Düzeltme İstendi)
- [ ] ✅ Tamamla (Taslak)
- [ ] 🗑️ Sil (Taslak) - Onay gerekli
- [ ] 📋 Kopyala (Reddedildi) - Yeni başvuru için
- [ ] 🔗 Paylaş (Yayınlandı)
```

---

#### E. İstatistik Kartları
**Durum:** Eksik
**Öncelik:** 🟢 Düşük

**Makalelerim Sayfası Üstünde:**
```html
İstatistik Kartları (4 adet):
1. 📄 Toplam Makale (Sayı)
2. ✅ Kabul Edilen (Sayı)
3. ⏳ Değerlendirmede (Sayı)
4. ❌ Reddedilen (Sayı)

Her kart:
- [ ] İkon (Bootstrap Icons)
- [ ] Başlık
- [ ] Sayı (büyük font)
- [ ] Renk kodlu (success, warning, danger)
```

---

### 3. 📂 **Dosya Yükleme Modülü (Step 8)**

**Durum:** Tamamen Eksik
**Öncelik:** 🔴 Yüksek

**Gerekli Dosya Tipleri:**
```javascript
Zorunlu Dosyalar:
- [ ] Ana Makale PDF (zorunlu)
  - Format: PDF
  - Max boyut: 10MB
  - Validasyon: PDF format kontrolü

Opsiyonel Dosyalar:
- [ ] Ek Tablolar (Excel, CSV)
- [ ] Ek Şekiller/Grafikler (PNG, JPG)
- [ ] Veri Setleri (ZIP, CSV)
- [ ] Etik Kurul Onayı (PDF)
- [ ] Telif Hakkı Formu (PDF)
```

**Özellikler:**
```javascript
- [ ] Drag & Drop alanı
- [ ] Çoklu dosya seçimi
- [ ] Progress bar (yükleme sırasında)
- [ ] Dosya önizleme (thumbnail)
- [ ] Dosya silme (yüklemeden önce)
- [ ] Dosya boyutu kontrolü
- [ ] Format kontrolü
- [ ] Dosya isimlendirme önerileri
```

---

### 4. ✉️ **Editöre Not Modülü (Step 10)**

**Durum:** Tamamen Eksik
**Öncelik:** 🟡 Orta

**Gerekli Özellikler:**
```html
<textarea>
  - [ ] Başlık: "Editöre Not"
  - [ ] Açıklama: "Makaleniz hakkında editöre iletmek istediğiniz ek bilgiler"
  - [ ] Max karakter: 1000
  - [ ] Karakter sayacı
  - [ ] Opsiyonel alan (zorunlu değil)
</textarea>

Örnek Notlar:
- Önceki gönderim hakkında
- Özel durumlar
- Acil değerlendirme talebi
- Çıkar çatışması açıklaması
```

---

### 5. ☑️ **Kontrol Listesi Modülü (Step 11)**

**Durum:** Tamamen Eksik
**Öncelik:** 🟡 Orta

**Kontrol Listesi İçeriği:**
```javascript
Checklist Items (Tümü işaretli olmalı):
- [ ] Makalem derginin kapsamına uygun
- [ ] APA formatına uygun referanslar ekledim
- [ ] Tüm yazarların onayını aldım
- [ ] ORCID numaralarını doğru girdim
- [ ] Etik kurul onayı gerekiyorsa ekledim
- [ ] Çıkar çatışması beyanı doldurdum
- [ ] Benzerlik raporu kontrol ettim (%20 altı)
- [ ] Telif hakkı formunu doldurdum
- [ ] Tüm şekil ve tabloların kalitesi yeterli
- [ ] Makalemi son kez kontrol ettim
```

**Özellikler:**
```javascript
- [ ] Her madde için checkbox
- [ ] Tümü işaretlenene kadar "Devam" butonu disabled
- [ ] Her maddede info icon (açıklama için)
- [ ] Modal popup (detaylı açıklamalar için)
```

---

### 6. 🔔 **Bildirim ve Uyarı Sistemi**

**Durum:** Eksik
**Öncelik:** 🟡 Orta

**Gerekli Bildirimler:**
```javascript
Makale Durumu Değişikliklerinde:
- [ ] Email bildirimi
- [ ] Sistem içi bildirim (notification badge)
- [ ] Dashboard'da alert kartı

Bildirim Türleri:
- [ ] Hakem ataması yapıldı
- [ ] Hakem değerlendirmesi tamamlandı
- [ ] Düzeltme talebi geldi
- [ ] Düzeltme son tarihi yaklaşıyor (7/3/1 gün)
- [ ] Makale kabul edildi
- [ ] Makale reddedildi
- [ ] Makale yayınlandı
```

---

### 7. 📤 **Dışa Aktarma Özellikleri**

**Durum:** Eksik
**Öncelik:** 🟢 Düşük

**Dışa Aktarma Seçenekleri:**
```javascript
- [ ] Makale listesini Excel'e aktar
- [ ] Makale listesini PDF'e aktar
- [ ] Gönderim özeti PDF
- [ ] Hakem raporları PDF
- [ ] İstatistikler grafiği (Chart.js)
```

---

### 8. 🔄 **Revizyon/Versiyon Sistemi**

**Durum:** Eksik
**Öncelik:** 🟡 Orta

**Özellikler:**
```javascript
- [ ] Makale versiyonları (v1, v2, v3...)
- [ ] Versiyon geçmişi tablosu
- [ ] Her versiyon için:
  - Tarih
  - Değişiklik açıklaması
  - Dosya linki
  - Hakem yorumlarına yanıt
```

**Revizyon Gönderimi:**
```javascript
- [ ] Revize edilmiş makale dosyası
- [ ] Değişiklikler listesi
- [ ] Hakemlere cevap mektubu
- [ ] Değişikliklerin işaretli olduğu dosya (track changes)
```

---

### 9. 📱 **Mobil Uyumluluk**

**Durum:** Kısmen Mevcut (Bootstrap ile)
**Öncelik:** 🟡 Orta

**İyileştirmeler:**
```javascript
- [ ] Mobilde sidebar collapse olmalı
- [ ] Tablo responsive olmalı (kartlara dönüşmeli)
- [ ] Touch gesture desteği (swipe)
- [ ] Mobil için optimize edilmiş buton boyutları
```

---

### 10. 🔍 **Makale Detay Sayfası**

**Durum:** Eksik
**Öncelik:** 🔴 Yüksek

**Yeni Sayfa:** `makale-detay.php`

**İçerik:**
```html
Üst Bölüm:
- [ ] Makale başlığı
- [ ] Makale ID
- [ ] Durum badge'i
- [ ] Gönderim tarihi

Sekmeler (Tabs):
1. 📋 Genel Bilgiler
   - Tür, Konu, Dil
   - Başlık (TR/EN)
   - Özet (TR/EN)
   - Anahtar Kelimeler
   - Yazarlar listesi
   - Referanslar

2. 📁 Dosyalar
   - Ana makale PDF
   - Ek dosyalar
   - İndirme butonları

3. 👥 Hakemler
   - Atanan hakemler (gizli, sadece editör görür)
   - Hakem durumları
   - Değerlendirme süreleri

4. 📝 Süreç Geçmişi (Timeline)
   - Gönderim
   - Editör incelemesi
   - Hakem ataması
   - Değerlendirme
   - Düzeltme talebi
   - Revizyon gönderimi
   - Kabul/Red

5. 💬 Mesajlar
   - Editör-Yazar iletişimi
   - Hakem yorumları
   - Sistem notları
```

---

## 🎯 ÖNCELİKLENDİRME

### Faz 1: Kritik (Hemen Yapılmalı) 🔴
1. ✅ Form gönderimi sonrası başarı mesajı ve yönlendirme
2. 📊 Makalelerim listesi temel özellikleri
3. 📂 Dosya yükleme modülü (Step 8)
4. 🔍 Makale detay sayfası (basit versiyon)

### Faz 2: Önemli (Kısa Vadede) 🟡
5. ☑️ Kontrol listesi modülü (Step 11)
6. ✉️ Editöre not modülü (Step 10)
7. 🔄 Duruma özel bilgiler ve butonlar
8. 📋 Genişletilebilir tablo yapısı

### Faz 3: İyileştirmeler (Orta Vadede) 🟢
9. 🔔 Bildirim sistemi
10. 🔄 Revizyon/Versiyon sistemi
11. 📤 Dışa aktarma özellikleri
12. 📱 Mobil iyileştirmeleri
13. 📊 İstatistik kartları

---

## 💡 ÖNERĐLER

### A. Veritabanı Değişiklikleri
```sql
-- Makale durumları için enum veya lookup table
-- Durum geçmişi için history tablosu
-- Dosyalar için files tablosu
-- Bildirimler için notifications tablosu
-- Versiyonlar için article_versions tablosu
```

### B. API Endpoints (Gerekli Olacak)
```php
// Makale işlemleri
GET    /api/articles         - Liste
GET    /api/articles/{id}    - Detay
POST   /api/articles         - Yeni (Mevcut ✅)
PUT    /api/articles/{id}    - Güncelle
DELETE /api/articles/{id}    - Sil (Taslak)

// Dosya işlemleri
POST   /api/articles/{id}/files      - Dosya yükle
DELETE /api/articles/{id}/files/{fid} - Dosya sil
GET    /api/articles/{id}/files      - Dosya listesi

// Durum değişiklikleri
PATCH  /api/articles/{id}/status     - Durum değiştir

// İstatistikler
GET    /api/articles/stats           - İstatistikler

// Dışa aktarma
GET    /api/articles/export/excel    - Excel
GET    /api/articles/export/pdf      - PDF
```

### C. JavaScript Modülleri (Yeni)
```javascript
- article-list.js      - Liste, filtreleme, arama
- article-detail.js    - Detay sayfası
- file-upload.js       - Dosya yükleme
- notification.js      - Bildirim sistemi
- timeline.js          - Süreç zaman çizelgesi
```

---

## 📝 KARAR NOKTLARI

Kullanıcıdan karar beklenen konular:

### 1. Dosya Yükleme Stratejisi
**Seçenek A:** Her adımda dosya yükleme (Step 8'de tüm dosyalar)
**Seçenek B:** Son adımda tümünü yükle (Step 12'de)
**Seçenek C:** Her dosya tipi için ayrı adım

👉 **Önerim:** Seçenek A - Daha organize ve kullanıcı dostu

### 2. Taslak Makale Davranışı
**Seçenek A:** Taslaklar ayrı bir sayfada
**Seçenek B:** Makalelerim listesinde birlikte (badge ile ayrım)

👉 **Önerim:** Seçenek B - Tek sayfa, daha az karmaşık

### 3. Kontrol Listesi Zorunluluğu
**Seçenek A:** Tüm maddeler zorunlu (işaretli olmalı)
**Seçenek B:** Sadece okuma ve onay yeterli

👉 **Önerim:** Seçenek A - Daha güvenli, hatalar azalır

### 4. Editöre Not Alanı
**Seçenek A:** Opsiyonel alan
**Seçenek B:** Zorunlu alan (en az 50 karakter)

👉 **Önerim:** Seçenek A - Opsiyonel, ama tavsiye edilen

### 5. Makale Detay Sayfası
**Seçenek A:** Ayrı sayfa (makale-detay.php)
**Seçenek B:** Modal popup
**Seçenek C:** Genişletilebilir satır (mevcut HTML gibi)

👉 **Önerim:** Seçenek A + C kombinasyonu:
- Basit bilgiler için genişletilebilir satır
- Detaylı inceleme için ayrı sayfa linki

---

## 🚀 SONRAKI ADIMLAR

Hangi özelliği öncelikli yapmamı istersiniz?

1. 🔴 Form başarı mesajı ve yönlendirme
2. 🔴 Dosya yükleme modülü (Step 8)
3. 🔴 Makalelerim listesi ve filtreleme
4. 🔴 Makale detay sayfası
5. 🟡 Kontrol listesi (Step 11)
6. 🟡 Editöre not (Step 10)
7. 🟡 Duruma özel özellikler

**Veya hepsini planlayıp aşamalı ilerleyelim mi?**

---

**Oluşturulma Tarihi:** 04.12.2025
**Son Güncelleme:** 04.12.2025
