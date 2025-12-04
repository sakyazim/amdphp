/**
 * Hakem Yönetim Sistemi
 * FAZ 5: Hakem Modülü
 *
 * Özellikler:
 * - Hakem ekleme
 * - Hakem listesi gösterme
 * - Hakem silme
 * - Minimum hakem kontrolü (en az 3)
 * - Form validasyonu
 */

class ReviewerManager {
    constructor(articleId) {
        this.articleId = articleId;
        this.apiBaseUrl = '/api';
        this.minReviewers = 3;
        this.reviewers = [];
        this.isLoading = false;
    }

    /**
     * Sistemi başlat
     */
    init() {
        // Buton click event (artık form değil)
        const submitBtn = document.getElementById('reviewer-submit-btn');
        if (submitBtn) {
            submitBtn.addEventListener('click', () => {
                this.addReviewer();
            });
        }

        // Mevcut hakemleri yükle
        this.loadReviewers();
    }

    /**
     * Yeni hakem ekle
     */
    async addReviewer() {
        if (this.isLoading) return;

        // Input değerlerini al
        const ad = document.getElementById('reviewer-ad')?.value.trim() || '';
        const soyad = document.getElementById('reviewer-soyad')?.value.trim() || '';
        const email = document.getElementById('reviewer-email')?.value.trim() || '';
        const kurum = document.getElementById('reviewer-kurum')?.value.trim() || '';
        const uzmanlik_alani = document.getElementById('reviewer-uzmanlik')?.value.trim() || '';
        const ulke = document.getElementById('reviewer-ulke')?.value.trim() || '';
        const orcid = document.getElementById('reviewer-orcid')?.value.trim() || '';
        const notlar = document.getElementById('reviewer-notlar')?.value.trim() || '';

        if (!ad || !soyad || !email || !kurum) {
            this.showError('Lütfen tüm zorunlu alanları doldurun');
            return;
        }

        if (!this.validateEmail(email)) {
            this.showError('Geçersiz email formatı');
            return;
        }

        // ORCID kontrolü (eğer girilmişse)
        if (orcid && !this.validateOrcid(orcid)) {
            this.showError('Geçersiz ORCID formatı (örnek: 0000-0001-2345-6789)');
            return;
        }

        // Draft mode - local array'e ekle
        if (this.articleId === 'draft') {
            const reviewer = {
                id: Date.now(),
                ad: ad,
                soyad: soyad,
                email: email,
                kurum: kurum,
                uzmanlik_alani: uzmanlik_alani,
                ulke: ulke,
                orcid: orcid,
                notlar: notlar
            };

            this.reviewers.push(reviewer);
            this.renderReviewers();
            this.updateStatus();
            this.updateHiddenInputs();
            this.clearForm();
            this.showSuccess('Hakem başarıyla eklendi');
            return;
        }

        // Normal mode - API'ye gönder
        this.isLoading = true;
        this.showLoading('Hakem ekleniyor...');

        // FormData oluştur
        const formData = new FormData();
        formData.append('ad', ad);
        formData.append('soyad', soyad);
        formData.append('email', email);
        formData.append('kurum', kurum);
        formData.append('uzmanlik_alani', uzmanlik_alani);
        formData.append('ulke', ulke);
        formData.append('orcid', orcid);
        formData.append('notlar', notlar);

        try {
            const response = await fetch(`${this.apiBaseUrl}/articles/${this.articleId}/reviewers`, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showSuccess('Hakem başarıyla eklendi');
                form.reset();
                await this.loadReviewers();
            } else {
                this.showError(result.error || 'Hakem eklenirken hata oluştu');
            }
        } catch (error) {
            console.error('Hakem ekleme hatası:', error);
            this.showError('Sunucu hatası oluştu');
        } finally {
            this.isLoading = false;
            this.hideLoading();
        }
    }

    /**
     * Hakem listesini yükle
     */
    async loadReviewers() {
        // Draft mode - boş liste
        if (this.articleId === 'draft') {
            this.reviewers = [];
            this.renderReviewers();
            this.updateStatus();
            return;
        }

        // Normal mode - API'den yükle
        try {
            const response = await fetch(`${this.apiBaseUrl}/articles/${this.articleId}/reviewers`);
            const result = await response.json();

            if (result.success) {
                this.reviewers = result.reviewers || [];
                this.renderReviewers();
                this.updateStatus();
            } else {
                console.error('Hakem listesi yüklenemedi:', result.error);
            }
        } catch (error) {
            console.error('Hakem listesi yükleme hatası:', error);
        }
    }

    /**
     * Hakem listesini render et
     */
    renderReviewers() {
        const container = document.getElementById('reviewers-container');
        if (!container) return;

        if (this.reviewers.length === 0) {
            container.innerHTML = `
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Henüz hakem eklenmedi. En az ${this.minReviewers} hakem önermeniz gerekmektedir.
                </div>
            `;
            return;
        }

        let html = `
            <div class="table-responsive">
                <table class="table table-sm table-bordered reviewer-table">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Ad Soyad</th>
                            <th>Email</th>
                            <th>Kurum</th>
                            <th>Uzmanlık</th>
                            <th style="width: 80px;">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        this.reviewers.forEach((reviewer, index) => {
            const fullName = `${reviewer.ad} ${reviewer.soyad}`;
            const expertise = reviewer.uzmanlik_alani || '-';
            const notes = reviewer.notlar || '';
            const orcid = reviewer.orcid || '';

            html += `
                <tr>
                    <td class="text-center">${index + 1}</td>
                    <td>
                        ${this.escapeHtml(fullName)}
                        ${orcid ? `<br><small class="text-muted"><i class="fa fa-id-card"></i> ${this.escapeHtml(orcid)}</small>` : ''}
                    </td>
                    <td>${this.escapeHtml(reviewer.email)}</td>
                    <td>
                        ${this.escapeHtml(reviewer.kurum)}
                        ${reviewer.ulke ? `<br><small class="text-muted">${this.escapeHtml(reviewer.ulke)}</small>` : ''}
                    </td>
                    <td>${this.escapeHtml(expertise)}</td>
                    <td class="text-center">
                        <button
                            class="btn btn-sm btn-danger"
                            onclick="reviewerManager.deleteReviewer(${reviewer.id})"
                            title="Hakemi Sil"
                        >
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;

            // Notlar varsa göster
            if (notes) {
                html += `
                    <tr>
                        <td colspan="6" class="reviewer-notes">
                            <small><strong>Not:</strong> ${this.escapeHtml(notes)}</small>
                        </td>
                    </tr>
                `;
            }
        });

        html += `
                    </tbody>
                </table>
            </div>
        `;

        container.innerHTML = html;
    }

    /**
     * Hakem sil
     */
    async deleteReviewer(id) {
        if (!confirm('Bu hakemi silmek istediğinize emin misiniz?')) {
            return;
        }

        // Draft mode - local listeden sil
        if (this.articleId === 'draft') {
            this.reviewers = this.reviewers.filter(r => r.id !== id);
            this.renderReviewers();
            this.updateStatus();
            this.updateHiddenInputs();
            this.showSuccess('Hakem silindi');
            return;
        }

        // Normal mode - API'den sil
        this.showLoading('Hakem siliniyor...');

        try {
            const response = await fetch(`${this.apiBaseUrl}/reviewers/${id}`, {
                method: 'DELETE'
            });

            const result = await response.json();

            if (result.success) {
                this.showSuccess('Hakem başarıyla silindi');
                await this.loadReviewers();
            } else {
                this.showError(result.error || 'Hakem silinemedi');
            }
        } catch (error) {
            console.error('Hakem silme hatası:', error);
            this.showError('Sunucu hatası oluştu');
        } finally {
            this.hideLoading();
        }
    }

    /**
     * Hakem sayısını güncelle ve göster
     */
    updateCount() {
        const countEl = document.getElementById('reviewer-count');
        if (!countEl) return;

        const count = this.reviewers.length;
        countEl.textContent = count;

        // Renk ayarla
        if (count >= this.minReviewers) {
            countEl.classList.remove('text-danger');
            countEl.classList.add('text-success');
        } else {
            countEl.classList.remove('text-success');
            countEl.classList.add('text-danger');
        }

        // Durum mesajı
        const statusEl = document.getElementById('reviewer-status');
        if (statusEl) {
            if (count >= this.minReviewers) {
                statusEl.innerHTML = `
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle"></i>
                        Hakem sayısı yeterli (${count}/${this.minReviewers})
                    </div>
                `;
            } else {
                statusEl.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        En az ${this.minReviewers} hakem önermeniz gerekiyor (şu anda: ${count})
                    </div>
                `;
            }
        }
    }

    /**
     * Durum bilgisini güncelle (hakem sayısı, durum mesajı vb.)
     */
    updateStatus() {
        const countEl = document.getElementById('reviewer-count');
        if (countEl) {
            const count = this.reviewers.length;
            countEl.textContent = count;

            // Badge rengini ayarla
            if (count >= this.minReviewers) {
                countEl.classList.remove('bg-secondary', 'bg-danger');
                countEl.classList.add('bg-success');
            } else {
                countEl.classList.remove('bg-secondary', 'bg-success');
                countEl.classList.add('bg-danger');
            }
        }

        // Durum mesajını güncelle
        const statusEl = document.getElementById('reviewer-status');
        if (statusEl) {
            const count = this.reviewers.length;
            if (count >= this.minReviewers) {
                statusEl.innerHTML = `
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle"></i>
                        Hakem sayısı yeterli (${count}/${this.minReviewers})
                    </div>
                `;
            } else {
                statusEl.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        En az ${this.minReviewers} hakem önermeniz gerekiyor (şu anda: ${count})
                    </div>
                `;
            }
        }
    }

    /**
     * Hakem sayısı validasyonu
     * @returns {Promise<boolean>}
     */
    async validate() {
        try {
            const response = await fetch(`${this.apiBaseUrl}/articles/${this.articleId}/reviewers/validate`);
            const result = await response.json();

            if (result.success) {
                return result.valid;
            }

            return false;
        } catch (error) {
            console.error('Validasyon hatası:', error);
            return false;
        }
    }

    /**
     * Geçerli hakem sayısını döndür
     * @returns {number}
     */
    getReviewerCount() {
        return this.reviewers.length;
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
     * Hakemleri hidden input olarak forma ekle
     * Form submit edildiğinde hakemler backend'e gönderilsin
     */
    updateHiddenInputs() {
        // Önce var olan reviewer input'larını temizle
        const existingInputs = document.querySelectorAll('input[name^="reviewers["]');
        existingInputs.forEach(input => input.remove());

        // Form elementini bul
        const form = document.getElementById('wizardForm');
        if (!form) {
            console.error('wizardForm bulunamadı');
            return;
        }

        // Her hakem için hidden input'lar oluştur
        this.reviewers.forEach((reviewer, index) => {
            // Tüm reviewer verilerini JSON olarak tek bir hidden input'a koy
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = `reviewers[${index}]`;
            hiddenInput.value = JSON.stringify(reviewer);
            form.appendChild(hiddenInput);
        });

        console.log(`${this.reviewers.length} hakem hidden input olarak eklendi`);
    }

    /**
     * Formu temizle
     */
    clearForm() {
        document.getElementById('reviewer-ad').value = '';
        document.getElementById('reviewer-soyad').value = '';
        document.getElementById('reviewer-email').value = '';
        document.getElementById('reviewer-kurum').value = '';
        document.getElementById('reviewer-uzmanlik').value = '';
        document.getElementById('reviewer-ulke').value = '';
        document.getElementById('reviewer-orcid').value = '';
        document.getElementById('reviewer-notlar').value = '';
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
        // Basit loading göstergesi - daha gelişmiş bir spinner eklenebilir
        const btn = document.getElementById('reviewer-submit-btn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<i class="fa fa-spinner fa-spin"></i> ${message}`;
        }
    }

    /**
     * Loading gizle
     */
    hideLoading() {
        const btn = document.getElementById('reviewer-submit-btn');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-plus"></i> Hakem Ekle';
        }
    }
}

// Global değişken
let reviewerManager = null;

// Sayfa yüklendiğinde başlat
document.addEventListener('DOMContentLoaded', () => {
    // Article ID'yi formdan veya data attribute'dan al
    const articleIdInput = document.querySelector('[name="article_id"]');
    const articleIdData = document.querySelector('[data-article-id]');

    const articleId = articleIdInput?.value || articleIdData?.dataset.articleId;

    if (articleId) {
        // Varolan makale - API kullan
        reviewerManager = new ReviewerManager(articleId);
        reviewerManager.init();
        console.log('✓ Hakem yönetim sistemi başlatıldı (Article ID: ' + articleId + ')');
    } else {
        // Yeni makale - local storage kullan
        reviewerManager = new ReviewerManager('draft');
        reviewerManager.init();
        console.log('✓ Hakem yönetim sistemi başlatıldı (Draft mode - local storage)');
    }
});
