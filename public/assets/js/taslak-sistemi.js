/**
 * AMDS - Taslak Kayıt Sistemi
 * Faz 4: Otomatik ve Manuel Taslak Kaydetme
 *
 * Özellikler:
 * - Otomatik kayıt (30 saniye interval)
 * - Manuel kayıt butonu
 * - Taslak yükleme
 * - Form verilerini serialize etme
 * - Son kayıt zamanını gösterme
 */

class TaslakSistemi {
    constructor(options) {
        this.apiBaseUrl = options.apiBaseUrl || '/api/drafts';
        this.formSelector = options.formSelector || '#article-form';
        this.autoSaveInterval = options.autoSaveInterval || 30000; // 30 saniye
        this.autoSaveEnabled = options.autoSaveEnabled !== false;
        this.lastSaveTime = null;
        this.draftId = null;
        this.intervalId = null;
        this.currentStep = 1;
        this.totalSteps = options.totalSteps || 13;
    }

    /**
     * Sistemi başlat
     */
    init() {
        console.log('Taslak sistemi başlatılıyor...');

        // Otomatik kayıt başlat
        if (this.autoSaveEnabled) {
            this.startAutoSave();
        }

        // Manuel kayıt butonu event listener
        const saveBtn = document.getElementById('manual-save-btn');
        if (saveBtn) {
            saveBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.manualSave();
            });
        }

        // Sayfa yüklendiğinde taslak var mı kontrol et
        this.checkForExistingDraft();

        console.log('Taslak sistemi başlatıldı!');
    }

    /**
     * Otomatik kayıt başlat
     */
    startAutoSave() {
        console.log('Otomatik kayıt başlatıldı (30 saniye interval)');

        this.intervalId = setInterval(() => {
            this.autoSave();
        }, this.autoSaveInterval);

        // Sayfa kapatılırken/yenilenirken otomatik kaydet
        window.addEventListener('beforeunload', () => {
            this.autoSave();
        });
    }

    /**
     * Otomatik kayıt durdur
     */
    stopAutoSave() {
        if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
            console.log('Otomatik kayıt durduruldu');
        }
    }

    /**
     * CSRF token'ı al
     */
    getCsrfToken() {
        // Önce meta tag'den dene
        const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (metaToken) return metaToken;

        // Form'dan dene
        const formToken = document.querySelector('[name="csrf_token"]')?.value;
        if (formToken) return formToken;

        // _csrf_token adıyla dene
        const altFormToken = document.querySelector('[name="_csrf_token"]')?.value;
        if (altFormToken) return altFormToken;

        return '';
    }

    /**
     * Otomatik kayıt yap
     */
    async autoSave() {
        console.log('Otomatik kayıt yapılıyor...');

        const data = this.serializeForm();

        if (!data) {
            console.error('Form verisi alınamadı');
            return;
        }

        // CSRF token'ı al
        const csrfToken = this.getCsrfToken();

        try {
            // JSON string'e çevirebiliyor muyuz test et
            let jsonBody;
            try {
                jsonBody = JSON.stringify(data);
            } catch (jsonError) {
                console.error('JSON stringify hatası:', jsonError);
                console.error('Problematic data:', data);
                return;
            }

            const response = await fetch(`${this.apiBaseUrl}/save`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json; charset=utf-8',
                    'X-CSRF-Token': csrfToken
                },
                body: jsonBody
            });

            const result = await response.json();

            if (result.success) {
                this.draftId = result.draft_id;
                this.lastSaveTime = new Date();
                this.updateSaveStatus('Otomatik kaydedildi');
                console.log('Taslak otomatik kaydedildi:', result.draft_id);
            } else {
                console.error('Taslak kaydedilemedi:', result.error);
                if (result.raw_input_preview) {
                    console.error('Server received:', result.raw_input_preview);
                }
            }
        } catch (error) {
            console.error('Otomatik kayıt hatası:', error);
        }
    }

    /**
     * Manuel kayıt yap
     */
    async manualSave() {
        console.log('Manuel kayıt yapılıyor...');

        const data = this.serializeForm();

        if (!data) {
            this.showErrorMessage('Form verisi alınamadı');
            return;
        }

        // CSRF token'ı al
        const csrfToken = this.getCsrfToken();

        try {
            // JSON string'e çevirebiliyor muyuz test et
            let jsonBody;
            try {
                jsonBody = JSON.stringify(data);
            } catch (jsonError) {
                console.error('JSON stringify hatası:', jsonError);
                console.error('Problematic data:', data);
                this.showErrorMessage('Veri JSON formatına çevrilemedi. Lütfen form verilerinizi kontrol edin.');
                return;
            }

            const response = await fetch(`${this.apiBaseUrl}/save`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json; charset=utf-8',
                    'X-CSRF-Token': csrfToken
                },
                body: jsonBody
            });

            const result = await response.json();

            if (result.success) {
                this.draftId = result.draft_id;
                this.lastSaveTime = new Date();
                this.updateSaveStatus('Taslak manuel kaydedildi');
                this.showSuccessMessage('Taslak başarıyla kaydedildi!');
                console.log('Taslak manuel kaydedildi:', result.draft_id);
            } else {
                this.showErrorMessage('Taslak kaydedilemedi: ' + result.error);
                if (result.raw_input_preview) {
                    console.error('Server received:', result.raw_input_preview);
                }
            }
        } catch (error) {
            console.error('Manuel kayıt hatası:', error);
            this.showErrorMessage('Taslak kaydedilemedi: ' + error.message);
        }
    }

    /**
     * Taslak yükle
     */
    async loadDraft(draftId) {
        console.log('Taslak yükleniyor:', draftId);

        try {
            const response = await fetch(`${this.apiBaseUrl}/${draftId}`);
            const result = await response.json();

            if (result.success) {
                const draft = result.draft;

                // Form alanlarını doldur
                this.fillForm(draft.data);

                this.draftId = draft.id;

                // Adım bilgisini güncelle
                if (draft.son_adim) {
                    this.currentStep = draft.son_adim;
                    this.goToStep(draft.son_adim);
                }

                this.showSuccessMessage('Taslak yüklendi: ' + draft.taslak_adi);
                console.log('Taslak yüklendi:', draft);
            } else {
                this.showErrorMessage('Taslak yüklenemedi: ' + result.error);
            }
        } catch (error) {
            console.error('Taslak yükleme hatası:', error);
            this.showErrorMessage('Taslak yüklenemedi: ' + error.message);
        }
    }

    /**
     * Form verilerini serialize et
     */
    serializeForm() {
        const form = document.querySelector(this.formSelector);

        if (!form) {
            console.error('Form bulunamadı:', this.formSelector);
            return null;
        }

        const formData = new FormData(form);

        // Mevcut adımı al
        this.currentStep = parseInt(formData.get('current_step') || this.currentStep || 1);

        // Taslak adını belirle (başlık varsa kullan)
        const taslakAdi = formData.get('baslik') ||
                         formData.get('baslik_en') ||
                         'İsimsiz Taslak';

        const data = {
            taslak_adi: this.sanitizeValue(taslakAdi),
            son_adim: this.currentStep,
            toplam_adim: this.totalSteps,
            data: {}
        };

        // Tüm form verilerini topla
        for (let [key, value] of formData.entries()) {
            // value'yu temizle - HTML karakterlerini escape et
            const sanitizedValue = this.sanitizeValue(value);

            // Dizi formatındaki verileri topla (örn: authors[], references[])
            if (key.endsWith('[]')) {
                const baseKey = key.replace('[]', '');
                if (!data.data[baseKey]) {
                    data.data[baseKey] = [];
                }
                data.data[baseKey].push(sanitizedValue);
            } else {
                data.data[key] = sanitizedValue;
            }
        }

        return data;
    }

    /**
     * Değerleri JSON için güvenli hale getir
     */
    sanitizeValue(value) {
        if (typeof value !== 'string') {
            return value;
        }

        // Kontrol karakterlerini temizle (JSON için geçersiz karakterler)
        // JSON specification'a göre control characters (0x00-0x1F) string içinde olmamalı
        return value.replace(/[\x00-\x1F\x7F-\x9F]/g, '');
    }

    /**
     * Form alanlarını doldur
     */
    fillForm(data) {
        if (!data) return;

        console.log('Form alanları dolduruluyor:', data);

        for (let [key, value] of Object.entries(data)) {
            // Normal input, textarea, select elemanları
            const input = document.querySelector(`[name="${key}"]`);

            if (input) {
                if (input.type === 'checkbox') {
                    input.checked = Boolean(value);
                } else if (input.type === 'radio') {
                    const radio = document.querySelector(`[name="${key}"][value="${value}"]`);
                    if (radio) radio.checked = true;
                } else {
                    input.value = value;
                }
            }

            // Dizi verileri için (örn: authors, references)
            if (Array.isArray(value)) {
                // Bu verileri wizard sistemine özgü fonksiyonlarla doldurmak gerekebilir
                console.log(`Dizi verisi bulundu: ${key}`, value);
            }
        }
    }

    /**
     * Belirli bir adıma git
     */
    goToStep(step) {
        console.log('Adıma gidiliyor:', step);

        // Wizard sistemine entegre et
        if (typeof window.goToStep === 'function') {
            window.goToStep(step);
        } else if (typeof window.wizardGoToStep === 'function') {
            window.wizardGoToStep(step);
        } else {
            console.warn('Wizard adım fonksiyonu bulunamadı');
        }

        this.currentStep = step;
    }

    /**
     * Kayıt durumu güncelle
     */
    updateSaveStatus(message) {
        const statusEl = document.getElementById('save-status');

        if (statusEl) {
            const time = this.lastSaveTime ?
                this.lastSaveTime.toLocaleTimeString('tr-TR', {
                    hour: '2-digit',
                    minute: '2-digit'
                }) : '';

            const icon = '<i class="fa fa-check text-success"></i>';
            statusEl.innerHTML = `${icon} ${message} ${time ? `(${time})` : ''}`;
        }
    }

    /**
     * URL'de draft_id var mı kontrol et
     */
    checkForExistingDraft() {
        const urlParams = new URLSearchParams(window.location.search);
        const draftId = urlParams.get('draft_id');

        if (draftId) {
            console.log('URL\'de taslak ID bulundu:', draftId);
            this.loadDraft(draftId);
        }
    }

    /**
     * Başarı mesajı göster
     */
    showSuccessMessage(message) {
        // Bootstrap toast veya alert kullanılabilir
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Başarılı!',
                text: message,
                timer: 3000,
                showConfirmButton: false
            });
        } else {
            alert(message);
        }
    }

    /**
     * Hata mesajı göster
     */
    showErrorMessage(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Hata!',
                text: message
            });
        } else {
            alert(message);
        }
    }

    /**
     * Mevcut adımı güncelle
     */
    updateCurrentStep(step) {
        this.currentStep = step;

        // Form'daki current_step hidden input'unu güncelle
        const stepInput = document.querySelector('[name="current_step"]');
        if (stepInput) {
            stepInput.value = step;
        }
    }
}

// Global değişken olarak kullanılabilir hale getir
window.TaslakSistemi = TaslakSistemi;
