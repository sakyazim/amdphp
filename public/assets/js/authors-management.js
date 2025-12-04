/**
 * Yazar Yönetim Sistemi
 * Hakem sistemi (ReviewerManager) pattern'i ile aynı mantık
 *
 * Özellikler:
 * - Yazar ekleme
 * - Yazar listesi gösterme
 * - Yazar silme
 * - Yazar düzenleme
 * - Form validasyonu
 */

class AuthorManager {
    constructor(articleId) {
        this.articleId = articleId;
        this.apiBaseUrl = '/api';
        this.minAuthors = 1;
        this.authors = [];
        this.isLoading = false;
        this.editingAuthorId = null;
    }

    /**
     * Sistemi başlat
     */
    init() {
        // Buton click event
        const submitBtn = document.getElementById('authorSubmitBtn');
        if (submitBtn) {
            submitBtn.addEventListener('click', () => {
                this.addAuthor();
            });
        }

        // Cancel button
        const cancelBtn = document.getElementById('authorCancelBtn');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                this.cancelEdit();
            });
        }

        // Mevcut yazarları yükle
        this.loadAuthors();
    }

    /**
     * Yeni yazar ekle veya güncelle
     */
    async addAuthor() {
        if (this.isLoading) return;

        // Input değerlerini al
        const title = document.getElementById('authorTitle')?.value.trim() || '';
        const firstName = document.getElementById('authorFirstName')?.value.trim() || '';
        const middleName = document.getElementById('authorMiddleName')?.value.trim() || '';
        const lastName = document.getElementById('authorLastName')?.value.trim() || '';
        const phone = document.getElementById('authorPhone')?.value.trim() || '';
        const email1 = document.getElementById('authorEmail1')?.value.trim() || '';
        const email2 = document.getElementById('authorEmail2')?.value.trim() || '';
        const department = document.getElementById('authorDepartment')?.value.trim() || '';
        const institution = document.getElementById('authorInstitution')?.value.trim() || '';
        const country = document.getElementById('authorCountry')?.value.trim() || '';
        const orcidId = document.getElementById('authorOrcidId')?.value.trim() || '';
        const order = document.getElementById('authorOrder')?.value.trim() || '';
        const type = document.getElementById('authorType')?.value.trim() || '';

        // Zorunlu alan kontrolü
        if (!title || !firstName || !lastName || !email1 || !order || !type) {
            this.showError('Lütfen tüm zorunlu alanları doldurun');
            return;
        }

        // Email kontrolü
        if (!this.validateEmail(email1)) {
            this.showError('Geçersiz email formatı');
            return;
        }

        // ORCID kontrolü (eğer girilmişse)
        if (orcidId && !this.validateOrcid(orcidId)) {
            this.showError('Geçersiz ORCID formatı (örnek: 0000-0001-2345-6789)');
            return;
        }

        // Draft mode
        if (this.articleId === 'draft') {
            // Güncelleme modunda mı?
            if (this.editingAuthorId !== null) {
                // Mevcut yazarı bul ve güncelle
                const index = this.authors.findIndex(a => a.id === this.editingAuthorId);
                if (index !== -1) {
                    this.authors[index] = {
                        ...this.authors[index],
                        title: title,
                        firstName: firstName,
                        middleName: middleName,
                        lastName: lastName,
                        phone: phone,
                        email1: email1,
                        email2: email2,
                        department: department,
                        institution: institution,
                        country: country,
                        orcidId: orcidId,
                        order: parseInt(order),
                        type: type
                    };
                    this.showSuccess('Yazar başarıyla güncellendi');
                }
            } else {
                // Yeni yazar ekle
                const author = {
                    id: Date.now(),
                    title: title,
                    firstName: firstName,
                    middleName: middleName,
                    lastName: lastName,
                    phone: phone,
                    email1: email1,
                    email2: email2,
                    department: department,
                    institution: institution,
                    country: country,
                    orcidId: orcidId,
                    order: parseInt(order),
                    type: type
                };

                this.authors.push(author);
                this.showSuccess('Yazar başarıyla eklendi');
            }

            this.renderAuthors();
            this.updateStatus();
            this.updateHiddenInputs();
            this.clearForm();
            return;
        }

        // Normal mode - API'ye gönder
        this.isLoading = true;

        // FormData oluştur
        const formData = new FormData();
        formData.append('title', title);
        formData.append('firstName', firstName);
        formData.append('middleName', middleName);
        formData.append('lastName', lastName);
        formData.append('phone', phone);
        formData.append('email1', email1);
        formData.append('email2', email2);
        formData.append('department', department);
        formData.append('institution', institution);
        formData.append('country', country);
        formData.append('orcidId', orcidId);
        formData.append('order', order);
        formData.append('type', type);

        try {
            let url, method, successMessage;

            // Güncelleme mi ekleme mi?
            if (this.editingAuthorId !== null) {
                url = `${this.apiBaseUrl}/authors/${this.editingAuthorId}`;
                method = 'PUT';
                successMessage = 'Yazar başarıyla güncellendi';
                this.showLoading('Yazar güncelleniyor...');
            } else {
                url = `${this.apiBaseUrl}/articles/${this.articleId}/authors`;
                method = 'POST';
                successMessage = 'Yazar başarıyla eklendi';
                this.showLoading('Yazar ekleniyor...');
            }

            const response = await fetch(url, {
                method: method,
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showSuccess(successMessage);
                this.clearForm();
                await this.loadAuthors();
            } else {
                this.showError(result.error || 'İşlem sırasında hata oluştu');
            }
        } catch (error) {
            console.error('Yazar işlem hatası:', error);
            this.showError('Sunucu hatası oluştu');
        } finally {
            this.isLoading = false;
            this.hideLoading();
        }
    }

    /**
     * Yazar listesini yükle
     */
    async loadAuthors() {
        // Draft mode - boş liste
        if (this.articleId === 'draft') {
            this.authors = [];
            this.renderAuthors();
            this.updateStatus();
            return;
        }

        // Normal mode - API'den yükle
        try {
            const response = await fetch(`${this.apiBaseUrl}/articles/${this.articleId}/authors`);
            const result = await response.json();

            if (result.success) {
                this.authors = result.authors || [];
                this.renderAuthors();
                this.updateStatus();
            } else {
                console.error('Yazar listesi yüklenemedi:', result.error);
            }
        } catch (error) {
            console.error('Yazar listesi yükleme hatası:', error);
        }
    }

    /**
     * Yazar listesini render et
     */
    renderAuthors() {
        const tbody = document.querySelector('#authorsTable tbody');
        if (!tbody) return;

        if (this.authors.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center">
                        <div class="alert alert-info mb-0">
                            <i class="fa fa-info-circle"></i>
                            Henüz yazar eklenmedi.
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        // Sıraya göre sırala
        const sortedAuthors = [...this.authors].sort((a, b) => a.order - b.order);

        let html = '';
        sortedAuthors.forEach((author, index) => {
            const fullName = `${this.getTitleText(author.title)} ${author.firstName} ${author.middleName ? author.middleName + ' ' : ''}${author.lastName}`;
            const typeText = this.getAuthorTypeText(author.type);

            html += `
                <tr>
                    <td class="text-center">${author.order}</td>
                    <td>
                        <strong>${this.escapeHtml(fullName)}</strong><br>
                        <small class="text-muted">
                            ${this.escapeHtml(author.email1)}
                            ${author.department ? ' | ' + this.escapeHtml(author.department) : ''}
                            ${author.institution ? ', ' + this.escapeHtml(author.institution) : ''}
                            ${author.orcidId ? '<br>ORCID: ' + this.escapeHtml(author.orcidId) : ''}
                        </small><br>
                        <span class="badge bg-info">${typeText}</span>
                    </td>
                    <td class="text-center">
                        <button
                            class="btn btn-sm btn-warning me-1"
                            onclick="authorManager.editAuthor(${author.id})"
                            title="Düzenle"
                        >
                            <i class="fas fa-edit"></i>
                        </button>
                        <button
                            class="btn btn-sm btn-danger"
                            onclick="authorManager.deleteAuthor(${author.id})"
                            title="Sil"
                        >
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
    }

    /**
     * Yazar sil
     */
    async deleteAuthor(id) {
        if (!confirm('Bu yazarı silmek istediğinize emin misiniz?')) {
            return;
        }

        // Draft mode - local listeden sil
        if (this.articleId === 'draft') {
            this.authors = this.authors.filter(a => a.id !== id);
            this.renderAuthors();
            this.updateStatus();
            this.updateHiddenInputs();
            this.showSuccess('Yazar silindi');
            return;
        }

        // Normal mode - API'den sil
        this.showLoading('Yazar siliniyor...');

        try {
            const response = await fetch(`${this.apiBaseUrl}/authors/${id}`, {
                method: 'DELETE'
            });

            const result = await response.json();

            if (result.success) {
                this.showSuccess('Yazar başarıyla silindi');
                await this.loadAuthors();
            } else {
                this.showError(result.error || 'Yazar silinemedi');
            }
        } catch (error) {
            console.error('Yazar silme hatası:', error);
            this.showError('Sunucu hatası oluştu');
        } finally {
            this.hideLoading();
        }
    }

    /**
     * Yazar düzenle
     */
    editAuthor(id) {
        const author = this.authors.find(a => a.id === id);
        if (!author) return;

        // Formu doldur
        document.getElementById('authorTitle').value = author.title || '';
        document.getElementById('authorFirstName').value = author.firstName || '';
        document.getElementById('authorMiddleName').value = author.middleName || '';
        document.getElementById('authorLastName').value = author.lastName || '';
        document.getElementById('authorPhone').value = author.phone || '';
        document.getElementById('authorEmail1').value = author.email1 || '';
        document.getElementById('authorEmail2').value = author.email2 || '';
        document.getElementById('authorDepartment').value = author.department || '';
        document.getElementById('authorInstitution').value = author.institution || '';
        document.getElementById('authorCountry').value = author.country || '';
        document.getElementById('authorOrcidId').value = author.orcidId || '';
        document.getElementById('authorOrder').value = author.order || '';
        document.getElementById('authorType').value = author.type || '';

        // UI güncelle
        document.getElementById('authorSubmitBtn').innerHTML = '<i class="fas fa-save me-2"></i>Güncelle';
        document.getElementById('authorFormTitle').textContent = 'Yazarı Düzenle';
        document.getElementById('authorCancelBtn').classList.remove('d-none');

        this.editingAuthorId = id;

        // Forma scroll
        document.getElementById('authorForm').scrollIntoView({ behavior: 'smooth' });
    }

    /**
     * Düzenlemeyi iptal et
     */
    cancelEdit() {
        this.clearForm();
    }

    /**
     * Durum bilgisini güncelle
     */
    updateStatus() {
        const countEl = document.getElementById('authorCount');
        if (countEl) {
            const count = this.authors.length;
            countEl.textContent = `${count} Yazar`;
        }
    }

    /**
     * Yazarları hidden input olarak forma ekle
     */
    updateHiddenInputs() {
        // Önce var olan author input'larını temizle
        const existingInputs = document.querySelectorAll('input[name^="authors["]');
        existingInputs.forEach(input => input.remove());

        // Form elementini bul
        const form = document.getElementById('wizardForm');
        if (!form) {
            console.error('wizardForm bulunamadı');
            return;
        }

        // Her yazar için hidden input'lar oluştur
        this.authors.forEach((author, index) => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = `authors[${index}]`;
            hiddenInput.value = JSON.stringify(author);
            form.appendChild(hiddenInput);
        });

        console.log(`${this.authors.length} yazar hidden input olarak eklendi`);
    }

    /**
     * Formu temizle
     */
    clearForm() {
        document.getElementById('authorTitle').value = '';
        document.getElementById('authorFirstName').value = '';
        document.getElementById('authorMiddleName').value = '';
        document.getElementById('authorLastName').value = '';
        document.getElementById('authorPhone').value = '';
        document.getElementById('authorEmail1').value = '';
        document.getElementById('authorEmail2').value = '';
        document.getElementById('authorDepartment').value = '';
        document.getElementById('authorInstitution').value = '';
        document.getElementById('authorCountry').value = '';
        document.getElementById('authorOrcidId').value = '';
        document.getElementById('authorOrder').value = '';
        document.getElementById('authorType').value = '';

        // UI reset
        document.getElementById('authorSubmitBtn').innerHTML = '<i class="fas fa-plus me-2"></i>Yazar Ekle';
        document.getElementById('authorFormTitle').textContent = 'Yeni Yazar Ekle';
        document.getElementById('authorCancelBtn').classList.add('d-none');

        this.editingAuthorId = null;
    }

    // ============================================
    // VALIDATION METODLARI
    // ============================================

    /**
     * Email formatını kontrol et
     */
    validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    /**
     * ORCID formatını kontrol et
     * Format: 0000-0001-2345-6789
     */
    validateOrcid(orcid) {
        const re = /^\d{4}-\d{4}-\d{4}-\d{3}[\dX]$/;
        return re.test(orcid);
    }

    // ============================================
    // UI HELPER METODLARI
    // ============================================

    /**
     * HTML escape
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Ünvan metni
     */
    getTitleText(titleValue) {
        const titles = {
            'prof': 'Prof. Dr.',
            'assocprof': 'Doç. Dr.',
            'assistprof': 'Dr. Öğr. Üyesi',
            'dr': 'Dr.',
            'res': 'Arş. Gör.',
            'other': ''
        };
        return titles[titleValue] || '';
    }

    /**
     * Yazar tipi metni
     */
    getAuthorTypeText(typeValue) {
        const types = {
            'primary': 'Birincil Yazar',
            'corresponding': 'Sorumlu Yazar',
            'contributor': 'Katkıda Bulunan'
        };
        return types[typeValue] || '';
    }

    /**
     * Başarı mesajı göster
     */
    showSuccess(message) {
        alert(message); // Basit versiyon - Bootstrap toast ile değiştirilebilir
    }

    /**
     * Hata mesajı göster
     */
    showError(message) {
        alert('Hata: ' + message);
    }

    /**
     * Loading göster
     */
    showLoading(message = 'Yükleniyor...') {
        const btn = document.getElementById('authorSubmitBtn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<i class="fa fa-spinner fa-spin"></i> ${message}`;
        }
    }

    /**
     * Loading gizle
     */
    hideLoading() {
        const btn = document.getElementById('authorSubmitBtn');
        if (btn) {
            btn.disabled = false;
            if (this.editingAuthorId) {
                btn.innerHTML = '<i class="fas fa-save me-2"></i>Güncelle';
            } else {
                btn.innerHTML = '<i class="fas fa-plus me-2"></i>Yazar Ekle';
            }
        }
    }
}

// Global değişken
let authorManager = null;

// Sayfa yüklendiğinde başlat
document.addEventListener('DOMContentLoaded', () => {
    // Article ID'yi formdan veya data attribute'dan al
    const articleIdInput = document.querySelector('[name="article_id"]');
    const articleIdData = document.querySelector('[data-article-id]');

    const articleId = articleIdInput?.value || articleIdData?.dataset.articleId;

    if (articleId) {
        // Varolan makale - API kullan
        authorManager = new AuthorManager(articleId);
        authorManager.init();
        console.log('✓ Yazar yönetim sistemi başlatıldı (Article ID: ' + articleId + ')');
    } else {
        // Yeni makale - local storage kullan
        authorManager = new AuthorManager('draft');
        authorManager.init();
        console.log('✓ Yazar yönetim sistemi başlatıldı (Draft mode - local storage)');
    }
});
