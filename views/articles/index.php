<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Makaleler' ?> - AMDS</title>

    <!-- External CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        .badge-status {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        .article-code {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #667eea;
        }
        .filter-section {
            background: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
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
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/dashboard') ?>"><i class="fas fa-home me-2"></i>Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('/makaleler') ?>"><i class="fas fa-file-alt me-2"></i>Makaleler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/logout') ?>"><i class="fas fa-sign-out-alt me-2"></i>Çıkış</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1><i class="fas fa-file-alt me-2"></i>Makaleler</h1>
                    <p class="mb-0">Toplam <?= $totalCount ?> makale bulundu</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="<?= base_url('/makaleler/yeni') ?>" class="btn btn-light btn-lg">
                        <i class="fas fa-plus me-2"></i>Yeni Makale
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="<?= base_url('/makaleler') ?>" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label"><i class="fas fa-search me-2"></i>Arama</label>
                    <input type="text" name="search" class="form-control" placeholder="Başlık, özet, anahtar kelime..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-filter me-2"></i>Durum</label>
                    <select name="durum" class="form-select">
                        <option value="">Tümü</option>
                        <?php foreach ($statusList as $key => $label): ?>
                            <option value="<?= $key ?>" <?= ($filters['durum'] ?? '') === $key ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-tag me-2"></i>Makale Türü</label>
                    <select name="makale_turu" class="form-select">
                        <option value="">Tümü</option>
                        <?php foreach ($typeList as $key => $label): ?>
                            <option value="<?= $key ?>" <?= ($filters['makale_turu'] ?? '') === $key ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Filtrele
                    </button>
                </div>
            </form>
        </div>

        <!-- Articles List -->
        <?php if (empty($makaleler)): ?>
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                    <h4>Makale Bulunamadı</h4>
                    <p class="text-muted">Henüz hiç makale bulunmamaktadır veya arama kriterlerinize uygun makale yoktur.</p>
                    <a href="<?= base_url('/makaleler/yeni') ?>" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-2"></i>İlk Makaleyi Ekle
                    </a>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($makaleler as $makale): ?>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="d-flex align-items-start mb-2">
                                    <span class="article-code me-3"><?= htmlspecialchars($makale['makale_kodu']) ?></span>
                                    <span class="badge badge-status bg-<?= getStatusColor($makale['durum']) ?>">
                                        <?= $statusList[$makale['durum']] ?? $makale['durum'] ?>
                                    </span>
                                </div>

                                <h5 class="card-title mb-2">
                                    <a href="<?= base_url('/makaleler/' . $makale['id']) ?>" class="text-decoration-none text-dark">
                                        <?= htmlspecialchars($makale['baslik_tr']) ?>
                                    </a>
                                </h5>

                                <?php if (!empty($makale['baslik_en'])): ?>
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-language me-1"></i><?= htmlspecialchars($makale['baslik_en']) ?>
                                    </p>
                                <?php endif; ?>

                                <p class="card-text text-muted mb-2">
                                    <?= mb_substr(strip_tags($makale['ozet_tr']), 0, 200) ?>...
                                </p>

                                <div class="d-flex align-items-center text-muted small">
                                    <span class="me-3">
                                        <i class="fas fa-users me-1"></i><?= htmlspecialchars($makale['yazarlar'] ?? 'Yazar bilgisi yok') ?>
                                    </span>
                                    <span class="me-3">
                                        <i class="fas fa-tag me-1"></i><?= $typeList[$makale['makale_turu']] ?? $makale['makale_turu'] ?>
                                    </span>
                                    <span>
                                        <i class="fas fa-calendar me-1"></i><?= date('d.m.Y', strtotime($makale['gonderi_tarihi'])) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="btn-group" role="group">
                                    <a href="<?= base_url('/makaleler/' . $makale['id']) ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i>Görüntüle
                                    </a>
                                    <a href="<?= base_url('/makaleler/' . $makale['id'] . '/duzenle') ?>" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-edit me-1"></i>Düzenle
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Makale sayfalama">
                    <ul class="pagination justify-content-center">
                        <!-- Previous -->
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= base_url('/makaleler?page=' . ($page - 1) . buildQueryString($filters)) ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>

                        <!-- Pages -->
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php if ($i == 1 || $i == $totalPages || abs($i - $page) <= 2): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= base_url('/makaleler?page=' . $i . buildQueryString($filters)) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php elseif (abs($i - $page) == 3): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <!-- Next -->
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= base_url('/makaleler?page=' . ($page + 1) . buildQueryString($filters)) ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>

                <p class="text-center text-muted">
                    Sayfa <?= $page ?> / <?= $totalPages ?> (Toplam <?= $totalCount ?> makale)
                </p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="mt-5 py-4 bg-white border-top">
        <div class="container text-center text-muted">
            <p class="mb-0">&copy; <?= date('Y') ?> AMDS - Akademik Makale Değerlendirme Sistemi</p>
        </div>
    </footer>

    <!-- External JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
/**
 * Helper: Durum rengini belirler
 */
function getStatusColor($durum) {
    $colors = [
        'gonderildi' => 'info',
        'on_kontrol' => 'warning',
        'editore_atandi' => 'primary',
        'hakem_ataniyor' => 'info',
        'hakemde' => 'warning',
        'degerlendirme_tamamlandi' => 'success',
        'duzeltme_bekleniyor' => 'warning',
        'kabul_edildi' => 'success',
        'reddedildi' => 'danger',
        'yayinlandi' => 'success',
    ];
    return $colors[$durum] ?? 'secondary';
}

/**
 * Helper: Query string olusturur
 */
function buildQueryString($filters) {
    $query = '';
    foreach ($filters as $key => $value) {
        if (!empty($value)) {
            $query .= '&' . $key . '=' . urlencode($value);
        }
    }
    return $query;
}
?>
