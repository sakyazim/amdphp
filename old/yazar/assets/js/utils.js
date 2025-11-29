/**
 * AMDS - Ortak Yardımcı İşlevler
 * Bu dosya tüm sayfalarda kullanılan ortak işlevleri içerir
 */

// ==============================================
// 1. BİLDİRİM İŞLEVLERİ
// ==============================================

/**
 * Bildirim gösterme işlevi
 * @param {string} message - Bildirim metni
 * @param {string} type - Bildirim türü: 'success', 'danger', 'warning', 'info'
 * @param {number} duration - Bildirimin görüntülenme süresi (ms)
 */
function showNotification(message, type = 'info', duration = 5000) {
    // Önceki bildirimler için konteyner kontrolü
    let alertContainer = document.getElementById('alertContainer');
    
    if (!alertContainer) {
        // Sayfa içinde bildirim konteynerı yoksa sabit bildirim kullan
        // Önceki bildirimleri temizle
        const existingNotifications = document.querySelectorAll('.notification-popup');
        existingNotifications.forEach(notification => {
            notification.remove();
        });
        
        // Yeni bildirim oluştur
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} notification-popup`;
        notification.innerHTML = `
            <i class="bi ${type === 'success' ? 'bi-check-circle' : type === 'danger' ? 'bi-exclamation-triangle' : 'bi-info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close ms-3" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Bildirimi sayfaya ekle
        document.body.appendChild(notification);
        
        // Bildirimi göster (animasyon için)
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        // Bildirimi otomatik kapat
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 500);
        }, duration);
    } else {
        // Sayfa içinde bildirim konteynerı varsa inline bildirim kullan
        // Önceki bildirimle aynıysa, tekrar oluşturma
        const existingAlerts = alertContainer.querySelectorAll('.notification');
        for (let alert of existingAlerts) {
            if (alert.querySelector('.notification-content').textContent === message) {
                // Animasyonu tekrarla
                alert.classList.remove('fade-in');
                void alert.offsetWidth; // Reflow
                alert.classList.add('fade-in');
                return;
            }
        }
        
        // Yeni bildirim oluştur
        const notification = document.createElement('div');
        notification.className = `notification notification-${type} fade-in`;
        
        // İkon seç
        let icon = '';
        switch (type) {
            case 'success': icon = 'fa-check-circle'; break;
            case 'danger': icon = 'fa-exclamation-circle'; break;
            case 'warning': icon = 'fa-exclamation-triangle'; break;
            default: icon = 'fa-info-circle';
        }
        
        notification.innerHTML = `
            <span class="notification-icon"><i class="fas ${icon}"></i></span>
            <div class="notification-content">${message}</div>
            <span class="notification-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></span>
        `;
        
        alertContainer.appendChild(notification);
        
        // Otomatik kapat
        setTimeout(() => {
            notification.classList.remove('fade-in');
            notification.classList.add('fade-out');
            
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, duration);
    }
}

// ==============================================
// 2. FORM DOĞRULAMA İŞLEVLERİ
// ==============================================

/**
 * Form alanı doğrulama
 * @param {HTMLElement} field - Doğrulanacak form alanı
 * @param {Object} rules - Doğrulama kuralları
 * @param {boolean} showErrors - Hata mesajlarını göster
 * @returns {boolean} Doğrulama sonucu
 */
function validateField(field, rules, showErrors = true) {
    if (!field) return false;
    
    let isValid = true;
    let errorMessage = '';
    
    // Zorunlu alan kontrolü
    if (rules.required && !field.value.trim()) {
        isValid = false;
        errorMessage = 'Bu alan zorunludur';
    }
    // Minimum uzunluk kontrolü
    else if (rules.minLength && field.value.length < rules.minLength) {
        isValid = false;
        errorMessage = `En az ${rules.minLength} karakter gerekli`;
    }
    // Maksimum uzunluk kontrolü
    else if (rules.maxLength && field.value.length > rules.maxLength) {
        isValid = false;
        errorMessage = `En fazla ${rules.maxLength} karakter girilebilir`;
    }
    // Minimum kelime sayısı kontrolü
    else if (rules.minWords && countWords(field.value) < rules.minWords) {
        isValid = false;
        errorMessage = `En az ${rules.minWords} kelime gerekli`;
    }
    // Maksimum kelime sayısı kontrolü
    else if (rules.maxWords && countWords(field.value) > rules.maxWords) {
        isValid = false;
        errorMessage = `En fazla ${rules.maxWords} kelime girilebilir`;
    }
    // Minimum anahtar kelime sayısı kontrolü
    else if (rules.minKeywords && countKeywords(field.value) < rules.minKeywords) {
        isValid = false;
        errorMessage = `En az ${rules.minKeywords} anahtar kelime gerekli`;
    }
    // Maksimum anahtar kelime sayısı kontrolü
    else if (rules.maxKeywords && countKeywords(field.value) > rules.maxKeywords) {
        isValid = false;
        errorMessage = `En fazla ${rules.maxKeywords} anahtar kelime girilebilir`;
    }
    // E-posta formatı kontrolü
    else if (rules.email && !validateEmail(field.value)) {
        isValid = false;
        errorMessage = 'Geçerli bir e-posta adresi girin';
    }
    
    // Doğrulama durumuna göre alan görünümünü güncelle
    if (showErrors) {
        if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            
            // Özel hata mesajını güncelle
            const feedbackElement = field.nextElementSibling;
            if (feedbackElement && feedbackElement.classList.contains('invalid-feedback')) {
                feedbackElement.textContent = errorMessage;
            }
        }
    }
    
    return isValid;
}

/**
 * E-posta doğrulama
 * @param {string} email - Doğrulanacak e-posta
 * @returns {boolean} Doğrulama sonucu
 */
function validateEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

/**
 * Kelime sayısı hesaplama
 * @param {string} text - Kelime sayısı hesaplanacak metin
 * @returns {number} Kelime sayısı
 */
function countWords(text) {
    if (!text) return 0;
    return text.trim().split(/\s+/).filter(word => word.length > 0).length;
}

/**
 * Anahtar kelime sayısı hesaplama
 * @param {string} text - Anahtar kelimeler (virgülle ayrılmış)
 * @returns {number} Anahtar kelime sayısı
 */
function countKeywords(text) {
    if (!text) return 0;
    return text.split(',').filter(keyword => keyword.trim().length > 0).length;
}

// ==============================================
// 3. TARİH İŞLEVLERİ
// ==============================================

/**
 * Tarih formatı
 * @param {Date|string} date - Formatlanacak tarih
 * @returns {string} Formatlanmış tarih
 */
function formatDate(date) {
    return new Date(date).toLocaleDateString('tr-TR', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// ==============================================
// 4. UYGULAMA AYARLARI İŞLEVLERİ
// ==============================================

/**
 * Sidebar toggle
 * Gizle/göster durumunu localstorage'a da kaydeder
 */
function toggleSidebar() {
    document.body.classList.toggle('sidebar-expanded');
    
    // Kullanıcı tercihini kaydet
    const isSidebarExpanded = document.body.classList.contains('sidebar-expanded');
    localStorage.setItem('sidebar-expanded', isSidebarExpanded);
}

/**
 * Mobil menüyü aç/kapat
 */
function toggleMobileMenu() {
    const sidebarContainer = document.querySelector('.sidebar-container');
    if (!sidebarContainer) return;
    
    sidebarContainer.classList.toggle('show');
    
    // Backdrop oluştur veya kaldır
    let backdrop = document.querySelector('.sidebar-backdrop');
    if (!backdrop) {
        backdrop = document.createElement('div');
        backdrop.className = 'sidebar-backdrop';
        document.body.appendChild(backdrop);
        
        // Backdrop'a tıklandığında sidebar'ı kapat
        backdrop.addEventListener('click', function() {
            sidebarContainer.classList.remove('show');
            backdrop.classList.remove('show');
        });
    }
    
    backdrop.classList.toggle('show');
}

// Sayfa yüklendiğinde çalışacak ortak kod
document.addEventListener('DOMContentLoaded', function() {
    // Mobil menü toggle işlevi
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', toggleMobileMenu);
    }
    
    // Sidebar toggle butonu
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }
    
    // Kaydedilmiş sidebar durumunu uygula
    const savedSidebarState = localStorage.getItem('sidebar-expanded');
    if (savedSidebarState === 'true') {
        document.body.classList.add('sidebar-expanded');
    } else if (savedSidebarState === 'false') {
        document.body.classList.remove('sidebar-expanded');
    }
    
    // Bootstrap tooltips'i etkinleştir
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Genişletilebilir satırlar için event listener
    setupExpandableRows();
});

/**
 * Genişletilebilir satırları ayarla
 */
function setupExpandableRows() {
    // Genişletme/daraltma ikonlarını değiştir
    document.querySelectorAll('.expandable-row').forEach(row => {
        row.addEventListener('click', function() {
            const button = this.querySelector('.expand-button i');
            const expanded = this.getAttribute('aria-expanded') === 'true';
            
            if (expanded) {
                button.classList.replace('bi-chevron-up', 'bi-chevron-down');
                this.setAttribute('aria-expanded', 'false');
            } else {
                button.classList.replace('bi-chevron-down', 'bi-chevron-up');
                this.setAttribute('aria-expanded', 'true');
            }
        });
    });
    
    // Sadece genişletme butonuna tıklandığında olayın yayılmasını engelle
    document.querySelectorAll('.expand-button').forEach(button => {
        button.addEventListener('click', function(event) {
            // Butona tıklandığında olayın üst öğelere yayılmasını engelle
            event.stopPropagation();
            
            // Satırı manuel olarak tetikle
            const row = this.closest('.expandable-row');
            const targetId = row.getAttribute('data-bs-target');
            const targetElement = document.querySelector(targetId);
            
            // Bootstrap 5 collapse işlevini manuel olarak çağır
            const bsCollapse = new bootstrap.Collapse(targetElement, {
                toggle: true
            });
            
            // İkon değişimini yap
            const icon = this.querySelector('i');
            const expanded = row.getAttribute('aria-expanded') === 'true';
            
            if (expanded) {
                icon.classList.replace('bi-chevron-up', 'bi-chevron-down');
                row.setAttribute('aria-expanded', 'false');
            } else {
                icon.classList.replace('bi-chevron-down', 'bi-chevron-up');
                row.setAttribute('aria-expanded', 'true');
            }
        });
    });
}