// Form utility functions
const FormUtils = {
    // Loading durumu gösterme
    showLoading: (button) => {
        button.disabled = true;
        button.dataset.originalHtml = button.innerHTML;
        button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> İşlem yapılıyor...';
    },

    // Loading durumunu kaldırma
    hideLoading: (button) => {
        button.disabled = false;
        button.innerHTML = button.dataset.originalHtml;
    },

    // Hata mesajı gösterme
    showError: (container, message) => {
        const alert = document.createElement('div');
        alert.className = 'alert alert-danger alert-dismissible fade show';
        alert.role = 'alert';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        container.insertBefore(alert, container.firstChild);
    },

    // Başarı mesajı gösterme
    showSuccess: (container, message) => {
        const alert = document.createElement('div');
        alert.className = 'alert alert-success alert-dismissible fade show';
        alert.role = 'alert';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        container.insertBefore(alert, container.firstChild);
    }
};
// Form utility functions

// Form validation functions
const FormValidation = {
    // Email doğrulama
    isValidEmail: (email) => {
        const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return pattern.test(email);
    },

    // Telefon numarası doğrulama
    isValidPhone: (phone) => {
        const pattern = /^\d{10}$/;
        return pattern.test(phone);
    },

    // ORCID doğrulama
    isValidORCID: (orcid) => {
        const pattern = /^\d{4}-\d{4}-\d{4}-\d{4}$/;
        return pattern.test(orcid);
    },

    // Şifre doğrulama
    isValidPassword: (password) => {
        const minLength = 8;
        const hasUpperCase = /[A-Z]/.test(password);
        const hasLowerCase = /[a-z]/.test(password);
        const hasNumbers = /\d/.test(password);
        const hasSpecialChar = /[!@#$%^&*]/.test(password);
        
        return password.length >= minLength && 
               hasUpperCase && 
               hasLowerCase && 
               hasNumbers && 
               hasSpecialChar;
    },

    // Form alanı doğrulama
    validateField: (input) => {
        const value = input.value.trim();
        let isValid = true;
        let message = '';

        switch(input.type) {
            case 'email':
                isValid = FormValidation.isValidEmail(value);
                message = 'Geçerli bir e-posta adresi giriniz';
                break;
            case 'tel':
                isValid = FormValidation.isValidPhone(value);
                message = 'Geçerli bir telefon numarası giriniz';
                break;
            case 'password':
                isValid = FormValidation.isValidPassword(value);
                message = 'Şifre kriterleri karşılanmıyor';
                break;
        }

        if (!isValid && value !== '') {
            input.setCustomValidity(message);
        } else {
            input.setCustomValidity('');
        }

        return isValid;
    }
};
// Form validation functions

// Form event handlers
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registrationForm');
    if (!form) return;

    // Form submit handler
    form.addEventListener('submit', async function(event) {
        event.preventDefault();
        
        if (!this.checkValidity()) {
            event.stopPropagation();
            this.classList.add('was-validated');
            return;
        }

        const submitButton = this.querySelector('button[type="submit"]');
        
        try {
            FormUtils.showLoading(submitButton);
            
            // Form verilerini topla
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            // API isteği simülasyonu
            await new Promise(resolve => setTimeout(resolve, 1500));
            
            FormUtils.showSuccess(this, 'Kayıt başarıyla tamamlandı!');
            
            // Başarılı kayıt sonrası yönlendirme
            setTimeout(() => {
                window.location.href = '/login';
            }, 2000);
            
        } catch (error) {
            FormUtils.showError(this, 'Bir hata oluştu. Lütfen tekrar deneyin.');
        } finally {
            FormUtils.hideLoading(submitButton);
        }
    });

    // Input validation handlers
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        // Blur event handler
        input.addEventListener('blur', function() {
            if (this.value.trim()) {
                FormValidation.validateField(this);
                this.closest('form').classList.add('was-validated');
            }
        });

        // Focus event handler
        input.addEventListener('focus', function() {
            this.closest('form').classList.remove('was-validated');
        });

        // Input event handler
        input.addEventListener('input', function() {
            FormValidation.validateField(this);
        });
    });
});
// Form event handlers

// Custom form fields handlers
document.addEventListener('DOMContentLoaded', function() {
    // Şifre görünürlük kontrolü
    setupPasswordVisibility();
    
    // ORCID alanı kontrolü
    setupORCIDField();
    
    // Telefon formatı
    setupPhoneField();
    
    // Uzmanlık alanları
    setupSpecializationField();
});

// Şifre görünürlük kontrolü
function setupPasswordVisibility() {
    const passwordFields = document.querySelectorAll('input[type="password"]');
    
    passwordFields.forEach(field => {
        // Göz ikonu ekleme
        const toggleIcon = document.createElement('i');
        toggleIcon.className = 'fas fa-eye-slash password-toggle';
        toggleIcon.style.cursor = 'pointer';
        toggleIcon.style.position = 'absolute';
        toggleIcon.style.right = '1rem';
        toggleIcon.style.top = '50%';
        toggleIcon.style.transform = 'translateY(-50%)';
        field.parentElement.appendChild(toggleIcon);

        // Tıklama olayı
        toggleIcon.addEventListener('click', () => {
            if (field.type === 'password') {
                field.type = 'text';
                toggleIcon.className = 'fas fa-eye password-toggle';
            } else {
                field.type = 'password';
                toggleIcon.className = 'fas fa-eye-slash password-toggle';
            }
        });
    });
}

// ORCID alanı doğrulama ve formatlama
function setupORCIDField() {
    const orcidInput = document.getElementById('orcid');
    if (!orcidInput) return;

    // Input event handler
    orcidInput.addEventListener('input', function(e) {
        // Sadece rakam ve tire işaretine izin ver
        let value = e.target.value.replace(/[^\d-]/g, '');
        
        // Tire işaretlerini kaldır
        value = value.replace(/-/g, '');
        
        // 16 karakterden fazlasını kes
        if (value.length > 16) {
            value = value.slice(0, 16);
        }
        
        // 4'lü gruplar halinde formatla
        if (value.length > 0) {
            value = value.match(/.{1,4}/g).join('-');
        }
        
        // Input değerini güncelle
        this.value = value;
        
        // Validasyon kontrolü
        if (value.length === 19) { // 16 rakam + 3 tire
            this.setCustomValidity('');
        } else if (value.length === 0) {
            this.setCustomValidity(''); // Boş bırakılabilir
        } else {
            this.setCustomValidity('Geçerli bir ORCID ID giriniz (Format: 0000-0000-0000-0000)');
        }
    });

    // Blur event handler - son kontrol
    orcidInput.addEventListener('blur', function() {
        const value = this.value.trim();
        const isValidFormat = /^\d{4}-\d{4}-\d{4}-\d{4}$/.test(value);
        
        if (value === '') {
            this.setCustomValidity(''); // Boş bırakılabilir
        } else if (!isValidFormat) {
            this.setCustomValidity('Geçerli bir ORCID ID giriniz (Format: 0000-0000-0000-0000)');
        } else {
            this.setCustomValidity('');
        }
    });
}

// DOM yüklendiğinde çalıştır
document.addEventListener('DOMContentLoaded', function() {
    setupORCIDField();
});

// Telefon formatı
function setupPhoneField() {
    const phoneInput = document.getElementById('phone');
    if (!phoneInput) return;

    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        
        if (value.length > 10) {
            value = value.substr(0, 10);
        }
        
        this.value = value;
    });
}

// Uzmanlık alanları
// Global değişken
let selectedSpecializations = new Set();

// Uzmanlık alanı ekleme fonksiyonu
function addSpecialization() {
    const select = document.getElementById('specialization');
    if (!select) return;

    const value = select.value;
    const text = select.options[select.selectedIndex]?.text;

    if (!value || !text) return;
    if (selectedSpecializations.has(value)) {
        alert('Bu alan zaten eklenmiş!');
        return;
    }

    const container = document.getElementById('selectedSpecializations');
    if (!container) return;

    selectedSpecializations.add(value);
    
    // Yeni tag oluştur
    const tag = document.createElement('div');
    tag.className = 'specialization-tag';
    tag.setAttribute('data-type', value);
    
    // Alan için ikon belirleme
    const icons = {
        computer_science: 'fas fa-laptop-code',
        mathematics: 'fas fa-square-root-alt',
        physics: 'fas fa-atom',
        chemistry: 'fas fa-flask',
        biology: 'fas fa-dna',
        literature: 'fas fa-book',
        history: 'fas fa-landmark',
        philosophy: 'fas fa-brain',
        psychology: 'fas fa-head-side-virus',
        sociology: 'fas fa-users',
        economics: 'fas fa-chart-line',
        business: 'fas fa-briefcase',
        law: 'fas fa-balance-scale',
        medicine: 'fas fa-user-md',
        engineering: 'fas fa-cogs'
    };

    const icon = icons[value] || 'fas fa-graduation-cap';
    
    tag.innerHTML = `
        <i class="${icon}"></i>
        <span>${text}</span>
        <button type="button" class="remove-btn" onclick="removeSpecialization('${value}')">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    container.appendChild(tag);
    select.value = ''; // Seçimi sıfırla
}

// Uzmanlık alanı silme fonksiyonu
function removeSpecialization(value) {
    const container = document.getElementById('selectedSpecializations');
    if (!container) return;

    selectedSpecializations.delete(value);
    const tag = container.querySelector(`[data-type="${value}"]`);
    
    if (tag) {
        tag.style.transform = 'translateX(10px)';
        tag.style.opacity = '0';
        
        setTimeout(() => {
            tag.remove();
        }, 300);
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Enter tuşu ile ekleme yapabilme
    const select = document.getElementById('specialization');
    if (select) {
        select.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addSpecialization();
            }
        });

        // Seçim yapıldığında otomatik ekleme (opsiyonel)
        select.addEventListener('change', function() {
            if (this.value) {
                addSpecialization();
            }
        });
    }
});
// Uzmanlık alanları

// Password validation and requirements check
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    if (!passwordInput) return;

    const requirements = {
        length: { regex: /.{8,}/, element: document.getElementById('length-check') },
        uppercase: { regex: /[A-Z]/, element: document.getElementById('uppercase-check') },
        lowercase: { regex: /[a-z]/, element: document.getElementById('lowercase-check') },
        number: { regex: /[0-9]/, element: document.getElementById('number-check') },
        special: { regex: /[!@#$%^&*]/, element: document.getElementById('special-check') }
    };

    // Check password strength on input
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        
        // Check each requirement
        Object.keys(requirements).forEach(requirement => {
            const { regex, element } = requirements[requirement];
            const isValid = regex.test(password);
            
            // Update UI
            if (isValid) {
                element.classList.add('valid');
                element.querySelector('i').className = 'fas fa-check-circle';
            } else {
                element.classList.remove('valid');
                element.querySelector('i').className = 'fas fa-circle';
            }
        });

        // Check overall validity
        const isValid = Object.keys(requirements).every(req => 
            requirements[req].regex.test(password)
        );

        if (isValid) {
            this.setCustomValidity('');
        } else {
            this.setCustomValidity('Lütfen tüm şifre gereksinimlerini karşılayın');
        }
    });
});

// Toggle password visibility
function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    const button = input.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye-slash';
    }
}
// Password validation and requirements check
