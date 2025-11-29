// Şablon Yönetimi JavaScript Kodları - hakem-ayarlar.js dosyasına eklenecek

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    // Şablon yönetim fonksiyonlarını çağır
    setupTemplateManagement();
});

// Şablon yönetimi kurulumu
function setupTemplateManagement() {
    // Sekme değiştirme fonksiyonlarını ayarla
    setupTemplateNavigation();
    
    // Şablon modalları ve olaylarını ayarla
    setupTemplateModals();
    
    // Şablon silme işlemlerini ayarla
    setupTemplateDeleteButtons();
    
    // Şablon içe/dışa aktarma işlemlerini ayarla
    setupTemplateImportExport();
}

// Şablon sekmesi navigasyonu
function setupTemplateNavigation() {
    // Sekmelere tıklama olaylarını dinle
    const sectionsNavs = document.querySelectorAll('[data-section="templates-section"]');
    
    sectionsNavs.forEach(nav => {
        nav.addEventListener('click', function(e) {
            e.preventDefault();
            switchToTemplateSection();
        });
    });
    
    // URL'de #templates-section varsa o sekmeyi aç
    if (window.location.hash === '#templates-section') {
        switchToTemplateSection();
    }
}

// Şablon bölümüne geçiş fonksiyonu
function switchToTemplateSection() {
    // Tüm sekmeleri gizle
    const allSections = document.querySelectorAll('.settings-section');
    allSections.forEach(section => {
        section.style.display = 'none';
        section.classList.remove('active');
    });
    
    // Şablon bölümünü göster
    const templateSection = document.getElementById('templates-section');
    if (templateSection) {
        templateSection.style.display = 'block';
        templateSection.classList.add('active');
        
        // Ek olarak URL'yi güncelle
        window.history.replaceState(null, null, '#templates-section');
    }
    
    // Navigasyon linklerini güncelle
    updateNavigationState('templates-section');
}

// Navigasyon durumunu güncelleme fonksiyonu
function updateNavigationState(sectionId) {
    // Mobil navigasyonu güncelle
    const mobileNavButtons = document.querySelectorAll('#mobileSettingsNav [data-section]');
    mobileNavButtons.forEach(btn => {
        if (btn.getAttribute('data-section') === sectionId) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
    
    // Masaüstü navigasyonu güncelle
    const desktopNavLinks = document.querySelectorAll('#desktopSettingsNav [data-section]');
    desktopNavLinks.forEach(link => {
        if (link.getAttribute('data-section') === sectionId) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
}

// Şablon modallarını ayarla
function setupTemplateModals() {
    // Yeni Şablon Ekleme
    const saveNewTemplateBtn = document.getElementById('saveNewTemplateBtn');
    if (saveNewTemplateBtn) {
        saveNewTemplateBtn.addEventListener('click', function() {
            saveNewTemplate();
        });
    }
    
    // Şablon Düzenleme
    const updateTemplateBtn = document.getElementById('updateTemplateBtn');
    if (updateTemplateBtn) {
        updateTemplateBtn.addEventListener('click', function() {
            updateTemplate();
        });
    }
    
    // Şablonu Görüntüleme düğmeleri
    const previewButtons = document.querySelectorAll('.btn-outline-primary:not(#exportTemplatesBtn):not(#importTemplatesBtn)');
    previewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const cardElement = this.closest('.card');
            if (cardElement) {
                previewTemplate(cardElement);
            }
        });
    });
    
    // Şablon Silme Onayı
    const confirmDeleteBtn = document.getElementById('confirmDeleteTemplateBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            deleteSelectedTemplate();
        });
    }
    
    // Kategori Yönetimi
    const saveCategoryBtn = document.getElementById('saveCategoryBtn');
    if (saveCategoryBtn) {
        saveCategoryBtn.addEventListener('click', function() {
            saveCategory();
        });
    }
}

// Yeni şablonu kaydet
function saveNewTemplate() {
    // Form verilerini al
    const title = document.getElementById('templateTitle').value;
    const category = document.getElementById('templateCategory').value;
    const content = document.getElementById('templateContent').value;
    const formatElements = document.getElementsByName('templateFormat');
    let format = 'plain'; // Varsayılan format
    
    // Seçilen formatı belirle
    for (let i = 0; i < formatElements.length; i++) {
        if (formatElements[i].checked) {
            format = formatElements[i].value;
            break;
        }
    }
    
    // Form doğrulama
    if (!title || !category || !content) {
        showToast('Hata', 'Lütfen tüm gerekli alanları doldurun.', 'danger');
        return false;
    }
    
    // Burada şablon kaydetme işlemleri yapılacak
    // Gerçek bir uygulamada AJAX ile sunucuya gönderilir
    
    // Başarılı olduğunu varsayalım
    const modal = bootstrap.Modal.getInstance(document.getElementById('newTemplateModal'));
    modal.hide();
    
    // Temizle
    document.getElementById('newTemplateForm').reset();
    
    // Bildirim göster
    showToast('Başarılı', 'Yeni şablon başarıyla eklendi.', 'success');
    
    // Sayfayı yenile (gerçek uygulamada JavaScript ile dinamik eklenebilir)
    // setTimeout(function() { location.reload(); }, 1500);
}

// Var olan şablonu güncelle
function updateTemplate() {
    // Form verilerini al
    const title = document.getElementById('editTemplateTitle').value;
    const category = document.getElementById('editTemplateCategory').value;
    const content = document.getElementById('editTemplateContent').value;
    const formatElements = document.getElementsByName('editTemplateFormat');
    let format = 'plain'; // Varsayılan format
    
    // Seçilen formatı belirle
    for (let i = 0; i < formatElements.length; i++) {
        if (formatElements[i].checked) {
            format = formatElements[i].value;
            break;
        }
    }
    
    // Form doğrulama
    if (!title || !category || !content) {
        showToast('Hata', 'Lütfen tüm gerekli alanları doldurun.', 'danger');
        return false;
    }
    
    // Burada şablon güncelleme işlemleri yapılacak
    // Gerçek bir uygulamada AJAX ile sunucuya gönderilir
    
    // Başarılı olduğunu varsayalım
    const modal = bootstrap.Modal.getInstance(document.getElementById('editTemplateModal'));
    modal.hide();
    
    // Bildirim göster
    showToast('Başarılı', 'Şablon başarıyla güncellendi.', 'success');
    
    // Sayfayı yenile (gerçek uygulamada JavaScript ile dinamik eklenebilir)
    // setTimeout(function() { location.reload(); }, 1500);
}

// Şablonu önizleme
function previewTemplate(cardElement) {
    // Şablon bilgilerini al
    const title = cardElement.querySelector('.card-title').textContent;
    const category = cardElement.querySelector('.badge').textContent;
    const content = cardElement.querySelector('.card-body').innerHTML;
    
    // Önizleme modalını doldur
    document.getElementById('previewTemplateTitle').textContent = title;
    document.getElementById('previewTemplateContent').innerHTML = content;
    
    // Kategori etiketini güncelle
    const categoryBadge = document.querySelector('#previewTemplateModal .badge.bg-primary');
    if (categoryBadge) {
        categoryBadge.textContent = category;
    }
    
    // Önizleme modalını göster
    const previewModal = new bootstrap.Modal(document.getElementById('previewTemplateModal'));
    previewModal.show();
}

// Şablon silme butonlarını ayarla
function setupTemplateDeleteButtons() {
    const deleteButtons = document.querySelectorAll('.template-delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const cardElement = this.closest('.card');
            if (cardElement) {
                prepareDeleteTemplate(cardElement);
            }
        });
    });
}

// Şablon silme işlemi için hazırlık
function prepareDeleteTemplate(cardElement) {
    // Global değişkene silme işlemi için şablon elementini ata
    window.templateToDelete = cardElement;
    
    // Şablon adını al ve göster
    const title = cardElement.querySelector('.card-title').textContent;
    document.getElementById('deleteTemplateName').textContent = title;
    
    // Silme onay modalını aç
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteTemplateModal'));
    deleteModal.show();
}

// Seçilen şablonu sil
function deleteSelectedTemplate() {
    // Global değişkenden şablon elementini al
    const templateElement = window.templateToDelete;
    
    if (!templateElement) {
        return;
    }
    
    // Gerçek bir uygulamada AJAX ile silme işlemi yapılır
    
    // Başarılı olduğunu varsayalım
    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteTemplateModal'));
    modal.hide();
    
    // Elementini DOM'dan kaldır (demo amaçlı)
    templateElement.remove();
    
    // Global değişkeni temizle
    window.templateToDelete = null;
    
    // Bildirim göster
    showToast('Başarılı', 'Şablon başarıyla silindi.', 'success');
}

// Kategori kaydet
function saveCategory() {
    // Form verilerini al
    const categoryName = document.getElementById('categoryName').value;
    const categoryColor = document.getElementById('categoryColor').value;
    
    // Doğrulama
    if (!categoryName) {
        alert('Lütfen kategori adını girin.');
        return;
    }
    
    // Gerçek bir uygulamada AJAX ile kategori kaydetme işlemi yapılır
    
    // Başarılı olduğunu varsayalım
    const modal = bootstrap.Modal.getInstance(document.getElementById('categoryModal'));
    modal.hide();
    
    // Formu temizle
    document.getElementById('categoryName').value = '';
    
    // Bildirim göster
    showToast('Başarılı', 'Kategori başarıyla kaydedildi.', 'success');
}

// İçe/Dışa aktarma işlemlerini ayarla
function setupTemplateImportExport() {
    // Dışa aktarma butonu
    const exportBtn = document.getElementById('exportTemplatesBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', exportTemplates);
    }
    
    // İçe aktarma butonu
    const importBtn = document.getElementById('importTemplatesBtn');
    if (importBtn) {
        importBtn.addEventListener('click', function() {
            // Gizli dosya girişini tıkla
            document.getElementById('importTemplatesInput').click();
        });
    }
    
    // Dosya girişi değişikliği
    const importInput = document.getElementById('importTemplatesInput');
    if (importInput) {
        importInput.addEventListener('change', importTemplates);
    }
    
    // Varsayılana sıfırlama
    const resetBtn = document.getElementById('resetTemplatesBtn');
    if (resetBtn) {
        resetBtn.addEventListener('click', resetTemplates);
    }
}

// Şablonları dışa aktar
function exportTemplates() {
    // Gerçek uygulamada şablon verilerini sunucudan alınır
    // Burada örnek veri kullanıyoruz
    const templates = [
        {
            id: 1,
            title: "Metodoloji Eleştirisi",
            category: "Metodoloji",
            content: "Makale metodolojisinde bazı iyileştirmeler yapılmalıdır:\n\n1. Örneklem seçimi ve büyüklüğü daha net açıklanmalıdır.\n2. Kullanılan veri toplama araçlarının geçerlilik ve güvenilirliklerine dair bilgiler eklenmelidir.\n3. Veri analiz yöntemleri seçimi için gerekçeler sunulmalıdır.",
            format: "numbered",
            tags: ["metodoloji", "yöntem", "örneklem"],
            lastUsed: "2025-03-01"
        },
        {
            id: 2,
            title: "Literatür Taraması Eleştirisi",
            category: "Literatür",
            content: "Literatür taraması şu yönlerden genişletilebilir:\n\n1. Son 3-5 yıldaki güncel çalışmalar dahil edilmelidir.\n2. Konuya farklı bakış açıları sunan çalışmalar eklenmelidir.\n3. Uluslararası literatürden daha fazla kaynak eklenmelidir.",
            format: "numbered",
            tags: ["literatür", "kaynakça"],
            lastUsed: "2025-02-25"
        }
    ];
    
    // JSON dosyasına dönüştürme
    const dataStr = JSON.stringify(templates, null, 2);
    const dataUri = "data:application/json;charset=utf-8," + encodeURIComponent(dataStr);
    
    // Dosya indirme bağlantısı oluşturma
    const exportFileDefaultName = "degerlendirme_sablonlari.json";
    const linkElement = document.createElement("a");
    linkElement.setAttribute("href", dataUri);
    linkElement.setAttribute("download", exportFileDefaultName);
    
    // Bağlantıyı tıkla ve temizle
    document.body.appendChild(linkElement);
    linkElement.click();
    document.body.removeChild(linkElement);
    
    // Bildirim göster
    showToast('Başarılı', 'Şablonlar başarıyla dışa aktarıldı.', 'success');
}

// Şablonları içe aktar
function importTemplates(event) {
    const file = event.target.files[0];
    
    // Dosya kontrolü
    if (!file) {
        return;
    }
    
    // JSON dosyası mı kontrol et
    if (file.type !== "application/json" && !file.name.endsWith('.json')) {
        showToast('Hata', 'Lütfen geçerli bir JSON dosyası seçin.', 'danger');
        event.target.value = '';
        return;
    }
    
    // Dosyayı oku
    const reader = new FileReader();
    
    reader.onload = function(e) {
        try {
            const templates = JSON.parse(e.target.result);
            
            // İçe aktarılan şablonları doğrula
            if (!Array.isArray(templates) || templates.length === 0) {
                throw new Error('Geçerli şablon verisi bulunamadı.');
            }
            
            // Gerçek uygulamada burada AJAX ile şablonlar sunucuya gönderilir
            
            // Bildirim göster
            showToast('Başarılı', `${templates.length} şablon başarıyla içe aktarıldı.`, 'success');
            
            // Sayfayı yenile (gerçek uygulamada JavaScript ile dinamik eklenebilir)
            // setTimeout(function() { location.reload(); }, 1500);
            
        } catch (error) {
            showToast('Hata', 'Şablon dosyası okunamadı: ' + error.message, 'danger');
            console.error('Şablon içe aktarma hatası:', error);
        }
    };
    
    reader.readAsText(file);
    
    // Dosya girişini sıfırla
    event.target.value = '';
}

// Şablonları varsayılana sıfırla
function resetTemplates() {
    // Onay iste
    if (!confirm('Tüm şablonlarınız varsayılan şablonlarla değiştirilecek. Bu işlem geri alınamaz. Devam etmek istiyor musunuz?')) {
        return;
    }
    
    // Gerçek uygulamada AJAX ile sıfırlama işlemi yapılır
    
    // Bildirim göster
    showToast('Başarılı', 'Tüm şablonlar varsayılana sıfırlandı.', 'success');
    
    // Sayfayı yenile
    // setTimeout(function() { location.reload(); }, 1500);
}

// Toast bildirim göster
function showToast(title, message, type) {
    // Toast container kontrol et/oluştur
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    // Toast element oluştur
    const toastId = 'toast-' + Date.now();
    const toastElement = document.createElement('div');
    toastElement.className = `toast align-items-center border-0 bg-${type}`;
    toastElement.id = toastId;
    toastElement.setAttribute('role', 'alert');
    toastElement.setAttribute('aria-live', 'assertive');
    toastElement.setAttribute('aria-atomic', 'true');
    
    // Toast içeriği
    const toastContent = `
        <div class="d-flex">
            <div class="toast-body text-white">
                <strong>${title}</strong> ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Kapat"></button>
        </div>
    `;
    
    toastElement.innerHTML = toastContent;
    toastContainer.appendChild(toastElement);
    
    // Toast'u göster
    const toast = new bootstrap.Toast(toastElement, {
        delay: 3000,
        autohide: true
    });
    
    toast.show();
    
    // Kapandığında toast'u kaldır
    toastElement.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}

// Şablon formatına göre içeriği biçimlendir
function formatTemplateContent(content, format) {
    let formattedContent = content.trim();
    
    if (format === 'bullet') {
        // Satırları madde işaretli liste olarak biçimlendir
        const lines = formattedContent.split('\n').filter(line => line.trim() !== '');
        let result = '';
        
        // İlk satır (başlık gibi) madde işareti olmadan
        if (lines.length > 0) {
            result += lines[0] + '\n\n<ul>';
            
            // Diğer satırlar madde işaretli
            for (let i = 1; i < lines.length; i++) {
                result += `\n  <li>${lines[i]}</li>`;
            }
            
            result += '\n</ul>';
        }
        
        formattedContent = result;
    } else if (format === 'numbered') {
        // Satırları numaralı liste olarak biçimlendir
        const lines = formattedContent.split('\n').filter(line => line.trim() !== '');
        let result = '';
        
        // İlk satır (başlık gibi) numara olmadan
        if (lines.length > 0) {
            result += lines[0] + '\n\n<ol>';
            
            // Diğer satırlar numaralı
            for (let i = 1; i < lines.length; i++) {
                result += `\n  <li>${lines[i]}</li>`;
            }
            
            result += '\n</ol>';
        }
        
        formattedContent = result;
    }
    
    return formattedContent;
}