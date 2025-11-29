/**
 * Makale Sihirbazı JavaScript - Hata Düzeltmeleri
 * 
 * Bu dosya, makale gönderim sihirbazının tüm işlevselliğini yönetir.
 */

// ==============================================
// 1. GLOBAL DEĞİŞKENLER VE BAŞLANGIÇ AYARLARI
// ==============================================

// Mevcut adımı ve toplam adım sayısını tanımla
let currentStep = 0;
const steps = [
    'Dil Seçimi', 'Ön Bilgi', 'Tür-Konu', 'Başlık', 'Özet', 'Anahtar Kelimeler', 'Referanslar',
    'Yazarlar', 'Dosyalar', 'Hakemler', 'Editöre Not', 'Kontrol Listesi', 'Makaleyi Gönder'
];
const totalSteps = steps.length;

// Global veri depolama değişkenleri
let authors = [];
let reviewers = [];
let uploadedFiles = []; // Yüklenen dosyaları takip etmek için yeni dizi
let lastSavedData = {};
let autoSaveInterval;
const AUTO_SAVE_DELAY = 60000; // 60 saniye (1 dakika)

// Sayfa yüklendiğinde başlangıç ayarlarını yap
document.addEventListener('DOMContentLoaded', function() {
    console.log('Makale sihirbazı başlatılıyor...');
    
    // Hata konteynerini temizle
    clearAlertContainer();
    
    // Event listener'ları ayarla
    setupEventListeners();
    
    // Kaydedilmiş ilerlemeyi yükle
    const loadedSaved = loadSavedProgress();
    
    // Kayıtlı ilerleme yüklenmediyse ilk adımı göster
    if (!loadedSaved) {
        showStep(currentStep);
    }
    
    // Otomatik kaydetmeyi başlat
    startAutoSave();
    
    // Sadece mevcut adımın doğrulama durumunu güncelle, hata mesajları gösterme
    updateStepValidationStatus();
    
    // Örnek veriler ekle (Test için)
    addSampleData();
    
    console.log('Makale sihirbazı başlatıldı.');
});

// Test için örnek veri ekleme
function addSampleData() {
    // Örnek yazar ekle
    if (authors.length === 0) {
        authors = [
            {
                id: "1",
                firstName: "Ahmet",
                middleName: "",
                lastName: "Yılmaz",
                title: "prof",
                phone: "05551234567",
                email1: "ahmet.yilmaz@example.com",
                email2: "",
                department: "Bilgisayar Mühendisliği",
                institution: "İstanbul Üniversitesi",
                country: "TR",
                orcidId: "0000-0001-2345-6789",
                authorOrder: "1",
                authorType: "primary"
            },
            {
                id: "2",
                firstName: "Ayşe",
                middleName: "",
                lastName: "Demir",
                title: "assocprof",
                phone: "05559876543",
                email1: "ayse.demir@example.com",
                email2: "ademir@gmail.com",
                department: "Bilgisayar Mühendisliği",
                institution: "Ankara Üniversitesi",
                country: "TR",
                orcidId: "0000-0002-3456-7890",
                authorOrder: "2",
                authorType: "corresponding"
            }
        ];
        // Yazarlar tablosunu güncelle
        updateAuthorsTable();
    }
    
    // Örnek hakem ekle
    if (reviewers.length === 0) {
        reviewers = [
            {
                id: "1",
                reviewerOrder: "1",
                reviewerType: "main",
                reviewerTitle: "prof",
                reviewerFirstName: "Mehmet",
                reviewerMiddleName: "",
                reviewerLastName: "Öztürk",
                reviewerEmail1: "mehmet.ozturk@example.com",
                reviewerEmail2: "",
                reviewerPhone: "05551234567",
                reviewerDepartment: "Bilgisayar Mühendisliği",
                reviewerInstitution: "İstanbul Teknik Üniversitesi",
                reviewerCountry: "TR",
                reviewerOrcidId: "0000-0001-2345-6789"
            },
            {
                id: "2",
                reviewerOrder: "2",
                reviewerType: "alternate",
                reviewerTitle: "assocprof",
                reviewerFirstName: "Zeynep",
                reviewerMiddleName: "",
                reviewerLastName: "Kaya",
                reviewerEmail1: "zeynep.kaya@example.com",
                reviewerEmail2: "zkaya@gmail.com",
                reviewerPhone: "05559876543",
                reviewerDepartment: "Bilgisayar Mühendisliği",
                reviewerInstitution: "Boğaziçi Üniversitesi",
                reviewerCountry: "TR",
                reviewerOrcidId: "0000-0002-3456-7890"
            }
        ];
        // Hakemler tablosunu güncelle
        updateReviewersTable();
    }
    
    // Örnek dosyalar ekle
    if (uploadedFiles.length === 0) {
        uploadedFiles = [
            {
                id: "1",
                type: "fullText",
                name: "makale_tam_metin.pdf",
                size: "1.2 MB",
                format: "PDF",
                date: "01.03.2025"
            },
            {
                id: "2",
                type: "copyright",
                name: "yayin_hakki_devir.pdf",
                size: "420 KB",
                format: "PDF",
                date: "01.03.2025"
            }
        ];
        // Dosyalar tablosunu güncelle
        updateFilesTable();
    }
}

// Tüm olay dinleyicilerini ayarla
function setupEventListeners() {
    // İleri/Geri butonları
    document.getElementById('nextBtn')?.addEventListener('click', goToNextStep);
    document.getElementById('prevBtn')?.addEventListener('click', goToPrevStep);
    
    // Sol menüdeki adım bağlantıları
    document.querySelectorAll('.step-link').forEach((link, index) => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            tryGoToStep(index);
        });
    });
    
    // Form alanlarına gerçek zamanlı doğrulama ekle
    setupFormValidation();
    
    // Kaydetme butonu
    document.getElementById('saveProgressBtn')?.addEventListener('click', () => {
        saveProgress(false);
    });
    
    // Ön bilgi onay kutusunu dinle
    document.getElementById('acceptInfo')?.addEventListener('change', function() {
        document.getElementById('nextBtn').disabled = !this.checked;
        validateStep(1);
        updateStepValidationStatus();
    });
    
    // Makale formu
    setupFormSpecificListeners();
    
    // Dosya yükleme formu
    document.getElementById('fileUploadForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        addFile();
    });
    
    // Yazar formu
    document.getElementById('authorForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        addAuthor();
    });
    
    // Hakem formu
    document.getElementById('reviewerForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        addReviewer();
    });
    
    // Editör notu kaydetme butonu
    document.getElementById('saveNoteBtn')?.addEventListener('click', saveNote);
    
    // Kontrol listesi butonları
    document.getElementById('checkAllBtn')?.addEventListener('click', checkAllItems);
    document.getElementById('clearAllBtn')?.addEventListener('click', clearAllItems);
    
    // Kontrol listesi checkbox'ları
    document.querySelectorAll('.checklist-container input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', updateChecklistProgress);
    });
    
    // Makale gönderme onay kutusu
    document.getElementById('submitConfirmation')?.addEventListener('change', toggleSubmitButton);
    
    // Gönder butonu
    document.getElementById('finalSubmitButton')?.addEventListener('click', submitArticle);
    
    // Kaydedilmemiş değişikliklerle sayfa ayrılma uyarısı
    window.addEventListener('beforeunload', (e) => {
        if (hasUnsavedChanges()) {
            e.preventDefault();
            e.returnValue = 'Kaydedilmemiş değişiklikleriniz var. Sayfadan ayrılmak istediğinizden emin misiniz?';
            return e.returnValue;
        }
    });
}

// Form alanları için spesifik dinleyiciler
function setupFormSpecificListeners() {
    // Başlık karakter sayıları
    document.getElementById('titleTR')?.addEventListener('input', function() {
        const charCount = this.value.length;
        document.getElementById('charCountTR').textContent = charCount;
        validateField(this, {
            required: true,
            minLength: 5,
            maxLength: 200
        });
    });
    
    document.getElementById('titleEN')?.addEventListener('input', function() {
        const charCount = this.value.length;
        document.getElementById('charCountEN').textContent = charCount;
        validateField(this, {
            required: true,
            minLength: 5,
            maxLength: 200
        });
    });
    
    // Özet kelime sayıları
    document.getElementById('abstractTR')?.addEventListener('input', function() {
        const wordCount = countWords(this.value);
        document.getElementById('wordCountTR').textContent = wordCount;
        validateField(this, {
            required: true,
            minWords: 150,
            maxWords: 250
        });
    });
    
    document.getElementById('abstractEN')?.addEventListener('input', function() {
        const wordCount = countWords(this.value);
        document.getElementById('wordCountEN').textContent = wordCount;
        validateField(this, {
            required: true,
            minWords: 150,
            maxWords: 250
        });
    });
    
    // Anahtar kelime sayıları
    document.getElementById('keywordsTR')?.addEventListener('input', function() {
        const keywordCount = countKeywords(this.value);
        document.getElementById('keywordCountTR').textContent = keywordCount;
        validateField(this, {
            required: true,
            minKeywords: 3,
            maxKeywords: 5
        });
    });
    
    document.getElementById('keywordsEN')?.addEventListener('input', function() {
        const keywordCount = countKeywords(this.value);
        document.getElementById('keywordCountEN').textContent = keywordCount;
        validateField(this, {
            required: true,
            minKeywords: 3,
            maxKeywords: 5
        });
    });
    
    // Dosya türü değiştiğinde kabul edilen formatları güncelle
    document.getElementById('fileType')?.addEventListener('change', updateAcceptedFormats);
    
    // Editör notu karakter sayısı
    document.getElementById('editorNote')?.addEventListener('input', function() {
        document.getElementById('characterCount').textContent = this.value.length;
    });
}

// Form doğrulama ayarları
function setupFormValidation() {
    // Tüm required alanlar için blur olayı ile doğrulama
    document.querySelectorAll('[required]').forEach(field => {
        field.addEventListener('blur', function() {
            if (this.type === 'email') {
                validateField(this, { required: true, email: true });
            } else {
                validateField(this, { required: true });
            }
        });
    });
}

// ==============================================
// 2. ADIM YÖNETİMİ VE NAVİGASYON
// ==============================================

// Adımı göster
function showStep(step) {
    // Önce hata konteynerini temizle
    clearAlertContainer();
    
    // Tüm adımları gizle
    document.querySelectorAll('.step-content').forEach(content => {
        content.classList.add('d-none');
    });
    
    // İlgili adımı göster
    document.getElementById(`step${step}`).classList.remove('d-none');
    
    // Sol menüdeki aktif adımı güncelle
    document.querySelectorAll('.step-link').forEach((link, index) => {
        if (index === step) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
    
    // İlerleme çubuğunu güncelle
    updateProgress(step);
    
    // Önceki/Sonraki butonlarını güncelle
    updateNavigationButtons(step);
    
    // Mevcut adımı güncelle
    currentStep = step;
    
    // Adım başlığını ilerleme bilgisine ekle
    document.querySelector('.progress-step-name').textContent = steps[step];
    
    // Özet sayfasında bilgileri güncelleme
    if (step === 12) { // Son adım (Makaleyi Gönder)
        updateSummary();
    }
    
    // Adımın içindeki form alanlarına odaklan
    focusFirstField(step);
    
    // Adımın doğrulama durumunu güncelle ama hata gösterme
    updateStepValidationStatus();
    
    console.log(`Adım ${step} gösteriliyor: ${steps[step]}`);
}

// İlerleme çubuğunu güncelle
function updateProgress(step) {
    const progress = Math.round(((step + 1) / totalSteps) * 100);
    const progressBar = document.querySelector('.progress-bar');
    
    progressBar.style.width = `${progress}%`;
    progressBar.setAttribute('aria-valuenow', progress);
    document.getElementById('progressText').textContent = `${progress}%`;
}

// Navigasyon butonlarını güncelle
function updateNavigationButtons(step) {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    // Önceki butonu (ilk adımda devre dışı)
    prevBtn.disabled = (step === 0);
    
    // Sonraki butonu
    if (step === totalSteps - 1) {
        // Son adımda sonraki butonu gizle
        nextBtn.style.display = 'none';
    } else {
        nextBtn.style.display = 'inline-block';
        // Ön bilgi adımındaysak onay kutusuna göre buton durumunu güncelle
        if (step === 1) {
            const acceptInfo = document.getElementById('acceptInfo');
            nextBtn.disabled = acceptInfo ? !acceptInfo.checked : true;
        } else {
            nextBtn.disabled = false;
        }
    }
}

// Sonraki adıma git
function goToNextStep() {
    // Önce hata konteynerini temizle
    clearAlertContainer();
    
    if (validateCurrentStep()) {
        if (currentStep < totalSteps - 1) {
            // Mevcut adım doğrulandıysa ilerle
            showStep(currentStep + 1);
            // Adımın doğrulama durumunu güncelle
            updateStepValidationStatus();
        }
    } else {
        showStepError();
    }
}

// Önceki adıma git
function goToPrevStep() {
    if (currentStep > 0) {
        showStep(currentStep - 1);
    }
}

// Belirli bir adıma gitmeyi dene
function tryGoToStep(step) {
    // Adım erişilebilirlik kontrolü yapılabilir
    // Örneğin önceki adımların doğrulanmasını zorunlu tutabilirsiniz
    
    // Şimdilik basit bir geçiş yapalım
    showStep(step);
    updateStepValidationStatus();
}

// Doğrudan belirli bir adıma git (düzenleme butonları için)
function goToStep(step) {
    showStep(step);
    updateStepValidationStatus();
}

// Adımdaki ilk alana odaklan
function focusFirstField(step) {
    const stepElement = document.getElementById(`step${step}`);
    if (!stepElement) return;
    
    const firstInput = stepElement.querySelector('input:not([type="hidden"]), select, textarea');
    if (firstInput) {
        setTimeout(() => {
            firstInput.focus();
        }, 100);
    }
}

// ==============================================
// 3. VERİ SAKLAMA VE KAYDETME
// ==============================================

// Otomatik kaydetmeyi başlat
function startAutoSave() {
    // Mevcut interval'ı temizle
    if (autoSaveInterval) {
        clearInterval(autoSaveInterval);
    }
    
    // Yeni interval oluştur
    autoSaveInterval = setInterval(() => {
        saveProgress(true); // otomatik kaydetme için true
    }, AUTO_SAVE_DELAY);
    
    console.log('Otomatik kaydetme başlatıldı.');
}

// Otomatik kaydetmeyi durdur
function stopAutoSave() {
    if (autoSaveInterval) {
        clearInterval(autoSaveInterval);
        autoSaveInterval = null;
    }
    
    console.log('Otomatik kaydetme durduruldu.');
}

// İlerlemeyi kaydet
function saveProgress(isAutoSave = false) {
    // Tüm form verilerini topla
    const formData = collectFormData();
    
    // LocalStorage'a kaydet
    localStorage.setItem('articleWizardData', JSON.stringify(formData));
    localStorage.setItem('articleWizardStep', currentStep);
    localStorage.setItem('articleWizardLastSaved', new Date().toISOString());
    
    // Son kaydedilen durumu güncelle
    lastSavedData = {...formData};
    
    // Kaydetme bildirimi göster
    if (!isAutoSave) {
        showNotification('İlerlemeniz başarıyla kaydedildi. İstediğiniz zaman kaldığınız yerden devam edebilirsiniz.', 'success');
    } else {
        // Otomatik kaydetme bildirimi
        const saveNotification = document.getElementById('autoSaveNotification');
        if (saveNotification) {
            const autoSaveText = saveNotification.querySelector('.auto-save-text');
            autoSaveText.textContent = `Son kayıt: ${new Date().toLocaleTimeString()}`;
            saveNotification.classList.remove('d-none');
            
            // 3 saniye sonra bildirimi gizle
            setTimeout(() => {
                saveNotification.classList.add('d-none');
            }, 3000);
        }
    }
    
    console.log(`İlerleme ${isAutoSave ? 'otomatik olarak' : 'manuel olarak'} kaydedildi.`);
}

// Form verilerini toplama
function collectFormData() {
    return {
        // Dil Seçimi
        language: document.getElementById('articleLanguage')?.value || '',
        
        // Ön Bilgi
        acceptInfo: document.getElementById('acceptInfo')?.checked || false,
        
        // Tür-Konu
        articleType: document.getElementById('articleType')?.value || '',
        articleSubject: document.getElementById('articleSubject')?.value || '',
        
        // Başlık
        titleTR: document.getElementById('titleTR')?.value || '',
        titleEN: document.getElementById('titleEN')?.value || '',
        
        // Özet
        abstractTR: document.getElementById('abstractTR')?.value || '',
        abstractEN: document.getElementById('abstractEN')?.value || '',
        
        // Anahtar Kelimeler
        keywordsTR: document.getElementById('keywordsTR')?.value || '',
        keywordsEN: document.getElementById('keywordsEN')?.value || '',
        
        // Referanslar
        references: collectReferences(),
        
        // Yazarlar, Hakemler ve Dosyalar
        authors: authors,
        reviewers: reviewers,
        uploadedFiles: uploadedFiles,
        
        // Editöre Not
        editorNote: document.getElementById('editorNote')?.value || '',
        
        // Kontrol Listesi
        checklist: collectChecklist()
    };
}

// Referansları topla
function collectReferences() {
    const references = [];
    const referenceItems = document.querySelectorAll('#referencesContainer .reference-item textarea');
    
    referenceItems.forEach(item => {
        if (item.value.trim()) {
            references.push(item.value.trim());
        }
    });
    
    return references;
}

// Kontrol listesini topla
function collectChecklist() {
    const checklist = {};
    const checkboxes = document.querySelectorAll('.checklist-container input[type="checkbox"]');
    
    checkboxes.forEach((checkbox, index) => {
        checklist[`check${index + 1}`] = checkbox.checked;
    });
    
    return checklist;
}

// Kaydedilmiş ilerlemeyi yükle
function loadSavedProgress() {
    const savedData = localStorage.getItem('articleWizardData');
    const savedStep = localStorage.getItem('articleWizardStep');
    const lastSaved = localStorage.getItem('articleWizardLastSaved');
    
    if (savedData && savedStep) {
        try {
            const formData = JSON.parse(savedData);
            
            // Form alanlarını doldur
            fillFormFields(formData);
            
            // Global dizileri güncelle
            if (formData.authors && Array.isArray(formData.authors)) {
                authors = formData.authors;
                updateAuthorsTable();
            }
            
            if (formData.reviewers && Array.isArray(formData.reviewers)) {
                reviewers = formData.reviewers;
                updateReviewersTable();
            }
            
            if (formData.uploadedFiles && Array.isArray(formData.uploadedFiles)) {
                uploadedFiles = formData.uploadedFiles;
                updateFilesTable();
            }
            
            // Son kaydedilen durumu güncelle
            lastSavedData = {...formData};
            
            // Kaydedilen adıma git
            const step = parseInt(savedStep);
            if (!isNaN(step) && step >= 0 && step < totalSteps) {
                showStep(step);
            }
            
            // Kaydedilme zamanını göster
            if (lastSaved) {
                const savedDate = new Date(lastSaved);
                showNotification(`Son kaydedilen ilerlemeniz yüklendi (${formatDate(savedDate)}).`, 'info');
            }
            
            console.log('Kayıtlı ilerleme başarıyla yüklendi.');
            return true;
        } catch (error) {
            console.error('Kayıtlı ilerleme yüklenirken hata oluştu:', error);
            return false;
        }
    }
    
    console.log('Kayıtlı ilerleme bulunamadı.');
    return false;
}

// Form alanlarını doldur
function fillFormFields(formData) {
    // Dil Seçimi
    if (formData.language) {
        document.getElementById('articleLanguage').value = formData.language;
    }
    
    // Ön Bilgi
    if (formData.acceptInfo) {
        document.getElementById('acceptInfo').checked = formData.acceptInfo;
    }
    
    // Tür-Konu
    if (formData.articleType) {
        document.getElementById('articleType').value = formData.articleType;
    }
    
    if (formData.articleSubject) {
        document.getElementById('articleSubject').value = formData.articleSubject;
    }
    
    // Başlık
    if (formData.titleTR) {
        document.getElementById('titleTR').value = formData.titleTR;
        document.getElementById('charCountTR').textContent = formData.titleTR.length;
    }
    
    if (formData.titleEN) {
        document.getElementById('titleEN').value = formData.titleEN;
        document.getElementById('charCountEN').textContent = formData.titleEN.length;
    }
    
    // Özet
    if (formData.abstractTR) {
        document.getElementById('abstractTR').value = formData.abstractTR;
        document.getElementById('wordCountTR').textContent = countWords(formData.abstractTR);
    }
    
    if (formData.abstractEN) {
        document.getElementById('abstractEN').value = formData.abstractEN;
        document.getElementById('wordCountEN').textContent = countWords(formData.abstractEN);
    }
    
    // Anahtar Kelimeler
    if (formData.keywordsTR) {
        document.getElementById('keywordsTR').value = formData.keywordsTR;
        document.getElementById('keywordCountTR').textContent = countKeywords(formData.keywordsTR);
    }
    
    if (formData.keywordsEN) {
        document.getElementById('keywordsEN').value = formData.keywordsEN;
        document.getElementById('keywordCountEN').textContent = countKeywords(formData.keywordsEN);
    }
    
    // Referanslar
    if (formData.references && Array.isArray(formData.references) && formData.references.length > 0) {
        // Referans kutularını temizle
        document.getElementById('referencesContainer').innerHTML = '';
        
        // Referansları yeniden oluştur
        formData.references.forEach(reference => {
            addReferenceWithValue(reference);
        });
    }
    
    // Editöre Not
    if (formData.editorNote) {
        document.getElementById('editorNote').value = formData.editorNote;
        document.getElementById('characterCount').textContent = formData.editorNote.length;
    }
    
    // Kontrol Listesi
    if (formData.checklist) {
        for (const [key, value] of Object.entries(formData.checklist)) {
            const checkbox = document.getElementById(key);
            if (checkbox) {
                checkbox.checked = value;
            }
        }
        updateChecklistProgress();
    }
}

// Değişiklik olup olmadığını kontrol et
function hasUnsavedChanges() {
    const currentData = collectFormData();
    return JSON.stringify(currentData) !== JSON.stringify(lastSavedData);
}

// Hata bildirim alanını temizle
function clearAlertContainer() {
    const alertContainer = document.getElementById('alertContainer');
    if (alertContainer) {
        alertContainer.innerHTML = '';
    }
}

// ==============================================
// 4. REFERANS İŞLEMLERİ
// ==============================================

// Yeni referans ekle
function addNewReference() {
    const container = document.getElementById('referencesContainer');
    const newReference = document.createElement('div');
    newReference.className = 'reference-item mb-3 animate__animated animate__fadeIn';
    newReference.innerHTML = `
        <div class="input-group">
            <textarea class="form-control" rows="3" 
                    placeholder="Örnek: Smith, J. (2023). Makale başlığı. Dergi Adı, 10(2), 100-120."></textarea>
            <button class="btn btn-danger" onclick="removeReference(this)" title="Referansı Sil">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(newReference);
    
    // Yeni eklenen alanı odakla
    const textarea = newReference.querySelector('textarea');
    if (textarea) {
        textarea.focus();
    }
    
    // Adım doğrulama durumunu güncelle
    validateStep(6);
}

// Belirli değere sahip referans ekle (yükleme için)
function addReferenceWithValue(value) {
    const container = document.getElementById('referencesContainer');
    const newReference = document.createElement('div');
    newReference.className = 'reference-item mb-3';
    newReference.innerHTML = `
        <div class="input-group">
            <textarea class="form-control" rows="3" 
                    placeholder="Örnek: Smith, J. (2023). Makale başlığı. Dergi Adı, 10(2), 100-120.">${value}</textarea>
            <button class="btn btn-danger" onclick="removeReference(this)" title="Referansı Sil">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(newReference);
}

// Referans sil
function removeReference(button) {
    const referenceItem = button.closest('.reference-item');
    referenceItem.classList.add('animate__fadeOut');
    
    setTimeout(() => {
        referenceItem.remove();
        // Adım doğrulama durumunu güncelle
        validateStep(6);
    }, 300);
}

// ==============================================
// 5. YAZAR İŞLEMLERİ
// ==============================================

// Yazar ekle/düzenle
function addAuthor() {
    // Form doğrulama
    const form = document.getElementById('authorForm');
    if (!form.checkValidity()) {
        // Bootstrap form doğrulamayı zorla
        form.classList.add('was-validated');
        return false;
    }
    
    // Form verilerini al
    const authorData = {
        id: document.getElementById('authorId').value || Date.now().toString(),
        firstName: document.getElementById('firstName').value,
        middleName: document.getElementById('middleName').value,
        lastName: document.getElementById('lastName').value,
        title: document.getElementById('title').value,
        phone: document.getElementById('phone').value,
        email1: document.getElementById('email1').value,
        email2: document.getElementById('email2').value,
        department: document.getElementById('department').value,
        institution: document.getElementById('institution').value,
        country: document.getElementById('country').value,
        orcidId: document.getElementById('orcidId').value,
        authorOrder: document.getElementById('authorOrder').value,
        authorType: document.getElementById('authorType').value
    };
    
    // Düzenleme modu kontrolü
    const isEditMode = document.getElementById('authorId').value !== '';
    
    if (isEditMode) {
        // Yazarı güncelle
        const index = authors.findIndex(author => author.id === authorData.id);
        if (index !== -1) {
            authors[index] = authorData;
        }
    } else {
        // Yeni yazar ekle
        authors.push(authorData);
    }
    
    // Yazarları sırala
    authors.sort((a, b) => parseInt(a.authorOrder) - parseInt(b.authorOrder));
    
    // Tabloyu güncelle
    updateAuthorsTable();
    
    // Formu sıfırla
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('authorId').value = '';
    document.getElementById('authorSubmitBtn').innerHTML = '<i class="fas fa-plus me-2"></i>Yazar Ekle';
    document.getElementById('authorFormTitle').textContent = 'Yeni Yazar Ekle';
    document.getElementById('authorCancelBtn').classList.add('d-none');
    
    // Başarı mesajı göster
    showNotification(isEditMode ? 'Yazar başarıyla güncellendi!' : 'Yazar başarıyla eklendi!', 'success');
    
    // Adım doğrulama durumunu güncelle
    validateStep(7);
    
    return true;
}

// Yazarlar tablosunu güncelle
function updateAuthorsTable() {
    const table = document.getElementById('authorsTable');
    if (!table) return;
    
    const tbody = table.querySelector('tbody');
    tbody.innerHTML = '';
    
    if (authors.length === 0) {
        // Tablo boşsa bilgi mesajı göster
        const row = document.createElement('tr');
        row.innerHTML = '<td colspan="3" class="text-center">Henüz yazar eklenmedi</td>';
        tbody.appendChild(row);
    } else {
        // Yazarları tabloya ekle
        authors.forEach(author => {
            const row = document.createElement('tr');
            
            const authorTypeLabel = author.authorType === 'primary' ? 
                '<span class="badge bg-primary">Birincil</span>' : 
                author.authorType === 'corresponding' ? 
                '<span class="badge bg-success">Sorumlu</span>' : 
                '<span class="badge bg-info">Katkıda Bulunan</span>';
                
            const authorTitle = author.title === 'prof' ? 'Prof. Dr.' : 
                author.title === 'assocprof' ? 'Doç. Dr.' : 
                author.title === 'assistprof' ? 'Dr. Öğr. Üyesi' : 
                author.title === 'dr' ? 'Dr.' : 
                author.title === 'res' ? 'Arş. Gör.' : '';
            
            row.innerHTML = `
                <td>${author.authorOrder}</td>
                <td>
                    <div class="author-card">
                        <div class="author-header">
                            <h5 class="mb-0">
                                ${authorTitle} ${author.firstName} ${author.middleName} ${author.lastName} ${authorTypeLabel}
                            </h5>
                        </div>
                        <div class="author-details">
                            <div class="detail-group">
                                <span class="detail-label">Departman/Kurum:</span>
                                <span class="detail-value">${author.department}, ${author.institution}, ${author.country}</span>
                            </div>
                            <div class="detail-group">
                                <span class="detail-label">İletişim:</span>
                                <span class="detail-value">${author.email1}${author.email2 ? ' | ' + author.email2 : ''} | ${author.phone}</span>
                            </div>
                            <div class="detail-group">
                                <span class="detail-label">ORCID ID:</span>
                                <a href="https://orcid.org/${author.orcidId}" target="_blank" class="orcid-link">
                                    <i class="fas fa-external-link-alt me-1"></i>${author.orcidId}
                                </a>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="d-grid gap-2">
                        <button class="btn btn-warning btn-action" onclick="editAuthor('${author.id}')">
                            <i class="fas fa-edit me-1"></i> Düzenle
                        </button>
                        <button class="btn btn-danger btn-action" onclick="removeAuthor('${author.id}')">
                            <i class="fas fa-trash me-1"></i> Sil
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }
    
    // Yazar sayısını göster
    const authorCountElement = document.getElementById('authorCount');
    if (authorCountElement) {
        authorCountElement.textContent = `${authors.length} Yazar`;
    }
}

// Yazarı düzenleme moduna al
function editAuthor(authorId) {
    const author = authors.find(a => a.id === authorId);
    if (!author) return;
    
    // Form alanlarını doldur
    document.getElementById('authorId').value = author.id;
    document.getElementById('firstName').value = author.firstName;
    document.getElementById('middleName').value = author.middleName;
    document.getElementById('lastName').value = author.lastName;
    document.getElementById('title').value = author.title;
    document.getElementById('phone').value = author.phone;
    document.getElementById('email1').value = author.email1;
    document.getElementById('email2').value = author.email2;
    document.getElementById('department').value = author.department;
    document.getElementById('institution').value = author.institution;
    document.getElementById('country').value = author.country;
    document.getElementById('orcidId').value = author.orcidId;
    document.getElementById('authorOrder').value = author.authorOrder;
    document.getElementById('authorType').value = author.authorType;
    
    // Düzenleme modunu aktifleştir
    document.getElementById('authorFormTitle').textContent = 'Yazarı Düzenle';
    document.getElementById('authorSubmitBtn').innerHTML = '<i class="fas fa-save me-2"></i>Değişiklikleri Kaydet';
    document.getElementById('authorCancelBtn').classList.remove('d-none');
    
    // Forma kaydır
    document.getElementById('authorForm').scrollIntoView({ behavior: 'smooth' });
}

// Yazarı sil
function removeAuthor(authorId) {
    if (confirm('Bu yazarı silmek istediğinizden emin misiniz?')) {
        authors = authors.filter(author => author.id !== authorId);
        updateAuthorsTable();
        showNotification('Yazar başarıyla silindi.', 'success');
        
        // Adım doğrulama durumunu güncelle
        validateStep(7);
    }
}

// Yazar düzenlemeyi iptal et
function cancelAuthorEdit() {
    const form = document.getElementById('authorForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('authorId').value = '';
    document.getElementById('authorFormTitle').textContent = 'Yeni Yazar Ekle';
    document.getElementById('authorSubmitBtn').innerHTML = '<i class="fas fa-plus me-2"></i>Yazar Ekle';
    document.getElementById('authorCancelBtn').classList.add('d-none');
}

// ==============================================
// 6. HAKEM İŞLEMLERİ
// ==============================================

// Hakem ekle/düzenle
function addReviewer() {
    // Form doğrulama
    const form = document.getElementById('reviewerForm');
    if (!form.checkValidity()) {
        // Bootstrap form doğrulamayı zorla
        form.classList.add('was-validated');
        return false;
    }
    
    // Form verilerini al
    const reviewerData = {
        id: document.getElementById('reviewerId').value || Date.now().toString(),
        reviewerOrder: document.getElementById('reviewerOrder').value,
        reviewerType: document.getElementById('reviewerType').value,
        reviewerTitle: document.getElementById('reviewerTitle').value,
        reviewerFirstName: document.getElementById('reviewerFirstName').value,
        reviewerMiddleName: document.getElementById('reviewerMiddleName').value,
        reviewerLastName: document.getElementById('reviewerLastName').value,
        reviewerEmail1: document.getElementById('reviewerEmail1').value,
        reviewerEmail2: document.getElementById('reviewerEmail2').value,
        reviewerPhone: document.getElementById('reviewerPhone').value,
        reviewerDepartment: document.getElementById('reviewerDepartment').value,
        reviewerInstitution: document.getElementById('reviewerInstitution').value,
        reviewerCountry: document.getElementById('reviewerCountry').value,
        reviewerOrcidId: document.getElementById('reviewerOrcidId').value
    };
    
    // Düzenleme modu kontrolü
    const isEditMode = document.getElementById('reviewerId').value !== '';
    
    if (isEditMode) {
        // Hakemi güncelle
        const index = reviewers.findIndex(reviewer => reviewer.id === reviewerData.id);
        if (index !== -1) {
            reviewers[index] = reviewerData;
        }
    } else {
        // Yeni hakem ekle
        reviewers.push(reviewerData);
    }
    
    // Hakemleri sırala
    reviewers.sort((a, b) => parseInt(a.reviewerOrder) - parseInt(b.reviewerOrder));
    
    // Tabloyu güncelle
    updateReviewersTable();
    
    // Formu sıfırla
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('reviewerId').value = '';
    document.getElementById('reviewerSubmitBtn').innerHTML = '<i class="fas fa-plus me-2"></i>Hakem Ekle';
    document.getElementById('reviewerFormTitle').textContent = 'Yeni Hakem Ekle';
    document.getElementById('reviewerCancelBtn').classList.add('d-none');
    
    // Başarı mesajı göster
    showNotification(isEditMode ? 'Hakem başarıyla güncellendi!' : 'Hakem başarıyla eklendi!', 'success');
    
    // Adım doğrulama durumunu güncelle
    validateStep(9);
    
    // Hakem uyarısını güncelle
    updateReviewerWarning();
    
    return true;
}

// Hakemler tablosunu güncelle
function updateReviewersTable() {
    const table = document.getElementById('reviewersTable');
    if (!table) return;
    
    const tbody = table.querySelector('tbody');
    tbody.innerHTML = '';
    
    if (reviewers.length === 0) {
        // Tablo boşsa bilgi mesajı göster
        const row = document.createElement('tr');
        row.innerHTML = '<td colspan="3" class="text-center">Henüz hakem eklenmedi</td>';
        tbody.appendChild(row);
    } else {
        // Hakemleri tabloya ekle
        reviewers.forEach(reviewer => {
            const row = document.createElement('tr');
            
            const reviewerTypeLabel = reviewer.reviewerType === 'main' ? 
                '<span class="badge bg-primary">Ana Hakem</span>' : 
                reviewer.reviewerType === 'alternate' ? 
                '<span class="badge bg-success">Yedek Hakem</span>' : 
                '<span class="badge bg-info">Dış Hakem</span>';
                
            const reviewerTitle = reviewer.reviewerTitle === 'prof' ? 'Prof. Dr.' : 
                reviewer.reviewerTitle === 'assocprof' ? 'Doç. Dr.' : 
                reviewer.reviewerTitle === 'assistprof' ? 'Dr. Öğr. Üyesi' : 
                reviewer.reviewerTitle === 'dr' ? 'Dr.' : 
                reviewer.reviewerTitle === 'res' ? 'Arş. Gör.' : '';
            
            row.innerHTML = `
                <td>${reviewer.reviewerOrder}</td>
                <td>
                    <div class="reviewer-card">
                        <div class="reviewer-header">
                            <h5 class="mb-0">
                                ${reviewerTitle} ${reviewer.reviewerFirstName} ${reviewer.reviewerMiddleName} ${reviewer.reviewerLastName} ${reviewerTypeLabel}
                            </h5>
                        </div>
                        <div class="reviewer-details">
                            <div class="detail-group">
                                <span class="detail-label">Departman/Kurum:</span>
                                <span class="detail-value">${reviewer.reviewerDepartment || '-'}, ${reviewer.reviewerInstitution || '-'}, ${reviewer.reviewerCountry || '-'}</span>
                            </div>
                            <div class="detail-group">
                                <span class="detail-label">İletişim:</span>
                                <span class="detail-value">${reviewer.reviewerEmail1}${reviewer.reviewerEmail2 ? ' | ' + reviewer.reviewerEmail2 : ''} ${reviewer.reviewerPhone ? ' | ' + reviewer.reviewerPhone : ''}</span>
                            </div>
                            ${reviewer.reviewerOrcidId ? `
                            <div class="detail-group">
                                <span class="detail-label">ORCID ID:</span>
                                <a href="https://orcid.org/${reviewer.reviewerOrcidId}" target="_blank" class="orcid-link">
                                    <i class="fas fa-external-link-alt me-1"></i>${reviewer.reviewerOrcidId}
                                </a>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                </td>
                <td>
                    <div class="d-grid gap-2">
                        <button class="btn btn-warning btn-action" onclick="editReviewer('${reviewer.id}')">
                            <i class="fas fa-edit me-1"></i> Düzenle
                        </button>
                        <button class="btn btn-danger btn-action" onclick="removeReviewer('${reviewer.id}')">
                            <i class="fas fa-trash me-1"></i> Sil
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }
    
    // Hakem sayısını göster
    const reviewerCountElement = document.getElementById('reviewerCount');
    if (reviewerCountElement) {
        reviewerCountElement.textContent = `${reviewers.length} Hakem`;
    }
}

// Hakemi düzenleme moduna al
function editReviewer(reviewerId) {
    const reviewer = reviewers.find(r => r.id === reviewerId);
    if (!reviewer) return;
    
    // Form alanlarını doldur
    document.getElementById('reviewerId').value = reviewer.id;
    document.getElementById('reviewerOrder').value = reviewer.reviewerOrder;
    document.getElementById('reviewerType').value = reviewer.reviewerType;
    document.getElementById('reviewerTitle').value = reviewer.reviewerTitle;
    document.getElementById('reviewerFirstName').value = reviewer.reviewerFirstName;
    document.getElementById('reviewerMiddleName').value = reviewer.reviewerMiddleName;
    document.getElementById('reviewerLastName').value = reviewer.reviewerLastName;
    document.getElementById('reviewerEmail1').value = reviewer.reviewerEmail1;
    document.getElementById('reviewerEmail2').value = reviewer.reviewerEmail2;
    document.getElementById('reviewerPhone').value = reviewer.reviewerPhone;
    document.getElementById('reviewerDepartment').value = reviewer.reviewerDepartment;
    document.getElementById('reviewerInstitution').value = reviewer.reviewerInstitution;
    document.getElementById('reviewerCountry').value = reviewer.reviewerCountry;
    document.getElementById('reviewerOrcidId').value = reviewer.reviewerOrcidId;
    
    // Düzenleme modunu aktifleştir
    document.getElementById('reviewerFormTitle').textContent = 'Hakemi Düzenle';
    document.getElementById('reviewerSubmitBtn').innerHTML = '<i class="fas fa-save me-2"></i>Değişiklikleri Kaydet';
    document.getElementById('reviewerCancelBtn').classList.remove('d-none');
    
    // Forma kaydır
    document.getElementById('reviewerForm').scrollIntoView({ behavior: 'smooth' });
}

// Hakemi sil
function removeReviewer(reviewerId) {
    if (confirm('Bu hakemi silmek istediğinizden emin misiniz?')) {
        reviewers = reviewers.filter(reviewer => reviewer.id !== reviewerId);
        updateReviewersTable();
        showNotification('Hakem başarıyla silindi.', 'success');
        
        // Adım doğrulama durumunu güncelle
        validateStep(9);
        
        // Hakem uyarısını güncelle
        updateReviewerWarning();
    }
}

// Hakem düzenlemeyi iptal et
function cancelReviewerEdit() {
    const form = document.getElementById('reviewerForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('reviewerId').value = '';
    document.getElementById('reviewerFormTitle').textContent = 'Yeni Hakem Ekle';
    document.getElementById('reviewerSubmitBtn').innerHTML = '<i class="fas fa-plus me-2"></i>Hakem Ekle';
    document.getElementById('reviewerCancelBtn').classList.add('d-none');
}

// Hakem uyarısını güncelle
function updateReviewerWarning() {
    const warningElement = document.getElementById('reviewerWarning');
    if (warningElement) {
        if (reviewers.length < 3) {
            warningElement.classList.remove('d-none');
        } else {
            warningElement.classList.add('d-none');
        }
    }
}

// ==============================================
// 7. DOSYA İŞLEMLERİ
// ==============================================

// Yeni dosya ekle
function addFile() {
    const fileType = document.getElementById('fileType');
    const fileInput = document.getElementById('fileInput');
    
    // Form doğrulama
    if (!fileType.value || !fileInput.files.length) {
        if (!fileType.value) {
            fileType.classList.add('is-invalid');
        }
        if (!fileInput.files.length) {
            fileInput.classList.add('is-invalid');
        }
        return;
    }
    
    // Dosya bilgilerini al
    const file = fileInput.files[0];
    const fileSize = formatFileSize(file.size);
    const fileFormat = file.name.split('.').pop().toUpperCase();
    
    // Dosya tipinin türkçe karşılığını bul
    const fileTypeText = getFileTypeText(fileType.value);
    
    // Dosya verilerini oluştur
    const fileData = {
        id: Date.now().toString(),
        type: fileType.value,
        typeText: fileTypeText,
        name: file.name,
        size: fileSize,
        format: fileFormat,
        date: formatDateShort(new Date())
    };
    
    // Dosyayı diziye ekle
    uploadedFiles.push(fileData);
    
    // Dosyalar tablosunu güncelle
    updateFilesTable();
    
    // Formu sıfırla
    document.getElementById('fileUploadForm').reset();
    fileType.classList.remove('is-invalid');
    fileInput.classList.remove('is-invalid');
    
    // Başarı mesajı göster
    showNotification('Dosya başarıyla yüklendi.', 'success');
    
    // Adım doğrulama durumunu güncelle
    validateStep(8);
}

// Dosyaları güncelle
function updateFilesTable() {
    const table = document.getElementById('filesTable');
    if (!table) return;
    
    const tbody = table.querySelector('tbody');
    tbody.innerHTML = '';
    
    if (uploadedFiles.length === 0) {
        // Tablo boşsa bilgi mesajı göster
        const row = document.createElement('tr');
        row.innerHTML = '<td colspan="6" class="text-center">Henüz dosya yüklenmedi</td>';
        tbody.appendChild(row);
    } else {
        // Dosyaları tabloya ekle
        uploadedFiles.forEach(file => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${file.typeText}</td>
                <td>${file.name}</td>
                <td>${file.size}</td>
                <td>${file.format}</td>
                <td>${file.date}</td>
                <td>
                    <button class="btn btn-sm btn-primary me-1" title="İndir">
                        <i class="fas fa-download"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="removeFile('${file.id}')" title="Sil">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }
}

// Dosya sil
function removeFile(fileId) {
    if (confirm('Bu dosyayı silmek istediğinizden emin misiniz?')) {
        uploadedFiles = uploadedFiles.filter(file => file.id !== fileId);
        updateFilesTable();
        showNotification('Dosya başarıyla silindi.', 'success');
        
        // Adım doğrulama durumunu güncelle
        validateStep(8);
    }
}

// Dosya boyutunu formatla
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

// Kısa tarih formatı
function formatDateShort(date) {
    return date.toLocaleDateString('tr-TR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

// Dosya türünün karşılığını bul
function getFileTypeText(fileType) {
    const fileTypes = {
        'fullText': 'Tam Metin',
        'copyright': 'Yayın Hakkı Devir Formu',
        'authorContribution': 'Yazar Katkı Formu',
        'icmjeCoi': 'ICMJE COI Form',
        'iThenticate': 'iThenticate Formu',
        'supplementary': 'Ek Dosya',
        'figures': 'Şekiller',
        'images': 'Görseller',
        'similarity': 'Benzerlik Raporu'
    };
    
    return fileTypes[fileType] || 'Bilinmeyen';
}

// Kabul edilen formatları güncelleme
function updateAcceptedFormats() {
    const fileType = document.getElementById('fileType').value;
    const formatHelp = document.getElementById('formatHelp');
    const fileInput = document.getElementById('fileInput');
    
    const formats = {
        'fullText': {
            accept: '.pdf,.docx',
            help: 'Kabul edilen formatlar: PDF, DOCX'
        },
        'copyright': {
            accept: '.pdf,.jpg,.png',
            help: 'Kabul edilen formatlar: PDF, JPG, PNG'
        },
        'authorContribution': {
            accept: '.pdf',
            help: 'Kabul edilen format: PDF'
        },
        'icmjeCoi': {
            accept: '.pdf',
            help: 'Kabul edilen format: PDF'
        },
        'iThenticate': {
            accept: '.pdf',
            help: 'Kabul edilen format: PDF'
        },
        'supplementary': {
            accept: '.pdf,.xlsx,.zip',
            help: 'Kabul edilen formatlar: PDF, XLSX, ZIP'
        },
        'figures': {
            accept: '.jpg,.png,.tif',
            help: 'Kabul edilen formatlar: JPG, PNG, TIF'
        },
        'images': {
            accept: '.jpg,.png,.tif',
            help: 'Kabul edilen formatlar: JPG, PNG, TIF'
        },
        'similarity': {
            accept: '.pdf',
            help: 'Kabul edilen format: PDF'
        }
    };
    
    if (fileType && formats[fileType]) {
        fileInput.setAttribute('accept', formats[fileType].accept);
        formatHelp.textContent = formats[fileType].help;
    } else {
        fileInput.removeAttribute('accept');
        formatHelp.textContent = '';
    }
}

// ==============================================
// 8. EDİTÖR NOTU İŞLEMLERİ
// ==============================================

// Editör Notunu Kaydet
function saveNote() {
    const noteContent = document.getElementById('editorNote').value;
    showNotification('Editör notu başarıyla kaydedildi.', 'success');
    
    // Adım doğrulama durumunu güncelle
    validateStep(10);
}

// Metin formatla
function formatText(format) {
    const editorNote = document.getElementById('editorNote');
    const selectedText = editorNote.value.substring(editorNote.selectionStart, editorNote.selectionEnd);
    
    if (!selectedText) return;
    
    let formattedText = '';
    
    switch (format) {
        case 'bold':
            formattedText = `**${selectedText}**`;
            break;
        case 'italic':
            formattedText = `*${selectedText}*`;
            break;
        case 'underline':
            formattedText = `_${selectedText}_`;
            break;
    }
    
    // Yeni içeriği oluştur
    const beforeText = editorNote.value.substring(0, editorNote.selectionStart);
    const afterText = editorNote.value.substring(editorNote.selectionEnd);
    editorNote.value = beforeText + formattedText + afterText;
    
    // Karakter sayısını güncelle
    document.getElementById('characterCount').textContent = editorNote.value.length;
}

// Format temizle
function clearFormat() {
    const editorNote = document.getElementById('editorNote');
    const selectedText = editorNote.value.substring(editorNote.selectionStart, editorNote.selectionEnd);
    
    if (!selectedText) return;
    
    // Markdown işaretlerini temizle
    const formattedText = selectedText
        .replace(/\*\*/g, '')
        .replace(/\*/g, '')
        .replace(/_/g, '');
    
    // Yeni içeriği oluştur
    const beforeText = editorNote.value.substring(0, editorNote.selectionStart);
    const afterText = editorNote.value.substring(editorNote.selectionEnd);
    editorNote.value = beforeText + formattedText + afterText;
    
    // Karakter sayısını güncelle
    document.getElementById('characterCount').textContent = editorNote.value.length;
}

// ==============================================
// 9. KONTROL LİSTESİ İŞLEMLERİ
// ==============================================

// Tüm öğeleri kontrol et
function checkAllItems() {
    document.querySelectorAll('.checklist-container input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = true;
    });
    updateChecklistProgress();
}

// Tüm öğeleri temizle
function clearAllItems() {
    document.querySelectorAll('.checklist-container input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    updateChecklistProgress();
}

// Kontrol listesi ilerlemesini güncelle
function updateChecklistProgress() {
    const checkboxes = document.querySelectorAll('.checklist-container input[type="checkbox"]');
    const checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
    const totalCount = checkboxes.length;
    
    // Progress metnini güncelle
    const progressText = document.getElementById('checkProgress');
    if (progressText) {
        progressText.textContent = `${checkedCount}/${totalCount}`;
    }
    
    // Progress çubuğunu güncelle
    const progressBar = document.querySelector('.checklist-container + .progress .progress-bar');
    if (progressBar) {
        const percentage = totalCount > 0 ? (checkedCount / totalCount) * 100 : 0;
        progressBar.style.width = `${percentage}%`;
        progressBar.setAttribute('aria-valuenow', percentage);
    }
    
    // Adım doğrulama durumunu güncelle
    validateStep(11);
}

// ==============================================
// 10. MAKALE GÖNDERME
// ==============================================

// Gönder butonunu etkinleştir/devre dışı bırak
function toggleSubmitButton() {
    const checkbox = document.getElementById('submitConfirmation');
    const submitButton = document.getElementById('finalSubmitButton');
    
    if (submitButton) {
        submitButton.disabled = !checkbox.checked;
    }
}

// Makale özet bilgilerini güncelle
function updateSummary() {
    // Makale Türü ve Konusu
    document.getElementById('summaryType').textContent = getSelectedOptionText('articleType') || '-';
    document.getElementById('summarySubject').textContent = getSelectedOptionText('articleSubject') || '-';
    
    // Başlıklar
    document.getElementById('summaryTitleTR').textContent = document.getElementById('titleTR').value || '-';
    document.getElementById('summaryTitleEN').textContent = document.getElementById('titleEN').value || '-';
    
    // Özetler
    document.getElementById('summaryAbstractTR').textContent = (document.getElementById('abstractTR').value || '-').substring(0, 200) + '...';
    document.getElementById('summaryAbstractEN').textContent = (document.getElementById('abstractEN').value || '-').substring(0, 200) + '...';
    
    // Anahtar Kelimeler
    document.getElementById('summaryKeywordsTR').textContent = document.getElementById('keywordsTR').value || '-';
    document.getElementById('summaryKeywordsEN').textContent = document.getElementById('keywordsEN').value || '-';
    
    // Referanslar
    const referencesList = document.getElementById('summaryReferences');
    if (referencesList) {
        const references = collectReferences();
        if (references.length > 0) {
            let referencesHtml = '<ol class="mb-0">';
            references.forEach(reference => {
                referencesHtml += `<li>${reference}</li>`;
            });
            referencesHtml += '</ol>';
            referencesList.innerHTML = referencesHtml;
        } else {
            referencesList.textContent = 'Referans eklenmedi';
        }
    }
    
    // Yazarlar
    const authorsList = document.getElementById('summaryAuthors');
    if (authorsList) {
        if (authors.length > 0) {
            let authorsHtml = '<ul class="list-group">';
            authors.forEach(author => {
                const authorTitle = author.title === 'prof' ? 'Prof. Dr.' : 
                    author.title === 'assocprof' ? 'Doç. Dr.' : 
                    author.title === 'assistprof' ? 'Dr. Öğr. Üyesi' : 
                    author.title === 'dr' ? 'Dr.' : 
                    author.title === 'res' ? 'Arş. Gör.' : '';
                    
                const authorTypeLabel = author.authorType === 'primary' ? 
                    '<span class="badge bg-primary">Birincil</span>' : 
                    author.authorType === 'corresponding' ? 
                    '<span class="badge bg-success">Sorumlu</span>' : 
                    '<span class="badge bg-info">Katkıda Bulunan</span>';
                    
                authorsHtml += `
                    <li class="list-group-item">
                        <strong>${author.authorOrder}. ${authorTitle} ${author.firstName} ${author.lastName}</strong> ${authorTypeLabel}<br>
                        ${author.department}, ${author.institution}, ${author.country}<br>
                        ${author.email1}
                    </li>
                `;
            });
            authorsHtml += '</ul>';
            authorsList.innerHTML = authorsHtml;
        } else {
            authorsList.textContent = 'Yazar eklenmedi';
        }
    }
    
    // Hakemler
    const reviewersList = document.getElementById('summaryReviewers');
    if (reviewersList) {
        if (reviewers.length > 0) {
            let reviewersHtml = '<ul class="list-group">';
            reviewers.forEach(reviewer => {
                const reviewerTitle = reviewer.reviewerTitle === 'prof' ? 'Prof. Dr.' : 
                    reviewer.reviewerTitle === 'assocprof' ? 'Doç. Dr.' : 
                    reviewer.reviewerTitle === 'assistprof' ? 'Dr. Öğr. Üyesi' : 
                    reviewer.reviewerTitle === 'dr' ? 'Dr.' : 
                    reviewer.reviewerTitle === 'res' ? 'Arş. Gör.' : '';
                    
                const reviewerTypeLabel = reviewer.reviewerType === 'main' ? 
                    '<span class="badge bg-primary">Ana Hakem</span>' : 
                    reviewer.reviewerType === 'alternate' ? 
                    '<span class="badge bg-success">Yedek Hakem</span>' : 
                    '<span class="badge bg-info">Dış Hakem</span>';
                    
                reviewersHtml += `
                    <li class="list-group-item">
                        <strong>${reviewer.reviewerOrder}. ${reviewerTitle} ${reviewer.reviewerFirstName} ${reviewer.reviewerLastName}</strong> ${reviewerTypeLabel}<br>
                        ${reviewer.reviewerInstitution || '-'}<br>
                        ${reviewer.reviewerEmail1}
                    </li>
                `;
            });
            reviewersHtml += '</ul>';
            reviewersList.innerHTML = reviewersHtml;
        } else {
            reviewersList.textContent = 'Hakem eklenmedi';
        }
    }
    
    // Dosyalar
    const filesList = document.getElementById('summaryFiles');
    if (filesList) {
        if (uploadedFiles.length > 0) {
            let filesHtml = '<ul class="list-group">';
            uploadedFiles.forEach(file => {
                filesHtml += `
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${file.typeText}:</strong> ${file.name} (${file.size})
                            </div>
                            <span class="badge bg-primary rounded-pill">${file.format}</span>
                        </div>
                    </li>
                `;
            });
            filesHtml += '</ul>';
            filesList.innerHTML = filesHtml;
        } else {
            filesList.textContent = 'Dosya yüklenmedi';
        }
    }
    
    // Editör Notu
    const editorNoteElement = document.getElementById('summaryEditorNote');
    if (editorNoteElement) {
        const editorNote = document.getElementById('editorNote').value;
        if (editorNote) {
            editorNoteElement.innerHTML = formatMarkdown(editorNote);
        } else {
            editorNoteElement.textContent = 'Editör notu eklenmedi';
        }
    }
}

// Seçilen option'ın metnini al
function getSelectedOptionText(selectId) {
    const select = document.getElementById(selectId);
    if (!select || select.selectedIndex === -1) return '';
    
    return select.options[select.selectedIndex].text;
}

// Makale gönderme
function submitArticle() {
    // Tüm form verilerini topla
    const formData = collectFormData();
    
    // Sunucuya göndermeden önce doğrulama
    if (!validateFinalSubmission()) {
        showNotification('Lütfen tüm zorunlu alanları doldurun ve kontrol edin.', 'danger');
        return;
    }
    
    // Yükleme göstergesi
    const submitButton = document.getElementById('finalSubmitButton');
    const originalButtonText = submitButton.innerHTML;
    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Gönderiliyor...';
    submitButton.disabled = true;
    
    // Gerçek uygulamada AJAX ile sunucuya gönderilir
    // Burada başarılı gönderim simülasyonu yapıyoruz
    setTimeout(() => {
        // Başarılı bildirim göster
        showNotification('Makaleniz başarıyla gönderildi! Makale kodu: ART-2025-0132', 'success');
        
        // Düğmeyi sıfırla
        submitButton.innerHTML = originalButtonText;
        submitButton.disabled = false;
        
        // Kullanıcıyı makaleler sayfasına yönlendir
        setTimeout(() => {
            window.location.href = 'yazar-makaleler.html';
        }, 3000);
    }, 2000);
}

// Son gönderim doğrulaması
function validateFinalSubmission() {
    // Tüm adımların doğrulamasını yap
    let isValid = true;
    
    for (let i = 0; i < totalSteps - 1; i++) {
        if (!validateStep(i)) {
            isValid = false;
            // İlk hatayı gösteren adıma git
            showStep(i);
            showStepError();
            break;
        }
    }
    
    return isValid;
}

// Markdown formatlamayı HTML'e dönüştür
function formatMarkdown(text) {
    return text
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.*?)\*/g, '<em>$1</em>')
        .replace(/_(.*?)_/g, '<u>$1</u>')
        .replace(/\n/g, '<br>');
}

// ==============================================
// 11. VERİ DOĞRULAMA İŞLEVLERİ
// ==============================================

// Adım doğrulama
function validateStep(step) {
    let isValid = true;
    
    switch (step) {
        case 0: // Dil Seçimi
            isValid = document.getElementById('articleLanguage')?.value ? true : false;
            break;
            
        case 1: // Ön Bilgi
            isValid = document.getElementById('acceptInfo')?.checked ? true : false;
            break;
            
        case 2: // Tür-Konu
            isValid = document.getElementById('articleType')?.value && 
                      document.getElementById('articleSubject')?.value ? true : false;
            break;
            
        case 3: // Başlık
            const titleTR = document.getElementById('titleTR');
            const titleEN = document.getElementById('titleEN');
            
            isValid = validateField(titleTR, {
                required: true,
                minLength: 5,
                maxLength: 200
            }, false) && validateField(titleEN, {
                required: true,
                minLength: 5,
                maxLength: 200
            }, false);
            break;
            
        case 4: // Özet
            const abstractTR = document.getElementById('abstractTR');
            const abstractEN = document.getElementById('abstractEN');
            
            isValid = validateField(abstractTR, {
                required: true,
                minWords: 150,
                maxWords: 250
            }, false) && validateField(abstractEN, {
                required: true,
                minWords: 150,
                maxWords: 250
            }, false);
            break;
            
        case 5: // Anahtar Kelimeler
            const keywordsTR = document.getElementById('keywordsTR');
            const keywordsEN = document.getElementById('keywordsEN');
            
            isValid = validateField(keywordsTR, {
                required: true,
                minKeywords: 3,
                maxKeywords: 5
            }, false) && validateField(keywordsEN, {
                required: true,
                minKeywords: 3,
                maxKeywords: 5
            }, false);
            break;
            
        case 6: // Referanslar
            // En az 1 referans kontrolü
            isValid = collectReferences().length > 0;
            break;
            
        case 7: // Yazarlar
            // En az 1 yazar kontrolü
            isValid = authors.length > 0;
            break;
            
        case 8: // Dosyalar
            // En az tam metin dosyası ve yayın hakkı devir formu kontrolü
            const fullTextExists = uploadedFiles.some(file => file.type === 'fullText');
            const copyrightExists = uploadedFiles.some(file => file.type === 'copyright');
            
            isValid = fullTextExists && copyrightExists;
            break;
            
        case 9: // Hakemler
            // En az 3 hakem kontrolü
            isValid = reviewers.length >= 3;
            break;
            
        case 10: // Editöre Not
            // İsteğe bağlı, boş değilse min 10 karakter
            const editorNote = document.getElementById('editorNote').value;
            isValid = editorNote === '' || (editorNote.length >= 10);
            break;
            
        case 11: // Kontrol Listesi
            // Tüm kontrol listesi öğelerinin işaretlendiğinden emin ol
            const checkboxes = document.querySelectorAll('.checklist-container input[type="checkbox"]');
            isValid = Array.from(checkboxes).every(checkbox => checkbox.checked);
            break;
            
        case 12: // Makaleyi Gönder
            // Onay kutusu işaretlenmişse gönderebilir
            isValid = document.getElementById('submitConfirmation')?.checked ? true : false;
            break;
    }
    
    // Adımın doğrulama durumunu güncelle
    updateStepStatus(step, isValid);
    
    return isValid;
}

// Mevcut adımı doğrula
function validateCurrentStep() {
    return validateStep(currentStep);
}

// Adım durumunu güncelle (sol menüdeki adımların yanındaki ikonlar)
function updateStepStatus(step, isValid) {
    const stepLinks = document.querySelectorAll('.step-link');
    
    if (step >= stepLinks.length) return;
    
    const statusIndicator = stepLinks[step].querySelector('.step-status');
    
    if (statusIndicator) {
        statusIndicator.className = 'step-status';
        
        if (isValid === true) {
            statusIndicator.classList.add('valid');
            statusIndicator.innerHTML = '<i class="fas fa-check"></i>';
        } else if (isValid === false) {
            statusIndicator.classList.add('invalid');
            statusIndicator.innerHTML = '<i class="fas fa-times"></i>';
        } else {
            statusIndicator.classList.add('pending');
            statusIndicator.innerHTML = '<i class="fas fa-circle"></i>';
        }
    }
}

// Tüm adımların doğrulama durumunu güncelle
function updateStepValidationStatus() {
    for (let i = 0; i < totalSteps; i++) {
        validateStep(i);
    }
}

// Adım hatası göster
function showStepError() {
    const alertContainer = document.getElementById('alertContainer');
    if (!alertContainer) return;
    
    let errorMessage = '';
    
    switch (currentStep) {
        case 0:
            errorMessage = 'Lütfen makale dilini seçin.';
            break;
        case 1:
            errorMessage = 'Devam etmek için makale bilgilerini girmek istediğinizi onaylayın.';
            break;
        case 2:
            errorMessage = 'Lütfen makale türü ve konusunu seçin.';
            break;
        case 3:
            errorMessage = 'Lütfen geçerli bir Türkçe ve İngilizce başlık girin (5-200 karakter).';
            break;
        case 4:
            errorMessage = 'Lütfen geçerli bir Türkçe ve İngilizce özet girin (150-250 kelime).';
            break;
        case 5:
            errorMessage = 'Lütfen geçerli sayıda anahtar kelime girin (3-5 adet).';
            break;
        case 6:
            errorMessage = 'Lütfen en az bir referans ekleyin.';
            break;
        case 7:
            errorMessage = 'Lütfen en az bir yazar ekleyin.';
            break;
        case 8:
            errorMessage = 'Lütfen en az tam metin ve yayın hakkı devir formu yükleyin.';
            break;
        case 9:
            errorMessage = 'Lütfen en az 3 hakem ekleyin.';
            break;
        case 10:
            errorMessage = 'Editör notunuz en az 10 karakter olmalıdır.';
            break;
        case 11:
            errorMessage = 'Lütfen tüm kontrol listesi maddelerini işaretleyin.';
            break;
        case 12:
            errorMessage = 'Lütfen makaleyi göndermek için onay kutusunu işaretleyin.';
            break;
    }
    
    alertContainer.innerHTML = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> ${errorMessage}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
}