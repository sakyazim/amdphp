/**
 * Genişletilebilir Çoklu Dil Yönetim Sistemi
 *
 * Özellikler:
 * - Dinamik dil listesi
 * - Otomatik DOM güncelleme (data-lang-key)
 * - RTL dil desteği
 * - LocalStorage ile dil tercihi
 * - API entegrasyonu
 */
class LanguageHelper {
    constructor(options = {}) {
        this.apiBaseUrl = options.apiBaseUrl || '/api/languages';
        this.currentLang = document.documentElement.lang || 'tr';
        this.availableLanguages = [];
        this.translations = {};
        this.autoDetect = options.autoDetect !== false;
        this.autoApply = options.autoApply !== false;
    }

    /**
     * Başlat
     */
    async init() {
        // Mevcut dilleri yükle
        await this.loadAvailableLanguages();

        // Mevcut dili güncelle
        await this.loadCurrentLanguage();

        // Otomatik DOM güncelleme
        if (this.autoApply) {
            this.applyTranslations();
        }

        // RTL desteği
        this.applyDirection();

        console.log('LanguageHelper initialized:', this.currentLang);
    }

    /**
     * Kullanılabilir dilleri yükle
     */
    async loadAvailableLanguages() {
        try {
            const response = await fetch(`${this.apiBaseUrl}/available`);
            const data = await response.json();

            if (data.success) {
                this.availableLanguages = data.languages;
                this.currentLang = data.current;
            }
        } catch (error) {
            console.error('Failed to load available languages:', error);
        }
    }

    /**
     * Mevcut dili yükle
     */
    async loadCurrentLanguage() {
        try {
            const response = await fetch(`${this.apiBaseUrl}/current`);
            const data = await response.json();

            if (data.success) {
                this.currentLang = data.language.code;
                document.documentElement.lang = this.currentLang;
            }
        } catch (error) {
            console.error('Failed to load current language:', error);
        }
    }

    /**
     * Sayfa çevirilerini yükle
     * @param {string} page - Sayfa adı (create_article, common, vb.)
     */
    async loadPageTranslations(page) {
        try {
            const response = await fetch(`${this.apiBaseUrl}/page?page=${page}`);
            const data = await response.json();

            if (data.success) {
                this.translations = { ...this.translations, ...data.translations };
                return data.translations;
            }
        } catch (error) {
            console.error('Failed to load page translations:', error);
        }

        return {};
    }

    /**
     * Çeviriyi getir
     * @param {string} key - Anahtar (örn: form.title)
     * @param {string} fallback - Bulunamazsa döndürülecek değer
     * @returns {string}
     */
    translate(key, fallback = null) {
        return this.translations[key] || fallback || key;
    }

    /**
     * Kısa alias: t()
     */
    t(key, fallback = null) {
        return this.translate(key, fallback);
    }

    /**
     * DOM'daki tüm [data-lang-key] elemanlarını güncelle
     */
    applyTranslations() {
        document.querySelectorAll('[data-lang-key]').forEach(el => {
            const key = el.getAttribute('data-lang-key');
            const translation = this.translate(key);

            // Element tipine göre güncelle
            if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
                if (el.placeholder !== undefined) {
                    el.placeholder = translation;
                } else {
                    el.value = translation;
                }
            } else {
                el.textContent = translation;
            }
        });
    }

    /**
     * Dil değiştir
     * @param {string} langCode - Dil kodu (tr, en, ja, vb.)
     * @param {boolean} reload - Sayfayı yenile
     */
    async switchLanguage(langCode, reload = true) {
        try {
            const response = await fetch(`${this.apiBaseUrl}/switch`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ language: langCode })
            });

            const data = await response.json();

            if (data.success) {
                this.currentLang = langCode;

                // LocalStorage'a kaydet
                localStorage.setItem('preferred_language', langCode);

                if (reload) {
                    // Sayfayı yenile
                    location.reload();
                } else {
                    // Sadece DOM'u güncelle
                    document.documentElement.lang = langCode;
                    await this.loadPageTranslations(this.getCurrentPage());
                    this.applyTranslations();
                    this.applyDirection();
                }

                return true;
            } else {
                console.error('Failed to switch language:', data.error);
                return false;
            }
        } catch (error) {
            console.error('Failed to switch language:', error);
            return false;
        }
    }

    /**
     * RTL (Right-to-Left) desteğini uygula
     */
    applyDirection() {
        const langInfo = this.availableLanguages.find(l => l.code === this.currentLang);

        if (langInfo && langInfo.direction === 'rtl') {
            document.documentElement.dir = 'rtl';
            document.body.classList.add('rtl');
        } else {
            document.documentElement.dir = 'ltr';
            document.body.classList.remove('rtl');
        }
    }

    /**
     * Mevcut sayfayı tespit et
     */
    getCurrentPage() {
        // URL'den veya data-page attribute'undan
        const pageEl = document.querySelector('[data-page]');
        if (pageEl) {
            return pageEl.getAttribute('data-page');
        }

        // URL path'inden
        const path = window.location.pathname;
        if (path.includes('create')) {
            return 'create_article';
        }

        return 'common';
    }

    /**
     * Dil bilgisini getir
     * @param {string} langCode - Dil kodu
     */
    getLanguageInfo(langCode) {
        return this.availableLanguages.find(l => l.code === langCode);
    }

    /**
     * Bayrak emoji'sini getir
     * @param {string} langCode - Dil kodu
     */
    getFlag(langCode) {
        const langInfo = this.getLanguageInfo(langCode);
        return langInfo?.flag || '🌍';
    }

    /**
     * Dil adını getir (native)
     * @param {string} langCode - Dil kodu
     */
    getLanguageName(langCode) {
        const langInfo = this.getLanguageInfo(langCode);
        return langInfo?.native_name || langCode.toUpperCase();
    }

    /**
     * RTL dil mi kontrol et
     */
    isRTL() {
        const langInfo = this.getLanguageInfo(this.currentLang);
        return langInfo?.direction === 'rtl';
    }
}

/**
 * Dil Seçici UI Bileşeni
 */
class LanguageSwitcher {
    constructor(languageHelper, containerId = 'language-switcher') {
        this.lang = languageHelper;
        this.containerId = containerId;
    }

    /**
     * Render et
     */
    render() {
        const container = document.getElementById(this.containerId);
        if (!container) {
            console.warn('Language switcher container not found:', this.containerId);
            return;
        }

        const currentLang = this.lang.getLanguageInfo(this.lang.currentLang);
        if (!currentLang) return;

        // Dropdown HTML
        container.innerHTML = `
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                        type="button"
                        id="languageDropdownBtn"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false">
                    <i class="fa fa-globe"></i>
                    <span class="lang-flag">${currentLang.flag}</span>
                    <span class="lang-name">${currentLang.native_name}</span>
                </button>
                <div class="dropdown-menu dropdown-menu-right" id="languageDropdownMenu">
                    ${this.renderLanguageItems()}
                </div>
            </div>
        `;

        // Event listeners ekle
        this.attachEventListeners();
    }

    /**
     * Dil öğelerini render et
     */
    renderLanguageItems() {
        return this.lang.availableLanguages.map(lang => {
            const isActive = lang.code === this.lang.currentLang;
            const activeClass = isActive ? 'active' : '';

            return `
                <a class="dropdown-item ${activeClass}" href="#" data-lang="${lang.code}">
                    <span class="lang-flag">${lang.flag}</span>
                    ${lang.native_name}
                    ${isActive ? '<i class="fa fa-check float-right text-success"></i>' : ''}
                </a>
            `;
        }).join('');
    }

    /**
     * Event listeners ekle
     */
    attachEventListeners() {
        const menu = document.getElementById('languageDropdownMenu');
        if (!menu) return;

        menu.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', async (e) => {
                e.preventDefault();
                const langCode = item.getAttribute('data-lang');

                // Zaten seçili ise return
                if (langCode === this.lang.currentLang) {
                    return;
                }

                // Dil değiştir
                await this.lang.switchLanguage(langCode, true);
            });
        });
    }
}

// Global instance (opsiyonel)
window.LanguageHelper = LanguageHelper;
window.LanguageSwitcher = LanguageSwitcher;

// Otomatik başlatma (opsiyonel)
if (typeof autoInitLanguage !== 'undefined' && autoInitLanguage) {
    document.addEventListener('DOMContentLoaded', async () => {
        const languageHelper = new LanguageHelper();
        await languageHelper.init();

        const languageSwitcher = new LanguageSwitcher(languageHelper);
        languageSwitcher.render();

        // Global erişim
        window.lang = languageHelper;
    });
}
