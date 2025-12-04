# Git Stratejisi - AMDS Projesi

## 📌 Mevcut Durum
- **Branch:** master
- **Remote:** https://github.com/sakyazim/amdphp.git
- **Son Commit:** 1a134e4 Initial commit

---

## 🎯 Commit Stratejisi

### 1. İlk Büyük Commit (ŞİMDİ)
**Commit Mesajı:** `feat: Tüm fazların tamamlanmış hali - Dil, Yazar, Referans, Taslak, Hakem sistemleri`

**İçerik:**
- ✅ Tüm MD dokümantasyon dosyaları (Faz 0-8)
- ✅ Controller'lar (Author, Language, Reference, Reviewer, Taslak)
- ✅ Services (LanguageService, ReferenceService, vb.)
- ✅ Views (layouts, author/, articles/create.php)
- ✅ Public assets (CSS, JS)
- ✅ Config dosyaları (languages/)
- ✅ Database SQL dosyaları
- ✅ .gitignore güncellemesi
- ✅ FORM-TAMAMLAMA-ANALIZI.md
- ✅ .claude/commands/form-devam.md

**Neden Tek Commit?**
- Çok fazla dosya var ve hepsi birbiriyle ilişkili
- Proje şu ana kadar yapılan tüm fazları içeriyor
- İlk commit'ten sonra yeni özellikler için küçük commit'ler atacağız

---

### 2. Bundan Sonraki Commit'ler (Faz Faz)

#### Faz 1: Kritik Düzeltmeler
```bash
# Her düzeltme ayrı commit
git commit -m "fix(yazar): Yazar arama form doldurma sorunu düzeltildi"
git commit -m "fix(yazar): Yazar güncelleme sorunu düzeltildi - yeni kayıt eklemesi önlendi"
git commit -m "style(yazar): Yazar arama listesi CSS iyileştirmesi"
```

#### Faz 2: Yazar ve Hakem Geliştirmeleri
```bash
git commit -m "feat(yazar): ORCID linki tıklanabilir yapıldı"
git commit -m "feat(yazar): Yazar sayısı badge dinamik güncelleme"
git commit -m "style(yazar): Yazar tipi renk kodlaması eklendi"
git commit -m "feat(hakem): Hakem formuna 7 ek alan eklendi"
git commit -m "feat(hakem): Hakem düzenleme butonu eklendi"
```

#### Faz 6: Veritabanı Düzenlemeleri
```bash
git commit -m "database: Form tamamlama için veritabanı düzenlemeleri eklendi"
```

#### Faz 3: Dosya Yükleme Sistemi
```bash
git commit -m "feat(dosya): 9 dosya türü desteği ve progress bar eklendi"
git commit -m "feat(dosya): Dosya güvenlik kontrolleri eklendi"
```

#### Faz 4: Editöre Not ve Kontrol Listesi
```bash
git commit -m "feat(editore-not): Rich text editor ve karakter sayacı eklendi"
git commit -m "feat(kontrol-listesi): 9 maddelik kontrol listesi eklendi"
```

#### Faz 5: Makale Özeti ve Gönderim
```bash
git commit -m "feat(ozet): Makale özeti sayfası ve düzenleme butonları eklendi"
git commit -m "feat(gonderim): Form submission ve validasyon tamamlandı"
```

---

## 📝 Commit Mesaj Formatı

### Convention: [Conventional Commits](https://www.conventionalcommits.org/)

```
<tip>(<kapsam>): <kısa açıklama>

[opsiyonel gövde]

[opsiyonel footer]
```

### Tipler:
- `feat`: Yeni özellik
- `fix`: Bug düzeltmesi
- `docs`: Sadece dokümantasyon değişikliği
- `style`: Kod anlamını değiştirmeyen değişiklikler (whitespace, formatting)
- `refactor`: Ne bug fix ne de yeni özellik olmayan kod değişikliği
- `perf`: Performans iyileştirmesi
- `test`: Test ekleme veya düzeltme
- `chore`: Build process veya yardımcı araç değişiklikleri
- `database`: Veritabanı değişiklikleri

### Kapsamlar:
- `yazar`: Yazar modülü
- `hakem`: Hakem modülü
- `referans`: Referans sistemi
- `taslak`: Taslak sistemi
- `dil`: Dil sistemi
- `dosya`: Dosya yükleme
- `editore-not`: Editöre not
- `kontrol-listesi`: Kontrol listesi
- `ozet`: Makale özeti
- `gonderim`: Form gönderimi

---

## 🚀 Push Stratejisi

### Seçenek 1: Her Commit Sonrası Push (ÖNERİLEN)
```bash
git add .
git commit -m "feat(yazar): Yazar arama form doldurma sorunu düzeltildi"
git push origin master
```

**Avantajları:**
- Her değişiklik anında yedeklenir
- Collaboration için daha iyi
- Rollback daha kolay

### Seçenek 2: Faz Sonunda Push
```bash
# Faz 1 tüm commit'leri
git commit -m "..."
git commit -m "..."
git commit -m "..."

# Faz sonunda tek push
git push origin master
```

**Avantajları:**
- Daha az network trafiği
- Gruplu değişiklikler

---

## 🔄 Branch Stratejisi (İleride)

Şu an `master` branch'te çalışıyoruz. İleride feature branch'ler kullanabiliriz:

```bash
# Yeni özellik için branch
git checkout -b feature/form-konfigurasyonu
# Çalış, commit et
git commit -m "feat: Form konfigürasyon sistemi eklendi"
# Master'a merge
git checkout master
git merge feature/form-konfigurasyonu
git push origin master
```

---

## ⚠️ GİT'E GİTMEMESİ GEREKEN DOSYALAR

### ✅ Şu Anda Doğru Ignore Ediliyor:
- `*.sql` (database-setup.sql hariç - önemli olanlar exception)
- `/vendor/`
- `.env`
- `*.log`
- `/storage/uploads/*`
- `/storage/cache/*`
- `nul`
- `/old/`

### 🤔 Soru İşaretleri (Siz Karar Verin):
- `deepseek.md` → Kişisel not mu? (Git'e commit edeyim mi?)
- `taslak-ayarla.md` → Kişisel not mu? (Git'e commit edeyim mi?)
- `views/articles/create--.php` → Eski versiyon mu? (Silelim mi?)

---

## 📦 GİT'E GİTMESİ GEREKEN TÜM DOSYALAR

### 1. Dokümantasyon (MD Dosyaları)
```
CHECKLIST-MASTER.md
DIL-SISTEMI-MIMARI.md
FAZ-0-PLANLAMA.md
FAZ-1-DIL-SISTEMI.md
FAZ-1-TAMAMLANDI.md
FAZ-2-TAMAMLANDI.md
FAZ-2-YAZAR-MODULU.md
FAZ-3-REFERANS-SISTEMI.md
FAZ-3-TAMAMLANDI.md
FAZ-4-TAMAMLANDI.md
FAZ-4-TASLAK-SISTEMI.md
FAZ-5-HAKEM-MODULU.md
FAZ-5-TAMAMLANDI.md
FAZ-5-TEST-REHBERI.md
FAZ-6-DOSYA-YUKLEME.md
FAZ-7-EDITORE-NOT.md
FAZ-8-KONTROL-LISTESI.md
FORM-TAMAMLAMA-ANALIZI.md
KURULUM-REHBERI.md
MAKALE-GONDERIM-ANALIZ.md
SISTEM-ANALIZ-VE-PLANLAMA.md
YENI-DIL-EKLEME-REHBERI.md
GIT-STRATEJISI.md (bu dosya)
```

### 2. Claude Commands
```
.claude/commands/form-devam.md
```

### 3. Controllers
```
app/Controllers/ArticleController.php (modified)
app/Controllers/YazarController.php (modified)
app/Controllers/AuthorController.php (new)
app/Controllers/LanguageController.php (new)
app/Controllers/ReferenceController.php (new)
app/Controllers/ReviewerController.php (new)
app/Controllers/TaslakController.php (new)
```

### 4. Services
```
app/Services/ (tüm klasör içeriği)
```

### 5. Middleware
```
app/Middleware/AuthMiddleware.php (modified)
```

### 6. Core
```
core/Router.php (modified)
```

### 7. Views
```
views/articles/create.php (modified)
views/articles/edit.php (modified)
views/author/ (tüm klasör)
views/layouts/ (tüm klasör)
```

### 8. Public Assets
```
public/assets/css/author-search.css
public/assets/css/create-wizard.css
public/assets/css/language-switcher.css
public/assets/css/reviewer-manager.css
public/assets/js/ (tüm klasör)
```

### 9. Config
```
config/languages/ (tüm klasör)
```

### 10. Database
```
database-setup.sql
database-reviewer-table.sql
eksik.sql
```

### 11. Storage (sadece klasör yapısı)
```
storage/.gitkeep
storage/cache/.gitkeep
storage/uploads/.gitkeep
```

### 12. Root Files
```
public/index.php (modified)
.gitignore (modified)
```

---

## 🎬 İLK COMMIT KOMUTU

```bash
# 1. Tüm dosyaları stage'e al
git add .

# 2. Commit et (detaylı mesaj)
git commit -m "feat: Tüm fazların tamamlanmış hali

- Dil sistemi (çoklu dil desteği, JSON config)
- Yazar modülü (arama, ekleme, ORCID entegrasyonu)
- Referans sistemi (tek/toplu ekleme, APA format)
- Taslak sistemi (otomatik kayıt, localStorage)
- Hakem modülü (öneri sistemi)
- Wizard sistemi (13 adımlı form)
- Dokümantasyon (20+ MD dosyası)
- Form tamamlama analizi
- Git stratejisi

Kapsamlı Faz 0-8 implementasyonu"

# 3. Push et
git push origin master
```

---

## 🔍 KONTROL KOMUTU

Commit öncesi son kontrol:

```bash
# Hangi dosyalar commit edilecek?
git status

# Dosya içeriğini kontrol et
git diff --cached

# Commit sonrası kontrol
git log --oneline -3
```

---

## ❓ SORULAR VE CEVAPLAR

### S1: `nul` dosyası nedir?
**C:** Windows'ta hatalı oluşturulmuş boş bir dosya. Git'e gitmesin (.gitignore'da eklendi).

### S2: `/old/` klasörünü commit etmeli miyiz?
**C:** Hayır. Eski HTML versiyonları sadece referans için. Git'e gitmesin (.gitignore'da eklendi).

### S3: `deepseek.md` ve `taslak-ayarla.md` ne yapmalı?
**C:** Sizin kararınız. Kişisel notlarsa ignore edebiliriz.

### S4: `views/articles/create--.php` ne yapmalı?
**C:** Muhtemelen eski backup. Silebiliriz veya ignore edebiliriz.

### S5: SQL backup dosyalarını commit etmeli miyiz?
**C:** Hayır. `*.sql` ignore edildi ama `database-setup.sql` gibi önemli schema dosyaları exception olarak eklendi.

---

**Hazırlayan:** Claude (Anthropic)
**Tarih:** 2024-12-04
**Versiyon:** 1.0
