// Form utility functions
const FormUtils = {
    showLoading: (button) => {
        button.disabled = true;
        button.dataset.originalHtml = button.innerHTML;
        button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> İşlem yapılıyor...';
    },

    hideLoading: (button) => {
        button.disabled = false;
        button.innerHTML = button.dataset.originalHtml;
    },

    showError: (container, message) => {
        const alert = document.createElement('div');
        alert.className = 'alert alert-danger alert-dismissible fade show';
        alert.role = 'alert';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        container.insertBefore(alert, container.firstChild);
    }
};

// Add validation icons to form inputs
document.querySelectorAll('.form-floating input').forEach(input => {
    const validationIcon = document.createElement('span');
    validationIcon.className = 'valid-feedback-icon';
    input.parentElement.appendChild(validationIcon);
});

// Real-time validation
document.querySelectorAll('.form-floating input').forEach(input => {
    input.addEventListener('input', function() {
        if (this.checkValidity()) {
            this.classList.add('is-valid');
        } else {
            this.classList.remove('is-valid');
        }
    });
});

// Login form submission
document.getElementById('loginForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!this.checkValidity()) {
        e.stopPropagation();
        this.classList.add('was-validated');
        return;
    }
    
    const submitButton = this.querySelector('button[type="submit"]');
    
    try {
        FormUtils.showLoading(submitButton);
        
        // Simulate API call
        const response = await fetch('/api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('input[name="_csrf"]').value
            },
            body: JSON.stringify({
                email: this.email.value,
                password: this.password.value,
                rememberMe: this.querySelector('#rememberMe').checked
            })
        });
        
        if (!response.ok) {
            throw new Error('Giriş başarısız. Lütfen bilgilerinizi kontrol edin.');
        }
        
        // Redirect on success
        window.location.href = '/dashboard';
    } catch (error) {
        FormUtils.showError(this.closest('.login-box'), error.message);
    } finally {
        FormUtils.hideLoading(submitButton);
    }
});

// Forgot password form submission
document.getElementById('forgotPasswordForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!this.checkValidity()) {
        e.stopPropagation();
        this.classList.add('was-validated');
        return;
    }
    
    const submitButton = this.querySelector('button[type="submit"]');
    
    try {
        FormUtils.showLoading(submitButton);
        
        // Simulate API call
        const response = await fetch('/api/forgot-password', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: this.querySelector('#resetEmail').value
            })
        });
        
        if (!response.ok) {
            throw new Error('İşlem başarısız.');
        }
        
        // Show success message
        FormUtils.showError(this.closest('.modal-body'), 
            'Şifre sıfırlama bağlantısı e-posta adresinize gönderildi.');
        
        // Close modal after delay
        setTimeout(() => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal'));
            modal.hide();
        }, 2000);
    } catch (error) {
        FormUtils.showError(this.closest('.modal-body'), error.message);
    } finally {
        FormUtils.hideLoading(submitButton);
    }
});

// Input icon animations
document.querySelectorAll('.form-floating input').forEach(input => {
    const icon = input.parentElement.querySelector('.input-icon');
    if (!icon) return;
    
    input.addEventListener('focus', () => {
        icon.style.color = '#0d6efd';
        icon.style.transform = 'translateY(-50%) scale(1.1)';
    });

    input.addEventListener('blur', () => {
        if (!input.value) {
            icon.style.color = '#6c757d';
            icon.style.transform = 'translateY(-50%)';
        }
    });
    
    // Check initial state
    if (input.value) {
        icon.style.color = '#0d6efd';
    }
});

// Form reset handling
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('reset', () => {
        setTimeout(() => {
            form.querySelectorAll('.input-icon').forEach(icon => {
                icon.style.color = '#6c757d';
                icon.style.transform = 'translateY(-50%)';
            });
            form.querySelectorAll('input').forEach(input => {
                input.classList.remove('is-valid');
            });
        }, 0);
    });
});