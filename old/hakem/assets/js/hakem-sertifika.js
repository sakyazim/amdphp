// Değerlendirme özeti ve sertifika modalı için JavaScript

// Modal içeriğini dolduran fonksiyon
function fillModalContent(modal, data) {
  if (!modal || !data) return;
  
  // Temel bilgileri doldur
  Object.keys(data).forEach(selector => {
    const element = modal.querySelector(selector);
    if (element) {
      element.textContent = data[selector];
    }
  });
  
  // Özel alanlar için (varsa)
  if (data.recommendation) {
    const badge = modal.querySelector('.badge.fs-6');
    if (badge) {
      badge.textContent = data.recommendation;
      
      // Badge rengini ayarla
      badge.className = 'badge fs-6';
      if (data.recommendation === 'Kabul') {
        badge.classList.add('bg-success');
      } else if (data.recommendation === 'Düzeltme') {
        badge.classList.add('bg-warning', 'text-dark');
      } else if (data.recommendation === 'Red') {
        badge.classList.add('bg-danger');
      }
    }
  }
}

// Sertifikanın resim olarak indirilmesi
function downloadCertificateAsImage() {
  // HTML2Canvas kütüphanesini kullanarak
  const certificateContainer = document.getElementById('certificate-container');
  
  html2canvas(certificateContainer).then(canvas => {
    // Canvas'ı resme dönüştür
    const image = canvas.toDataURL('image/png');
    
    // İndirme bağlantısı oluştur
    const downloadLink = document.createElement('a');
    downloadLink.href = image;
    downloadLink.download = 'değerlendirme_sertifikası.png';
    
    // Tıklama olayını tetikle ve indir
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
  });
}

// Sertifikanın PDF olarak indirilmesi
function downloadCertificateAsPDF() {
  // HTML2Canvas ve jsPDF kütüphanelerini kullanarak
  const certificateContainer = document.getElementById('certificate-container');
  
  html2canvas(certificateContainer).then(canvas => {
    const imgData = canvas.toDataURL('image/png');
    const pdf = new jsPDF('p', 'mm', 'a4');
    
    // PDF sayfa boyutları
    const pdfWidth = pdf.internal.pageSize.getWidth();
    const pdfHeight = pdf.internal.pageSize.getHeight();
    
    // Canvas oranlarını koru
    const canvasWidth = canvas.width;
    const canvasHeight = canvas.height;
    const ratio = Math.min(pdfWidth / canvasWidth, pdfHeight / canvasHeight);
    
    // PDF'e resmi ekle
    const imgWidth = canvasWidth * ratio;
    const imgHeight = canvasHeight * ratio;
    
    pdf.addImage(imgData, 'PNG', (pdfWidth - imgWidth) / 2, 20, imgWidth, imgHeight);
    pdf.save('değerlendirme_sertifikası.pdf');
  });
}

// Değerlendirme süreç çizelgesinin oluşturulması
function createReviewTimeline(element, steps) {
  if (!element || !steps || !steps.length) return;
  
  // Mevcut içeriği temizle
  element.innerHTML = '';
  
  // Liste oluştur
  const ul = document.createElement('ul');
  ul.className = 'list-group list-group-flush';
  
  // Adımları ekle
  steps.forEach(step => {
    const li = document.createElement('li');
    li.className = 'list-group-item d-flex justify-content-between align-items-center';
    
    // Sol taraf - adım açıklaması
    const div = document.createElement('div');
    
    // İkon ekle
    const icon = document.createElement('i');
    if (step.completed) {
      icon.className = 'bi bi-check-circle-fill text-success me-2';
    } else {
      icon.className = 'bi bi-circle text-secondary me-2';
    }
    div.appendChild(icon);
    
    // Adım metnini ekle
    div.appendChild(document.createTextNode(step.label));
    li.appendChild(div);
    
    // Sağ taraf - tarih veya durum
    const span = document.createElement('span');
    if (step.completed) {
      span.className = 'badge bg-light text-dark';
      span.textContent = step.date;
    } else {
      span.className = 'badge bg-secondary';
      span.textContent = 'Bekliyor';
    }
    li.appendChild(span);
    
    ul.appendChild(li);
  });
  
  element.appendChild(ul);
}

// Dosya indirme fonksiyonu
function downloadReviewAsPDF(reviewId, reviewTitle) {
  // PDF dosyasını oluştur ve indir
  const pdf = new jsPDF();
  
  // Başlık ekle
  pdf.setFontSize(16);
  pdf.text('Değerlendirme Raporu', 105, 15, { align: 'center' });
  
  // Makale bilgileri
  pdf.setFontSize(12);
  pdf.text(`Makale ID: ${reviewId}`, 20, 30);
  pdf.text(`Başlık: ${reviewTitle}`, 20, 40);
  pdf.text(`Tarih: ${new Date().toLocaleDateString()}`, 20, 50);
  
  // İçerik ekle
  pdf.setFontSize(10);
  pdf.text('Bu belge, değerlendirme raporunuzun resmi bir kopyasıdır.', 20, 70);
  
  // PDF'i indir
  pdf.save(`değerlendirme_${reviewId}.pdf`);
}

// Farklı değerlendirme türleri için filtreleme
function filterReviews(filterType) {
  // Tüm satırları al
  const rows = document.querySelectorAll('.completed-reviews-section tbody tr.expandable-row');
  
  rows.forEach(row => {
    // İlgili değerlendirme türü ve editör yorumunu bul
    const recommendation = row.querySelector('.badge').textContent.trim();
    const editorComment = row.querySelector('td:nth-child(6) .badge').textContent.trim();
    
    // Filtrelemeyi uygula
    if (filterType === 'all') {
      row.style.display = '';
      row.nextElementSibling.style.display = ''; // Detay satırı
    } else if (filterType === 'accept' && recommendation === 'Kabul') {
      row.style.display = '';
      row.nextElementSibling.style.display = '';
    } else if (filterType === 'revision' && recommendation === 'Düzeltme') {
      row.style.display = '';
      row.nextElementSibling.style.display = '';
    } else if (filterType === 'reject' && recommendation === 'Red') {
      row.style.display = '';
      row.nextElementSibling.style.display = '';
    } else if (filterType === 'thanks' && editorComment === 'Teşekkür') {
      row.style.display = '';
      row.nextElementSibling.style.display = '';
    } else if (filterType === 'comment' && editorComment === 'Yorum Yapıldı') {
      row.style.display = '';
      row.nextElementSibling.style.display = '';
    } else {
      row.style.display = 'none';
      row.nextElementSibling.style.display = 'none';
    }
  });
}

// Makale araması
function searchReviews(searchText) {
  if (!searchText) {
    // Arama metni yoksa tüm satırları göster
    filterReviews('all');
    return;
  }
  
  // Küçük harfe çevir
  searchText = searchText.toLowerCase();
  
  // Tüm satırları al
  const rows = document.querySelectorAll('.completed-reviews-section tbody tr.expandable-row');
  
  rows.forEach(row => {
    // Makale ID ve başlığını al
    const id = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
    const title = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
    
    // Arama metninin ID veya başlıkta olup olmadığını kontrol et
    if (id.includes(searchText) || title.includes(searchText)) {
      row.style.display = '';
      row.nextElementSibling.style.display = '';
    } else {
      row.style.display = 'none';
      row.nextElementSibling.style.display = 'none';
    }
  });
}

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
  // Kabul edilen değerlendirme modalı için olay dinleyicisi
  document.querySelectorAll('[data-bs-target="#reviewSummaryModal"]').forEach(button => {
    button.addEventListener('click', function() {
      const modal = document.querySelector('#reviewSummaryModal');
      
      // Modal verilerini hazırla
      const data = {
        '#reviewArticleId': this.getAttribute('data-article-id'),
        '#reviewArticleTitle': this.getAttribute('data-article-title'),
        '#reviewDate': this.getAttribute('data-article-date'),
        recommendation: this.getAttribute('data-article-recommendation')
      };
      
      // Modalı doldur
      fillModalContent(modal, data);
    });
  });
  
  // Düzeltme özeti modalı için olay dinleyicisi
  document.querySelectorAll('[data-bs-target="#revisionSummaryModal"]').forEach(button => {
    button.addEventListener('click', function() {
      const modal = document.querySelector('#revisionSummaryModal');
      
      // Modal verilerini hazırla
      const data = {
        '#revisionArticleId': this.getAttribute('data-article-id'),
        '#revisionArticleTitle': this.getAttribute('data-article-title'),
        '#revisionDate': this.getAttribute('data-article-date'),
        recommendation: this.getAttribute('data-article-recommendation')
      };
      
      // Modalı doldur
      fillModalContent(modal, data);
    });
  });
  
  // Red özeti modalı için olay dinleyicisi
  document.querySelectorAll('[data-bs-target="#rejectionSummaryModal"]').forEach(button => {
    button.addEventListener('click', function() {
      const modal = document.querySelector('#rejectionSummaryModal');
      
      // Modal verilerini hazırla
      const data = {
        '#rejectionArticleId': this.getAttribute('data-article-id'),
        '#rejectionArticleTitle': this.getAttribute('data-article-title'),
        '#rejectionDate': this.getAttribute('data-article-date'),
        recommendation: this.getAttribute('data-article-recommendation')
      };
      
      // Modalı doldur
      fillModalContent(modal, data);
    });
  });
  
  // Sertifika modalı için olay dinleyicisi
  document.querySelectorAll('[data-bs-target="#certificateModal"]').forEach(button => {
    button.addEventListener('click', function() {
      const modal = document.querySelector('#certificateModal');
      
      // Modal verilerini hazırla
      const data = {
        '#certificateArticleTitle': this.getAttribute('data-article-title'),
        '#certificateDate': this.getAttribute('data-review-date'),
        '#certificateId': 'CERT-' + this.getAttribute('data-article-id').substring(4)
      };
      
      // Modalı doldur
      fillModalContent(modal, data);
    });
  });
  
  // Sertifika indirme butonları için olay dinleyicileri
  const imgBtn = document.querySelector('#certificateModal .btn-outline-success');
  if (imgBtn) {
    imgBtn.addEventListener('click', downloadCertificateAsImage);
  }
  
  const pdfBtn = document.querySelector('#certificateModal .btn-primary');
  if (pdfBtn) {
    pdfBtn.addEventListener('click', downloadCertificateAsPDF);
  }
  
  // Filtre dropdown'ı için olay dinleyicileri
  document.querySelectorAll('.dropdown-menu a.dropdown-item').forEach(item => {
    item.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Filtre türünü belirle
      let filterType = 'all';
      
      if (this.textContent.includes('Kabul')) {
        filterType = 'accept';
      } else if (this.textContent.includes('Düzeltme')) {
        filterType = 'revision';
      } else if (this.textContent.includes('Red')) {
        filterType = 'reject';
      } else if (this.textContent.includes('Teşekkür')) {
        filterType = 'thanks';
      } else if (this.textContent.includes('Yorum Yapıldı')) {
        filterType = 'comment';
      }
      
      // Filtreleme işlemini uygula
      filterReviews(filterType);
    });
  });
  
  // Arama kutusu için olay dinleyicisi
  const searchInput = document.querySelector('.input-group input');
  if (searchInput) {
    searchInput.addEventListener('input', function() {
      searchReviews(this.value);
    });
    
    // Arama butonu için olay dinleyicisi
    const searchButton = document.querySelector('.input-group button');
    if (searchButton) {
      searchButton.addEventListener('click', function() {
        searchReviews(searchInput.value);
      });
    }
  }
  
  // Düzeltme takip çizelgesi oluşturma örneği
  const timelineContainer = document.querySelector('#revisionTrackingModal .list-group');
  if (timelineContainer) {
    // Örnek adımlar
    const steps = [
      { label: 'Değerlendirme Tamamlandı', completed: true, date: '20.02.2025' },
      { label: 'Yazara Düzeltme Talebi Gönderildi', completed: true, date: '21.02.2025' },
      { label: 'Düzeltmeler Yazardan Alındı', completed: true, date: '01.03.2025' },
      { label: 'Yeniden Değerlendirme', completed: false },
      { label: 'Son Karar', completed: false }
    ];
    
    createReviewTimeline(timelineContainer, steps);
  }
});

// Grafiklerin oluşturulması için fonksiyonlar
function createReviewDistributionChart() {
  const ctx = document.getElementById('reviewDistributionChart');
  if (!ctx) return;
  
  return new Chart(ctx, {
    type: 'pie',
    data: {
      labels: ['Kabul', 'Düzeltme', 'Red'],
      datasets: [{
        data: [16, 12, 7],
        backgroundColor: ['rgba(40, 167, 69, 0.8)', 'rgba(255, 193, 7, 0.8)', 'rgba(220, 53, 69, 0.8)'],
        borderColor: ['rgb(40, 167, 69)', 'rgb(255, 193, 7)', 'rgb(220, 53, 69)'],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            usePointStyle: true,
            padding: 15
          }
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const label = context.label || '';
              const value = context.raw || 0;
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percentage = Math.round((value / total) * 100);
              return `${label}: ${value} (${percentage}%)`;
            }
          }
        }
      }
    }
  });
}

function createMonthlyReviewsChart() {
  const ctx = document.getElementById('monthlyReviewsChart');
  if (!ctx) return;
  
  return new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran'],
      datasets: [{
        label: 'Tamamlanan Değerlendirmeler',
        data: [4, 8, 6, 3, 2, 1],
        backgroundColor: 'rgba(0, 123, 255, 0.7)',
        borderColor: 'rgb(0, 123, 255)',
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
      },
      plugins: {
        legend: {
          display: false
        }
      }
    }
  });
}

function createTopicDistributionChart() {
  const ctx = document.getElementById('topicDistributionChart');
  if (!ctx) return;
  
  return new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Eğitim Teknolojileri', 'Yapay Zeka', 'Uzaktan Eğitim', 'Eğitim Psikolojisi'],
      datasets: [{
        data: [8, 5, 4, 1],
        backgroundColor: [
          'rgba(23, 162, 184, 0.8)',
          'rgba(111, 66, 193, 0.8)',
          'rgba(253, 126, 20, 0.8)',
          'rgba(32, 201, 151, 0.8)'
        ],
        borderColor: [
          'rgb(23, 162, 184)',
          'rgb(111, 66, 193)',
          'rgb(253, 126, 20)',
          'rgb(32, 201, 151)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '70%',
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            usePointStyle: true,
            padding: 15
          }
        }
      }
    }
  });
}

// Grafikleri oluştur
document.addEventListener('DOMContentLoaded', function() {
  // Chart.js kütüphanesi yüklüyse grafikleri oluştur
  if (typeof Chart !== 'undefined') {
    createReviewDistributionChart();
    createMonthlyReviewsChart();
    createTopicDistributionChart();
  }
});