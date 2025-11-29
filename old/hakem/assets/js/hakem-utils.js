// PDF Görüntüleyici için düzeltilmiş kodlar

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
    
    /* Gerçek bir web sunucusunda çalıştığında bu kod aktif edilmelidir
    if (extension === 'pdf') {
        loadPdfDocument(filename);
    } else if (extension === 'docx' || extension === 'doc') {
        loadWordDocument(filename);
    } else if (extension === 'xlsx' || extension === 'xls') {
        loadExcelDocument(filename);
    } else {
        document.getElementById('documentViewer').innerHTML = `
            <div class="alert alert-warning m-4">
                Bu dosya türü doğrudan görüntülenemiyor. Lütfen indirip açınız.
                <br><br>
                <a href="#" onclick="downloadDocument('${filename}'); return false;" class="btn btn-primary">
                    <i class="bi bi-download"></i> İndir
                </a>
            </div>
        `;
    }
    */
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

// PDF dosyasını yükle
function loadPdfDocument(filename, isFullscreen = false) {
    // PDF.js kütüphanesini dinamik olarak yükle
    if (!window.pdfjsLib) {
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js';
        script.onload = function() {
            window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';
            initPdfViewer(filename, isFullscreen);
        };
        document.head.appendChild(script);
    } else {
        initPdfViewer(filename, isFullscreen);
    }
}

// PDF görüntüleyiciyi başlat
function initPdfViewer(filename, isFullscreen = false) {
    pageNum = 1;
    scale = 1.0;
    
    // Web sunucusuna göre göreceli yol kullan (/)
    const url = `assets/files/${filename}`;
    
    // PDF dosyasını yükle
    window.pdfjsLib.getDocument(url).promise.then(function(pdf) {
        pdfDoc = pdf;
        
        // Toplam sayfa sayısını güncelle
        if (isFullscreen) {
            document.getElementById('fsTotalPages').textContent = pdf.numPages;
        } else {
            document.getElementById('totalPages').textContent = pdf.numPages;
        }
        
        // İlk sayfayı render et
        renderPage(pageNum, isFullscreen);
    }).catch(function(error) {
        console.error('PDF yüklenirken hata:', error);
        const viewer = isFullscreen ? 'fullscreenDocumentViewer' : 'documentViewer';
        document.getElementById(viewer).innerHTML = `
            <div class="alert alert-danger m-4">
                PDF dosyası yüklenemedi. Hata: ${error.message}
                <br><br>
                <a href="#" onclick="downloadDocument('${filename}'); return false;" class="btn btn-primary">
                    <i class="bi bi-download"></i> İndir
                </a>
            </div>
        `;
    });
}

// PDF sayfasını render et
function renderPage(num, isFullscreen = false) {
    pageRendering = true;
    
    // Sayfa numarasını güncelle
    if (isFullscreen) {
        document.getElementById('fsCurrentPage').textContent = num;
    } else {
        document.getElementById('currentPage').textContent = num;
    }
    
    // PDF sayfasını al
    pdfDoc.getPage(num).then(function(page) {
        // Canvas için ölçeklendirme
        const viewport = page.getViewport({ scale: scale });
        
        // Canvas hazırlığı
        let targetCanvas;
        if (isFullscreen) {
            targetCanvas = document.getElementById('fullscreenDocumentViewer').querySelector('canvas');
        } else {
            targetCanvas = document.getElementById('documentViewer').querySelector('canvas');
        }
        
        targetCanvas.height = viewport.height;
        targetCanvas.width = viewport.width;
        
        // Render işlemi
        const renderContext = {
            canvasContext: targetCanvas.getContext('2d'),
            viewport: viewport
        };
        
        const renderTask = page.render(renderContext);
        
        // Render tamamlandığında
        renderTask.promise.then(function() {
            pageRendering = false;
            
            // Bekleyen sayfa var mı kontrol et
            if (pageNumPending !== null) {
                renderPage(pageNumPending, isFullscreen);
                pageNumPending = null;
            }
        });
    });
}

// Sayfa render kuyruğu
function queueRenderPage(num, isFullscreen = false) {
    if (pageRendering) {
        pageNumPending = num;
    } else {
        renderPage(num, isFullscreen);
    }
}

// Word dokümanı yükle
function loadWordDocument(filename, isFullscreen = false) {
    // Google Docs Viewer kullanımı - sunucu üzerindeki bir URL gerekiyor
    const viewerId = isFullscreen ? 'fullscreenDocumentViewer' : 'documentViewer';
    
    // Bu yalnızca gerçek bir web sunucusunda çalışır - yerel geliştirme için örnek gösterilecek
    document.getElementById(viewerId).innerHTML = `
        <div class="alert alert-info m-4">
            <h5 class="alert-heading">Word Dosyası Önizlemesi</h5>
            <p>Gerçek bir web sunucusunda, bu alanda Word dökümanı görüntülenecektir.</p>
            <p>Dosya: ${filename}</p>
            <hr>
            <p class="mb-0">Dosyayı indirmek için: 
                <a href="#" onclick="downloadDocument('${filename}'); return false;" class="btn btn-sm btn-primary">
                    <i class="bi bi-download"></i> İndir
                </a>
            </p>
        </div>
    `;
}

// Excel dokümanı yükle
function loadExcelDocument(filename, isFullscreen = false) {
    // Google Docs Viewer kullanımı - sunucu üzerindeki bir URL gerekiyor
    const viewerId = isFullscreen ? 'fullscreenDocumentViewer' : 'documentViewer';
    
    // Bu yalnızca gerçek bir web sunucusunda çalışır - yerel geliştirme için örnek gösterilecek
    document.getElementById(viewerId).innerHTML = `
        <div class="alert alert-info m-4">
            <h5 class="alert-heading">Excel Dosyası Önizlemesi</h5>
            <p>Gerçek bir web sunucusunda, bu alanda Excel tablosu görüntülenecektir.</p>
            <p>Dosya: ${filename}</p>
            <hr>
            <p class="mb-0">Dosyayı indirmek için: 
                <a href="#" onclick="downloadDocument('${filename}'); return false;" class="btn btn-sm btn-primary">
                    <i class="bi bi-download"></i> İndir
                </a>
            </p>
        </div>
    `;
}

// Dokümanı indir
function downloadDocument(filename) {
    alert(`${filename} dosyası indiriliyor... (Gerçek bir uygulamada dosya indirilecektir)`);
    
    // Örnek indirme URL'i - gerçek uygulamada sunucu yoluna göre ayarlanmalı
    const downloadUrl = `assets/files/${filename}`;
    
    // İndirme işlemi (gerçek bir web sunucusunda çalışır)
    // window.open(downloadUrl, '_blank');
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
    
    // PDF sayfası ölçeğini yeniden ayarla
    if (pdfDoc) {
        renderPage(pageNum);
    }
}

// Bu fonksiyonu ana DOMContentLoaded event'ine eklemek için
function setupDocumentViewerIntegration() {
    // DOMContentLoaded içinde çağrılabilecek şekilde hazırla
    if (document.getElementById('documentViewerContainer')) {
        initDocumentViewer();
    }
}