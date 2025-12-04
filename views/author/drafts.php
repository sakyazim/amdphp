<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taslak Makalelerim - AMDS</title>

    <!-- External CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }

        .badge-step {
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
        }

        .btn-action {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        .draft-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }

        @media (max-width: 768px) {
            .draft-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?= base_url('/') ?>">
                <i class="fas fa-graduation-cap me-2 text-primary"></i>
                <span>AMDS</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/yazar/dashboard') ?>">
                            <i class="fas fa-home me-1"></i>Anasayfa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('/yazar/taslaklar') ?>">
                            <i class="fas fa-file-alt me-1"></i>Taslaklar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/yazar/makalelerim') ?>">
                            <i class="fas fa-book me-1"></i>Makalelerim
                        </a>
                    </li>
                    <li class="nav-item">
                        <span class="nav-link">
                            <i class="bi bi-person-circle me-1"></i>
                            <?= htmlspecialchars($_SESSION['user_ad'] ?? '') ?> <?= htmlspecialchars($_SESSION['user_soyad'] ?? '') ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('/logout') ?>" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-sign-out-alt me-1"></i>Çıkış
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 class="mb-2">
                <i class="fas fa-file-alt me-2"></i> Taslak Makalelerim
            </h1>
            <p class="mb-0">Yarıda bıraktığınız makale gönderimleri</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mb-5">
        <!-- Action Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="<?= base_url('/makaleler/yeni') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Yeni Makale Başlat
                </a>
            </div>
            <div>
                <button class="btn btn-outline-secondary" onclick="loadDrafts()">
                    <i class="fas fa-sync-alt me-2"></i>Yenile
                </button>
            </div>
        </div>

        <!-- Drafts Table Card -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Taslak Listesi
                </h5>
            </div>
            <div class="card-body">
                <!-- Loading State -->
                <div id="loading-state" class="text-center py-5 d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Yükleniyor...</span>
                    </div>
                    <p class="mt-3 text-muted">Taslaklar yükleniyor...</p>
                </div>

                <!-- Empty State -->
                <div id="empty-state" class="empty-state d-none">
                    <i class="fas fa-file-alt"></i>
                    <h4>Henüz Taslak Yok</h4>
                    <p>Henüz hiç taslak makale oluşturmadınız.</p>
                    <a href="<?= base_url('/makaleler/yeni') ?>" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-2"></i>İlk Makaleyi Başlat
                    </a>
                </div>

                <!-- Drafts Table -->
                <div id="drafts-container" class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="35%">Taslak Adı</th>
                                <th width="15%" class="text-center">İlerleme</th>
                                <th width="20%">Son Güncelleme</th>
                                <th width="15%">Oluşturma</th>
                                <th width="10%" class="text-end">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody id="draft-list">
                            <!-- Dinamik olarak doldurulacak -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-5 py-4 bg-white border-top">
        <div class="container text-center text-muted">
            <p class="mb-0">&copy; <?= date('Y') ?> AMDS - Akademik Makale Değerlendirme Sistemi</p>
        </div>
    </footer>

    <!-- External JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // API Base URL
        const API_BASE_URL = '<?= base_url('/api/drafts') ?>';

        /**
         * Taslakları yükle
         */
        async function loadDrafts() {
            showLoading();

            try {
                const response = await fetch(API_BASE_URL);
                const result = await response.json();

                if (result.success) {
                    displayDrafts(result.drafts);
                } else {
                    showError('Taslaklar yüklenemedi');
                }
            } catch (error) {
                console.error('Taslak yükleme hatası:', error);
                showError('Taslaklar yüklenirken bir hata oluştu');
            }
        }

        /**
         * Taslakları tabloya yerleştir
         */
        function displayDrafts(drafts) {
            hideLoading();

            const tbody = document.getElementById('draft-list');
            const emptyState = document.getElementById('empty-state');
            const draftsContainer = document.getElementById('drafts-container');

            if (!drafts || drafts.length === 0) {
                tbody.innerHTML = '';
                draftsContainer.classList.add('d-none');
                emptyState.classList.remove('d-none');
                return;
            }

            draftsContainer.classList.remove('d-none');
            emptyState.classList.add('d-none');

            tbody.innerHTML = '';

            drafts.forEach((draft, index) => {
                const tr = document.createElement('tr');

                // İlerleme yüzdesi hesapla
                const progress = Math.round((draft.son_adim / draft.toplam_adim) * 100);

                // Tarihleri formatla
                const lastUpdate = formatDate(draft.son_guncelleme);
                const created = formatDate(draft.olusturma_tarihi);

                tr.innerHTML = `
                    <td>${index + 1}</td>
                    <td>
                        <strong>${escapeHtml(draft.taslak_adi || 'İsimsiz Taslak')}</strong>
                    </td>
                    <td class="text-center">
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar ${progress < 30 ? 'bg-danger' : progress < 70 ? 'bg-warning' : 'bg-success'}"
                                role="progressbar"
                                style="width: ${progress}%"
                                aria-valuenow="${progress}"
                                aria-valuemin="0"
                                aria-valuemax="100">
                                ${progress}%
                            </div>
                        </div>
                        <small class="text-muted">Adım ${draft.son_adim} / ${draft.toplam_adim}</small>
                    </td>
                    <td>
                        <i class="far fa-clock me-1"></i>${lastUpdate}
                    </td>
                    <td>
                        <i class="far fa-calendar me-1"></i>${created}
                    </td>
                    <td>
                        <div class="draft-actions">
                            <a href="<?= base_url('/makaleler/yeni') ?>?draft_id=${draft.id}"
                                class="btn btn-sm btn-primary btn-action"
                                title="Devam Et">
                                <i class="fas fa-edit"></i> Devam Et
                            </a>
                            <button class="btn btn-sm btn-danger btn-action"
                                onclick="deleteDraft(${draft.id})"
                                title="Sil">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;

                tbody.appendChild(tr);
            });
        }

        /**
         * Taslak sil
         */
        async function deleteDraft(id) {
            const result = await Swal.fire({
                title: 'Emin misiniz?',
                text: "Bu taslak kalıcı olarak silinecek!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Evet, Sil!',
                cancelButtonText: 'İptal'
            });

            if (!result.isConfirmed) {
                return;
            }

            try {
                const response = await fetch(`${API_BASE_URL}/${id}/delete`, {
                    method: 'POST'
                });

                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Silindi!',
                        text: 'Taslak başarıyla silindi.',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Listeyi yenile
                    loadDrafts();
                } else {
                    showError('Taslak silinemedi');
                }
            } catch (error) {
                console.error('Silme hatası:', error);
                showError('Taslak silinirken bir hata oluştu');
            }
        }

        /**
         * Yükleme durumu göster
         */
        function showLoading() {
            document.getElementById('loading-state').classList.remove('d-none');
            document.getElementById('drafts-container').classList.add('d-none');
            document.getElementById('empty-state').classList.add('d-none');
        }

        /**
         * Yükleme durumu gizle
         */
        function hideLoading() {
            document.getElementById('loading-state').classList.add('d-none');
        }

        /**
         * Hata mesajı göster
         */
        function showError(message) {
            hideLoading();
            Swal.fire({
                icon: 'error',
                title: 'Hata!',
                text: message
            });
        }

        /**
         * Tarihi formatla
         */
        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);

            if (diffMins < 1) {
                return 'Az önce';
            } else if (diffMins < 60) {
                return `${diffMins} dakika önce`;
            } else if (diffHours < 24) {
                return `${diffHours} saat önce`;
            } else if (diffDays < 7) {
                return `${diffDays} gün önce`;
            } else {
                return date.toLocaleDateString('tr-TR', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
        }

        /**
         * HTML escape
         */
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        // Sayfa yüklendiğinde taslakları getir
        loadDrafts();
    </script>
</body>
</html>
