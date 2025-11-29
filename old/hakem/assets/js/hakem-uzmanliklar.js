// Uzmanlık Alanları JavaScript Kodu

document.addEventListener('DOMContentLoaded', function() {
    // Select2 Başlatma
    if ($.fn.select2) {
        $('.select2-multi').select2({
            placeholder: "Seçim yapınız...",
            allowClear: true,
            tags: true
        });
    }

    // Uzmanlık Seviyesi Rengi Belirleme Fonksiyonu
    function getExpertiseLevelColor(level) {
        switch(level) {
            case 'expert':
                return 'success';
            case 'intermediate':
                return 'warning';
            case 'beginner':
                return 'info';
            default:
                return 'secondary';
        }
    }

    // Uzmanlık Alanı Kartı Oluşturma
    function createExpertiseCard(expertise) {
        const levelColor = getExpertiseLevelColor(expertise.level);
        const levelText = expertise.level === 'expert' ? 'Uzman' : 
                        expertise.level === 'intermediate' ? 'Orta' : 'Başlangıç';
        
        const priorityBadge = expertise.priority === 'primary' ? 
            '<span class="badge bg-primary">Birincil</span>' : 
            '<span class="badge bg-secondary">İkincil</span>';
        
        // Alt alanları oluştur
        let subfieldsHTML = '';
        if (expertise.subfields && expertise.subfields.length > 0) {
            expertise.subfields.forEach(subfield => {
                subfieldsHTML += `<span class="tag-badge tag-${levelColor}"><i class="bi bi-tag"></i>${subfield}</span>`;
            });
        }
        
        // Kart HTML'i
        const cardHTML = `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card expertise-card h-100" style="border-left-color: ${expertise.priority === 'primary' ? '#0d6efd' : '#28a745'};">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <h5 class="card-title">${expertise.title}</h5>
                            <div>
                                ${priorityBadge}
                            </div>
                        </div>
                        <p class="card-text small text-muted mb-3">${expertise.description}</p>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small">Uzmanlık Seviyesi</span>
                                <span class="small fw-bold">${levelText}</span>
                            </div>
                            <div class="progress experience-progress">
                                <div class="progress-bar bg-${levelColor}" role="progressbar" style="width: ${expertise.levelPercentage}%;" 
                                    aria-valuenow="${expertise.levelPercentage}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <span class="small">İlgili Alt Alanlar:</span>
                            <div class="tag-container mt-2">
                                ${subfieldsHTML}
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted">${expertise.reviewCount} tamamlanan değerlendirme</span>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary edit-expertise" data-id="${expertise.id}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger delete-expertise" data-id="${expertise.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        return cardHTML;
    }

    // Yeni Uzmanlık Alanı Ekleme
    document.querySelector('#addExpertiseModal .btn-primary').addEventListener('click', function() {
        const title = document.getElementById('expertiseTitle').value;
        const description = document.getElementById('expertiseDescription').value;
        const level = document.getElementById('expertiseLevel').value;
        const priority = document.getElementById('expertisePriority').value;
        const years = document.getElementById('expertiseYears').value;
        const keywords = document.getElementById('expertiseKeywords').value;
        const isWillingToReview = document.getElementById('isWillingToReview').checked;
        
        // Select2 değerlerini al
        let subfields = [];
        if ($.fn.select2) {
            subfields = $('#expertiseSubfields').select2('data').map(item => item.text);
        }
        
        // Temel doğrulama
        if (!title || !level) {
            alert('Lütfen zorunlu alanları doldurun.');
            return;
        }
        
        // Seviye yüzdesini hesapla
        let levelPercentage = 0;
        switch(level) {
            case 'expert':
                levelPercentage = 90;
                break;
            case 'intermediate':
                levelPercentage = 65;
                break;
            case 'beginner':
                levelPercentage = 40;
                break;
        }
        
        // Yeni uzmanlık alanı nesnesi
        const newExpertise = {
            id: 'exp-' + Date.now(),
            title: title,
            description: description,
            level: level,
            levelPercentage: levelPercentage,
            priority: priority,
            subfields: subfields,
            years: years,
            keywords: keywords.split(',').map(k => k.trim()),
            isWillingToReview: isWillingToReview,
            reviewCount: 0
        };
        
        // Kart oluştur ve ekle
        const expertiseCard = createExpertiseCard(newExpertise);
        const container = document.querySelector('#current-expertise .row');
        
        // "Yeni Ekle" kartından önce ekle
        const addNewCard = container.querySelector('.add-expertise-button').closest('.col-md-6');
        addNewCard.insertAdjacentHTML('beforebegin', expertiseCard);
        
        // İstatistikleri güncelle
        updateExpertiseStats();
        
        // Modali kapat
        const modal = bootstrap.Modal.getInstance(document.getElementById('addExpertiseModal'));
        modal.hide();
        
        // Formu temizle
        document.getElementById('expertiseTitle').value = '';
        document.getElementById('expertiseDescription').value = '';
        document.getElementById('expertiseLevel').selectedIndex = 0;
        document.getElementById('expertisePriority').selectedIndex = 0;
        document.getElementById('expertiseYears').value = '5';
        document.getElementById('expertiseKeywords').value = '';
        document.getElementById('isWillingToReview').checked = true;
        
        if ($.fn.select2) {
            $('#expertiseSubfields').val(null).trigger('change');
        }
        
        // Başarılı mesajı
        showToast('Uzmanlık alanı başarıyla eklendi', 'success');
    });

    // Uzmanlık Alanı Düzenleme
    document.querySelectorAll('.edit-expertise').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const expertiseId = this.getAttribute('data-id');
            
            // Gerçek bir uygulamada, burada sunucudan güncel veriyi alırdık
            // Bu örnekte mevcut DOM'dan alıyoruz
            const card = this.closest('.expertise-card');
            const title = card.querySelector('.card-title').textContent;
            const description = card.querySelector('.card-text').textContent;
            const levelBadge = card.querySelector('.progress-bar').classList.contains('bg-success') ? 'expert' : 
                              card.querySelector('.progress-bar').classList.contains('bg-warning') ? 'intermediate' : 'beginner';
            const priorityBadge = card.querySelector('.badge').textContent === 'Birincil' ? 'primary' : 'secondary';
            
            // Düzenleme modalını doldur
            document.getElementById('editExpertiseTitle').value = title;
            document.getElementById('editExpertiseDescription').value = description;
            document.getElementById('editExpertiseLevel').value = levelBadge;
            document.getElementById('editExpertisePriority').value = priorityBadge;
            
            // Düzenleme modalını göster
            const modal = new bootstrap.Modal(document.getElementById('editExpertiseModal'));
            modal.show();
        });
    });

    // Uzmanlık Alanı Silme
    document.querySelectorAll('.delete-expertise').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const expertiseId = this.getAttribute('data-id');
            const card = this.closest('.col-md-6');
            const title = card.querySelector('.card-title').textContent;
            
            // Silme modalını güncelle ve göster
            document.querySelector('#deleteExpertiseModal .modal-body p').textContent = 
                `"${title}" uzmanlık alanını silmek istediğinizden emin misiniz?`;
            
            // Silme butonuna tıklandığında
            document.querySelector('#deleteExpertiseModal .btn-danger').onclick = function() {
                // Kartı kaldır
                card.remove();
                
                // İstatistikleri güncelle
                updateExpertiseStats();
                
                // Modalı kapat
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteExpertiseModal'));
                modal.hide();
                
                // Başarılı mesajı
                showToast('Uzmanlık alanı başarıyla silindi', 'success');
            };
            
            const modal = new bootstrap.Modal(document.getElementById('deleteExpertiseModal'));
            modal.show();
        });
    });

    // İstatistikleri güncelleme
    function updateExpertiseStats() {
        // Toplam uzmanlık alanı
        const totalExpertise = document.querySelectorAll('.expertise-card').length;
        const primaryExpertise = document.querySelectorAll('.badge.bg-primary').length;
        
        // İstatistik değerlerini güncelle
        document.querySelector('.stat-value:nth-child(1)').textContent = totalExpertise;
        document.querySelector('.stat-value:nth-child(2)').textContent = primaryExpertise;
        
        // Profil tamamlanma oranı (basit hesaplama)
        const completionPercentage = Math.min(100, Math.round((totalExpertise / 20) * 100));
        document.querySelector('.stat-value:nth-child(3)').textContent = completionPercentage + '%';
        
        // Grafikleri yeniden oluştur
        updateCharts();
    }

    // Grafikleri güncelle
    function updateCharts() {
        if (typeof Chart === 'undefined') return;
        
        // Mevcut grafikleri yok et
        Chart.getChart('expertiseDistribution')?.destroy();
        Chart.getChart('reviewDistribution')?.destroy();
        Chart.getChart('expertiseRadar')?.destroy();
        
        // Uzmanlık alanlarını topla
        const expertiseTitles = Array.from(document.querySelectorAll('.expertise-card .card-title')).map(el => el.textContent);
        const reviewCounts = Array.from(document.querySelectorAll('.expertise-card .small.text-muted')).map(el => {
            const text = el.textContent;
            return parseInt(text.split(' ')[0]);
        });
        
        // Radar grafiği için seviye yüzdeleri
        const levelPercentages = Array.from(document.querySelectorAll('.expertise-card .progress-bar')).map(el => {
            return parseInt(el.getAttribute('aria-valuenow'));
        });
        
        // Uzmanlık Alanları Dağılımı
        const expertiseCtx = document.getElementById('expertiseDistribution');
        if (expertiseCtx) {
            new Chart(expertiseCtx, {
                type: 'pie',
                data: {
                    labels: expertiseTitles,
                    datasets: [{
                        data: levelPercentages,
                        backgroundColor: [
                            'rgba(13, 110, 253, 0.7)',
                            'rgba(220, 53, 69, 0.7)',
                            'rgba(23, 162, 184, 0.7)',
                            'rgba(255, 193, 7, 0.7)',
                            'rgba(40, 167, 69, 0.7)',
                            'rgba(108, 117, 125, 0.7)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Değerlendirme Dağılımı
        const reviewCtx = document.getElementById('reviewDistribution');
        if (reviewCtx) {
            new Chart(reviewCtx, {
                type: 'bar',
                data: {
                    labels: expertiseTitles,
                    datasets: [{
                        label: 'Tamamlanan Değerlendirmeler',
                        data: reviewCounts,
                        backgroundColor: 'rgba(40, 167, 69, 0.7)',
                        borderColor: 'rgb(40, 167, 69)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        // Uzmanlık Radar Grafiği
        const radarCtx = document.getElementById('expertiseRadar');
        if (radarCtx) {
            new Chart(radarCtx, {
                type: 'radar',
                data: {
                    labels: expertiseTitles,
                    datasets: [{
                        label: 'Uzmanlık Seviyesi',
                        data: levelPercentages,
                        fill: true,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgb(54, 162, 235)',
                        pointBackgroundColor: 'rgb(54, 162, 235)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgb(54, 162, 235)'
                    }]
                },
                options: {
                    elements: {
                        line: {
                            borderWidth: 3
                        }
                    },
                    scales: {
                        r: {
                            angleLines: {
                                display: true
                            },
                            suggestedMin: 0,
                            suggestedMax: 100
                        }
                    }
                }
            });
        }
    }

    // Toast bildirim gösterme
    function showToast(message, type = 'info') {
        // Toast container yoksa oluştur
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }
        
        // Toast HTML
        const toastId = 'toast-' + Date.now();
        const toastHTML = `
            <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-${type} text-white">
                    <strong class="me-auto">Bildirim</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Kapat"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;
        
        // Toast'u ekle
        toastContainer.insertAdjacentHTML('beforeend', toastHTML);
        
        // Toast'u göster
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            delay: 3000
        });
        toast.show();
        
        // Toast'u otomatik olarak kaldır
        toastElement.addEventListener('hidden.bs.toast', function() {
            this.remove();
        });
    }

    // Uzmanlık alanı filtresi
    document.querySelector('.dropdown-menu').addEventListener('click', function(e) {
        if (e.target.classList.contains('dropdown-item')) {
            e.preventDefault();
            
            const filterType = e.target.textContent.trim();
            const cards = document.querySelectorAll('.expertise-card');
            
            cards.forEach(card => {
                const container = card.closest('.col-md-6');
                const level = card.querySelector('.progress-bar').classList.contains('bg-success') ? 'expert' : 
                             card.querySelector('.progress-bar').classList.contains('bg-warning') ? 'intermediate' : 'beginner';
                const priority = card.querySelector('.badge').textContent === 'Birincil' ? 'primary' : 'secondary';
                
                // Filtreleme mantığı
                if (filterType === 'Tüm Alanlar') {
                    container.style.display = '';
                } else if (filterType === 'Sadece Öncelikli Alanlar' && priority === 'primary') {
                    container.style.display = '';
                } else if (filterType === 'Uzman Seviyesi' && level === 'expert') {
                    container.style.display = '';
                } else if (filterType === 'Orta Seviye' && level === 'intermediate') {
                    container.style.display = '';
                } else if (filterType === 'Başlangıç Seviyesi' && level === 'beginner') {
                    container.style.display = '';
                } else {
                    container.style.display = 'none';
                }
            });
        }
    });

    // Uzmanlık alanı arama
    document.querySelector('.input-group input').addEventListener('input', function() {
        const searchText = this.value.toLowerCase();
        const cards = document.querySelectorAll('.expertise-card');
        
        cards.forEach(card => {
            const container = card.closest('.col-md-6');
            const title = card.querySelector('.card-title').textContent.toLowerCase();
            const description = card.querySelector('.card-text').textContent.toLowerCase();
            const tags = Array.from(card.querySelectorAll('.tag-badge')).map(tag => tag.textContent.toLowerCase());
            
            // Arama mantığı
            if (searchText === '' || 
                title.includes(searchText) || 
                description.includes(searchText) || 
                tags.some(tag => tag.includes(searchText))) {
                container.style.display = '';
            } else {
                container.style.display = 'none';
            }
        });
    });

    // Kelime bulutu oluşturma (yalnızca görsel amaçlı)
    function generateWordCloud() {
        // Burada gerçek bir kelime bulutu kütüphanesi kullanılabilir
        // Örnek: wordcloud2.js, d3-cloud, vb.
        console.log('Kelime bulutu oluşturuldu');
    }

    // Dil ve sayfa ayarları
    const languageButtons = document.querySelectorAll('#languageDropdown .dropdown-item');
    languageButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const currentLang = document.querySelector('#languageDropdownBtn span').textContent;
            const newLang = this.querySelector('span').textContent || this.textContent.trim();
            
            // Dil değişimi yapılabilir (gerçek uygulamada AJAX isteği vs.)
            document.querySelector('#languageDropdownBtn span').textContent = newLang;
            
            // Değişiklik bildirimi
            showToast(`Dil ${currentLang}'den ${newLang}'e değiştirildi.`, 'info');
        });
    });

    // Mobil menü toggle
    document.getElementById('mobileMenuToggle').addEventListener('click', function() {
        document.querySelector('.sidebar-container').classList.toggle('show');
    });

    // İlk yükleme
    updateCharts();
    generateWordCloud();
});