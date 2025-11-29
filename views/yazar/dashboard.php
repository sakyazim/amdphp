<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Yazar Paneli') ?> - AMDS</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        body {
            background: #f8f9fa;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
            border-left: 4px solid;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .stat-card.primary {
            border-left-color: #667eea;
        }
        .stat-card.warning {
            border-left-color: #ffc107;
        }
        .stat-card.info {
            border-left-color: #0dcaf0;
        }
        .stat-card.success {
            border-left-color: #198754;
        }
        .stat-card.danger {
            border-left-color: #dc3545;
        }
        .stat-card.secondary {
            border-left-color: #6c757d;
        }
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        .action-btn {
            border-radius: 10px;
            padding: 1rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url('yazar/dashboard') ?>">
                <i class="bi bi-journal-text"></i> AMDS - Yazar Paneli
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('yazar/dashboard') ?>">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('yazar/makalelerim') ?>">
                            <i class="bi bi-file-earmark-text"></i> Makalelerim
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('yazar/yeni-makale') ?>">
                            <i class="bi bi-plus-circle"></i> Yeni Makale
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                            <?= htmlspecialchars($user['ad'] . ' ' . $user['soyad']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= base_url('profil') ?>">
                                <i class="bi bi-person"></i> Profilim
                            </a></li>
                            <li><a class="dropdown-item" href="<?= base_url('ayarlar') ?>">
                                <i class="bi bi-gear"></i> Ayarlar
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= base_url('logout') ?>">
                                <i class="bi bi-box-arrow-right"></i> Çıkış Yap
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <!-- Welcome Card -->
        <div class="welcome-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2>
                        <i class="bi bi-emoji-smile"></i>
                        Hoş Geldiniz, <?= htmlspecialchars($user['ad']) ?>!
                    </h2>
                    <p class="mb-0 mt-2">
                        <i class="bi bi-envelope"></i> <?= htmlspecialchars($user['email']) ?>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-light text-dark fs-6 px-3 py-2">
                        <i class="bi bi-pencil-square"></i> Yazar
                    </span>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-4">
                <div class="stat-card primary">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Toplam Makale</p>
                            <h3 class="mb-0"><?= $stats['toplam'] ?? 0 ?></h3>
                        </div>
                        <i class="bi bi-file-earmark-text text-primary stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card warning">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Beklemede</p>
                            <h3 class="mb-0"><?= $stats['beklemede'] ?? 0 ?></h3>
                        </div>
                        <i class="bi bi-clock-history text-warning stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card info">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Değerlendirmede</p>
                            <h3 class="mb-0"><?= $stats['degerlendirmede'] ?? 0 ?></h3>
                        </div>
                        <i class="bi bi-eye text-info stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="stat-card secondary">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Taslak</p>
                            <h3 class="mb-0"><?= $stats['taslak'] ?? 0 ?></h3>
                        </div>
                        <i class="bi bi-file-earmark text-secondary stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card warning">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Revizyon</p>
                            <h3 class="mb-0"><?= $stats['revizyon'] ?? 0 ?></h3>
                        </div>
                        <i class="bi bi-arrow-repeat text-warning stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card success">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Kabul Edilen</p>
                            <h3 class="mb-0"><?= $stats['kabul'] ?? 0 ?></h3>
                        </div>
                        <i class="bi bi-check-circle text-success stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card danger">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Reddedilen</p>
                            <h3 class="mb-0"><?= $stats['red'] ?? 0 ?></h3>
                        </div>
                        <i class="bi bi-x-circle text-danger stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bi bi-lightning-fill text-warning"></i> Hızlı İşlemler
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <a href="<?= base_url('yazar/yeni-makale') ?>" class="btn btn-primary w-100 action-btn">
                                    <i class="bi bi-plus-circle"></i>
                                    <div class="mt-2">Yeni Makale Gönder</div>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="<?= base_url('yazar/makalelerim') ?>" class="btn btn-outline-primary w-100 action-btn">
                                    <i class="bi bi-list-ul"></i>
                                    <div class="mt-2">Makalelerimi Görüntüle</div>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="<?= base_url('yazar/makalelerim?durum=taslak') ?>" class="btn btn-outline-secondary w-100 action-btn">
                                    <i class="bi bi-file-earmark"></i>
                                    <div class="mt-2">Taslak Makaleler</div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Son Aktiviteler -->
        <div class="row mt-4 mb-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bi bi-activity"></i> Son Aktiviteler
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            <p class="mt-3">Henüz aktivite bulunmuyor</p>
                            <small class="text-muted">Makale gönderdiğinizde burada görüntülenecektir</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
