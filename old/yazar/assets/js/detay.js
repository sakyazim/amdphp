/**
 * Makale detay sayfası için JavaScript - Optimize edilmiş
 */

// Sayfa yüklendiğinde çalış
document.addEventListener('DOMContentLoaded', function() {
    // Tüm genişletilmiş detay satırlarına detay butonu ekle
    setupDetailButtons();
    
    // Detay sayfasında URL parametreleri varsa ilgili içerikleri doldur
    if (window.location.pathname.includes('makale-detay.html')) {
        loadArticleDetails();
    }
});

/**
 * Makaleler sayfasındaki detay satırlarına detay butonları ekle
 */
function setupDetailButtons() {
    const detailRows = document.querySelectorAll('.collapse');
    
    detailRows.forEach(detailRow => {
        // Mevcut bir buton bölümü var mı kontrol et
        const cardBody = detailRow.querySelector('.card-body');
        if (cardBody) {
            // Detay ID'sini al (details-1, details-2 gibi)
            const detailId = detailRow.id;
            // Makale ID'sini tablodaki satırdan al
            const articleRow = document.querySelector(`[data-bs-target="#${detailId}"]`);
            if (articleRow) {
                const articleId = articleRow.querySelector('td:nth-child(2)')?.textContent.trim();
                const articleTitle = articleRow.querySelector('td:nth-child(3)')?.textContent.trim();
                
                if (articleId && articleTitle) {
                    // Detay butonunu oluştur
                    const detailButton = document.createElement('div');
                    detailButton.className = 'text-center mt-3';
                    detailButton.innerHTML = `
                        <a href="makale-detay.html?id=${articleId}&title=${encodeURIComponent(articleTitle)}" 
                           class="btn btn-info text-white">
                            <i class="bi bi-search me-1"></i> Detaylı İncele
                        </a>
                    `;
                    
                    // Butonu ekle
                    const buttonSection = cardBody.querySelector('.row:last-child');
                    if (buttonSection) {
                        buttonSection.appendChild(detailButton);
                    } else {
                        // Eğer uygun bir yer bulunamazsa en sona ekle
                        cardBody.appendChild(detailButton);
                    }
                }
            }
        }
    });
}

/**
 * Detay sayfasına yönlendirildiğinde URL parametrelerinden makale bilgisini al
 */
function loadArticleDetails() {
    const urlParams = new URLSearchParams(window.location.search);
    const articleId = urlParams.get('id');
    const articleTitle = urlParams.get('title');
    
    if (articleId && articleTitle) {
        document.getElementById('article-id').textContent = articleId;
        document.getElementById('article-title').textContent = decodeURIComponent(articleTitle);
        document.title = `AMDS - ${decodeURIComponent(articleTitle)}`;
        
        // Durumu belirle
        updateArticleStatus(articleId);
    }
}

/**
 * Makale ID'sine göre doğru durumu ve diğer bilgileri ayarlar
 * @param {string} articleId - Makale ID'si
 */
function updateArticleStatus(articleId) {
    // Örnek statik veri - gerçek uygulamada API yanıtından gelecek
    const articleStatuses = {
        'ART-2025-0103': {
            status: 'Değerlendirmede',
            statusClass: 'bg-warning text-dark',
            date: '12.02.2025',
            type: 'Özgün Araştırma',
            completion: 50
        },
        'ART-2025-0087': {
            status: 'Düzeltme İstendi',
            statusClass: 'bg-danger',
            date: '05.02.2025',
            type: 'Derleme',
            completion: 70
        },
        'ART-2025-0042': {
            status: 'Kabul Edildi',
            statusClass: 'bg-success',
            date: '25.01.2025',
            type: 'Vaka Çalışması',
            completion: 90
        },
        'ART-2024-0198': {
            status: 'Revizyon Sonrası',
            statusClass: 'bg-info text-white',
            date: '20.12.2024',
            type: 'Özgün Araştırma',
            completion: 70
        }
    };
    
    const articleInfo = articleStatuses[articleId] || {
        status: 'Değerlendirmede',
        statusClass: 'bg-warning text-dark',
        date: '01.01.2025',
        type: 'Belirtilmemiş',
        completion: 30
    };
    
    // Durumu güncelle
    const statusElement = document.getElementById('article-status');
    if (statusElement) {
        statusElement.textContent = articleInfo.status;
        statusElement.className = `badge ${articleInfo.statusClass} fs-6`;
    }
    
    // Diğer bilgileri güncelle
    const submissionDate = document.getElementById('submission-date');
    if (submissionDate) submissionDate.textContent = articleInfo.date;
    
    const articleType = document.getElementById('article-type');
    if (articleType) articleType.textContent = articleInfo.type;
    
    // İlerleme çubuğunu güncelle
    const progressBar = document.querySelector('.progress-bar');
    if (progressBar) {
        progressBar.style.width = `${articleInfo.completion}%`;
        progressBar.setAttribute('aria-valuenow', articleInfo.completion);
    }
}