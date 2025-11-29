<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($makale['baslik_tr']) ?> - AMDS</title>

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
        .article-code {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #667eea;
            font-size: 1.1rem;
        }
        .info-row {
            border-bottom: 1px solid #eee;
            padding: 1rem 0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #6c757d;
        }
        .author-card {
            background: #f8f9fa;
            border-left: 3px solid #667eea;
            padding: 1rem;
            margin-bottom: 0.5rem;
        }
        .file-item {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s;
        }
        .file-item:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
                        <a class="nav-link" href="<?= base_url('/makaleler') ?>"><i class="fas fa-file-alt me-2"></i>Makaleler</a>
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
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="article-code mb-2"><?= htmlspecialchars($makale['makale_kodu']) ?></div>
                    <h2 class="mb-0"><?= htmlspecialchars($makale['baslik_tr']) ?></h2>
                </div>
                <div>
                    <span class="badge bg-light text-dark fs-6 px-3 py-2">
                        <?= $statusList[$makale['durum']] ?? $makale['durum'] ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <!-- Makale Bilgileri -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Makale Bilgileri</h5>
                    </div>
                    <div class="card-body">
                        <!-- Türkçe Başlık -->
                        <div class="info-row">
                            <div class="info-label mb-1">Türkçe Başlık</div>
                            <div><?= htmlspecialchars($makale['baslik_tr']) ?></div>
                        </div>

                        <!-- İngilizce Başlık -->
                        <?php if (!empty($makale['baslik_en'])): ?>
                            <div class="info-row">
                                <div class="info-label mb-1">İngilizce Başlık</div>
                                <div><?= htmlspecialchars($makale['baslik_en']) ?></div>
                            </div>
                        <?php endif; ?>

                        <!-- Makale Türü -->
                        <div class="info-row">
                            <div class="info-label mb-1">Makale Türü</div>
                            <div>
                                <span class="badge bg-info">
                                    <?= $typeList[$makale['makale_turu']] ?? $makale['makale_turu'] ?>
                                </span>
                            </div>
                        </div>

                        <!-- Türkçe Özet -->
                        <div class="info-row">
                            <div class="info-label mb-1">Türkçe Özet</div>
                            <div class="text-muted"><?= nl2br(htmlspecialchars($makale['ozet_tr'])) ?></div>
                        </div>

                        <!-- İngilizce Özet -->
                        <?php if (!empty($makale['ozet_en'])): ?>
                            <div class="info-row">
                                <div class="info-label mb-1">İngilizce Özet</div>
                                <div class="text-muted"><?= nl2br(htmlspecialchars($makale['ozet_en'])) ?></div>
                            </div>
                        <?php endif; ?>

                        <!-- Anahtar Kelimeler TR -->
                        <?php if (!empty($makale['anahtar_kelimeler_tr'])): ?>
                            <div class="info-row">
                                <div class="info-label mb-1">Anahtar Kelimeler (TR)</div>
                                <div>
                                    <?php
                                    $keywords = explode(',', $makale['anahtar_kelimeler_tr']);
                                    foreach ($keywords as $keyword): ?>
                                        <span class="badge bg-secondary me-1"><?= trim($keyword) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Anahtar Kelimeler EN -->
                        <?php if (!empty($makale['anahtar_kelimeler_en'])): ?>
                            <div class="info-row">
                                <div class="info-label mb-1">Anahtar Kelimeler (EN)</div>
                                <div>
                                    <?php
                                    $keywords = explode(',', $makale['anahtar_kelimeler_en']);
                                    foreach ($keywords as $keyword): ?>
                                        <span class="badge bg-secondary me-1"><?= trim($keyword) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Yazarlar -->
                <?php if (!empty($makale['yazarlar'])): ?>
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-users me-2"></i>Yazarlar</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($makale['yazarlar'] as $yazar): ?>
                                <div class="author-card">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                <?= htmlspecialchars($yazar['ad'] . ' ' . $yazar['soyad']) ?>
                                                <?php if ($yazar['sorumlu_yazar']): ?>
                                                    <span class="badge bg-warning text-dark ms-2">Sorumlu Yazar</span>
                                                <?php endif; ?>
                                            </h6>
                                            <?php if (!empty($yazar['email'])): ?>
                                                <div class="text-muted small">
                                                    <i class="fas fa-envelope me-1"></i><?= htmlspecialchars($yazar['email']) ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($yazar['kurum'])): ?>
                                                <div class="text-muted small">
                                                    <i class="fas fa-building me-1"></i><?= htmlspecialchars($yazar['kurum']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <span class="badge bg-primary">Sıra: <?= $yazar['yazar_sirasi'] ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Dosyalar -->
                <?php if (!empty($makale['dosyalar'])): ?>
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-paperclip me-2"></i>Dosyalar</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($makale['dosyalar'] as $dosya): ?>
                                <div class="file-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">
                                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                                <?= htmlspecialchars($dosya['dosya_adi']) ?>
                                            </h6>
                                            <div class="text-muted small">
                                                <span class="me-3">
                                                    <i class="fas fa-hdd me-1"></i><?= formatFileSize($dosya['dosya_boyutu']) ?>
                                                </span>
                                                <span>
                                                    <i class="fas fa-calendar me-1"></i><?= date('d.m.Y H:i', strtotime($dosya['yuklenme_tarihi'])) ?>
                                                </span>
                                            </div>
                                        </div>
                                        <a href="<?= base_url('/storage/' . $dosya['dosya_yolu']) ?>" class="btn btn-sm btn-outline-primary" download>
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Aksiyon Kartı -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-cog me-2"></i>İşlemler</h5>
                    </div>
                    <div class="card-body">
                        <a href="<?= base_url('/makaleler/' . $makale['id'] . '/duzenle') ?>" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-edit me-2"></i>Düzenle
                        </a>
                        <a href="<?= base_url('/makaleler') ?>" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-arrow-left me-2"></i>Geri Dön
                        </a>
                    </div>
                </div>

                <!-- Tarihler -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-calendar me-2"></i>Tarihler</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <div class="info-label mb-1">Gönderim Tarihi</div>
                            <div class="text-muted">
                                <?= date('d.m.Y H:i', strtotime($makale['gonderi_tarihi'])) ?>
                            </div>
                        </div>

                        <?php if (!empty($makale['kabul_tarihi'])): ?>
                            <div class="info-row">
                                <div class="info-label mb-1">Kabul Tarihi</div>
                                <div class="text-success">
                                    <?= date('d.m.Y H:i', strtotime($makale['kabul_tarihi'])) ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($makale['yayin_tarihi'])): ?>
                            <div class="info-row">
                                <div class="info-label mb-1">Yayın Tarihi</div>
                                <div class="text-success">
                                    <?= date('d.m.Y H:i', strtotime($makale['yayin_tarihi'])) ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($makale['ret_tarihi'])): ?>
                            <div class="info-row">
                                <div class="info-label mb-1">Ret Tarihi</div>
                                <div class="text-danger">
                                    <?= date('d.m.Y H:i', strtotime($makale['ret_tarihi'])) ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="info-row">
                            <div class="info-label mb-1">Son Güncelleme</div>
                            <div class="text-muted small">
                                <?= date('d.m.Y H:i', strtotime($makale['guncelleme_tarihi'])) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Durum Bilgisi -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Durum</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <div class="info-label mb-1">Mevcut Durum</div>
                            <div>
                                <span class="badge bg-<?= getStatusColor($makale['durum']) ?> fs-6 px-3 py-2">
                                    <?= $statusList[$makale['durum']] ?? $makale['durum'] ?>
                                </span>
                            </div>
                        </div>

                        <?php if (!empty($makale['mevcut_asamasi'])): ?>
                            <div class="info-row">
                                <div class="info-label mb-1">Mevcut Aşama</div>
                                <div class="text-muted"><?= htmlspecialchars($makale['mevcut_asamasi']) ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($makale['ret_nedeni'])): ?>
                            <div class="info-row">
                                <div class="info-label mb-1">Ret Nedeni</div>
                                <div class="alert alert-danger mb-0">
                                    <?= nl2br(htmlspecialchars($makale['ret_nedeni'])) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
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
 * Helper: Dosya boyutunu formatlar
 */
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
?>
