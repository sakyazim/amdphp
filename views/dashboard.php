<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Dashboard') ?> - AMDS</title>

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
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .stat-icon {
            font-size: 3rem;
            opacity: 0.8;
        }
        .role-badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url('dashboard') ?>">
                <i class="bi bi-journal-text"></i> AMDS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('dashboard') ?>">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('makaleler') ?>">
                            <i class="bi bi-file-earmark-text"></i> Makaleler
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
                    <?php foreach ($user['roles'] as $role): ?>
                        <span class="badge role-badge bg-light text-dark me-1">
                            <i class="bi bi-shield-check"></i> <?= htmlspecialchars($role) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <i class="bi bi-file-earmark-text text-primary stat-icon"></i>
                    <h3 class="mt-3">0</h3>
                    <p class="text-muted mb-0">Toplam Makale</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <i class="bi bi-clock-history text-warning stat-icon"></i>
                    <h3 class="mt-3">0</h3>
                    <p class="text-muted mb-0">Bekleyen</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <i class="bi bi-check-circle text-success stat-icon"></i>
                    <h3 class="mt-3">0</h3>
                    <p class="text-muted mb-0">Onaylanan</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <i class="bi bi-x-circle text-danger stat-icon"></i>
                    <h3 class="mt-3">0</h3>
                    <p class="text-muted mb-0">Reddedilen</p>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row mt-4">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-4 mb-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bi bi-lightning"></i> Hızlı İşlemler
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2 d-md-flex">
                            <button class="btn btn-primary" disabled>
                                <i class="bi bi-plus-circle"></i> Yeni Makale Gönder
                            </button>
                            <button class="btn btn-outline-primary" disabled>
                                <i class="bi bi-pencil-square"></i> Taslak Makaleler
                            </button>
                            <button class="btn btn-outline-primary" disabled>
                                <i class="bi bi-search"></i> Makale Ara
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle"></i> Bu özellikler yakında aktif olacak
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
