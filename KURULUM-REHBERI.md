# 🚀 AMDS - KURULUM REHBERİ

**Tarih**: 2024-12-03
**Versiyon**: 1.0

---

## 📋 CHECKLIST SİSTEMİ NASIL KULLANILIR?

1. **Ana kontrol listesi**: [CHECKLIST-MASTER.md](CHECKLIST-MASTER.md) dosyasını açın
2. **Komut**: `checklist uygula` yazın
3. **Claude otomatik olarak**:
   - Mevcut fazı kontrol eder
   - İlgili faz dosyasını okur
   - Görevleri sırayla yapar
   - Checkboxları işaretler

---

## 🎯 FazLAR VE DOSYALAR

| Faz | Dosya | Açıklama |
|-----|-------|----------|
| 0 | [FAZ-0-PLANLAMA.md](FAZ-0-PLANLAMA.md) | ✅ Altyapı hazırlığı (TAMAMLANDI) |
| 1 | [FAZ-1-DIL-SISTEMI.md](FAZ-1-DIL-SISTEMI.md) | Çoklu dil desteği |
| 2 | [FAZ-2-YAZAR-MODULU.md](FAZ-2-YAZAR-MODULU.md) | Email/ORCID ile yazar arama |
| 3 | [FAZ-3-REFERANS-SISTEMI.md](FAZ-3-REFERANS-SISTEMI.md) | Referans ekleme (tek/toplu) |
| 4 | [FAZ-4-TASLAK-SISTEMI.md](FAZ-4-TASLAK-SISTEMI.md) | Otomatik taslak kaydetme |
| 5 | [FAZ-5-HAKEM-MODULU.md](FAZ-5-HAKEM-MODULU.md) | Hakem önerme sistemi |
| 6 | [FAZ-6-DOSYA-YUKLEME.md](FAZ-6-DOSYA-YUKLEME.md) | Dosya yükleme ve yönetim |
| 7 | [FAZ-7-EDITORE-NOT.md](FAZ-7-EDITORE-NOT.md) | Editöre not gönderme |
| 8 | [FAZ-8-KONTROL-LISTESI.md](FAZ-8-KONTROL-LISTESI.md) | Gönderim öncesi kontroller |

---

## ✅ FAZ 0 TAMAMLANAN İŞLER

### 1. Dosya Yapısı Oluşturuldu

```
✅ Tüm faz MD dosyaları (FAZ-1 ile FAZ-8)
✅ database-setup.sql (veritabanı script)
✅ KURULUM-REHBERI.md (bu dosya)
```

### 2. Klasör Yapısı Oluşturuldu

```
amdsphp/
├── config/
│   └── languages/           ✅ OLUŞTURULDU
│       ├── tr/              ✅
│       └── en/              ✅
├── app/
│   └── Services/            ✅ OLUŞTURULDU
├── storage/                 ✅ OLUŞTURULDU
│   ├── manuscripts/         ✅
│   ├── forms/               ✅
│   ├── supplements/         ✅
│   ├── temp/                ✅
│   └── .htaccess            ✅ (güvenlik)
```

### 3. Veritabanı Script Hazırlandı

**Dosya**: [database-setup.sql](database-setup.sql)

**İçindeki tablolar**:
- ✅ `dil_degiskenleri` (Çoklu dil sistemi)
- ✅ `dil_paketleri` (Dil paketleri)
- ✅ `dergi_ayarlari` (Makale türleri, konular, diller)
- ✅ `kullanici_yazar_profilleri` (Yazar profilleri, ORCID)
- ✅ `makale_taslaklari` (Taslak kayıt sistemi)
- ✅ `makale_hakem_onerileri` (Hakem sistemi)
- ✅ `makale_dosyalari` (Dosya yönetimi)
- ✅ `makale_yazarlari` (ORCID alanları eklendi)
- ✅ `makaleler` (editore_notu alanı eklendi)

**Varsayılan veriler**:
- ✅ 6 makale türü (Araştırma, Derleme, Olgu Sunumu, vb.)
- ✅ 6 makale konusu (Bilgisayar, Mühendislik, Tıp, vb.)
- ✅ 4 makale dili (TR, EN, DE, FR)

---

## 🔧 ŞİMDİ NE YAPMALI?

### Adım 1: Veritabanını Kur

**Manuel kurulum:**

1. phpMyAdmin'i aç
2. Veritabanınızı seç
3. [database-setup.sql](database-setup.sql) dosyasını içe aktar
4. "Kurulum tamamlandı!" mesajını gör

**Veya terminal ile:**

```bash
mysql -u kullanici_adi -p veritabani_adi < database-setup.sql
```

**Kontrol et:**

```sql
SHOW TABLES;
SELECT * FROM dergi_ayarlari;
```

### Adım 2: Claude'a Faz 1'i Başlatmasını Söyle

**Komut:**

```
Faz 1'i başlat
```

veya

```
checklist uygula
```

Claude otomatik olarak:
- Faz 1 dosyasını okuyacak
- Dil sistemi kodlarını yazacak
- Testleri yapacak
- Checkboxları işaretleyecek

---

## 📊 PROJE DURUMU

### Tamamlanan İşler (Faz 0)

- ✅ Tüm planlama dosyaları oluşturuldu (9 dosya)
- ✅ Veritabanı script hazırlandı
- ✅ Klasör yapısı oluşturuldu
- ✅ Güvenlik (storage/.htaccess)
- ✅ Varsayılan ayarlar hazırlandı

### Bekleyen İşler

- ⏳ Veritabanını kurmanız gerekiyor (Manuel adım)
- ⏳ Faz 1-8 kodlamalar (Claude ile)

### İlerleme

```
Faz 0: ████████████████████ 100% ✅ TAMAMLANDI
Faz 1: □□□□□□□□□□□□□□□□□□□□   0% ⏳ Bekliyor
Faz 2: □□□□□□□□□□□□□□□□□□□□   0% ⏳ Bekliyor
...
```

---

## 🎓 KULLANIM TALİMATLARI

### Claude ile Çalışma

**Doğru komutlar:**

```
✅ "checklist uygula"
✅ "Faz 1'i başlat"
✅ "Faz 1 görev 1.2'yi yap"
✅ "Test et"
```

**Yanlış komutlar:**

```
❌ "Her şeyi yap" (çok genel)
❌ "Kodu yaz" (hangi kod?)
❌ "Tamamla" (ne tamamlansın?)
```

### Faz Geçişleri

Her faz tamamlandığında:

1. Claude checkboxları işaretler
2. [CHECKLIST-MASTER.md](CHECKLIST-MASTER.md)'de fazı tamamlar
3. Sonraki faza geçer
4. Siz sadece **"devam et"** dersiniz

---

## 🔗 ÖNEMLİ LİNKLER

- [Ana Checklist](CHECKLIST-MASTER.md)
- [Sistem Analiz](SISTEM-ANALIZ-VE-PLANLAMA.md)
- [Veritabanı Script](database-setup.sql)
- [Faz 0 - Planlama](FAZ-0-PLANLAMA.md)

---

## 💡 İPUÇLARI

1. **Her faz bağımsız**: Bir fazı atlayabilirsiniz
2. **Testler önemli**: Her faz sonunda test yapın
3. **Hata durumunda**: İlgili faz dosyasına not düşün
4. **Gereksinimler**: Faz 5 ve 6'da sizin kararlarınızı bekliyor

---

## 📞 YARDIM

**Sorun mu var?**

1. İlgili faz dosyasının "NOTLAR" bölümüne yaz
2. Claude'a "Faz X'te sorun var, kontrol et" de
3. Claude sorunu analiz edip çözüm önerir

---

## 🎉 HAZIRSANız

**Veritabanını kurduktan sonra:**

```
Faz 1'i başlat
```

**Claude sizin için**:
- LanguageService.php yazacak
- JSON dil paketleri oluşturacak
- Testleri yapacak
- Sonuçları raporlayacak

---

**İyi çalışmalar! 🚀**

**Son Güncelleme**: 2024-12-03
**Durum**: Faz 0 tamamlandı, Faz 1 için hazır
