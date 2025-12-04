let currentStep = 0;
const totalSteps = 13;
const completedSteps = [];
let referenceCount = 0;

// Referans kuralları
const REFERENCE_RULES = {
    minReferences: 1,
    maxReferences: 50,
    minLength: 20,
    maxLength: 1000
};

// Sayfa yüklendiğinde çalıştır
document.addEventListener('DOMContentLoaded', function() {
    initializeFieldValidation();
    initializeMethodSelection();
    initializeAutoResize();
    initializeBulkReferenceCounter();
    
    // İlk referansı ekle
    addNewReference();
});

// GENEL DOĞRULAMA SİSTEMİ
function initializeFieldValidation() {
    const requiredFields = document.querySelectorAll('input[required], select[required], textarea[required]');
    
    requiredFields.forEach(field => {
        validateField(field);
        
        field.addEventListener('change', function() {
            validateField(this);
        });
        
        if (field.tagName === 'INPUT' || field.tagName === 'TEXTAREA') {
            field.addEventListener('input', function() {
                validateField(this);
            });
        }
    });
}

function validateField(field) {
    const isValid = checkFieldValidity(field);
    
    if (isValid) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
    } else {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
    }
    
    return isValid;
}

function checkFieldValidity(field) {
    if (field.type === 'checkbox') return field.checked;
    if (field.type === 'select-one') return field.value !== '';
    
    // Özel doğrulamalar
    if (field.id === 'titleTR' || field.id === 'titleEN') {
        return field.value.length >= 10 && field.value.length <= 500;
    }
    
    if (field.id === 'abstractTR' || field.id === 'abstractEN') {
        const wordCount = countWords(field.value);
        return wordCount >= 150 && wordCount <= 250;
    }
    
    if (field.id === 'keywordsTR' || field.id === 'keywordsEN') {
        const keywordCount = countKeywords(field.value);
        return keywordCount >= 3 && keywordCount <= 5;
    }
    
    return field.value.trim() !== '';
}

// STEP NAVIGATION
document.getElementById('nextBtn').addEventListener('click', async function() {
    if (await validateStep(currentStep)) {
        markStepCompleted(currentStep);
        showStep(currentStep + 1);
    }
});

document.getElementById('prevBtn').addEventListener('click', function() {
    showStep(currentStep - 1);
});

document.querySelectorAll('.step-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const step = parseInt(this.dataset.step);
        if (step <= currentStep || completedSteps.includes(step - 1)) {
            showStep(step);
        }
    });
});

function showStep(step) {
    document.getElementById('step' + currentStep).classList.add('d-none');
    document.querySelector('[data-step="' + currentStep + '"]').classList.remove('active');

    currentStep = step;
    document.getElementById('step' + currentStep).classList.remove('d-none');
    document.querySelector('[data-step="' + currentStep + '"]').classList.add('active');

    // Hidden input'u güncelle
    const currentStepInput = document.getElementById('current_step');
    if (currentStepInput) {
        currentStepInput.value = currentStep;
    }

    document.getElementById('prevBtn').disabled = currentStep === 0;
    document.getElementById('nextBtn').style.display = currentStep === totalSteps - 1 ? 'none' : 'block';

    const progress = ((currentStep + 1) / totalSteps) * 100;
    document.getElementById('progressBar').style.width = progress + '%';
    document.getElementById('progressText').textContent = Math.round(progress) + '%';

    window.scrollTo({ top: 0, behavior: 'smooth' });

    if (currentStep === totalSteps - 1) {
        updateSummary();
    }
}

async function validateStep(step) {
    const stepEl = document.getElementById('step' + step);
    const inputs = stepEl.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!checkFieldValidity(input)) {
            isValid = false;
        }
    });

    // Step 6: Referans validasyonu
    if (step === 6) {
        if (!validateReferences()) {
            isValid = false;
        }
    }

    // Step 9: Hakem validasyonu
    if (step === 9) {
        const reviewersValid = await validateReviewers();
        if (!reviewersValid) {
            isValid = false;
        }
    }

    return isValid;
}

function validateReferences() {
    const selectedMethod = document.querySelector('input[name="referenceMethod"]:checked').value;
    let references = [];
    
    // Referansları topla
    if (selectedMethod === 'single') {
        const singleRefs = document.querySelectorAll('.reference-input');
        singleRefs.forEach(ref => {
            if (ref.value.trim().length >= 20) {
                references.push(ref.value.trim());
            }
        });
    } else {
        const bulkText = document.getElementById('bulkReferences').value.trim();
        if (bulkText) {
            references = bulkText.split('\n')
                .filter(line => line.trim().length >= 20)
                .map(line => line.trim());
        }
    }
    
    // Minimum referans sayısı kontrolü
    if (references.length < REFERENCE_RULES.minReferences) {
        showReferenceError(`En az ${REFERENCE_RULES.minReferences} referans eklemelisiniz.`);
        return false;
    }
    
    hideReferenceError();
    return true;
}

function showReferenceError(message) {
    let errorContainer = document.getElementById('referenceErrors');
    
    if (!errorContainer) {
        errorContainer = document.createElement('div');
        errorContainer.id = 'referenceErrors';
        const step6 = document.getElementById('step6');
        const firstCard = step6.querySelector('.card');
        step6.insertBefore(errorContainer, firstCard);
    }

    errorContainer.innerHTML = `
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>${message}
        </div>
    `;
}

function hideReferenceError() {
    const errorContainer = document.getElementById('referenceErrors');
    if (errorContainer) {
        errorContainer.innerHTML = '';
    }
}

function markStepCompleted(step) {
    if (!completedSteps.includes(step)) {
        completedSteps.push(step);
        document.querySelector('[data-step="' + step + '"]').classList.add('completed');
    }
}

function countWords(text) {
    return text.trim().split(/\s+/).filter(word => word.length > 0).length;
}

function countKeywords(text) {
    return text.split(',').filter(k => k.trim().length > 0).length;
}

function updateSummary() {
    const typeSelect = document.getElementById('articleType');
    document.getElementById('summaryType').textContent = typeSelect.options[typeSelect.selectedIndex]?.text || '-';
    document.getElementById('summarySubject').textContent = document.getElementById('articleSubject').value || '-';
    document.getElementById('summaryTitleTR').textContent = document.getElementById('titleTR').value || '-';
    document.getElementById('summaryTitleEN').textContent = document.getElementById('titleEN').value || '-';
    document.getElementById('summaryKeywordsTR').textContent = document.getElementById('keywordsTR').value || '-';
    document.getElementById('summaryKeywordsEN').textContent = document.getElementById('keywordsEN').value || '-';

    // Referansları topla
    let references = [];
    const selectedMethod = document.querySelector('input[name="referenceMethod"]:checked').value;
    
    if (selectedMethod === 'single') {
        const singleRefs = document.querySelectorAll('.reference-input');
        singleRefs.forEach(ref => {
            if (ref.value.trim()) {
                references.push(ref.value.trim());
            }
        });
    } else {
        const bulkText = document.getElementById('bulkReferences').value.trim();
        if (bulkText) {
            references = bulkText.split('\n').filter(line => line.trim().length > 0);
        }
    }

    // Özeti güncelle
    const summaryList = document.getElementById('summaryReferences');
    summaryList.innerHTML = '';

    if (references.length === 0) {
        summaryList.innerHTML = '<li>-</li>';
    } else {
        references.forEach((ref, index) => {
            const li = document.createElement('li');
            li.textContent = ref;
            summaryList.appendChild(li);
        });
    }
}

// Character counters
document.getElementById('titleTR').addEventListener('input', function() {
    document.getElementById('charCountTR').textContent = this.value.length;
});

document.getElementById('titleEN').addEventListener('input', function() {
    document.getElementById('charCountEN').textContent = this.value.length;
});

// Word counters
document.getElementById('abstractTR').addEventListener('input', function() {
    document.getElementById('wordCountTR').textContent = countWords(this.value);
});

document.getElementById('abstractEN').addEventListener('input', function() {
    document.getElementById('wordCountEN').textContent = countWords(this.value);
});

// Keyword counters
document.getElementById('keywordsTR').addEventListener('input', function() {
    document.getElementById('keywordCountTR').textContent = countKeywords(this.value);
});

document.getElementById('keywordsEN').addEventListener('input', function() {
    document.getElementById('keywordCountEN').textContent = countKeywords(this.value);
});

// REFERANS YÖNETİMİ
function initializeMethodSelection() {
    const methodOptions = document.querySelectorAll('.method-option');
    const methodRadios = document.querySelectorAll('input[name="referenceMethod"]');
    
    methodOptions.forEach(option => {
        option.addEventListener('click', function() {
            const method = this.dataset.method;
            document.getElementById(`method${method.charAt(0).toUpperCase() + method.slice(1)}`).checked = true;
            showReferenceMethod(method);
        });
    });
    
    methodRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            showReferenceMethod(this.value);
        });
    });
}

function showReferenceMethod(method) {
    document.querySelectorAll('.method-option').forEach(option => {
        option.classList.remove('border-primary');
        option.classList.add('border-light');
    });
    
    document.querySelector(`.method-option[data-method="${method}"]`).classList.add('border-primary');
    document.querySelector(`.method-option[data-method="${method}"]`).classList.remove('border-light');
    
    if (method === 'single') {
        document.getElementById('singleReferenceUI').classList.remove('d-none');
        document.getElementById('bulkReferenceUI').classList.add('d-none');
    } else {
        document.getElementById('singleReferenceUI').classList.add('d-none');
        document.getElementById('bulkReferenceUI').classList.remove('d-none');
    }
    
    // Validasyonu tetikle
    setTimeout(() => validateReferences(), 100);
}

function initializeAutoResize() {
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('auto-resize') || e.target.classList.contains('reference-input')) {
            autoResizeTextarea(e.target);
        }
    });
    
    document.querySelectorAll('.auto-resize, .reference-input').forEach(textarea => {
        autoResizeTextarea(textarea);
    });
}

function autoResizeTextarea(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = (textarea.scrollHeight) + 'px';
}

function initializeBulkReferenceCounter() {
    const bulkTextarea = document.getElementById('bulkReferences');
    if (bulkTextarea) {
        bulkTextarea.addEventListener('input', function() {
            const lines = this.value.split('\n').filter(line => line.trim().length > 0);
            document.getElementById('bulkReferenceCount').textContent = lines.length;
            
            if (lines.length > REFERENCE_RULES.maxReferences) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
            
            setTimeout(() => validateReferences(), 100);
        });
    }
}

function addNewReference() {
    const currentReferences = document.querySelectorAll('.reference-input');
    if (currentReferences.length >= REFERENCE_RULES.maxReferences) {
        alert(`En fazla ${REFERENCE_RULES.maxReferences} referans ekleyebilirsiniz.`);
        return;
    }

    referenceCount++;
    const container = document.getElementById('referencesContainer');

    const newReferenceDiv = document.createElement('div');
    newReferenceDiv.className = 'reference-item mb-3';
    newReferenceDiv.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label mb-0">Referans #${referenceCount}</label>
            <small class="text-muted"><span class="char-count">0</span> karakter</small>
        </div>
        <div class="input-group">
            <textarea class="form-control reference-input auto-resize" rows="1" name="referanslar[]"
                    placeholder="Smith, J. (2023). Yapay Zeka ve Eğitim. Journal of Educational Technology, 15(2), 45-67."></textarea>
            <button type="button" class="btn btn-outline-danger" onclick="removeReference(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;

    container.appendChild(newReferenceDiv);
    
    const newInput = newReferenceDiv.querySelector('.reference-input');
    const charCount = newReferenceDiv.querySelector('.char-count');
    
    newInput.addEventListener('input', function() {
        charCount.textContent = this.value.length;
        autoResizeTextarea(this);
        validateReferenceField(this);
        updateSingleReferenceCounter();
        setTimeout(() => validateReferences(), 100);
    });
    
    updateReferenceNumbers();
    updateSingleReferenceCounter();
}

function validateReferenceField(field) {
    const value = field.value.trim();
    
    if (value === '') {
        field.classList.remove('is-valid', 'is-invalid');
        return false;
    }
    
    if (value.length < 20) {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
        return false;
    }
    
    field.classList.remove('is-invalid');
    field.classList.add('is-valid');
    return true;
}

function removeReference(button) {
    const referenceItem = button.closest('.reference-item');
    referenceItem.remove();
    updateReferenceNumbers();
    updateSingleReferenceCounter();
    hideReferenceError();
    setTimeout(() => validateReferences(), 100);
}

function updateReferenceNumbers() {
    const items = document.querySelectorAll('.reference-item');
    referenceCount = items.length;

    items.forEach((item, index) => {
        const label = item.querySelector('label');
        label.textContent = `Referans #${index + 1}`;

        const deleteBtn = item.querySelector('.btn-outline-danger');
        if (items.length === 1) {
            deleteBtn.disabled = true;
        } else {
            deleteBtn.disabled = false;
        }
    });
}

function updateSingleReferenceCounter() {
    const references = document.querySelectorAll('.reference-input');
    const validReferences = Array.from(references).filter(ref => ref.value.trim().length >= 20).length;
    document.getElementById('singleReferenceCount').textContent = validReferences;
}

// ============================================
// HAKEM VALİDASYONU (Step 9)
// ============================================

/**
 * Hakem sayısını kontrol et (en az 3 hakem)
 */
async function validateReviewers() {
    const minReviewers = 3;

    // reviewerManager global değişkenini kontrol et
    if (typeof reviewerManager === 'undefined') {
        showReviewerError('Hakem sistemi yüklenmedi. Lütfen sayfayı yenileyin.');
        return false;
    }

    try {
        // API'den hakem sayısını kontrol et
        const isValid = await reviewerManager.validate();
        const count = reviewerManager.getReviewerCount();

        if (!isValid) {
            showReviewerError(`En az ${minReviewers} hakem önermeniz gerekiyor. Şu anda ${count} hakem eklediniz.`);
            return false;
        }

        hideReviewerError();
        return true;
    } catch (error) {
        console.error('Hakem validasyon hatası:', error);
        showReviewerError('Hakem kontrolü yapılırken bir hata oluştu.');
        return false;
    }
}

/**
 * Hakem hatası göster
 */
function showReviewerError(message) {
    let errorContainer = document.getElementById('reviewerErrors');

    if (!errorContainer) {
        errorContainer = document.createElement('div');
        errorContainer.id = 'reviewerErrors';
        const step9 = document.getElementById('step9');
        const firstCard = step9.querySelector('.alert');
        step9.insertBefore(errorContainer, firstCard);
    }

    errorContainer.innerHTML = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Hata!</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    // Otomatik scroll
    errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

/**
 * Hakem hatasını gizle
 */
function hideReviewerError() {
    const errorContainer = document.getElementById('reviewerErrors');
    if (errorContainer) {
        errorContainer.innerHTML = '';
    }
}