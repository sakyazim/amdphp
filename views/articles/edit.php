<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Makale Düzenle - <?= htmlspecialchars($makale['baslik_tr']) ?> - AMDS</title>

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
        .form-section-title {
            color: #495057;
            font-size: 1.1rem;
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #dee2e6;
        }
        .card {
            border: none;
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
            <div class="ms-auto d-flex align-items-center">
                <span class="me-3">
                    <i class="fas fa-user-circle me-2"></i>
                    <?= htmlspecialchars($_SESSION['user_ad'] ?? '') ?> <?= htmlspecialchars($_SESSION['user_soyad'] ?? '') ?>
                </span>
                <a href="<?= base_url('/makaleler/' . $makale['id']) ?>" class="btn btn-outline-secondary btn-sm me-2">
                    <i class="fas fa-arrow-left me-1"></i>Geri
                </a>
                <a href="<?= base_url('/logout') ?>" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i>Çıkış
                </a>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h2><i class="fas fa-edit me-2"></i>Makale Düzenle</h2>
            <p class="mb-0">Makale Kodu: <strong><?= htmlspecialchars($makale['makale_kodu']) ?></strong></p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mb-5">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="<?= base_url('/makaleler/' . $makale['id']) ?>">
                    <!-- CSRF Token -->
                    <input type="hidden" name="_csrf_token" value="<?= $_SESSION['_csrf_token'] ?? '' ?>">

                    <!-- Makale Türü -->
                    <h5 class="form-section-title">
                        <i class="fas fa-book me-2"></i>Makale Türü
                    </h5>
                    <div class="mb-4">
                        <label for="makale_turu" class="form-label">Makale Türü <span class="text-danger">*</span></label>
                        <select class="form-select" id="makale_turu" name="makale_turu" required>
                            <option value="">Seçiniz</option>
                            <?php foreach ($typeList as $key => $label): ?>
                                <option value="<?= $key ?>" <?= $makale['makale_turu'] === $key ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Lütfen makale türü seçin</div>
                    </div>

                    <!-- Başlıklar -->
                    <h5 class="form-section-title">
                        <i class="fas fa-heading me-2"></i>Başlıklar
                    </h5>
                    <div class="mb-4">
                        <label for="baslik_tr" class="form-label">Türkçe Başlık <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="baslik_tr" name="baslik_tr" rows="3"
                                maxlength="500" required><?= htmlspecialchars($makale['baslik_tr']) ?></textarea>
                        <div class="form-text">
                            <span id="charCountTR"><?= mb_strlen($makale['baslik_tr']) ?></span>/500 karakter
                        </div>
                        <div class="invalid-feedback">Türkçe başlık gereklidir (en az 10, en fazla 500 karakter)</div>
                    </div>

                    <div class="mb-4">
                        <label for="baslik_en" class="form-label">İngilizce Başlık</label>
                        <textarea class="form-control" id="baslik_en" name="baslik_en" rows="3"
                                maxlength="500"><?= htmlspecialchars($makale['baslik_en'] ?? '') ?></textarea>
                        <div class="form-text">
                            <span id="charCountEN"><?= mb_strlen($makale['baslik_en'] ?? '') ?></span>/500 karakter
                        </div>
                    </div>

                    <!-- Özetler -->
                    <h5 class="form-section-title">
                        <i class="fas fa-file-alt me-2"></i>Özetler
                    </h5>
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>Özet bölümü 150-250 kelime arasında olmalıdır.
                    </div>

                    <div class="mb-4">
                        <label for="ozet_tr" class="form-label">Türkçe Özet <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="ozet_tr" name="ozet_tr" rows="6" required><?= htmlspecialchars($makale['ozet_tr']) ?></textarea>
                        <div class="form-text">
                            <span id="wordCountTR"><?= str_word_count($makale['ozet_tr']) ?></span> kelime
                        </div>
                        <div class="invalid-feedback">Türkçe özet gereklidir (150-250 kelime)</div>
                    </div>

                    <div class="mb-4">
                        <label for="ozet_en" class="form-label">İngilizce Özet</label>
                        <textarea class="form-control" id="ozet_en" name="ozet_en" rows="6"><?= htmlspecialchars($makale['ozet_en'] ?? '') ?></textarea>
                        <div class="form-text">
                            <span id="wordCountEN"><?= str_word_count($makale['ozet_en'] ?? '') ?></span> kelime
                        </div>
                    </div>

                    <!-- Anahtar Kelimeler -->
                    <h5 class="form-section-title">
                        <i class="fas fa-key me-2"></i>Anahtar Kelimeler
                    </h5>
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>En az 3, en fazla 5 anahtar kelime giriniz. Virgül ile ayırınız.
                    </div>

                    <div class="mb-4">
                        <label for="anahtar_kelimeler_tr" class="form-label">Türkçe Anahtar Kelimeler</label>
                        <textarea class="form-control" id="anahtar_kelimeler_tr" name="anahtar_kelimeler_tr" rows="3"
                                placeholder="Örnek: yapay zeka, makine öğrenmesi, derin öğrenme"><?= htmlspecialchars($makale['anahtar_kelimeler_tr'] ?? '') ?></textarea>
                        <div class="form-text">
                            <span id="keywordCountTR"><?= !empty($makale['anahtar_kelimeler_tr']) ? count(explode(',', $makale['anahtar_kelimeler_tr'])) : 0 ?></span> anahtar kelime
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="anahtar_kelimeler_en" class="form-label">İngilizce Anahtar Kelimeler</label>
                        <textarea class="form-control" id="anahtar_kelimeler_en" name="anahtar_kelimeler_en" rows="3"
                                placeholder="Example: artificial intelligence, machine learning, deep learning"><?= htmlspecialchars($makale['anahtar_kelimeler_en'] ?? '') ?></textarea>
                        <div class="form-text">
                            <span id="keywordCountEN"><?= !empty($makale['anahtar_kelimeler_en']) ? count(explode(',', $makale['anahtar_kelimeler_en'])) : 0 ?></span> anahtar kelime
                        </div>
                    </div>

                    <!-- Form Buttons -->
                    <div class="d-flex justify-content-between mt-5">
                        <a href="<?= base_url('/makaleler/' . $makale['id']) ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>İptal
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Değişiklikleri Kaydet
                        </button>
                    </div>
                </form>
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

    <script>
    // Character counters
    document.getElementById('baslik_tr').addEventListener('input', function() {
        document.getElementById('charCountTR').textContent = this.value.length;
    });

    document.getElementById('baslik_en').addEventListener('input', function() {
        document.getElementById('charCountEN').textContent = this.value.length;
    });

    // Word counters
    function countWords(text) {
        return text.trim().split(/\s+/).filter(word => word.length > 0).length;
    }

    document.getElementById('ozet_tr').addEventListener('input', function() {
        document.getElementById('wordCountTR').textContent = countWords(this.value);
    });

    document.getElementById('ozet_en').addEventListener('input', function() {
        document.getElementById('wordCountEN').textContent = countWords(this.value);
    });

    // Keyword counters
    function countKeywords(text) {
        return text.split(',').filter(k => k.trim().length > 0).length;
    }

    document.getElementById('anahtar_kelimeler_tr').addEventListener('input', function() {
        document.getElementById('keywordCountTR').textContent = countKeywords(this.value);
    });

    document.getElementById('anahtar_kelimeler_en').addEventListener('input', function() {
        document.getElementById('keywordCountEN').textContent = countKeywords(this.value);
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        let isValid = true;

        // Validate title TR (min 10, max 500 chars)
        const titleTR = document.getElementById('baslik_tr').value;
        if (titleTR.length < 10 || titleTR.length > 500) {
            document.getElementById('baslik_tr').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('baslik_tr').classList.remove('is-invalid');
        }

        // Validate abstract TR (150-250 words)
        const abstractTR = document.getElementById('ozet_tr').value;
        const wordCount = countWords(abstractTR);
        if (wordCount < 150 || wordCount > 250) {
            document.getElementById('ozet_tr').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('ozet_tr').classList.remove('is-invalid');
        }

        if (!isValid) {
            e.preventDefault();
            alert('Lütfen tüm alanları doğru şekilde doldurun.');
        }
    });
    </script>
</body>
</html>
