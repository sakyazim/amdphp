// hakem-makale-detay.js - Hakem makale değerlendirme sayfası için JavaScript kodları

document.addEventListener('DOMContentLoaded', function() {
    // URL parametrelerinden makale bilgilerini al
    const urlParams = new URLSearchParams(window.location.search);
    const articleId = urlParams.get('id');
    
    // Makalenin var olup olmadığını kontrol et
    if (articleId) {
        // Gerçek bir uygulamada burada AJAX ile makale verilerini çekersiniz
        // Şimdilik statik olarak gösterme amaçlı ID'yi yerleştiriyoruz
        document.getElementById('article-id').textContent = articleId;
        
        // Makale başlığı, durumu vb. bilgileri burada dinamik olarak doldururdunuz
        // Örnek: fetchArticleDetails(articleId);
    }
    
    // Yıldız derecelendirme sistemi için tıklama olayları
    setupRatingStars();
    
    // Çıkar çatışması kontrol kutusuna olay dinleyicisi ekle
    setupConflictCheckbox();
    
    // Şablon ekleme butonuna olay dinleyicisi ekle
    setupTemplateButtons();
    
    // Vurgu ekleme butonu
    setupHighlightButton();
    
    // Önizleme butonu
    setupPreviewButton();
    
    // Gönder butonuna tıklama
    document.getElementById('submit-btn').addEventListener('click', function() {
        validateAndSubmit();
    });
    
    // Modal içindeki gönder butonu
    document.getElementById('submit-review-btn').addEventListener('click', function() {
        bootstrap.Modal.getInstance(document.getElementById('previewModal')).hide();
        validateAndSubmit();
    });
    
    // Dosya yükleme işlevi
    setupFileUpload();
    
    // Mobil menü toggle işlevi
    setupMobileMenu();
    
    // Taslak olarak kaydet butonu
    document.getElementById('save-draft-btn').addEventListener('click', function() {
        saveDraft();
    });
    
    // Doküman görüntüleyici entegrasyonu
    if (document.getElementById('documentViewerContainer')) {
        initDocumentViewer();
    }
});

// Yıldız derecelendirme sistemini kurar
function setupRatingStars() {
    document.querySelectorAll('.rating-stars .bi').forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.getAttribute('data-rating');
            const starsContainer = this.closest('.rating-stars');
            const criteriaId = starsContainer.closest('.card').querySelector('.card-title').textContent.trim().split('.')[0];
            
            // Tüm yıldızları sıfırla
            starsContainer.querySelectorAll('.bi').forEach(s => {
                s.classList.remove('bi-star-fill');
                s.classList.add('bi-star');
            });
            
            // Seçilen yıldıza kadar doldur
            for (let i = 1; i <= rating; i++) {
                const starToFill = starsContainer.querySelector(`.bi[data-rating="${i}"]`);
                starToFill.classList.remove('bi-star');
                starToFill.classList.add('bi-star-fill');
            }
            
            // Puanı tabloya yaz
            const scoreId = getScoreIdFromCriteriaNumber(criteriaId);
            document.getElementById(scoreId).textContent = rating;
            
            // Ortalama puanı güncelle
            updateAverageScore();
        });
        
        // Hover efekti
        star.addEventListener('mouseenter', function() {
            const rating = this.getAttribute('data-rating');
            const starsContainer = this.closest('.rating-stars');
            
            starsContainer.querySelectorAll('.bi').forEach(s => {
                const starRating = s.getAttribute('data-rating');
                if (starRating <= rating) {
                    s.classList.add('text-warning');
                }
            });
        });
        
        star.addEventListener('mouseleave', function() {
            const starsContainer = this.closest('.rating-stars');
            starsContainer.querySelectorAll('.bi').forEach(s => {
                s.classList.remove('text-warning');
            });
        });
    });
}

// Çıkar çatışması checkboxunu ayarlar
function setupConflictCheckbox() {
    const conflictCheck = document.getElementById('conflict-check');
    const conflictContainer = document.getElementById('conflict-description-container');
    
    conflictCheck.addEventListener('change', function() {
        if (!this.checked) {
            conflictContainer.classList.remove('d-none');
        } else {
            conflictContainer.classList.add('d-none');
        }
    });
    
    // Çıkar çatışması bildir butonu
    document.getElementById('report-conflict-btn').addEventListener('click', function() {
        const description = document.getElementById('conflict-description').value.trim();
        
        if (description === '') {
            alert('Lütfen çıkar çatışmasını açıklayın.');
            return;
        }
        
        // Gerçek uygulamada burada AJAX ile bildirim gönderilir
        alert('Çıkar çatışması editöre bildirildi. Değerlendirme süreciniz durduruldu.');
        window.location.href = 'hakem-panel.html';
    });
}

// Şablon butonlarını ayarlar
function setupTemplateButtons() {
    document.getElementById('add-template').addEventListener('click', function() {
        const templateModal = new bootstrap.Modal(document.getElementById('templateModal'));
        templateModal.show();
    });
    
    // Şablon seçimi olay dinleyicisi
    document.querySelectorAll('[data-template]').forEach(button => {
        button.addEventListener('click', function() {
            const templateType = this.getAttribute('data-template');
            const authorComments = document.getElementById('author-comments');
            
            // Template içeriğini belirle
            let templateContent = '';
            
            switch(templateType) {
                case 'methodology':
                    templateContent = "Makale metodolojisinde bazı iyileştirmeler yapılmalıdır:\n\n1. Örneklem seçimi ve büyüklüğü daha net açıklanmalıdır.\n2. Kullanılan veri toplama araçlarının geçerlilik ve güvenilirliklerine dair bilgiler eklenmelidir.\n3. Veri analiz yöntemleri seçimi için gerekçeler sunulmalıdır.";
                    break;
                case 'literature':
                    templateContent = "Literatür taraması şu yönlerden genişletilebilir:\n\n1. Son 3-5 yıldaki güncel çalışmalar dahil edilmelidir.\n2. Konuya farklı bakış açıları sunan çalışmalar eklenmelidir.\n3. Uluslararası literatürden daha fazla kaynak eklenmelidir.";
                    break;
                case 'structure':
                    templateContent = "Makalenin yapısal organizasyonu şu şekilde iyileştirilebilir:\n\n1. Giriş bölümü araştırma sorusunu daha net ortaya koymalıdır.\n2. Bulgular bölümü alt başlıklar halinde düzenlenerek daha anlaşılır hale getirilebilir.\n3. Tartışma bölümünde literatürle ilişkilendirme güçlendirilmelidir.";
                    break;
                case 'positive':
                    templateContent = "Makale genel olarak iyi hazırlanmış ve alan yazına değerli katkılar sunmaktadır. Özellikle aşağıdaki yönleri takdir edilmelidir:\n\n1. Güncel ve önemli bir konuyu ele alması\n2. Metodolojinin titizlikle uygulanması\n3. Bulguların açık ve anlaşılır bir şekilde sunulması\n4. Tartışma bölümünün kapsamlı ve derinlemesine olması";
                    break;
            }
            
            // Mevcut içeriğin sonuna ekle
            authorComments.value = authorComments.value ? authorComments.value + "\n\n" + templateContent : templateContent;
            
            // Modal'ı kapat
            bootstrap.Modal.getInstance(document.getElementById('templateModal')).hide();
        });
    });
}

// Vurgu butonunu ayarlar
function setupHighlightButton() {
    document.getElementById('add-highlight').addEventListener('click', function() {
        const authorComments = document.getElementById('author-comments');
        const selectionStart = authorComments.selectionStart;
        const selectionEnd = authorComments.selectionEnd;
        
        if (selectionStart !== selectionEnd) {
            const selectedText = authorComments.value.substring(selectionStart, selectionEnd);
            const highlightedText = `[ÖNEMLİ: ${selectedText}]`;
            
            const newValue = authorComments.value.substring(0, selectionStart) + 
                            highlightedText + 
                            authorComments.value.substring(selectionEnd);
                            
            authorComments.value = newValue;
        }
    });
}

// Önizleme butonunu ayarlar
function setupPreviewButton() {
    document.getElementById('preview-btn').addEventListener('click', function() {
        const previewContent = document.getElementById('preview-content');
        let content = '<div class="review-preview">';
        
        // Makale bilgileri
        content += `<h4 class="mb-3">Makale: ${document.getElementById('article-title').textContent}</h4>`;
        content += `<p class="text-muted mb-4">ID: ${document.getElementById('article-id').textContent} | Değerlendirme Tarihi: ${new Date().toLocaleDateString()}</p>`;
        
        // Değerlendirme Özeti
        content += '<div class="card mb-4"><div class="card-header bg-light"><h5>Değerlendirme Özeti</h5></div><div class="card-body">';
        content += '<table class="table table-bordered"><thead><tr><th>Kriter</th><th class="text-center">Puan</th><th>Yorum</th></tr></thead><tbody>';
        
        // Kriterleri ekle
        const criteria = [
            {id: '1', name: 'Özgünlük ve Yenilik', scoreId: 'originality-score', commentId: 'originality-comment'},
            {id: '2', name: 'Metodoloji', scoreId: 'methodology-score', commentId: 'methodology-comment'},
            {id: '3', name: 'Sonuçlar ve Tartışma', scoreId: 'results-score', commentId: 'results-comment'},
            {id: '4', name: 'Literatür Taraması', scoreId: 'literature-score', commentId: 'literature-comment'},
            {id: '5', name: 'Yazım ve Organizasyon', scoreId: 'writing-score', commentId: 'writing-comment'},
            {id: '6', name: 'Bilimsel Etki ve Önemi', scoreId: 'impact-score', commentId: 'impact-comment'}
        ];
        
        criteria.forEach(criterion => {
            const score = document.getElementById(criterion.scoreId).textContent;
            const comment = document.getElementById(criterion.commentId).value;
            
            content += `<tr>
                <td>${criterion.id}. ${criterion.name}</td>
                <td class="text-center">${score}</td>
                <td>${comment || '<em>Yorum yok</em>'}</td>
            </tr>`;
        });
        
        content += `<tr class="table-primary">
            <td colspan="2" class="fw-bold">Ortalama Puan</td>
            <td class="fw-bold">${document.getElementById('average-score').textContent}</td>
        </tr>`;
        
        content += '</tbody></table></div></div>';
        
        // Genel değerlendirme
        content += '<div class="card mb-4"><div class="card-header bg-light"><h5>Genel Değerlendirme</h5></div><div class="card-body">';
        
        // Editör yorumları
        const editorComments = document.getElementById('editor-comments').value;
        content += `<div class="mb-4">
            <h6 class="fw-bold">Editöre Yorumlar</h6>
            <div class="alert alert-info">
                ${editorComments || '<em>Editöre yorum eklenmemiş</em>'}
            </div>
        </div>`;
        
        // Yazar yorumları
        const authorComments = document.getElementById('author-comments').value;
        content += `<div class="mb-4">
            <h6 class="fw-bold">Yazara Yorumlar</h6>
            <div class="alert alert-light border">
                ${authorComments || '<em>Yazara yorum eklenmemiş</em>'}
            </div>
        </div>`;
        
        // Güçlü ve zayıf yönler
        const strengths = document.getElementById('strengths').value;
        const weaknesses = document.getElementById('weaknesses').value;
        
        content += '<div class="row mb-4">';
        content += `<div class="col-md-6">
            <h6 class="fw-bold">Güçlü Yönler</h6>
            <div class="alert alert-success">
                ${strengths || '<em>Güçlü yönler belirtilmemiş</em>'}
            </div>
        </div>`;
        
        content += `<div class="col-md-6">
            <h6 class="fw-bold">Zayıf Yönler</h6>
            <div class="alert alert-danger">
                ${weaknesses || '<em>Zayıf yönler belirtilmemiş</em>'}
            </div>
        </div>`;
        content += '</div>';
        
        // Karar
        let decision = '';
        if (document.getElementById('decision-accept').checked) decision = 'Kabul';
        else if (document.getElementById('decision-minor').checked) decision = 'Küçük Düzeltmeler ile Kabul';
        else if (document.getElementById('decision-major').checked) decision = 'Büyük Düzeltmeler Gerekli';
        else if (document.getElementById('decision-reject').checked) decision = 'Ret';
        
        content += `<div class="mb-4">
            <h6 class="fw-bold">Karar</h6>
            <div class="alert ${getDecisionClass(decision)}">
                <strong>${decision || 'Karar verilmemiş'}</strong>
            </div>
        </div>`;
        
        content += '</div></div>';
        
        // Ek dosyalar
        const fileList = document.getElementById('file-list');
        if (fileList.children.length > 0) {
            content += '<div class="card mb-4"><div class="card-header bg-light"><h5>Ek Dosyalar</h5></div><div class="card-body">';
            content += '<ul class="list-group">';
            
            Array.from(fileList.children).forEach(file => {
                content += `<li class="list-group-item">${file.textContent}</li>`;
            });
            
            content += '</ul></div></div>';
        }
        
        content += '</div>'; // review-preview div kapanışı
        
        previewContent.innerHTML = content;
        
        const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
        previewModal.show();
    });
}

// Dosya yükleme fonksiyonu
function setupFileUpload() {
    const attachment = document.getElementById('attachment');
    if (attachment) {
        attachment.addEventListener('change', function(e) {
            const fileList = document.getElementById('file-list');
            
            for (let i = 0; i < this.files.length; i++) {
                const file = this.files[i];
                const fileItem = document.createElement('a');
                fileItem.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                fileItem.href = '#';
                
                const fileName = document.createElement('span');
                fileName.textContent = file.name;
                
                const fileSize = document.createElement('span');
                fileSize.className = 'badge bg-primary rounded-pill';
                fileSize.textContent = formatFileSize(file.size);
                
                fileItem.appendChild(fileName);
                fileItem.appendChild(fileSize);
                fileList.appendChild(fileItem);
            }
            
            // Input'u temizle
            this.value = '';
        });
    }
}

// Mobil menüyü ayarlar
function setupMobileMenu() {
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const sidebarContainer = document.querySelector('.sidebar-container');
    
    if (mobileMenuToggle && sidebarContainer) {
        mobileMenuToggle.addEventListener('click', function() {
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
        });
    }
}

// Taslak olarak kaydeder
function saveDraft() {
    // Form verilerini topla
    const formData = collectFormData();
    
    // LocalStorage'a kaydet (gerçek uygulamada AJAX ile sunucuya gönderilir)
    localStorage.setItem(`draft_${document.getElementById('article-id').textContent}`, JSON.stringify(formData));
    
    // Kullanıcıya bildir
    const toast = new bootstrap.Toast(document.getElementById('saveToast'));
    toast.show();
    
    // İlerleme çubuğunu güncelle
    updateProgressBar();
}

// Form verilerini toplar
function collectFormData() {
    const formData = {
        articleId: document.getElementById('article-id').textContent,
        ratings: {},
        comments: {},
        generalComments: {
            editor: document.getElementById('editor-comments').value,
            author: document.getElementById('author-comments').value,
            strengths: document.getElementById('strengths').value,
            weaknesses: document.getElementById('weaknesses').value
        },
        decision: document.querySelector('input[name="decision"]:checked')?.value || '',
        files: Array.from(document.getElementById('file-list').children).map(item => item.querySelector('span').textContent),
        savedAt: new Date().toISOString()
    };
    
    // Kriterleri topla
    const criteria = [
        {id: 'originality', name: 'Özgünlük ve Yenilik'},
        {id: 'methodology', name: 'Metodoloji'},
        {id: 'results', name: 'Sonuçlar ve Tartışma'},
        {id: 'literature', name: 'Literatür Taraması'},
        {id: 'writing', name: 'Yazım ve Organizasyon'},
        {id: 'impact', name: 'Bilimsel Etki ve Önemi'}
    ];
    
    criteria.forEach(criterion => {
        const scoreElement = document.getElementById(`${criterion.id}-score`);
        formData.ratings[criterion.id] = scoreElement.textContent !== '-' ? parseInt(scoreElement.textContent) : null;
        formData.comments[criterion.id] = document.getElementById(`${criterion.id}-comment`).value;
    });
    
    return formData;
}

// İlerleme çubuğunu günceller
function updateProgressBar() {
    // Form verilerini topla
    const formData = collectFormData();
    
    // Tamamlanan alanları say
    let completed = 0;
    let total = 0;
    
    // Puanlamalar
    Object.values(formData.ratings).forEach(rating => {
        total++;
        if (rating !== null) completed++;
    });
    
    // Yorumlar
    Object.values(formData.comments).forEach(comment => {
        total++;
        if (comment) completed++;
    });
    
    // Genel yorumlar
    Object.values(formData.generalComments).forEach(comment => {
        total++;
        if (comment) completed++;
    });
    
    // Karar
    total++;
    if (formData.decision) completed++;
    
    // Yüzde hesapla
    const percentage = Math.round((completed / total) * 100);
    
    // İlerleme çubuğunu güncelle
    const progressBar = document.querySelector('.progress .progress-bar');
    progressBar.style.width = `${percentage}%`;
    progressBar.setAttribute('aria-valuenow', percentage);
    
    // İlerleme yüzdesini güncelle
    document.querySelector('.card-header .badge').textContent = `%${percentage} Tamamlandı`;
}

// Yardımcı fonksiyonlar
function getScoreIdFromCriteriaNumber(criteriaNumber) {
    const criteriaMap = {
        '1': 'originality-score',
        '2': 'methodology-score',
        '3': 'results-score',
        '4': 'literature-score',
        '5': 'writing-score',
        '6': 'impact-score'
    };
    
    return criteriaMap[criteriaNumber] || '';
}

function updateAverageScore() {
    const scores = [
        'originality-score',
        'methodology-score',
        'results-score',
        'literature-score',
        'writing-score',
        'impact-score'
    ].map(id => {
        const scoreElement = document.getElementById(id);
        const scoreText = scoreElement.textContent;
        return scoreText !== '-' ? parseInt(scoreText) : null;
    }).filter(score => score !== null);
    
    if (scores.length > 0) {
        const sum = scores.reduce((total, score) => total + score, 0);
        const average = (sum / scores.length).toFixed(1);
        document.getElementById('average-score').textContent = average;
    } else {
        document.getElementById('average-score').textContent = '-';
    }
}

function validateAndSubmit() {
    // Değerlendirme formunun gerekli alanlarını kontrol et
    const requiredFields = [
        { id: 'conflict-check', type: 'checkbox', message: 'Lütfen çıkar çatışması beyanını onaylayın.' },
        { id: 'editor-comments', type: 'text', message: 'Lütfen editöre yorumlarınızı girin.' },
        { id: 'author-comments', type: 'text', message: 'Lütfen yazara yorumlarınızı girin.' }
    ];
    
    let isValid = true;
    let firstInvalidField = null;
    
    // Tüm gerekli alanları kontrol et
    requiredFields.forEach(field => {
        const element = document.getElementById(field.id);
        let isFieldValid = false;
        
        if (field.type === 'checkbox') {
            isFieldValid = element.checked;
        } else if (field.type === 'text') {
            isFieldValid = element.value.trim() !== '';
        }
        
        if (!isFieldValid) {
            isValid = false;
            
            // İlk geçersiz alanı kaydet
            if (!firstInvalidField) {
                firstInvalidField = element;
            }
            
            // Alanı vurgula
            element.classList.add('is-invalid');
            
            // Hata mesajı varsa ekle
            const feedbackDiv = element.nextElementSibling;
            if (feedbackDiv && feedbackDiv.classList.contains('invalid-feedback')) {
                feedbackDiv.textContent = field.message;
            } else {
                const invalidFeedback = document.createElement('div');
                invalidFeedback.className = 'invalid-feedback';
                invalidFeedback.textContent = field.message;
                element.parentNode.insertBefore(invalidFeedback, element.nextSibling);
            }
        } else {
            // Geçerli ise vurgulamayı kaldır
            element.classList.remove('is-invalid');
            const feedbackDiv = element.nextElementSibling;
            if (feedbackDiv && feedbackDiv.classList.contains('invalid-feedback')) {
                feedbackDiv.remove();
            }
        }
    });
    
    // Karar seçimi kontrolü
    const decisionSelected = document.querySelector('input[name="decision"]:checked');
    if (!decisionSelected) {
        isValid = false;
        const decisionContainer = document.querySelector('input[name="decision"]').closest('.mb-4');
        
        // Hata mesajı ekle
        const errorMsg = document.createElement('div');
        errorMsg.className = 'text-danger mt-2';
        errorMsg.textContent = 'Lütfen bir karar seçin.';
        
        // Varsa önceki hata mesajını kaldır
        const existingError = decisionContainer.querySelector('.text-danger');
        if (existingError) {
            existingError.remove();
        }
        
        decisionContainer.appendChild(errorMsg);
        
        if (!firstInvalidField) {
            firstInvalidField = document.querySelector('input[name="decision"]');
        }
    } else {
        // Hata mesajını kaldır
        const decisionContainer = document.querySelector('input[name="decision"]').closest('.mb-4');
        const existingError = decisionContainer.querySelector('.text-danger');
        if (existingError) {
            existingError.remove();
        }
    }
    
    // Geçerli değilse ilk hatalı alana odaklan
    if (!isValid && firstInvalidField) {
        firstInvalidField.focus();
        return false;
    }
    
    // Tüm kontroller geçildiyse formu gönder
    if (isValid) {
        // Gerçek bir uygulamada burada form verilerini sunucuya göndermek için AJAX kullanılır
        // Burada simülasyon amacıyla başarılı bir gönderim mesajı gösteriyoruz
        alert('Değerlendirmeniz başarıyla gönderildi. Teşekkür ederiz!');
        
        // LocalStorage'dan taslağı temizle
        localStorage.removeItem(`draft_${document.getElementById('article-id').textContent}`);
        
        // Hakem paneline geri dön
        window.location.href = 'hakem-panel.html';
    }
    
    return isValid;
}

function getDecisionClass(decision) {
    switch (decision) {
        case 'Kabul': return 'alert-success';
        case 'Küçük Düzeltmeler ile Kabul': return 'alert-info';
        case 'Büyük Düzeltmeler Gerekli': return 'alert-warning';
        case 'Ret': return 'alert-danger';
        default: return 'alert-secondary';
    }
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Doküman görüntüleyici fonksiyonları
let pdfDoc = null;
let pageNum = 1;
let pageRendering = false;
let pageNumPending = null;
let scale = 1.0;
let canvas = null;
let ctx = null;
let isSplitView = false;

// PDF.js yükleme işlemi
function initDocumentViewer() {
    // Normal görünüm için
    setupDocumentViewer();
    
    // Tam ekran görünüm için
    setupFullscreenViewer();
    
    // Doküman seçici
    document.getElementById('documentSelector').addEventListener('change', function() {
        loadSelectedDocument(this.value);
    });
    
    // Tam ekran doküman seçici
    document.getElementById('fullscreenDocumentSelector').addEventListener('change', function() {
        loadFullscreenDocument(this.value);
    });
    
    // Bölünmüş görünüm toggle
    document.getElementById('toggleViewMode').addEventListener('click', toggleSplitView);
    
    // Tam ekran butonu
    document.getElementById('fullscreenBtn').addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('fullscreenDocumentModal'));
        modal.show();
        
        // Seçili dokümanı tam ekranda yükle
        const selectedDoc = document.getElementById('documentSelector').value;
        document.getElementById('fullscreenDocumentSelector').value = selectedDoc;
        loadFullscreenDocument(selectedDoc);
        
        // Modal başlığını güncelle
        document.getElementById('fullscreenDocName').textContent = selectedDoc;
    });
    
    // Zoom butonları
    document.getElementById('zoomOut').addEventListener('click', function() {
        if (scale > 0.5) {
            scale -= 0.2;
            queueRenderPage(pageNum);
        }
    });
    
    document.getElementById('zoomReset').addEventListener('click', function() {
        scale += 0.2;
        queueRenderPage(pageNum);
    });
    
    // Tam ekran zoom butonları
    document.getElementById('fsZoomOut').addEventListener('click', function() {
        if (scale > 0.5) {
            scale -= 0.2;
            queueRenderPage(pageNum, true);
        }
    });
    
    document.getElementById('fsZoomReset').addEventListener('click', function() {
        scale += 0.2;
        queueRenderPage(pageNum, true);
    });
    
    // Sayfa gezinme butonları
    document.getElementById('prevPage').addEventListener('click', function() {
        if (pageNum <= 1) return;
        pageNum--;
        queueRenderPage(pageNum);
    });
    
    document.getElementById('nextPage').addEventListener('click', function() {
        if (pdfDoc && pageNum >= pdfDoc.numPages) return;
        pageNum++;
        queueRenderPage(pageNum);
    });
    
    // Tam ekran sayfa gezinme butonları
    document.getElementById('fsPrevPage').addEventListener('click', function() {
        if (pageNum <= 1) return;
        pageNum--;
        queueRenderPage(pageNum, true);
    });
    
    document.getElementById('fsNextPage').addEventListener('click', function() {
        if (pdfDoc && pageNum >= pdfDoc.numPages) return;
        pageNum++;
        queueRenderPage(pageNum, true);
    });
    
    // İndirme butonları
    document.getElementById('downloadDoc').addEventListener('click', function() {
        const selectedDoc = document.getElementById('documentSelector').value;
        downloadDocument(selectedDoc);
    });
    
    document.getElementById('fsDownloadDoc').addEventListener('click', function() {
        const selectedDoc = document.getElementById('fullscreenDocumentSelector').value;
        downloadDocument(selectedDoc);
    });
    
    // İlk dokümanı yükle - CORS problemi nedeniyle doğrudan örnek görüntü göster
    showSamplePdfViewer();
}

// PDF görüntüleyiciyi hazırla
function setupDocumentViewer() {
    canvas = document.createElement('canvas');
    ctx = canvas.getContext('2d');
    document.getElementById('documentViewer').innerHTML = '';
    document.getElementById('documentViewer').appendChild(canvas);
}

// Tam ekran PDF görüntüleyiciyi hazırla
function setupFullscreenViewer() {
    const fsCanvas = document.createElement('canvas');
    document.getElementById('fullscreenDocumentViewer').innerHTML = '';
    document.getElementById('fullscreenDocumentViewer').appendChild(fsCanvas);
}

// CORS problemi nedeniyle yerel geliştirme için bir örnek PDF görüntüsü göster
function showSamplePdfViewer() {
    const viewer = document.getElementById('documentViewer');
    viewer.innerHTML = `
        <div class="p-4 bg-light">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Yapay Zeka ve Eğitim</h5>
                </div>
                <div class="card-body">
                    <h6 class="card-subtitle mb-3 text-muted">Doç. Dr. Ahmet Yılmaz, Mehmet Can, Ayşe Demir</h6>
                    
                    <div class="mb-4">
                        <h6 class="fw-bold">Özet</h6>
                        <p>Bu çalışmada, yapay zeka teknolojilerinin eğitim alanında kullanımı ve etkileri incelenmiştir. Özellikle kişiselleştirilmiş öğrenme, otomatik değerlendirme ve eğitimci destekleme sistemleri üzerinde durulmuş, bu teknolojilerin eğitim süreçlerine entegrasyonu için öneriler geliştirilmiştir.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="fw-bold">1. Giriş</h6>
                        <p>Yapay zeka teknolojilerinin hızla gelişmesi ve yaygınlaşması, eğitim alanında da önemli dönüşümlere yol açmaktadır. Günümüzde yapay zeka destekli sistemler, öğrenme süreçlerinin kişiselleştirilmesi, değerlendirme süreçlerinin otomatikleştirilmesi ve eğitimcilere destek sağlanması gibi çeşitli amaçlarla kullanılmaktadır.</p>
                        <p>Bu çalışmada, yapay zekanın eğitim alanındaki mevcut uygulamaları, potansiyel etkileri ve entegrasyonu önündeki zorluklar ele alınmaktadır. Ayrıca, eğitim süreçlerinde yapay zeka teknolojilerinin etkin kullanımı için bir dizi öneri sunulmaktadır.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="fw-bold">2. Literatür Taraması</h6>
                        <p>Son yıllarda yapay zeka ve eğitim alanında yapılan çalışmalar incelendiğinde, bu konuya olan ilginin giderek arttığı görülmektedir (Smith ve Johnson, 2023). Özellikle makine öğrenmesi, doğal dil işleme ve bilgisayarlı görü gibi teknolojilerin eğitim alanında uygulanması, çeşitli araştırmalara konu olmaktadır (Wang vd., 2022).</p>
                        <p>Lee ve Zhang (2024), yapay zeka destekli öğrenme sistemlerinin öğrenci başarısı üzerindeki etkilerini inceledikleri çalışmada, kişiselleştirilmiş öğrenme deneyimlerinin akademik performansı olumlu yönde etkilediğini ortaya koymuşlardır.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="fw-bold">3. Metodoloji</h6>
                        <p>Bu araştırmada, karma yöntem yaklaşımı benimsenmiştir. İlk aşamada, yapay zeka ve eğitim alanındaki literatür sistematik olarak taranmış ve mevcut uygulamalar kategorize edilmiştir. İkinci aşamada, 10 farklı eğitim kurumunda yapay zeka teknolojilerinin kullanımına ilişkin vaka çalışmaları gerçekleştirilmiştir. Son olarak, 150 eğitimci ve 300 öğrenciyle yapılan anketler ve 25 uzmanla gerçekleştirilen derinlemesine görüşmeler yoluyla veriler toplanmıştır.</p>
                    </div>
                    
                    <div>
                        <h6 class="fw-bold">4. Bulgular ve Tartışma</h6>
                        <p>(Dosya önizlemesi için daha fazla içerik buraya eklenecek...)</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Tam ekran görüntüleyici için de benzer içerik
    document.getElementById('fullscreenDocumentViewer').innerHTML = document.getElementById('documentViewer').innerHTML;
    
    // Sayfa bilgisini güncelle
    document.getElementById('currentPage').textContent = '1';
    document.getElementById('totalPages').textContent = '10';
    document.getElementById('fsCurrentPage').textContent = '1';
    document.getElementById('fsTotalPages').textContent = '10';
}

// Seçilen dokümanı yükle
function loadSelectedDocument(filename) {
    if (!filename) return;
    
    // Dosya uzantısını kontrol et
    const extension = filename.split('.').pop().toLowerCase();
    
    // Yükleniyor durumunu göster
    document.getElementById('documentViewer').innerHTML = `
        <div class="document-placeholder d-flex align-items-center justify-content-center p-5">
            <div class="text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Yükleniyor...</span>
                </div>
                <p class="mb-0">Doküman yükleniyor, lütfen bekleyiniz...</p>
            </div>
        </div>
    `;
    
    // Yerel geliştirme ortamında CORS hatası alınacağından örnek görüntü göster
    showSamplePdfViewer();
}

// Tam ekranda doküman yükle
function loadFullscreenDocument(filename) {
    if (!filename) return;
    
    // Dosya uzantısını kontrol et
    const extension = filename.split('.').pop().toLowerCase();
    
    // Yükleniyor durumunu göster
    document.getElementById('fullscreenDocumentViewer').innerHTML = `
        <div class="document-placeholder d-flex align-items-center justify-content-center p-5">
            <div class="text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Yükleniyor...</span>
                </div>
                <p class="mb-0">Doküman yükleniyor, lütfen bekleyiniz...</p>
            </div>
        </div>
    `;
    
    // Yerel geliştirme ortamında CORS hatası alınacağından örnek görüntü göster
    showSamplePdfViewer();
}

// Bölünmüş görünüm modunu aç/kapat
function toggleSplitView() {
    isSplitView = !isSplitView;
    const button = document.getElementById('toggleViewMode');
    const contentWrapper = document.querySelector('.content-area');
    
    // Makale görüntüleyici ve değerlendirme formunu içeren bölümleri grupla
    if (!document.querySelector('.document-section')) {
        // İlk kez bölünmüş görünüm açılıyorsa, DOM yapısını düzenle
        const evaluationForm = document.querySelectorAll('.card:not(#documentViewerContainer):not(.document-section)');
        const documentViewer = document.getElementById('documentViewerContainer').closest('.card');
        
        // Doküman bölümünü oluştur
        const documentSection = document.createElement('div');
        documentSection.className = 'document-section';
        documentSection.appendChild(documentViewer);
        
        // Değerlendirme bölümünü oluştur
        const evaluationSection = document.createElement('div');
        evaluationSection.className = 'evaluation-section';
        evaluationForm.forEach(form => evaluationSection.appendChild(form));
        
        // DOM'a ekle
        contentWrapper.innerHTML = '';
        contentWrapper.appendChild(documentSection);
        contentWrapper.appendChild(evaluationSection);
    }
    
    if (isSplitView) {
        contentWrapper.classList.add('split-view-mode');
        button.innerHTML = '<i class="bi bi-layout-sidebar"></i> Normal Görünüm';
        button.classList.remove('btn-outline-primary');
        button.classList.add('btn-primary');
    } else {
        contentWrapper.classList.remove('split-view-mode');
        button.innerHTML = '<i class="bi bi-layout-split"></i> Bölünmüş Görünüm';
        button.classList.remove('btn-primary');
        button.classList.add('btn-outline-primary');
    }
}






// Makale değerlendirme sayfasındaki şablon entegrasyonu - hakem-makale-detay.js dosyasına eklenecek

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    // Şablon ekle butonu
    const addTemplateBtn = document.getElementById('add-template');
    
    if (addTemplateBtn) {
        addTemplateBtn.addEventListener('click', function() {
            openTemplateSelector();
        });
    }
    
    // Şablon modalındaki şablonlara tıklama olaylarını ekle
    setupTemplateSelectionEvents();
});

// Şablon seçici modalını açma
function openTemplateSelector() {
    // Modal zaten tanımlı olduğunu varsayıyoruz (hakem-makale-detay.html içinde)
    const templateModal = new bootstrap.Modal(document.getElementById('templateModal'));
    templateModal.show();
}

// Şablon seçme olaylarını ayarlama
function setupTemplateSelectionEvents() {
    const templateButtons = document.querySelectorAll('[data-template]');
    
    templateButtons.forEach(button => {
        button.addEventListener('click', function() {
            const templateType = this.getAttribute('data-template');
            insertTemplateContent(templateType);
            
            // Modali kapat
            const modal = bootstrap.Modal.getInstance(document.getElementById('templateModal'));
            if (modal) {
                modal.hide();
            }
        });
    });
}

// Şablon içeriğini editöre ekleme
function insertTemplateContent(templateType) {
    const authorComments = document.getElementById('author-comments