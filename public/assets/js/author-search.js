/**
 * Yazar Arama Modülü
 *
 * Özellikler:
 * - Email ile yazar arama
 * - ORCID ile yazar arama
 * - Debounce ile performans optimizasyonu
 * - Otomatik form doldurma
 * - Sonuçları gösterme
 */
class AuthorSearch {
    constructor(options = {}) {
        this.apiBaseUrl = options.apiBaseUrl || '/api/authors';
        this.emailInput = options.emailInput;
        this.orcidInput = options.orcidInput;
        this.emailResultContainer = options.emailResultContainer;
        this.orcidResultContainer = options.orcidResultContainer;
        this.onSelect = options.onSelect || null;
        this.debounceTime = options.debounceTime || 500;
        this.minSearchLength = options.minSearchLength || 3;

        // Loading state
        this.isLoading = false;
    }

    /**
     * Modülü başlat
     */
    init() {
        if (this.emailInput) {
            this.initEmailSearch();
        }

        if (this.orcidInput) {
            this.initOrcidSearch();
        }
    }

    /**
     * Email arama input'unu başlat
     */
    initEmailSearch() {
        this.emailInput.addEventListener('input',
            this.debounce(() => this.searchByEmail(), this.debounceTime)
        );

        // Blur event'inde sonuçları gizle (gecikme ile)
        this.emailInput.addEventListener('blur', () => {
            setTimeout(() => {
                if (this.emailResultContainer) {
                    this.emailResultContainer.style.display = 'none';
                }
            }, 300);
        });

        // Focus event'inde sonuçları tekrar göster
        this.emailInput.addEventListener('focus', () => {
            if (this.emailResultContainer && this.emailResultContainer.innerHTML.trim() !== '') {
                this.emailResultContainer.style.display = 'block';
            }
        });
    }

    /**
     * ORCID arama input'unu başlat
     */
    initOrcidSearch() {
        this.orcidInput.addEventListener('input',
            this.debounce(() => this.searchByOrcid(), this.debounceTime)
        );

        // ORCID formatı için otomatik tire ekleme
        this.orcidInput.addEventListener('input', (e) => {
            this.formatOrcidInput(e);
        });

        // Blur event'inde sonuçları gizle
        this.orcidInput.addEventListener('blur', () => {
            setTimeout(() => {
                if (this.orcidResultContainer) {
                    this.orcidResultContainer.style.display = 'none';
                }
            }, 300);
        });

        // Focus event'inde sonuçları tekrar göster
        this.orcidInput.addEventListener('focus', () => {
            if (this.orcidResultContainer && this.orcidResultContainer.innerHTML.trim() !== '') {
                this.orcidResultContainer.style.display = 'block';
            }
        });
    }

    /**
     * Email ile yazar ara
     */
    async searchByEmail() {
        const email = this.emailInput.value.trim();

        // Minimum uzunluk kontrolü
        if (email.length < this.minSearchLength) {
            this.clearResults(this.emailResultContainer);
            return;
        }

        // Basit email formatı kontrolü
        if (!this.isValidEmail(email)) {
            this.clearResults(this.emailResultContainer);
            return;
        }

        this.showLoading(this.emailResultContainer);

        try {
            const response = await fetch(`${this.apiBaseUrl}/search-by-email?email=${encodeURIComponent(email)}`);
            const data = await response.json();

            if (data.success) {
                this.displayEmailResults(data);
            } else {
                this.showError(this.emailResultContainer, data.message || 'Arama sırasında hata oluştu');
            }
        } catch (error) {
            console.error('Email search error:', error);
            this.showError(this.emailResultContainer, 'Bağlantı hatası oluştu');
        }
    }

    /**
     * ORCID ile yazar ara
     */
    async searchByOrcid() {
        const orcid = this.orcidInput.value.trim();

        // Minimum uzunluk kontrolü
        if (orcid.length < 10) {
            this.clearResults(this.orcidResultContainer);
            return;
        }

        // ORCID formatı kontrolü
        if (!this.validateOrcid(orcid)) {
            // Henüz tam yazmamış olabilir, hata gösterme
            return;
        }

        this.showLoading(this.orcidResultContainer);

        try {
            const response = await fetch(`${this.apiBaseUrl}/search-by-orcid?orcid=${encodeURIComponent(orcid)}`);
            const data = await response.json();

            if (data.success) {
                this.displayOrcidResults(data);
            } else {
                this.showError(this.orcidResultContainer, data.message || 'Arama sırasında hata oluştu');
            }
        } catch (error) {
            console.error('ORCID search error:', error);
            this.showError(this.orcidResultContainer, 'Bağlantı hatası oluştu');
        }
    }

    /**
     * Email arama sonuçlarını göster
     */
    displayEmailResults(data) {
        if (!this.emailResultContainer) return;

        if (data.found) {
            const author = data.author;
            const source = data.source === 'internal' ? 'Sistemde kayıtlı' : 'Harici kaynak';

            this.emailResultContainer.innerHTML = `
                <div class="author-search-result found">
                    <div class="result-header">
                        <span class="badge badge-success">${source}</span>
                    </div>
                    <div class="result-body">
                        <strong>${this.escapeHtml(author.name || 'İsimsiz')}</strong>
                        <p class="text-muted mb-1">
                            ${author.title ? this.escapeHtml(author.title) + ' - ' : ''}
                            ${this.escapeHtml(author.institution || 'Kurum belirtilmemiş')}
                        </p>
                        ${author.department ? `<p class="text-muted mb-1">${this.escapeHtml(author.department)}</p>` : ''}
                        ${author.country ? `<p class="text-muted mb-1">${this.escapeHtml(author.country)}</p>` : ''}
                        ${author.orcid ? `<p class="text-muted mb-1">ORCID: ${this.escapeHtml(author.orcid)}</p>` : ''}
                    </div>
                    <div class="result-footer">
                        <button type="button" class="btn btn-sm btn-primary" onclick="authorSearch.fillForm(${this.escapeHtml(JSON.stringify(author))})">
                            <i class="fas fa-check"></i> Bu Yazarı Kullan
                        </button>
                    </div>
                </div>
            `;
            this.emailResultContainer.style.display = 'block';
        } else {
            this.emailResultContainer.innerHTML = `
                <div class="author-search-result not-found">
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle"></i> Yazar bulunamadı
                    </p>
                </div>
            `;
            this.emailResultContainer.style.display = 'block';
        }
    }

    /**
     * ORCID arama sonuçlarını göster
     */
    displayOrcidResults(data) {
        if (!this.orcidResultContainer) return;

        if (data.found) {
            const author = data.author;
            const source = data.source === 'internal' ? 'Sistemde kayıtlı' : 'ORCID API';

            this.orcidResultContainer.innerHTML = `
                <div class="author-search-result found">
                    <div class="result-header">
                        <span class="badge ${data.source === 'internal' ? 'badge-success' : 'badge-info'}">${source}</span>
                    </div>
                    <div class="result-body">
                        <strong>${this.escapeHtml(author.name || 'İsimsiz')}</strong>
                        <p class="text-muted mb-1">
                            ${author.title ? this.escapeHtml(author.title) + ' - ' : ''}
                            ${this.escapeHtml(author.institution || 'Kurum belirtilmemiş')}
                        </p>
                        ${author.department ? `<p class="text-muted mb-1">${this.escapeHtml(author.department)}</p>` : ''}
                        ${author.country ? `<p class="text-muted mb-1">${this.escapeHtml(author.country)}</p>` : ''}
                        ${author.email ? `<p class="text-muted mb-1">Email: ${this.escapeHtml(author.email)}</p>` : ''}
                        <p class="text-muted mb-1">ORCID: ${this.escapeHtml(author.orcid)}</p>
                    </div>
                    <div class="result-footer">
                        <button type="button" class="btn btn-sm btn-primary" onclick="authorSearch.fillForm(${this.escapeHtml(JSON.stringify(author))})">
                            <i class="fas fa-check"></i> Bu Yazarı Kullan
                        </button>
                    </div>
                </div>
            `;
            this.orcidResultContainer.style.display = 'block';
        } else {
            this.orcidResultContainer.innerHTML = `
                <div class="author-search-result not-found">
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle"></i> ORCID bulunamadı
                    </p>
                </div>
            `;
            this.orcidResultContainer.style.display = 'block';
        }
    }

    /**
     * Formu yazar bilgileriyle doldur
     */
    fillForm(author) {
        if (this.onSelect) {
            this.onSelect(author);
        }

        // Sonuçları gizle
        this.clearResults(this.emailResultContainer);
        this.clearResults(this.orcidResultContainer);
    }

    /**
     * Loading göster
     */
    showLoading(container) {
        if (!container) return;

        container.innerHTML = `
            <div class="author-search-result loading">
                <p class="text-muted mb-0">
                    <i class="fas fa-spinner fa-spin"></i> Aranıyor...
                </p>
            </div>
        `;
        container.style.display = 'block';
    }

    /**
     * Hata mesajı göster
     */
    showError(container, message) {
        if (!container) return;

        container.innerHTML = `
            <div class="author-search-result error">
                <p class="text-danger mb-0">
                    <i class="fas fa-exclamation-circle"></i> ${this.escapeHtml(message)}
                </p>
            </div>
        `;
        container.style.display = 'block';
    }

    /**
     * Sonuçları temizle
     */
    clearResults(container) {
        if (container) {
            container.innerHTML = '';
            container.style.display = 'none';
        }
    }

    /**
     * Email formatı kontrolü (basit)
     */
    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    /**
     * ORCID formatı kontrolü
     * Format: 0000-0001-2345-6789
     */
    validateOrcid(orcid) {
        return /^\d{4}-\d{4}-\d{4}-\d{3}[0-9X]$/.test(orcid);
    }

    /**
     * ORCID input formatla (otomatik tire ekleme)
     */
    formatOrcidInput(event) {
        let value = event.target.value.replace(/[^\dX]/gi, ''); // Sadece rakam ve X
        let formatted = '';

        for (let i = 0; i < value.length && i < 16; i++) {
            if (i > 0 && i % 4 === 0) {
                formatted += '-';
            }
            formatted += value[i];
        }

        event.target.value = formatted;
    }

    /**
     * HTML escape (XSS koruması)
     */
    escapeHtml(text) {
        if (typeof text !== 'string') {
            text = JSON.stringify(text);
        }

        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Debounce fonksiyonu
     * Performans için gereksiz API çağrılarını engeller
     */
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}

// Global instance (opsiyonel)
let authorSearch = null;

/**
 * AuthorSearch'i başlat
 *
 * @param {Object} options
 * @returns {AuthorSearch}
 */
function initAuthorSearch(options) {
    authorSearch = new AuthorSearch(options);
    authorSearch.init();
    return authorSearch;
}
