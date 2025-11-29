# AMDS - Akademik Makale Değerlendirme Sistemi

Multi-tenant SaaS platformu - WordPress benzeri akademik dergi yönetim sistemi.

---

## ⚡ HIZLI BAŞLANGIÇ

### 🎯 Tamamlanan: MVC Yapısı (Makale Yönetimi)

**Makale Yönetim Sistemi Hazır!** ✅

Tamamlanan özellikler:
1. ✅ Article Model (CRUD operasyonları)
2. ✅ Article Controller (tam özellikli)
3. ✅ Makale listesi ve detay sayfaları
4. ✅ Wizard formatında yeni makale formu
5. ✅ Makale düzenleme formu
6. ✅ Pagination (sayfalama)
7. ✅ Filtering ve Search (filtreleme ve arama)

---

### 📊 Mevcut Durum

#### ✅ Tamamlanan
- [x] ⚙️ Framework kurulumu (Router, Database, TenantResolver)
- [x] 🗄️ Core Database (8 tablo)
- [x] 🏢 Tenant Database (14 tablo)
- [x] 👥 Test verileri (7 kullanıcı, 5 rol, 1 makale)
- [x] 🌐 Multi-tenant altyapı
- [x] 🔌 API endpoints
- [x] 📁 VS Code workspace
- [x] 🔐 **Authentication Sistemi** ✨ YENİ!
  - Login/Register sayfaları
  - Auth Controller & Middleware
  - Session yönetimi
  - Rol tabanlı yetkilendirme
  - Password hashing (bcrypt)
  - CSRF koruması
  - Bootstrap 5 UI

#### ⏳ Yapılacaklar (Sırayla)
1. ~~**📄 İlk MVC Yapısı (Makale Listesi)**~~ ✅ TAMAMLANDI
2. **👥 Yazar Yönetimi Sistemi** ← ŞİMDİ BURADASINIZ
3. 📝 Form Validation (gelişmiş)
4. 📧 E-posta Sistemi
5. 🎨 Dashboard Geliştirme
6. 🧑‍⚖️ Hakem Sistemi
7. 🔧 Install Wizard

---

### 🧪 Çalışan Test URL'leri

#### 🔐 Authentication
```
✅ http://localhost/amdsphp/public/login           - Giriş sayfası
✅ http://localhost/amdsphp/public/register        - Kayıt sayfası
✅ http://localhost/amdsphp/public/dashboard       - Dashboard (giriş gerekli)
```

#### 📄 Makale Yönetimi (YENİ!)
```
✅ http://localhost/amdsphp/public/makaleler       - Makale listesi
✅ http://localhost/amdsphp/public/makaleler/yeni  - Yeni makale (wizard form)
✅ http://localhost/amdsphp/public/makaleler/1     - Makale detay
✅ http://localhost/amdsphp/public/makaleler/1/duzenle - Makale düzenle
```

#### 🔧 Database & Debug
```
✅ http://localhost/amdsphp/public/db/test         - Database bağlantı testi
✅ http://localhost/amdsphp/public/db/tenants      - Tenant listesi
✅ http://localhost/amdsphp/public/db/users        - Kullanıcı listesi
✅ http://localhost/amdsphp/public/phpinfo         - PHP bilgileri
```

---

## 📋 Kurulum

### Gereksinimler
- PHP 8.0+
- MySQL 8.0+ / MariaDB 10.4+
- Composer
- Apache (mod_rewrite etkin)

### Yerel Geliştirme

1. **Projeyi Klonla**
   ```bash
   git clone [repository-url]
   cd amdsphp
   ```

2. **Bağımlılıkları Yükle**
   ```bash
   composer install
   ```

3. **Environment Ayarları**
   ```bash
   cp .env.example .env
   # .env dosyasını düzenle
   ```

4. **Database Kurulumu**
   ```bash
   mysql -u root < migrations/001_create_core_database.sql
   ```

5. **XAMPP ile Çalıştırma**
   - Projeyi `C:\xampp\htdocs\amdsphp` klasörüne kopyala
   - Apache ve MySQL'i başlat
   - Tarayıcıda aç: `http://localhost/amdsphp/public/`

## 🗂️ Klasör Yapısı

```
amdsphp/
├── app/              # Uygulama kodu
│   ├── Controllers/  # Controller'lar
│   ├── Models/       # Model'ler
│   └── Middleware/   # Middleware'ler
├── config/           # Konfigürasyon dosyaları
├── core/             # Framework çekirdeği
│   ├── Database.php
│   ├── Router.php
│   ├── TenantResolver.php
│   └── helpers.php
├── migrations/       # Database migration'ları
├── public/           # Web root (DocumentRoot burası olmalı)
│   ├── index.php     # Giriş noktası
│   └── assets/       # CSS, JS, resimler
├── storage/          # Yüklenen dosyalar, loglar
├── views/            # View şablonları
├── old/              # Eski HTML prototipleri
└── vendor/           # Composer bağımlılıkları
```

## 🔧 Geliştirme

### VS Code ile Çalışma
1. VS Code'u aç: `code C:\xampp\htdocs\amdsphp`
2. Önerilen extension'ları yükle
3. PHP path: `C:\xampp\php\php.exe`

### Test URL'leri
- Ana sayfa: `http://localhost/amdsphp/public/`
- **Login:** `http://localhost/amdsphp/public/login`
- **Dashboard:** `http://localhost/amdsphp/public/dashboard`
- DB Test: `http://localhost/amdsphp/public/db/test`
- Tenantlar: `http://localhost/amdsphp/public/db/tenants`
- PHP Info: `http://localhost/amdsphp/public/phpinfo`

### Database
- **Core DB**: `amds_core` (tüm dergiler için merkezi)
- **Tenant DB**: `amds_tenant_[slug]` (her dergi için ayrı)

## 📝 Test Kullanıcıları

```
Email: yazar1@test.com | Şifre: 123456 | Rol: Yazar
Email: hakem1@test.com | Şifre: 123456 | Rol: Hakem
Email: editor@test.com | Şifre: 123456 | Rol: Alan Editörü
Email: yonetici@test.com | Şifre: 123456 | Rol: Dergi Yöneticisi
```

## 🚀 Yol Haritası

- [x] Temel Framework
- [x] Database Mimarisi
- [x] Multi-Tenant Altyapı
- [x] **Authentication Sistemi** ✅
- [ ] Makale Yönetimi
- [ ] Hakem Sistemi
- [ ] E-posta Sistemi
- [ ] Install Wizard

## 📚 Dokümantasyon

Detaylı dokümantasyon için: [AMDS_ANALIZ_VE_YOL_HARITASI.md](AMDS_ANALIZ_VE_YOL_HARITASI.md)

## 📄 Lisans

Proprietary - Sadece yetkili kullanım
