# 📋 AMDS GELİŞTİRME ANA CHECKLIST

> **Kullanım**: Her görev tamamlandığında `[ ]` işaretini `[x]` yapın.
>
> **Komut**: `checklist uygula` dediğinizde Claude bu listeyi takip edecek.

---

## 🎯 GENEL DURUM

**Başlangıç Tarihi**: 2024-12-03
**Tahmini Tamamlanma**: 10-12 iş günü
**Mevcut Faz**: Faz 1 - Dil Desteği Sistemi

---

## 📊 İLERLEME ÖZETI

| Faz | Modül | Durum | İlerleme | MD Dosyası |
|-----|-------|-------|----------|------------|
| 0 | Planlama ve Hazırlık | 🟢 Tamamlandı | 100% | [FAZ-0-PLANLAMA.md](FAZ-0-PLANLAMA.md) |
| 1 | Dil Desteği Sistemi | 🟢 Tamamlandı | 100% | [FAZ-1-DIL-SISTEMI.md](FAZ-1-DIL-SISTEMI.md) |
| 2 | Yazar Modülü | 🟢 Tamamlandı | 100% | [FAZ-2-YAZAR-MODULU.md](FAZ-2-YAZAR-MODULU.md) |
| 3 | Referans Sistemi | 🟢 Tamamlandı | 100% | [FAZ-3-REFERANS-SISTEMI.md](FAZ-3-REFERANS-SISTEMI.md) |
| 4 | Taslak Kayıt Sistemi | 🟢 Tamamlandı | 100% | [FAZ-4-TASLAK-SISTEMI.md](FAZ-4-TASLAK-SISTEMI.md) |
| 5 | Hakem Modülü | 🟢 Tamamlandı | 100% | [FAZ-5-HAKEM-MODULU.md](FAZ-5-HAKEM-MODULU.md) |
| 6 | Dosya Yükleme | ⚪ Bekliyor | 0% | [FAZ-6-DOSYA-YUKLEME.md](FAZ-6-DOSYA-YUKLEME.md) |
| 7 | Editöre Not | ⚪ Bekliyor | 0% | [FAZ-7-EDITORE-NOT.md](FAZ-7-EDITORE-NOT.md) |
| 8 | Kontrol Listesi | ⚪ Bekliyor | 0% | [FAZ-8-KONTROL-LISTESI.md](FAZ-8-KONTROL-LISTESI.md) |

**Durum Göstergeleri:**
- ⚪ Bekliyor
- 🔵 Başlanmadı (Aktif faz)
- 🟡 Devam Ediyor
- 🟢 Tamamlandı
- 🔴 Sorun Var

---

## 🚀 FAZ 0: PLANLAMA VE HAZIRLIK

**Tahmini Süre**: 2-3 saat
**Öncelik**: 🔥 Kritik

### Görevler

- [x] **0.1** - Tüm faz MD dosyalarını oluştur
- [x] **0.2** - Veritabanı tablolarını oluştur (SQL)
- [x] **0.3** - Klasör yapısını oluştur
- [x] **0.4** - Dergi ayarları (makale türü/konu/dil) tanımla
- [x] **0.5** - Hakem formu gereksinimlerini belirle
- [x] **0.6** - Dosya yükleme gereksinimlerini belirle

**Detaylar**: [FAZ-0-PLANLAMA.md](FAZ-0-PLANLAMA.md) dosyasına bakın.

---

## 🌍 FAZ 1: DİL DESTEĞİ SİSTEMİ

**Tahmini Süre**: 2-3 gün
**Öncelik**: 🔥 Kritik
**Bağımlılık**: Faz 0 tamamlanmalı

### Görevler

- [ ] **1.1** - Veritabanı tablolarını oluştur (`dil_degiskenleri`, `dil_paketleri`)
- [ ] **1.2** - `LanguageService.php` sınıfını yaz
- [ ] **1.3** - JSON dil paketlerini oluştur (TR)
- [ ] **1.4** - `language-helper.js` yaz
- [ ] **1.5** - `create.php` dosyasını dönüştür (sabit metinleri kaldır)
- [ ] **1.6** - EN dil paketini oluştur
- [ ] **1.7** - Dergi yöneticisi özelleştirme paneli (basit versiyon)
- [ ] **1.8** - Test et (TR/EN dil değişimi)

**Detaylar**: [FAZ-1-DIL-SISTEMI.md](FAZ-1-DIL-SISTEMI.md) dosyasına bakın.

---

## 👥 FAZ 2: YAZAR MODÜLÜ

**Tahmini Süre**: 2-3 gün
**Öncelik**: 🔥 Kritik
**Bağımlılık**: Faz 1 tamamlanmalı

### Görevler

- [x] **2.1** - Veritabanı tablolarını güncelle (`kullanici_yazar_profilleri`)
- [x] **2.2** - `AuthorController.php` oluştur
- [x] **2.3** - `OrcidService.php` oluştur
- [x] **2.4** - Email arama API'si yaz (`/api/authors/search-by-email`)
- [x] **2.5** - ORCID arama API'si yaz (`/api/authors/search-by-orcid`)
- [x] **2.6** - `author-search.js` oluştur
- [x] **2.7** - Email arama UI'ını ekle
- [x] **2.8** - ORCID arama UI'ını ekle
- [x] **2.9** - Otomatik form doldurma ekle
- [x] **2.10** - Test et (Email, ORCID, Form doldurma)

**Detaylar**: [FAZ-2-YAZAR-MODULU.md](FAZ-2-YAZAR-MODULU.md) dosyasına bakın.
**Tamamlandı**: [FAZ-2-TAMAMLANDI.md](FAZ-2-TAMAMLANDI.md) ✅

---

## 📚 FAZ 3: REFERANS SİSTEMİ

**Tahmini Süre**: 1 gün
**Öncelik**: 🟡 Orta
**Bağımlılık**: Faz 1 tamamlanmalı

### Görevler

- [ ] **3.1** - Tek tek ekleme modunu test et (zaten mevcut)
- [ ] **3.2** - Toplu ekleme modu UI'ını ekle
- [ ] **3.3** - Backend array parse ekle
- [ ] **3.4** - İki mod arası geçiş ekle
- [ ] **3.5** - Test et (Tek tek, Toplu ekleme)

**Detaylar**: [FAZ-3-REFERANS-SISTEMI.md](FAZ-3-REFERANS-SISTEMI.md) dosyasına bakın.

---

## 💾 FAZ 4: TASLAK KAYIT SİSTEMİ

**Tahmini Süre**: 2 gün
**Öncelik**: 🟡 Orta
**Bağımlılık**: Faz 1 tamamlanmalı
**Durum**: 🟢 Tamamlandı

### Görevler

- [x] **4.1** - Veritabanı tablosunu oluştur (`makale_taslaklari`)
- [x] **4.2** - `TaslakController.php` oluştur
- [x] **4.3** - Otomatik kayıt API'si yaz
- [x] **4.4** - Manuel kayıt API'si yaz
- [x] **4.5** - Taslak yükleme API'si yaz
- [x] **4.6** - `taslak-sistemi.js` oluştur
- [x] **4.7** - Otomatik kayıt (30 saniye interval) ekle
- [x] **4.8** - Manuel kayıt butonu ekle
- [x] **4.9** - Taslak listesi (yazar paneli) ekle
- [x] **4.10** - Test et (Otomatik/Manuel kayıt, Yükleme)

**Detaylar**: [FAZ-4-TASLAK-SISTEMI.md](FAZ-4-TASLAK-SISTEMI.md) dosyasına bakın.
**Tamamlandı**: [FAZ-4-TAMAMLANDI.md](FAZ-4-TAMAMLANDI.md) ✅

---

## 👨‍⚖️ FAZ 5: HAKEM MODÜLÜ

**Tahmini Süre**: 2-3 gün
**Öncelik**: 🟢 Düşük
**Bağımlılık**: Faz 2 tamamlanmalı (Yazar modülü template olacak)

### Görevler

- [ ] **5.1** - Gereksinimler belirle (Email/ORCID arama?)
- [ ] **5.2** - Veritabanı tablosunu oluştur (`makale_hakem_onerileri`)
- [ ] **5.3** - `ReviewerController.php` oluştur
- [ ] **5.4** - Email/ORCID arama API'leri (yazar modülüne benzer)
- [ ] **5.5** - Hakem ekleme formu UI
- [ ] **5.6** - Hakem listesi tablosu
- [ ] **5.7** - Minimum hakem kontrolü (en az 3)
- [ ] **5.8** - Test et

**Detaylar**: [FAZ-5-HAKEM-MODULU.md](FAZ-5-HAKEM-MODULU.md) dosyasına bakın.

---

## 📁 FAZ 6: DOSYA YÜKLEME

**Tahmini Süre**: 2 gün
**Öncelik**: 🟢 Düşük
**Bağımlılık**: Faz 1 tamamlanmalı

### Görevler

- [ ] **6.1** - Gereksinimler belirle (dosya türleri, limitler)
- [ ] **6.2** - Storage klasör yapısı oluştur
- [ ] **6.3** - `FileController.php` oluştur
- [ ] **6.4** - Dosya validasyonu ekle
- [ ] **6.5** - Dosya yükleme UI ekle
- [ ] **6.6** - Progress bar ekle
- [ ] **6.7** - Dosya listesi tablosu ekle
- [ ] **6.8** - Dosya silme/indirme özellikleri
- [ ] **6.9** - Test et

**Detaylar**: [FAZ-6-DOSYA-YUKLEME.md](FAZ-6-DOSYA-YUKLEME.md) dosyasına bakın.

---

## ✉️ FAZ 7: EDİTÖRE NOT

**Tahmini Süre**: 4 saat
**Öncelik**: 🟢 Düşük
**Bağımlılık**: Faz 1 tamamlanmalı

### Görevler

- [ ] **7.1** - Gereksinimler belirle (Rich text? Limit?)
- [ ] **7.2** - Veritabanı alanı ekle (`makaleler.editore_notu`)
- [ ] **7.3** - Basit text editor veya rich text editor?
- [ ] **7.4** - Karakter sayacı ekle
- [ ] **7.5** - Test et

**Detaylar**: [FAZ-7-EDITORE-NOT.md](FAZ-7-EDITORE-NOT.md) dosyasına bakın.

---

## ✅ FAZ 8: KONTROL LİSTESİ

**Tahmini Süre**: 1 gün
**Öncelik**: 🟢 Düşük
**Bağımlılık**: Faz 1 tamamlanmalı

### Görevler

- [ ] **8.1** - Kontrol listesi maddelerini belirle
- [ ] **8.2** - UI oluştur (3 kategori)
- [ ] **8.3** - Progress bar ekle
- [ ] **8.4** - "Tümünü İşaretle" butonu
- [ ] **8.5** - Frontend validasyon
- [ ] **8.6** - Test et

**Detaylar**: [FAZ-8-KONTROL-LISTESI.md](FAZ-8-KONTROL-LISTESI.md) dosyasına bakın.

---

## 🎉 FİNAL KONTROL

Tüm fazlar tamamlandıktan sonra:

- [ ] **F.1** - End-to-end test (baştan sona makale gönderimi)
- [ ] **F.2** - Çoklu dil testi (TR/EN)
- [ ] **F.3** - Performans testi
- [ ] **F.4** - Güvenlik kontrolleri (CSRF, XSS, SQL Injection)
- [ ] **F.5** - Mobil uyumluluk testi
- [ ] **F.6** - Tarayıcı uyumluluk testi (Chrome, Firefox, Safari)
- [ ] **F.7** - Dokümantasyon güncelle
- [ ] **F.8** - Kullanıcı kılavuzu yaz

---

## 📝 NOTLAR VE SORUNLAR

### Çözülmesi Gereken Sorunlar

1. [ ] -
2. [ ] -
3. [ ] -

### Gelecek İyileştirmeler

1. [ ] -
2. [ ] -
3. [ ] -

### Öğrenilen Dersler

1. -
2. -
3. -

---

## 🔗 HIZLI ERİŞİM

- [Genel Mimari ve Planlama](SISTEM-ANALIZ-VE-PLANLAMA.md)
- [Veritabanı Şemaları](DATABASE-SCHEMA.md)
- [API Dokümantasyonu](API-DOCUMENTATION.md)
- [Frontend Bileşenleri](FRONTEND-COMPONENTS.md)

---

## 📊 İSTATİSTİKLER

**Son Güncelleme**: 2024-12-03 13:35
**Toplam Görev**: 80+
**Tamamlanan**: 6 (Faz 0)
**Kalan**: 74+
**Genel İlerleme**: 7%

---

## 🎯 BİR SONRAKİ ADIM

> **Şu anda yapılacak**:
> 1. **Manuel Adım**: [database-setup.sql](database-setup.sql) dosyasını phpMyAdmin'de çalıştırın
> 2. **Sonra**: `Faz 1'i başlat` komutu verin
>
> **Veya**: [KURULUM-REHBERI.md](KURULUM-REHBERI.md) dosyasını okuyun

---

**Son Güncelleme**: 2024-12-03
**Versiyon**: 1.0
