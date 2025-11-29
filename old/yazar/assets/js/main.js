/**
 * AMDS - Main JavaScript File - Optimize edilmiş versiyon
 */

// Rol değiştirme işlevi
function setupRoleSelection() {
    const roleDropdown = document.getElementById('roleDropdown');
    const roleDropdownBtn = document.getElementById('roleDropdownBtn');
    
    if(roleDropdown && roleDropdownBtn) {
        roleDropdown.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Rol değişikliğini uygula
                const icon = this.querySelector('i').cloneNode(true);
                const roleName = this.textContent.trim();
                
                // Düğme içeriğini güncelle
                roleDropdownBtn.innerHTML = '';
                roleDropdownBtn.appendChild(icon);
                
                // Mobil görünümde sadece ikon, masaüstünde metin de göster
                if(window.innerWidth > 768) {
                    roleDropdownBtn.appendChild(document.createTextNode(' ' + roleName));
                } else {
                    // Mobilde tooltip ekle
                    roleDropdownBtn.setAttribute('title', roleName);
                }
                
                // Rol uyarısını güncelle
                const roleAlert = document.querySelector('.role-alert div');
                if(roleAlert) {
                    roleAlert.textContent = roleName + ' olarak giriş yaptınız';
                }
            });
        });
    }
}

// Dil değiştirme işlevi
function setupLanguageSelection() {
    const languageDropdownBtn = document.getElementById('languageDropdownBtn');
    
    if(languageDropdownBtn) {
        document.querySelectorAll('[aria-labelledby="languageDropdownBtn"] .dropdown-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Dil değişikliğini uygula
                const img = this.querySelector('img').cloneNode(true);
                const langCode = img.alt === 'Türkçe' ? 'TR' : 'EN';
                
                // Düğme içeriğini güncelle
                languageDropdownBtn.innerHTML = '';
                languageDropdownBtn.appendChild(img);
                
                // Mobil görünümde sadece ikon, masaüstünde metin de göster
                if(window.innerWidth > 768) {
                    languageDropdownBtn.appendChild(document.createTextNode(' ' + langCode));
                } else {
                    // Mobilde tooltip ekle
                    languageDropdownBtn.setAttribute('title', img.alt);
                }
            });
        });
    }
}

// Bildirim işaretleme işlevi
function setupNotificationMarking() {
    const notificationItems = document.querySelectorAll('.notification-item');
    notificationItems.forEach(item => {
        const markAsReadBtn = item.querySelector('.mark-as-read');
        if (markAsReadBtn) {
            markAsReadBtn.addEventListener('click', function(e) {
                e.stopPropagation(); // Tıklama olayının üst öğelere yayılmasını engelle
                item.classList.remove('unread');
                this.remove(); // Butonu kaldır
            });
        }
    });
}

// Form düzenleme yönetimi
function setupFormEditing() {
    // Tüm düzenle butonlarına olay dinleyicisi ekle
    document.querySelectorAll('[onclick*="toggleEdit"]').forEach(button => {
        button.addEventListener('click', function() {
            const formId = this.getAttribute('onclick').match(/'([^']+)'/)[1];
            toggleEditMode(formId, this);
        });
        // onclick özelliğini kaldır (çift tetiklemeyi önlemek için)
        button.removeAttribute('onclick');
    });
    
    // İptal butonlarına olay dinleyicisi ekle
    document.querySelectorAll('[onclick*="cancelEdit"]').forEach(button => {
        button.addEventListener('click', function() {
            const formId = this.getAttribute('onclick').match(/'([^']+)'/)[1];
            cancelEditMode(formId);
        });
        // onclick özelliğini kaldır
        button.removeAttribute('onclick');
    });
    
    // Şifre göster/gizle butonlarına olay dinleyicisi ekle
    document.querySelectorAll('.password-toggle').forEach(button => {
        button.addEventListener('click', function() {
            togglePasswordVisibility(this);
        });
    });
    
    // Şifre kontrolü için olay dinleyicisi
    const newPasswordInput = document.getElementById('newPassword');
    if (newPasswordInput) {
        newPasswordInput.addEventListener('input', checkPasswordStrength);
    }
    
    // Form gönderimlerini işle
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            const formId = this.id;
            const formData = new FormData(this);
            
            // Form verilerini konsola yazdır (gerçek uygulamada AJAX ile sunucuya gönderilir)
            console.log('Form gönderiliyor: ' + formId);
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }
            
            // Başarılı bildirim göster
            showNotification('Bilgileriniz başarıyla kaydedildi!', 'success');
            
            // Düzenleme modunu kapat
            if (formId !== 'password-form') {
                cancelEditMode(formId);
            } else {
                // Şifre formunu temizle
                this.reset();
            }
        });
    });
}

// Düzenleme modunu etkinleştir
function toggleEditMode(formId, editButton) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    const inputs = form.querySelectorAll('input, select, textarea');
    const buttonsContainer = document.getElementById(formId + '-buttons');
    
    // Form elemanlarını etkinleştir
    inputs.forEach(input => {
        input.disabled = false;
    });
    
    // Düzenle butonunu gizle, kaydet/iptal butonlarını göster
    editButton.style.display = 'none';
    if (buttonsContainer) {
        buttonsContainer.classList.remove('d-none');
    }
    
    // Özel alanları göster (örn. uzmanlık alanı ekleme)
    const specialSelect = form.querySelector('.specialization-select-group');
    if (specialSelect) {
        specialSelect.classList.remove('d-none');
    }
}

// Düzenleme modunu iptal et
function cancelEditMode(formId) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    const inputs = form.querySelectorAll('input, select, textarea');
    const buttonsContainer = document.getElementById(formId + '-buttons');
    const tabPane = form.closest('.tab-pane');
    const editButton = tabPane ? tabPane.querySelector('.btn-outline-primary') : null;
    
    // Form elemanlarını devre dışı bırak
    inputs.forEach(input => {
        input.disabled = true;
    });
    
    // Düzenle butonunu göster, kaydet/iptal butonlarını gizle
    if (editButton) {
        editButton.style.display = 'block';
    }
    
    if (buttonsContainer) {
        buttonsContainer.classList.add('d-none');
    }
    
    // Özel alanları gizle
    const specialSelect = form.querySelector('.specialization-select-group');
    if (specialSelect) {
        specialSelect.classList.add('d-none');
    }
    
    // Formu sıfırla (değişiklikleri geri al)
    form.reset();
}

// Şifre göster/gizle
function togglePasswordVisibility(button) {
    const passwordInput = button.previousElementSibling;
    const icon = button.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    } else {
        passwordInput.type = 'password';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    }
}

// Şifre gücü kontrolü
function checkPasswordStrength() {
    const password = this.value;
    
    // En az 8 karakter
    updateCheckIcon('length-check', password.length >= 8);
    
    // En az 1 büyük harf
    updateCheckIcon('uppercase-check', /[A-Z]/.test(password));
    
    // En az 1 küçük harf
    updateCheckIcon('lowercase-check', /[a-z]/.test(password));
    
    // En az 1 rakam
    updateCheckIcon('number-check', /[0-9]/.test(password));
    
    // En az 1 özel karakter
    updateCheckIcon('special-check', /[^A-Za-z0-9]/.test(password));
}

// Şifre kontrolü ikonlarını güncelle
function updateCheckIcon(elementId, isValid) {
    const element = document.getElementById(elementId);
    if (element) {
        const icon = element.querySelector('i');
        if (icon) {
            icon.className = isValid ? 'bi bi-check-circle-fill text-success' : 'bi bi-circle';
        }
    }
}

// Ana başlangıç fonksiyonu
document.addEventListener('DOMContentLoaded', function() {
    // Rol ve dil seçimi ayarlarını kur
    setupRoleSelection();
    setupLanguageSelection();
    
    // Bildirim işaretleme
    setupNotificationMarking();
    
    // Form düzenleme
    setupFormEditing();
});