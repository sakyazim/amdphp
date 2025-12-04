/**
 * Referans Yönetim Modülü
 *
 * Özellikler:
 * - Tek tek referans ekleme
 * - Toplu referans ekleme
 * - İki mod arası geçiş
 * - Referans validasyonu
 * - Parse edilmiş referansları gösterme
 */

class ReferenceManager {
    constructor(options = {}) {
        this.apiBaseUrl = options.apiBaseUrl || '/api/references';
        this.maxReferences = options.maxReferences || 50;
        this.references = [];
        this.currentMode = 'single';
    }

    /**
     * Modülü başlat
     */
    init() {
        this.initModeSwitch();
        this.initBulkParse();
    }

    /**
     * Mod değiştirme butonlarını başlat
     */
    initModeSwitch() {
        const methodRadios = document.querySelectorAll('input[name="referenceMethod"]');

        methodRadios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                this.switchMode(e.target.value);
            });
        });
    }

    /**
     * Mod değiştir (single/bulk)
     */
    switchMode(mode) {
        this.currentMode = mode;

        // UI'ı güncelle
        const singleUI = document.getElementById('singleReferenceUI');
        const bulkUI = document.getElementById('bulkReferenceUI');

        if (mode === 'single') {
            singleUI.classList.remove('d-none');
            bulkUI.classList.add('d-none');
        } else {
            singleUI.classList.add('d-none');
            bulkUI.classList.remove('d-none');
        }

        // Kartların border'larını güncelle
        document.querySelectorAll('.method-option').forEach(card => {
            const cardMethod = card.getAttribute('data-method');
            if (cardMethod === mode) {
                card.classList.remove('border-light');
                card.classList.add('border-primary');
            } else {
                card.classList.remove('border-primary');
                card.classList.add('border-light');
            }
        });
    }

    /**
     * Toplu parse butonu için event listener
     */
    initBulkParse() {
        const bulkTextarea = document.getElementById('bulkReferences');

        if (bulkTextarea) {
            // Karakter sayacı
            bulkTextarea.addEventListener('input', () => {
                this.updateBulkCount();
            });

            // Otomatik resize
            bulkTextarea.addEventListener('input', () => {
                this.autoResize(bulkTextarea);
            });
        }
    }

    /**
     * Toplu referansları parse et
     */
    async parseBulkReferences() {
        const textarea = document.getElementById('bulkReferences');
        const text = textarea.value.trim();

        if (!text) {
            this.showAlert('Lütfen referans metni girin', 'warning');
            return;
        }

        // Loading göster
        this.showLoading(true);

        try {
            const response = await fetch(`${this.apiBaseUrl}/parse-bulk`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ text })
            });

            const data = await response.json();

            if (data.success) {
                this.displayParsedReferences(data.references, data.statistics);
            } else {
                this.showAlert(data.message || 'Parse hatası oluştu', 'danger');
            }
        } catch (error) {
            console.error('Parse error:', error);
            this.showAlert('Bağlantı hatası oluştu', 'danger');
        } finally {
            this.showLoading(false);
        }
    }

    /**
     * Parse edilmiş referansları göster
     */
    displayParsedReferences(references, statistics) {
        // Önce mevcut preview alanını oluştur veya temizle
        let previewContainer = document.getElementById('bulkParsePreview');

        if (!previewContainer) {
            previewContainer = document.createElement('div');
            previewContainer.id = 'bulkParsePreview';
            previewContainer.className = 'mt-4';
            document.getElementById('bulkReferenceUI').appendChild(previewContainer);
        }

        // İstatistikleri göster
        let html = `
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>Parse Sonuçları
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="mb-0">${statistics.total}</h4>
                                <small class="text-muted">Toplam</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                                <h4 class="mb-0 text-success">${statistics.valid}</h4>
                                <small class="text-muted">Geçerli</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-danger bg-opacity-10 rounded">
                                <h4 class="mb-0 text-danger">${statistics.invalid}</h4>
                                <small class="text-muted">Geçersiz</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-primary bg-opacity-10 rounded">
                                <h4 class="mb-0 text-primary">${statistics.percentage}%</h4>
                                <small class="text-muted">Başarı</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Referanslar:</h6>
                        <button type="button" class="btn btn-success btn-sm" onclick="referenceManager.acceptAllValid()">
                            <i class="fas fa-check-double me-1"></i>Tüm Geçerlileri Kabul Et
                        </button>
                    </div>

                    <div class="parsed-references-list">
        `;

        references.forEach((ref, index) => {
            const statusClass = ref.valid ? 'border-success' : 'border-danger';
            const statusBadge = ref.valid ?
                '<span class="badge bg-success">Geçerli</span>' :
                '<span class="badge bg-danger">Geçersiz</span>';
            const addButton = ref.valid ?
                `<button type="button" class="btn btn-sm btn-primary" onclick="referenceManager.addParsedReference(${index})">
                    <i class="fas fa-plus me-1"></i>Ekle
                </button>` : '';

            html += `
                <div class="card mb-2 ${statusClass} border-start border-3" data-ref-index="${index}">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="mb-2">
                                    <strong>Referans #${ref.order}</strong>
                                    ${statusBadge}
                                </div>
                                <p class="mb-1 small">${this.escapeHtml(ref.cleaned)}</p>
                                ${!ref.valid && ref.errors.length > 0 ?
                                    `<div class="alert alert-danger alert-sm p-2 mt-2 mb-0">
                                        <small><i class="fas fa-exclamation-triangle me-1"></i>${ref.errors.join(', ')}</small>
                                    </div>` : ''}
                            </div>
                            <div class="ms-3">
                                ${addButton}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        html += `
                    </div>
                </div>
            </div>
        `;

        previewContainer.innerHTML = html;

        // Referansları sakla
        this.parsedReferences = references;
    }

    /**
     * Tek bir parse edilmiş referansı ekle
     */
    addParsedReference(index) {
        const ref = this.parsedReferences[index];

        if (!ref || !ref.valid) {
            return;
        }

        // Mevcut referans sistemine ekle
        // Bu fonksiyon create-wizard.js'teki fonksiyonla entegre olmalı
        if (typeof addReferenceFromText === 'function') {
            addReferenceFromText(ref.cleaned);
        } else {
            console.warn('addReferenceFromText function not found');
        }

        // Buton'u devre dışı bırak
        const card = document.querySelector(`[data-ref-index="${index}"]`);
        if (card) {
            const button = card.querySelector('button');
            if (button) {
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-check me-1"></i>Eklendi';
                button.classList.remove('btn-primary');
                button.classList.add('btn-secondary');
            }
        }

        this.showAlert('Referans eklendi', 'success', 2000);
    }

    /**
     * Tüm geçerli referansları kabul et
     */
    acceptAllValid() {
        if (!this.parsedReferences) {
            return;
        }

        const validRefs = this.parsedReferences.filter(ref => ref.valid);

        if (validRefs.length === 0) {
            this.showAlert('Eklenecek geçerli referans yok', 'warning');
            return;
        }

        validRefs.forEach((ref, index) => {
            const originalIndex = this.parsedReferences.indexOf(ref);
            this.addParsedReference(originalIndex);
        });

        this.showAlert(`${validRefs.length} referans eklendi`, 'success');
    }

    /**
     * Toplu referans sayacını güncelle
     */
    updateBulkCount() {
        const textarea = document.getElementById('bulkReferences');
        const counter = document.getElementById('bulkReferenceCount');

        if (!textarea || !counter) return;

        const lines = textarea.value.split('\n').filter(line => line.trim().length > 0);
        counter.textContent = lines.length;
    }

    /**
     * Textarea otomatik yükseklik ayarlama
     */
    autoResize(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
    }

    /**
     * Loading state göster/gizle
     */
    showLoading(show) {
        // Loading overlay veya spinner eklenebilir
        const parseButton = document.querySelector('#bulkReferenceUI button');
        if (parseButton) {
            parseButton.disabled = show;
            if (show) {
                parseButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>İşleniyor...';
            } else {
                parseButton.innerHTML = '<i class="fas fa-check me-1"></i>Referansları İşle';
            }
        }
    }

    /**
     * Alert göster
     */
    showAlert(message, type = 'info', duration = 3000) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);

        setTimeout(() => {
            alertDiv.remove();
        }, duration);
    }

    /**
     * HTML escape (XSS koruması)
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Global instance
let referenceManager = null;

/**
 * ReferenceManager'ı başlat
 */
function initReferenceManager(options = {}) {
    referenceManager = new ReferenceManager(options);
    referenceManager.init();
    return referenceManager;
}

// Toplu parse fonksiyonu (global scope için)
function parseBulkReferences() {
    if (referenceManager) {
        referenceManager.parseBulkReferences();
    }
}

// Manuel başlatma için bir fonksiyon ekle
function addReferenceFromText(text) {
    // Bu fonksiyon create-wizard.js içinde tanımlanmalı
    // veya global bir fonksiyon olarak referans eklemelidir
    console.log('Adding reference:', text);

    // Varsayılan davranış: tek tek ekleme moduna geç ve textarea'ya ekle
    // Bu, mevcut sisteminize göre özelleştirilmelidir
    if (typeof addNewReference === 'function') {
        addNewReference();
        // Son eklenen referans alanını bul ve doldur
        const containers = document.querySelectorAll('[id^="referenceContainer-"]');
        if (containers.length > 0) {
            const lastContainer = containers[containers.length - 1];
            const textarea = lastContainer.querySelector('textarea');
            if (textarea) {
                textarea.value = text;
            }
        }
    }
}
