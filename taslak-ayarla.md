````markdown
# AMDS Makale Oluşturma Sihirbazı - Veritabanı Tabanlı Taslak Sistemi

## Veritabanı Yapısı Geliştirmesi

### 1.1. Yeni Tablo: makale_taslaklari

```sql
CREATE TABLE `makale_taslaklari` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `kullanici_id` int(10) UNSIGNED NOT NULL,
  `makale_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Tamamlandığında ilişkilendirilecek',
  `taslak_adi` varchar(255) DEFAULT NULL,
  `son_adim` tinyint(3) UNSIGNED DEFAULT 0,
  `taslak_verisi` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`taslak_verisi`)),
  `durum` enum('taslak','tamamlandi','iptal') DEFAULT 'taslak',
  `toplam_adim` tinyint(3) UNSIGNED DEFAULT 13,
  `son_guncelleme` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `olusturma_tarihi` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `kullanici_id` (`kullanici_id`),
  KEY `makale_id` (`makale_id`),
  KEY `durum` (`durum`),
  CONSTRAINT `makale_taslaklari_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE,
  CONSTRAINT `makale_taslaklari_ibfk_2` FOREIGN KEY (`makale_id`) REFERENCES `makaleler` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 1.2. Yazar Paneli İçin Görünüm

Dosya: views/author/dashboard.php - Taslaklar bölümü ekle

```html
<!-- Taslak Makalelerim -->
<div class="card">
  <div class="card-header bg-warning text-dark">
    <h5 class="mb-0">
      <i class="fas fa-edit me-2"></i>Devam Edebilirsiniz
    </h5>
  </div>
  <div class="card-body">
    <div id="taslakListesi">
      <!-- JavaScript ile doldurulacak -->
      <div class="text-center py-4">
        <div class="spinner-border text-warning" role="status">
          <span class="visually-hidden">Yükleniyor...</span>
        </div>
        <p class="mt-2 text-muted">Taslaklar yükleniyor...</p>
      </div>
    </div>
  </div>
</div>
```

## Aşama 1: Backend - Veritabanı Tabanlı Taslak Sistemi

### 1.1. Taslak Controller

Dosya: controllers/TaslakController.php

```php
<?php
class TaslakController {

    // TASLAK OLUŞTUR VEYA GÜNCELLE
    public function kaydet() {
        // CSRF doğrulama
        if (!validateCSRFToken($_POST['csrf_token'])) {
            return jsonResponse(false, "Geçersiz CSRF token");
        }

        $userId = $_SESSION['user_id'];
        $taslakId = $_POST['taslak_id'] ?? null;
        $adim = $_POST['adim'];
        $veri = $_POST['veri'];

        try {
            if ($taslakId) {
                // MEVCUT TASLAĞI GÜNCELLE
                $result = $this->taslakGuncelle($taslakId, $userId, $adim, $veri);
            } else {
                // YENİ TASLAK OLUŞTUR
                $result = $this->yeniTaslakOlustur($userId, $adim, $veri);
            }

            if ($result['success']) {
                return jsonResponse(true, "Taslak kaydedildi", [
                    'taslak_id' => $result['taslak_id'],
                    'timestamp' => date('H:i:s'),
                    'son_adim' => $adim
                ]);
            } else {
                return jsonResponse(false, $result['message']);
            }

        } catch (Exception $e) {
            return jsonResponse(false, "Taslak kaydedilirken hata: " . $e->getMessage());
        }
    }

    private function yeniTaslakOlustur($userId, $adim, $veri) {
        // Makale kodunu önceden oluştur
        $makaleKodu = $this->makaleKoduOlustur();

        // Taslak verisini hazırla
        $taslakVerisi = [
            'makale_kodu' => $makaleKodu,
            'adimlar' => [],
            'olusturulma_tarihi' => date('Y-m-d H:i:s'),
            'son_guncelleme' => date('Y-m-d H:i:s')
        ];

        // İlk adım verisini ekle
        $taslakVerisi['adimlar'][$adim] = [
            'veri' => $veri,
            'kayit_tarihi' => date('Y-m-d H:i:s')
        ];

        // Veritabanına kaydet
        $taslakId = DB::table('makale_taslaklari')->insertGetId([
            'kullanici_id' => $userId,
            'taslak_adi' => 'Yeni Makale - ' . date('d.m.Y H:i'),
            'son_adim' => $adim,
            'taslak_verisi' => json_encode($taslakVerisi),
            'durum' => 'taslak',
            'toplam_adim' => 13
        ]);

        if ($taslakId) {
            return ['success' => true, 'taslak_id' => $taslakId];
        } else {
            return ['success' => false, 'message' => 'Taslak oluşturulamadı'];
        }
    }

    private function taslakGuncelle($taslakId, $userId, $adim, $veri) {
        // Taslağı getir ve kontrol et
        $taslak = DB::table('makale_taslaklari')
            ->where('id', $taslakId)
            ->where('kullanici_id', $userId)
            ->first();

        if (!$taslak) {
            return ['success' => false, 'message' => 'Taslak bulunamadı'];
        }

        // Taslak verisini decode et
        $taslakVerisi = json_decode($taslak->taslak_verisi, true);

        // Yeni adım verisini ekle/güncelle
        $taslakVerisi['adimlar'][$adim] = [
            'veri' => $veri,
            'kayit_tarihi' => date('Y-m-d H:i:s')
        ];

        $taslakVerisi['son_guncelleme'] = date('Y-m-d H:i:s');

        // Veritabanını güncelle
        $guncelleme = DB::table('makale_taslaklari')
            ->where('id', $taslakId)
            ->update([
                'son_adim' => $adim,
                'taslak_verisi' => json_encode($taslakVerisi),
                'son_guncelleme' => date('Y-m-d H:i:s')
            ]);

        if ($guncelleme) {
            return ['success' => true, 'taslak_id' => $taslakId];
        } else {
            return ['success' => false, 'message' => 'Taslak güncellenemedi'];
        }
    }

    // KULLANICININ TASLAKLARINI LİSTELE
    public function listele() {
        $userId = $_SESSION['user_id'];

        $taslaklar = DB::table('makale_taslaklari')
            ->where('kullanici_id', $userId)
            ->where('durum', 'taslak')
            ->orderBy('son_guncelleme', 'DESC')
            ->get();

        $formattedTaslaklar = [];
        foreach ($taslaklar as $taslak) {
            $veri = json_decode($taslak->taslak_verisi, true);
            $formattedTaslaklar[] = [
                'id' => $taslak->id,
                'taslak_adi' => $taslak->taslak_adi,
                'son_adim' => $taslak->son_adim,
                'toplam_adim' => $taslak->toplam_adim,
                'makale_kodu' => $veri['makale_kodu'] ?? 'KOD-YOK',
                'son_guncelleme' => $taslak->son_guncelleme,
                'ilerleme' => round(($taslak->son_adim / $taslak->toplam_adim) * 100, 1)
            ];
        }

        return jsonResponse(true, "Taslaklar getirildi", $formattedTaslaklar);
    }

    // TASLAĞI YÜKLE (Makale oluşturma sayfasında kullanılacak)
    public function yukle($taslakId) {
        $userId = $_SESSION['user_id'];

        $taslak = DB::table('makale_taslaklari')
            ->where('id', $taslakId)
            ->where('kullanici_id', $userId)
            ->first();

        if (!$taslak) {
            return jsonResponse(false, "Taslak bulunamadı");
        }

        $taslakVerisi = json_decode($taslak->taslak_verisi, true);

        return jsonResponse(true, "Taslak yüklendi", [
            'taslak_id' => $taslak->id,
            'son_adim' => $taslak->son_adim,
            'taslak_verisi' => $taslakVerisi,
            'makale_kodu' => $taslakVerisi['makale_kodu'] ?? null
        ]);
    }

    // TASLAĞI SİL
    public function sil($taslakId) {
        $userId = $_SESSION['user_id'];

        $silme = DB::table('makale_taslaklari')
            ->where('id', $taslakId)
            ->where('kullanici_id', $userId)
            ->delete();

        if ($silme) {
            return jsonResponse(true, "Taslak silindi");
        } else {
            return jsonResponse(false, "Taslak silinemedi");
        }
    }

    // TASLAĞI TAMAMLA (Makale gönderildiğinde)
    public function tamamla($taslakId, $makaleId) {
        $guncelleme = DB::table('makale_taslaklari')
            ->where('id', $taslakId)
            ->update([
                'makale_id' => $makaleId,
                'durum' => 'tamamlandi',
                'son_guncelleme' => date('Y-m-d H:i:s')
            ]);

        return $guncelleme;
    }

    private function makaleKoduOlustur() {
        $yil = date('Y');

        // Bu yıla ait makale sayısını bul
        $sayac = DB::table('makaleler')
            ->whereYear('olusturma_tarihi', $yil)
            ->count();

        $yeniNo = $sayac + 1;

        return 'MAK-' . $yil . '-' . str_pad($yeniNo, 4, '0', STR_PAD_LEFT);
    }
}
```

## Aşama 2: Frontend - Gelişmiş Taslak Yönetimi

### 2.1. Ana JavaScript Dosyası - Güncellenmiş

Dosya: assets/js/taslak-sistemi.js

```javascript
class TaslakSistemi {
    constructor() {
        this.taslakId = null;
        this.makaleKodu = null;
        this.autoSaveEnabled = true;
        this.autoSaveInterval = 30000; // 30 saniye
        this.isSaving = false;

        this.init();
    }

    init() {
        // Sayfa yüklendiğinde taslak kontrolü
        this.sayfaYuklendiğindeTaslakKontrol();

        // Otomatik kayıt interval'i
        setInterval(() => {
            if (this.autoSaveEnabled && !this.isSaving) {
                this.otomatikKaydet();
            }
        }, this.autoSaveInterval);

        // Manuel kayıt butonu
        document.getElementById('manualSaveBtn')?.addEventListener('click', () => {
            this.manuelKaydet();
        });

        // Adım değişikliklerinde kaydet
        document.addEventListener('stepChanged', (e) => {
            setTimeout(() => this.otomatikKaydet(), 1000);
        });

        // Input değişikliklerinde kaydet (debounce)
        this.inputDinleyicileriKur();
    }

    async sayfaYuklendiğindeTaslakKontrol() {
        const urlParams = new URLSearchParams(window.location.search);
        const taslakId = urlParams.get('taslak_id');

        if (taslakId) {
            // Belirli bir taslağı yükle
            await this.taslakYukle(taslakId);
        } else {
            // Son taslağı kontrol et (isteğe bağlı)
            // await this.sonTaslagiKontrolEt();
        }
    }

    async taslakYukle(taslakId) {
        try {
            const response = await fetch(`/taslak/yukle/${taslakId}`);
            const result = await response.json();

            if (result.success) {
                this.taslakId = result.data.taslak_id;
                this.makaleKodu = result.data.makale_kodu;

                // Formu doldur
                this.formuDoldur(result.data.taslak_verisi);

                // Kaldığı adıma git
                if (result.data.son_adim !== undefined) {
                    showStep(result.data.son_adim);
                }

                this.bildirimGoster('Taslak yüklendi - kaldığınız yerden devam edebilirsiniz', 'success');
            }
        } catch (error) {
            console.error('Taslak yükleme hatası:', error);
        }
    }

    formuDoldur(taslakVerisi) {
        if (!taslakVerisi.adimlar) return;

        Object.keys(taslakVerisi.adimlar).forEach(adim => {
            const adimVerisi = taslakVerisi.adimlar[adim].veri;
            this.adimFormunuDoldur(adim, adimVerisi);
        });
    }

    adimFormunuDoldur(adim, veri) {
        const adimEl = document.getElementById('step' + adim);
        if (!adimEl) return;

        Object.keys(veri).forEach(alanAdi => {
            const alan = adimEl.querySelector(`[name="${alanAdi}"]`);
            if (alan) {
                if (alan.type === 'checkbox' || alan.type === 'radio') {
                    alan.checked = (alan.value === veri[alanAdi]);
                } else {
                    alan.value = veri[alanAdi];
                }

                // Validasyonu tetikle
                validateField(alan);
            }
        });
    }

    async otomatikKaydet() {
        if (this.isSaving) return;

        this.isSaving = true;
        const mevcutAdim = window.currentStep;
        const adimVerisi = this.adimVerisiniTopla(mevcutAdim);

        try {
            const response = await fetch('/taslak/kaydet', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    taslak_id: this.taslakId,
                    adim: mevcutAdim,
                    veri: adimVerisi,
                    csrf_token: document.querySelector('input[name="csrf_token"]').value
                })
            });

            const result = await response.json();

            if (result.success) {
                // İlk kayıtta taslakId'yi sakla
                if (result.data.taslak_id && !this.taslakId) {
                    this.taslakId = result.data.taslak_id;
                }

                this.otomatikKayitBildirimi(result.data.timestamp);
            }
        } catch (error) {
            console.error('Otomatik kayıt hatası:', error);
        } finally {
            this.isSaving = false;
        }
    }

    async manuelKaydet() {
        const kaydetBtn = document.getElementById('manualSaveBtn');
        const kaydetYazi = document.getElementById('saveBtnText');
        const kaydetSpinner = document.getElementById('saveSpinner');

        // Butonu disable et
        kaydetBtn.disabled = true;
        kaydetYazi.textContent = 'Kaydediliyor...';
        kaydetSpinner.classList.remove('d-none');

        try {
            const mevcutAdim = window.currentStep;
            const adimVerisi = this.adimVerisiniTopla(mevcutAdim);

            const response = await fetch('/taslak/kaydet', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    taslak_id: this.taslakId,
                    adim: mevcutAdim,
                    veri: adimVerisi,
                    csrf_token: document.querySelector('input[name="csrf_token"]').value
                })
            });

            const result = await response.json();

            if (result.success) {
                if (result.data.taslak_id && !this.taslakId) {
                    this.taslakId = result.data.taslak_id;
                }

                this.manuelKayitBildirimi(result.data.timestamp);
                this.kayitDurumunuGuncelle(result.data.timestamp);
            } else {
                throw new Error(result.message);
            }

        } catch (error) {
            this.kayitHatasiGoster(error.message);
        } finally {
            // Butonu tekrar aktif et
            kaydetBtn.disabled = false;
            kaydetYazi.textContent = 'Kaydet';
            kaydetSpinner.classList.add('d-none');
        }
    }

    adimVerisiniTopla(adim) {
        const adimEl = document.getElementById('step' + adim);
        const veri = {};

        if (!adimEl) return veri;

        const inputlar = adimEl.querySelectorAll('input, select, textarea');
        inputlar.forEach(input => {
            if (input.name && !input.name.includes('csrf')) {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    if (input.checked) {
                        veri[input.name] = input.value;
                    }
                } else {
                    veri[input.name] = input.value;
                }
            }
        });

        return veri;
    }

    inputDinleyicileriKur() {
        const inputlar = document.querySelectorAll('input, select, textarea');
        let kaydetZamanlayici;

        inputlar.forEach(input => {
            input.addEventListener('input', () => {
                clearTimeout(kaydetZamanlayici);
                kaydetZamanlayici = setTimeout(() => {
                    if (this.autoSaveEnabled) {
                        this.otomatikKaydet();
                    }
                }, 2000); // 2 saniye debounce
            });
        });
    }

    otomatikKayitBildirimi(zamanDamgasi) {
        let bildirim = document.getElementById('autoSaveNotification');

        if (!bildirim) {
            bildirim = document.createElement('div');
            bildirim.id = 'autoSaveNotification';
            bildirim.className = 'alert alert-info alert-dismissible fade show position-fixed';
            bildirim.style.cssText = `
                top: 20px;
                right: 20px;
                z-index: 9999;
                min-width: 300px;
            `;
            document.body.appendChild(bildirim);
        }

        bildirim.innerHTML = `
            <i class="fas fa-save me-2"></i>
            <strong>Otomatik kayıt</strong> oluşturuldu (${zamanDamgasi})
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        setTimeout(() => {
            if (bildirim.parentNode) {
                const bsAlert = new bootstrap.Alert(bildirim);
                bsAlert.close();
            }
        }, 3000);
    }

    manuelKayitBildirimi(zamanDamgasi) {
        const bildirim = document.createElement('div');
        bildirim.className = 'alert alert-success alert-dismissible fade show position-fixed';
        bildirim.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        `;

        bildirim.innerHTML = `
            <i class="fas fa-check-circle me-2"></i>
            <strong>Başarıyla kaydedildi</strong> (${zamanDamgasi})
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(bildirim);

        setTimeout(() => {
            if (bildirim.parentNode) {
                const bsAlert = new bootstrap.Alert(bildirim);
                bsAlert.close();
            }
        }, 3000);
    }

    kayitDurumunuGuncelle(zamanDamgasi) {
        const sonKayitZamani = document.getElementById('lastSaveTime');
        if (sonKayitZamani) {
            sonKayitZamani.textContent = `Son kayıt: ${zamanDamgasi}`;
            sonKayitZamani.classList.remove('text-muted');
            sonKayitZamani.classList.add('text-success');

            setTimeout(() => {
                sonKayitZamani.classList.remove('text-success');
                sonKayitZamani.classList.add('text-muted');
            }, 10000);
        }
    }

    kayitHatasiGoster(mesaj) {
        const bildirim = document.createElement('div');
        bildirim.className = 'alert alert-danger alert-dismissible fade show position-fixed';
        bildirim.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        `;

        bildirim.innerHTML = `
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Kayıt hatası:</strong> ${mesaj}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(bildirim);
    }

    bildirimGoster(mesaj, tip) {
        const bildirim = document.createElement('div');
        bildirim.className = `alert alert-${tip} alert-dismissible fade show position-fixed`;
        bildirim.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        `;

        bildirim.innerHTML = `
            ${mesaj}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(bildirim);

        setTimeout(() => {
            if (bildirim.parentNode) {
                const bsAlert = new bootstrap.Alert(bildirim);
                bsAlert.close();
            }
        }, 5000);
    }
}

// Taslak sistemini başlat
document.addEventListener('DOMContentLoaded', function() {
    window.taslakSistemi = new TaslakSistemi();
});
```

### 2.2. Yazar Paneli Taslak Listesi

Dosya: assets/js/yazar-taslak-listesi.js

```javascript
class YazarTaslakListesi {
    constructor() {
        this.init();
    }

    async init() {
        await this.taslaklariYukle();
    }

    async taslaklariYukle() {
        try {
            const response = await fetch('/taslak/listele');
            const result = await response.json();

            if (result.success) {
                this.taslaklariGoster(result.data);
            } else {
                this.hataGoster(result.message);
            }
        } catch (error) {
            this.hataGoster('Taslaklar yüklenirken hata oluştu');
        }
    }

    taslaklariGoster(taslaklar) {
        const konteyner = document.getElementById('taslakListesi');

        if (taslaklar.length === 0) {
            konteyner.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Henüz kayıtlı taslak bulunmuyor.</p>
                    <a href="/makaleler/olustur" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Yeni Makale Başlat
                    </a>
                </div>
            `;
            return;
        }

        let html = '';
        taslaklar.forEach(taslak => {
            html += `
                <div class="taslak-item card mb-3">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="card-title">${taslak.taslak_adi}</h6>
                                <div class="d-flex flex-wrap gap-3 text-muted small">
                                    <span><i class="fas fa-hashtag me-1"></i>${taslak.makale_kodu}</span>
                                    <span><i class="fas fa-step-forward me-1"></i>${taslak.son_adim + 1}. Adım</span>
                                    <span><i class="fas fa-calendar me-1"></i>${this.tarihFormatla(taslak.son_guncelleme)}</span>
                                </div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-warning" role="progressbar"
                                         style="width: ${taslak.ilerleme}%"
                                         aria-valuenow="${taslak.ilerleme}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <small class="text-muted">%${taslak.ilerleme} tamamlandı</small>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="btn-group">
                                    <a href="/makaleler/olustur?taslak_id=${taslak.id}"
                                       class="btn btn-success btn-sm">
                                        <i class="fas fa-play me-1"></i>Devam Et
                                    </a>
                                    <button class="btn btn-outline-danger btn-sm"
                                            onclick="taslakSil(${taslak.id})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        konteyner.innerHTML = html;
    }

    tarihFormatla(tarih) {
        const date = new Date(tarih);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);

        if (diffMins < 1) return 'Az önce';
        if (diffMins < 60) return `${diffMins} dakika önce`;
        if (diffHours < 24) return `${diffHours} saat önce`;
        if (diffDays < 7) return `${diffDays} gün önce`;

        return date.toLocaleDateString('tr-TR');
    }

    hataGoster(mesaj) {
        const konteyner = document.getElementById('taslakListesi');
        konteyner.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${mesaj}
                <button class="btn btn-sm btn-outline-danger ms-2" onclick="yazarTaslakListesi.taslaklariYukle()">
                    <i class="fas fa-redo"></i>
                </button>
            </div>
        `;
    }
}

// Global fonksiyon - taslak silme
async function taslakSil(taslakId) {
    if (!confirm('Bu taslağı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')) {
        return;
    }

    try {
        const response = await fetch(`/taslak/sil/${taslakId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                csrf_token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            })
        });

        const result = await response.json();

        if (result.success) {
            // Listeyi yenile
            window.yazarTaslakListesi.taslaklariYukle();

            // Bildirim göster
            const bildirim = document.createElement('div');
            bildirim.className = 'alert alert-success alert-dismissible fade show';
            bildirim.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>
                Taslak başarıyla silindi
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.querySelector('.container').insertBefore(bildirim, document.querySelector('.container').firstChild);
        } else {
            alert('Taslak silinemedi: ' + result.message);
        }
    } catch (error) {
        alert('Taslak silinirken hata oluştu: ' + error.message);
    }
}

// Yazar taslak listesini başlat
document.addEventListener('DOMContentLoaded', function() {
    window.yazarTaslakListesi = new YazarTaslakListesi();
});
```

## Aşama 3: Makale Gönderiminde Taslak Tamamlama

### 3.1. Makale Controller Güncellemesi

Dosya: controllers/MakalelerController.php

```php
public function gonder() {
    // ... mevcut makale oluşturma kodu

    $makaleId = $this->makaleOlustur($_POST);

    if ($makaleId && isset($_POST['taslak_id'])) {
        // Taslağı tamamlandı olarak işaretle
        $taslakController = new TaslakController();
        $taslakController->tamamla($_POST['taslak_id'], $makaleId);
    }

    // ... diğer işlemler
}
```

## Uygulama Sırası - Güncellenmiş Öncelik Listesi

### ✅ HEMEN YAPILACAKLAR (Bugün)
- `makale_taslaklari` tablosunu oluştur
- TaslakController'ı yaz ve endpoint'leri oluştur
- Temel otomatik kayıt sistemini frontend'e entegre et

### 🟡 KISA VADEDE (Bu Hafta)
- Yazar paneli taslak listesini oluştur
- Taslak yükleme sistemini makale oluşturma sayfasına entegre et
- Manuel kayıt butonunu arayüze ekle

### 🟢 ORTA VADEDE (Önümüzdeki Hafta)
- Makale gönderiminde taslak tamamlama işlemini ekle
- Taslak paylaşımı (isteğe bağlı) ekle
- Taslak yedekleme ve geri yükleme özellikleri

## Test Senaryoları - Veritabanı Tabanlı Sistem

### Test 1: Taslak Oluşturma ve Kayıt
- Yeni makaleye başlayınca otomatik taslak oluşuyor mu?
- Farklı cihazlardan aynı taslağa erişilebiliyor mu?
- Otomatik kayıtlar veritabanına yazılıyor mu?

### Test 2: Yazar Paneli Entegrasyonu
- Yazar panelinde taslaklar listeleniyor mu?
- "Devam Et" butonu doğru taslağı açıyor mu?
- İlerleme çubuğu doğru çalışıyor mu?

### Test 3: Taslak Yükleme
- Taslak yüklendiğinde form doğru dolduruluyor mu?
- Kullanıcı kaldığı adıma yönlendiriliyor mu?
- Makale kodu korunuyor mu?

## Bu sistemle kullanıcılar:
- 💾 Farklı cihazlardan taslaklarına erişebilecek
- 🔄 Kaldıkları yerden kesintisiz devam edebilecek
- 📊 İlerlemelerini takip edebilecek
- 🗂️ Birden fazla taslakla aynı anda çalışabilecek
````