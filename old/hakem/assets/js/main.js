 // Mobil Menü Açma/Kapama
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const sidebarContainer = document.querySelector('.sidebar-container');
        
        if(mobileMenuToggle && sidebarContainer) {
            mobileMenuToggle.addEventListener('click', function() {
                sidebarContainer.classList.toggle('show');
            });
            
            // Tıklanan alan dışında menüyü kapat
            document.addEventListener('click', function(event) {
                if (!sidebarContainer.contains(event.target) && !mobileMenuToggle.contains(event.target)) {
                    sidebarContainer.classList.remove('show');
                }
            });
        }
        
        // Modal veri aktarımı
const acceptModal = document.getElementById('acceptModal');
if (acceptModal) {
    acceptModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const articleId = button.getAttribute('data-article-id');
        const articleTitle = button.getAttribute('data-article-title');
        const articleType = button.getAttribute('data-article-type') || 'Özgün Araştırma';
        const articleSubject = button.getAttribute('data-article-subject') || 'Eğitim Teknolojileri';
        const articleLanguage = button.getAttribute('data-article-language') || 'Türkçe';
        const articleKeywords = button.getAttribute('data-article-keywords') || 'eğitim, teknoloji, araştırma';
        const articleSummary = button.getAttribute('data-article-summary') || 
            'Bu çalışma, eğitim teknolojileri alanında yapılan araştırmaları incelemektedir. Özellikle uzaktan eğitim ve yapay zeka uygulamaları üzerine odaklanılmıştır.';
        
        document.getElementById('acceptArticleId').textContent = articleId;
        document.getElementById('acceptArticleTitle').textContent = articleTitle;
        document.getElementById('acceptArticleType').textContent = articleType;
        document.getElementById('acceptArticleSubject').textContent = articleSubject;
        document.getElementById('acceptArticleLanguage').textContent = articleLanguage;
        document.getElementById('acceptArticleKeywords').textContent = articleKeywords;
        document.getElementById('acceptArticleSummary').textContent = articleSummary;
    });
    
    // Modal kapanma davranışını özelleştirme
    acceptModal.addEventListener('click', function(event) {
        if (event.target === acceptModal) {
            // Modal dışına tıklandığında kapanmayacak, sadece hafif bir titreşim efekti
            const modalDialog = acceptModal.querySelector('.modal-dialog');
            modalDialog.classList.add('animate__animated', 'animate__shakeX');
            setTimeout(() => {
                modalDialog.classList.remove('animate__animated', 'animate__shakeX');
            }, 500);
        }
    });
    
            
            // Kabul butonuna tıklandığında
            document.getElementById('confirmAccept').addEventListener('click', function() {
                const acceptTerms = document.getElementById('acceptTerms');
                
                if (!acceptTerms.checked) {
                    alert('Lütfen değerlendirme koşullarını kabul edin.');
                    return;
                }
                
                // Burada backend'e istek gönderilebilir
                const articleId = document.getElementById('acceptArticleId').textContent;
                const note = document.getElementById('acceptNote').value;
                
                // Örnek için konsola yazdırma
                console.log('Davet kabul edildi:', {
                    articleId: articleId,
                    note: note
                });
                
                // Modal'ı kapat ve başarı mesajı göster
                const modal = bootstrap.Modal.getInstance(acceptModal);
                modal.hide();
                
                // Başarılı işlem sonrası bildirim gösterme
                alert('Değerlendirme daveti başarıyla kabul edildi. Makale değerlendirme listenize eklenmiştir.');
                
                // Sayfayı yenile (gerçek uygulamada AJAX ile güncelleme yapılabilir)
                // window.location.reload();
            });
        }
        
        // Red Modal Ayarları
const rejectModal = document.getElementById('rejectModal');
if (rejectModal) {
    rejectModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const articleId = button.getAttribute('data-article-id');
        const articleTitle = button.getAttribute('data-article-title');
        const articleType = button.getAttribute('data-article-type') || 'Özgün Araştırma';
        const articleSubject = button.getAttribute('data-article-subject') || 'Eğitim Teknolojileri';
        const articleLanguage = button.getAttribute('data-article-language') || 'Türkçe';
        const articleKeywords = button.getAttribute('data-article-keywords') || 'eğitim, teknoloji, araştırma';
        const articleSummary = button.getAttribute('data-article-summary') || 
            'Bu çalışma, eğitim teknolojileri alanında yapılan araştırmaları incelemektedir. Özellikle uzaktan eğitim ve yapay zeka uygulamaları üzerine odaklanılmıştır.';
        
        document.getElementById('rejectArticleId').textContent = articleId;
        document.getElementById('rejectArticleTitle').textContent = articleTitle;
        document.getElementById('rejectArticleType').textContent = articleType;
        document.getElementById('rejectArticleSubject').textContent = articleSubject;
        document.getElementById('rejectArticleLanguage').textContent = articleLanguage;
        document.getElementById('rejectArticleKeywords').textContent = articleKeywords;
        document.getElementById('rejectArticleSummary').textContent = articleSummary;
    });
    
    // Modal kapanma davranışını özelleştirme
    rejectModal.addEventListener('click', function(event) {
        if (event.target === rejectModal) {
            // Modal dışına tıklandığında kapanmayacak, sadece hafif bir titreşim efekti
            const modalDialog = rejectModal.querySelector('.modal-dialog');
            modalDialog.classList.add('animate__animated', 'animate__shakeX');
            setTimeout(() => {
                modalDialog.classList.remove('animate__animated', 'animate__shakeX');
            }, 500);
        }
    });
            
            // Red butonuna tıklandığında
            document.getElementById('confirmReject').addEventListener('click', function() {
                const rejectReason = document.getElementById('rejectReason').value;
                const rejectNote = document.getElementById('rejectNote').value;
                
                if (!rejectReason) {
                    alert('Lütfen bir reddetme sebebi seçin.');
                    return;
                }
                
                if (!rejectNote) {
                    alert('Lütfen reddetme sebebinizi detaylandırın.');
                    return;
                }
                
                // Burada backend'e istek gönderilebilir
                const articleId = document.getElementById('rejectArticleId').textContent;
                const reviewerSuggestion = document.getElementById('reviewerSuggestion').value;
                
                // Örnek için konsola yazdırma
                console.log('Davet reddedildi:', {
                    articleId: articleId,
                    reason: rejectReason,
                    note: rejectNote,
                    suggestion: reviewerSuggestion
                });
                
                // Modal'ı kapat ve başarı mesajı göster
                const modal = bootstrap.Modal.getInstance(rejectModal);
                modal.hide();
                
                // Başarılı işlem sonrası bildirim gösterme
                alert('Değerlendirme daveti reddedildi. Geri bildiriminiz için teşekkür ederiz.');
                
                // Sayfayı yenile (gerçek uygulamada AJAX ile güncelleme yapılabilir)
                // window.location.reload();
            });
        }
        
        // Genişletilen satırların simge değişimi
        const expandButtons = document.querySelectorAll('.expand-button');
        expandButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const icon = this.querySelector('i');
                if (icon.classList.contains('bi-chevron-down')) {
                    icon.classList.remove('bi-chevron-down');
                    icon.classList.add('bi-chevron-up');
                } else {
                    icon.classList.remove('bi-chevron-up');
                    icon.classList.add('bi-chevron-down');
                }
            });
        });
        
        // Bootstrap tooltips aktifleştirme
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    });