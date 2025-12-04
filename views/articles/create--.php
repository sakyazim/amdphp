<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Makale - AMDS</title>

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
        .wizard-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .progress-info {
            margin-bottom: 1rem;
        }
        .wizard-steps {
            background: #f8f9fa;
            padding: 1.5rem 1rem;
            border-radius: 8px 0 0 8px;
        }
        .step-link {
            display: block;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            color: #6c757d;
            text-decoration: none;
            border-radius: 0.375rem;
            transition: all 0.3s;
            position: relative;
        }
        .step-link:hover {
            background: #e9ecef;
            color: #495057;
        }
        .step-link.active {
            background: #667eea;
            color: white;
        }
        .step-link.completed {
            background: #d4edda;
            color: #155724;
        }
        .step-link .step-status {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
        }
        .step-link.completed .step-status::before {
            content: "\f058";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            color: #28a745;
        }
        .step-content {
            padding: 2rem;
        }
        .step-title {
            color: #667eea;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
        }
        .form-section-title {
            color: #495057;
            font-size: 1.1rem;
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #dee2e6;
        }
        .btn-navigation {
            min-width: 120px;
        }
        .char-counter {
            font-size: 0.875rem;
            color: #6c757d;
        }
        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        /* Method seçim kartları */
        .method-option {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .method-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .method-option.border-primary {
            border-width: 2px !important;
        }
        /* Auto-resize textarea */
        .auto-resize, .reference-input {
            resize: none;
            overflow: hidden;
            min-height: 38px;
        }
        .reference-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        /* Referans limit uyarısı */
        .reference-input.is-invalid, #bulkReferences.is-invalid {
            border-color: #dc3545;
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
                    <i class="bi bi-person-circle me-2"></i>
                    <?= htmlspecialchars($_SESSION['user_ad'] ?? '') ?> <?= htmlspecialchars($_SESSION['user_soyad'] ?? '') ?>
                </span>
                <a href="<?= base_url('/logout') ?>" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i>Çıkış
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-5">
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

        <div class="wizard-container">
            <!-- Progress Bar -->
            <div class="p-4 pb-0">
                <div class="progress-info d-flex justify-content-between mb-2">
                    <span class="fw-bold" id="progressStepName">Dil Seçimi</span>
                    <span><span id="progressText">0%</span> tamamlandı</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 0%" id="progressBar"></div>
                </div>
            </div>

            <div class="row g-0">
                <!-- Sidebar Steps -->
                <div class="col-md-3">
                    <div class="wizard-steps">
                        <div class="steps-list">
                            <a href="#" class="step-link active" data-step="0">
                                <i class="fas fa-language me-2"></i> <span>Dil Seçimi</span>
                                <span class="step-status"></span>
                            </a>
                            <a href="#" class="step-link" data-step="1">
                                <i class="fas fa-info-circle me-2"></i> <span>Ön Bilgi</span>
                                <span class="step-status"></span>
                            </a>
                            <a href="#" class="step-link" data-step="2">
                                <i class="fas fa-book me-2"></i> <span>Tür-Konu</span>
                                <span class="step-status"></span>
                            </a>
                            <a href="#" class="step-link" data-step="3">
                                <i class="fas fa-heading me-2"></i> <span>Başlık</span>
                                <span class="step-status"></span>
                            </a>
                            <a href="#" class="step-link" data-step="4">
                                <i class="fas fa-file-alt me-2"></i> <span>Özet</span>
                                <span class="step-status"></span>
                            </a>
                            <a href="#" class="step-link" data-step="5">
                                <i class="fas fa-key me-2"></i> <span>Anahtar Kelimeler</span>
                                <span class="step-status"></span>
                            </a>
                            <a href="#" class="step-link" data-step="6">
                                <i class="fas fa-quote-right me-2"></i> <span>Referanslar</span>
                                <span class="step-status"></span>
                            </a>
                            <a href="#" class="step-link" data-step="7">
                                <i class="fas fa-paper-plane me-2"></i> <span>Makaleyi Gönder</span>
                                <span class="step-status"></span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="col-md-9">
                    <form id="wizardForm" method="POST" action="<?= base_url('/makaleler') ?>" enctype="multipart/form-data">
                        <!-- CSRF Token -->
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                        <!-- Step 0: Dil Seçimi -->
                        <div id="step0" class="step-content">
                            <h3 class="step-title">
                                <i class="fas fa-language me-2"></i> Dil Seçimi
                            </h3>
                            <div class="mb-3">
                                <label for="articleLanguage" class="form-label">Makale Dili <span class="text-danger">*</span></label>
                                <select class="form-select" id="articleLanguage" name="makale_dili" required>
                                    <option value="">Lütfen bir makale dili seçiniz</option>
                                    <option value="tr">Türkçe</option>
                                    <option value="en">İngilizce</option>
                                    <option value="de">Almanca</option>
                                    <option value="fr">Fransızca</option>
                                </select>
                                <div class="invalid-feedback">Lütfen bir makale dili seçiniz</div>
                            </div>
                        </div>

                        <!-- Step 1: Ön Bilgi -->
                        <div id="step1" class="step-content d-none">
                            <h3 class="step-title">
                                <i class="fas fa-info-circle me-2"></i> Ön Bilgi
                            </h3>
                            <div class="alert alert-info">
                                <h5 class="alert-heading">Makale Gönderim Süreci Hakkında</h5>
                                <ul class="mb-0">
                                    <li>Makaleniz için uygun bir başlık belirlemelisiniz.</li>
                                    <li>En az 3, en fazla 5 anahtar kelime girmelisiniz.</li>
                                    <li>Özet kısmı 150-250 kelime arasında olmalıdır.</li>
                                    <li>Referanslar APA formatında olmalıdır.</li>
                                    <li>Makaleniz PDF formatında yüklenmelidir.</li>
                                    <li>Yazarların ORCID bilgileri eklenmelidir.</li>
                                    <li>Çıkar çatışması beyanı doldurulmalıdır.</li>
                                </ul>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="acceptInfo" required>
                                <label class="form-check-label" for="acceptInfo">
                                    Bilgileri okudum ve anladım, devam etmek istiyorum
                                </label>
                                <div class="invalid-feedback">Devam etmek için onaylamanız gerekmektedir</div>
                            </div>
                        </div>

                        <!-- Step 2: Tür-Konu -->
                        <div id="step2" class="step-content d-none">
                            <h3 class="step-title">
                                <i class="fas fa-book me-2"></i> Tür ve Konu
                            </h3>
                            <div class="mb-4">
                                <label for="articleType" class="form-label">Makale Türü <span class="text-danger">*</span></label>
                                <select class="form-select" id="articleType" name="makale_turu" required>
                                    <option value="">Makale türü seçin</option>
                                    <?php foreach ($typeList as $key => $label): ?>
                                        <option value="<?= $key ?>"><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Lütfen makale türü seçin</div>
                            </div>
                            <div class="mb-4">
                                <label for="articleSubject" class="form-label">Makale Konusu <span class="text-danger">*</span></label>
                                <select class="form-select" id="articleSubject" name="makale_konusu" required>
                                    <option value="">Makale konusu seçin</option>
                                    <?php foreach ($subjectList as $key => $label): ?>
                                        <option value="<?= $key ?>"><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Lütfen makale konusu seçin</div>
                            </div>
                        </div>

                        <!-- Step 3: Başlık -->
                        <div id="step3" class="step-content d-none">
                            <h3 class="step-title">
                                <i class="fas fa-heading me-2"></i> Başlık
                            </h3>
                            <div class="mb-4">
                                <label for="titleTR" class="form-label">Türkçe Başlık <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="titleTR" name="baslik_tr" rows="3"
                                        placeholder="Makalenizin Türkçe başlığını girin"
                                        maxlength="500" required></textarea>
                                <div class="form-text char-counter">
                                    <span id="charCountTR">0</span>/500 karakter
                                </div>
                                <div class="invalid-feedback">Türkçe başlık girmelisiniz (en az 10, en fazla 500 karakter)</div>
                            </div>
                            <div class="mb-3">
                                <label for="titleEN" class="form-label">İngilizce Başlık <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="titleEN" name="baslik_en" rows="3"
                                        placeholder="Enter the English title" maxlength="500" required></textarea>
                                <div class="form-text char-counter">
                                    <span id="charCountEN">0</span>/500 karakter
                                </div>
                                <div class="invalid-feedback">İngilizce başlık girmelisiniz (en az 10, en fazla 500 karakter)</div>
                            </div>
                        </div>

                        <!-- Step 4: Özet -->
                        <div id="step4" class="step-content d-none">
                            <h3 class="step-title">
                                <i class="fas fa-file-alt me-2"></i> Özet
                            </h3>
                            <div class="alert alert-info mb-4">
                                <i class="fas fa-info-circle me-2"></i> Özet bölümü 150-250 kelime arasında olmalıdır.
                            </div>
                            <div class="mb-4">
                                <label for="abstractTR" class="form-label">Türkçe Özet <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="abstractTR" name="ozet_tr" rows="6"
                                        placeholder="Makalenizin Türkçe özetini girin (150-250 kelime)" required></textarea>
                                <div class="form-text">
                                    <span id="wordCountTR">0</span> kelime
                                </div>
                                <div class="invalid-feedback">Geçerli bir Türkçe özet girmelisiniz (150-250 kelime)</div>
                            </div>
                            <div class="mb-3">
                                <label for="abstractEN" class="form-label">İngilizce Özet <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="abstractEN" name="ozet_en" rows="6"
                                        placeholder="Enter the English abstract (150-250 words)" required></textarea>
                                <div class="form-text">
                                    <span id="wordCountEN">0</span> kelime
                                </div>
                                <div class="invalid-feedback">Geçerli bir İngilizce özet girmelisiniz (150-250 kelime)</div>
                            </div>
                        </div>

                        <!-- Step 5: Anahtar Kelimeler -->
                        <div id="step5" class="step-content d-none">
                            <h3 class="step-title">
                                <i class="fas fa-key me-2"></i> Anahtar Kelimeler
                            </h3>
                            <div class="alert alert-info mb-4">
                                En az 3, en fazla 5 anahtar kelime giriniz. Anahtar kelimeleri virgül ile ayırınız.
                            </div>
                            <div class="mb-4">
                                <label for="keywordsTR" class="form-label">Türkçe Anahtar Kelimeler <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="keywordsTR" name="anahtar_kelimeler_tr" rows="3"
                                        placeholder="Örnek: yapay zeka, makine öğrenmesi, derin öğrenme" required></textarea>
                                <div class="form-text">
                                    <span id="keywordCountTR">0</span> anahtar kelime
                                </div>
                                <div class="invalid-feedback">Geçerli sayıda anahtar kelime girmelisiniz (3-5 adet)</div>
                            </div>
                            <div class="mb-3">
                                <label for="keywordsEN" class="form-label">İngilizce Anahtar Kelimeler <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="keywordsEN" name="anahtar_kelimeler_en" rows="3"
                                        placeholder="Example: artificial intelligence, machine learning, deep learning" required></textarea>
                                <div class="form-text">
                                    <span id="keywordCountEN">0</span> anahtar kelime
                                </div>
                                <div class="invalid-feedback">Geçerli sayıda anahtar kelime girmelisiniz (3-5 adet)</div>
                            </div>
                        </div>

                        <!-- Step 6: Referanslar -->
                        <div id="step6" class="step-content d-none">
                            <h3 class="step-title">
                                <i class="fas fa-quote-right me-2"></i> Referanslar
                            </h3>

                            <!-- Referans Kuralları -->
                            <div class="alert alert-warning">
                                <h6><i class="fas fa-info-circle me-2"></i>Referans Kuralları:</h6>
                                <ul class="mb-0">
                                    <li>En az <strong>1 referans</strong> eklemelisiniz</li>
                                    <li>Her referans <strong>en az 20 karakter</strong> olmalıdır</li>
                                    <li>APA formatı önerilir: <em>Yazar, A. (Yıl). Başlık. Dergi, Cilt(Sayı), Sayfa.</em></li>
                                </ul>
                            </div>

                            <!-- Yöntem Seçimi -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Referans Ekleme Yöntemi Seçin</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="method-option card h-100 border-2 border-primary" data-method="single">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-plus-circle fa-3x text-primary mb-3"></i>
                                                    <h5>Tek Tek Ekle</h5>
                                                    <p class="text-muted">Her referansı ayrı ayrı ekleyin</p>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="referenceMethod" id="methodSingle" value="single" checked>
                                                        <label class="form-check-label" for="methodSingle">
                                                            Bu yöntemi seç
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="method-option card h-100 border-2 border-light" data-method="bulk">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-paste fa-3x text-success mb-3"></i>
                                                    <h5>Toplu Ekle</h5>
                                                    <p class="text-muted">Tüm referansları tek seferde ekleyin</p>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="referenceMethod" id="methodBulk" value="bulk">
                                                        <label class="form-check-label" for="methodBulk">
                                                            Bu yöntemi seç
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tek Tek Ekleme Arayüzü -->
                            <div id="singleReferenceUI">
                                <div class="alert alert-info mb-4">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Her bir referansı ayrı ayrı ekleyin. "Yeni Referans Ekle" butonu ile yeni referans alanı açabilirsiniz.
                                    <br><small class="mt-1"><strong>APA Format Örneği:</strong> Smith, J. (2023). Başlık. <em>Dergi Adı</em>, <em>Cilt</em>(Sayı), Sayfa.</small>
                                </div>

                                <div id="referencesContainer" class="mb-3">
                                    <!-- Dinamik olarak referans alanları buraya eklenecek -->
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <button type="button" class="btn btn-success" onclick="addNewReference()">
                                        <i class="fas fa-plus me-2"></i>Yeni Referans Ekle
                                    </button>
                                    <span class="text-muted">
                                        <span id="singleReferenceCount">0</span> / 50 referans
                                    </span>
                                </div>
                            </div>

                            <!-- Toplu Ekleme Arayüzü -->
                            <div id="bulkReferenceUI" class="d-none">
                                <div class="alert alert-info mb-4">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Tüm referansları toplu olarak ekleyin. Her bir referansı ayrı bir satıra yazın. APA formatında olmalıdır.
                                    <br><small class="mt-1"><strong>Örnek:</strong> Her referansı ayrı satıra yazın. APA formatında olmalıdır.</small>
                                </div>

                                <div class="mb-3">
                                    <label for="bulkReferences" class="form-label">Referanslar (her biri ayrı satırda):</label>
                                    <textarea class="form-control auto-resize" id="bulkReferences" name="bulk_references" rows="10" 
                                            placeholder="Smith, J. (2023). Yapay Zeka ve Eğitim. Journal of Educational Technology, 15(2), 45-67.&#10;Johnson, M. (2022). Makine Öğrenmesi. AI Review, 8(4), 123-145."></textarea>
                                    <div class="form-text">
                                        <span id="bulkReferenceCount">0</span> / 50 referans eklendi
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 7: Özet ve Gönderim -->
                        <div id="step7" class="step-content d-none">
                            <h3 class="step-title">
                                <i class="fas fa-paper-plane me-2"></i> Makale Özeti ve Gönderim
                            </h3>

                            <!-- Özet Kartları -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Makale Türü ve Konusu</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>Tür:</strong> <span id="summaryType">-</span></p>
                                    <p class="mb-0"><strong>Konu:</strong> <span id="summarySubject">-</span></p>
                                </div>
                            </div>

                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Başlıklar</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>Türkçe:</strong> <span id="summaryTitleTR">-</span></p>
                                    <p class="mb-0"><strong>İngilizce:</strong> <span id="summaryTitleEN">-</span></p>
                                </div>
                            </div>

                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Anahtar Kelimeler</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>Türkçe:</strong> <span id="summaryKeywordsTR">-</span></p>
                                    <p class="mb-0"><strong>İngilizce:</strong> <span id="summaryKeywordsEN">-</span></p>
                                </div>
                            </div>

                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Referanslar</h6>
                                </div>
                                <div class="card-body">
                                    <ol id="summaryReferences" class="mb-0">
                                        <li>-</li>
                                    </ol>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="submitConfirmation" required>
                                    <label class="form-check-label" for="submitConfirmation">
                                        Yukarıdaki bilgilerin doğruluğunu onaylıyorum ve makaleyi göndermek istiyorum.
                                    </label>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i> Makaleyi Gönder
                                </button>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="step-content">
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary btn-navigation" id="prevBtn" disabled>
                                    <i class="fas fa-arrow-left me-2"></i>Önceki
                                </button>
                                <button type="button" class="btn btn-primary btn-navigation" id="nextBtn">
                                    Sonraki<i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </form>
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

    <!-- Wizard Logic -->
    <script>
        let currentStep = 0;
        const totalSteps = 8;
        const completedSteps = [];
        let referenceCount = 0;

        // Referans kuralları
        const REFERENCE_RULES = {
            minReferences: 1,
            maxReferences: 50,
            minLength: 20,
            maxLength: 1000
        };

        // Sayfa yüklendiğinde çalıştır
        document.addEventListener('DOMContentLoaded', function() {
            initializeFieldValidation();
            initializeMethodSelection();
            initializeAutoResize();
            initializeBulkReferenceCounter();
            
            // İlk referansı ekle
            addNewReference();
        });

        // GENEL DOĞRULAMA SİSTEMİ
        function initializeFieldValidation() {
            const requiredFields = document.querySelectorAll('input[required], select[required], textarea[required]');
            
            requiredFields.forEach(field => {
                validateField(field);
                
                field.addEventListener('change', function() {
                    validateField(this);
                });
                
                if (field.tagName === 'INPUT' || field.tagName === 'TEXTAREA') {
                    field.addEventListener('input', function() {
                        validateField(this);
                    });
                }
            });
        }

        function validateField(field) {
            const isValid = checkFieldValidity(field);
            
            if (isValid) {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
            } else {
                field.classList.remove('is-valid');
                field.classList.add('is-invalid');
            }
            
            return isValid;
        }

        function checkFieldValidity(field) {
            if (field.type === 'checkbox') return field.checked;
            if (field.type === 'select-one') return field.value !== '';
            
            // Özel doğrulamalar
            if (field.id === 'titleTR' || field.id === 'titleEN') {
                return field.value.length >= 10 && field.value.length <= 500;
            }
            
            if (field.id === 'abstractTR' || field.id === 'abstractEN') {
                const wordCount = countWords(field.value);
                return wordCount >= 150 && wordCount <= 250;
            }
            
            if (field.id === 'keywordsTR' || field.id === 'keywordsEN') {
                const keywordCount = countKeywords(field.value);
                return keywordCount >= 3 && keywordCount <= 5;
            }
            
            return field.value.trim() !== '';
        }

        // STEP NAVIGATION
        document.getElementById('nextBtn').addEventListener('click', function() {
            if (validateStep(currentStep)) {
                markStepCompleted(currentStep);
                showStep(currentStep + 1);
            }
        });

        document.getElementById('prevBtn').addEventListener('click', function() {
            showStep(currentStep - 1);
        });

        document.querySelectorAll('.step-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const step = parseInt(this.dataset.step);
                if (step <= currentStep || completedSteps.includes(step - 1)) {
                    showStep(step);
                }
            });
        });

        function showStep(step) {
            document.getElementById('step' + currentStep).classList.add('d-none');
            document.querySelector('[data-step="' + currentStep + '"]').classList.remove('active');

            currentStep = step;
            document.getElementById('step' + currentStep).classList.remove('d-none');
            document.querySelector('[data-step="' + currentStep + '"]').classList.add('active');

            document.getElementById('prevBtn').disabled = currentStep === 0;
            document.getElementById('nextBtn').style.display = currentStep === totalSteps - 1 ? 'none' : 'block';

            const progress = ((currentStep + 1) / totalSteps) * 100;
            document.getElementById('progressBar').style.width = progress + '%';
            document.getElementById('progressText').textContent = Math.round(progress) + '%';

            window.scrollTo({ top: 0, behavior: 'smooth' });

            if (currentStep === totalSteps - 1) {
                updateSummary();
            }
        }

        function validateStep(step) {
            const stepEl = document.getElementById('step' + step);
            const inputs = stepEl.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!checkFieldValidity(input)) {
                    isValid = false;
                }
            });

            // Step 6: Referans validasyonu
            if (step === 6) {
                if (!validateReferences()) {
                    isValid = false;
                }
            }

            return isValid;
        }

        function validateReferences() {
            const selectedMethod = document.querySelector('input[name="referenceMethod"]:checked').value;
            let references = [];
            
            // Referansları topla
            if (selectedMethod === 'single') {
                const singleRefs = document.querySelectorAll('.reference-input');
                singleRefs.forEach(ref => {
                    if (ref.value.trim().length >= 20) {
                        references.push(ref.value.trim());
                    }
                });
            } else {
                const bulkText = document.getElementById('bulkReferences').value.trim();
                if (bulkText) {
                    references = bulkText.split('\n')
                        .filter(line => line.trim().length >= 20)
                        .map(line => line.trim());
                }
            }
            
            // Minimum referans sayısı kontrolü
            if (references.length < REFERENCE_RULES.minReferences) {
                showReferenceError(`En az ${REFERENCE_RULES.minReferences} referans eklemelisiniz.`);
                return false;
            }
            
            hideReferenceError();
            return true;
        }

        function showReferenceError(message) {
            let errorContainer = document.getElementById('referenceErrors');
            
            if (!errorContainer) {
                errorContainer = document.createElement('div');
                errorContainer.id = 'referenceErrors';
                const step6 = document.getElementById('step6');
                const firstCard = step6.querySelector('.card');
                step6.insertBefore(errorContainer, firstCard);
            }

            errorContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>${message}
                </div>
            `;
        }

        function hideReferenceError() {
            const errorContainer = document.getElementById('referenceErrors');
            if (errorContainer) {
                errorContainer.innerHTML = '';
            }
        }

        function markStepCompleted(step) {
            if (!completedSteps.includes(step)) {
                completedSteps.push(step);
                document.querySelector('[data-step="' + step + '"]').classList.add('completed');
            }
        }

        function countWords(text) {
            return text.trim().split(/\s+/).filter(word => word.length > 0).length;
        }

        function countKeywords(text) {
            return text.split(',').filter(k => k.trim().length > 0).length;
        }

        function updateSummary() {
            const typeSelect = document.getElementById('articleType');
            document.getElementById('summaryType').textContent = typeSelect.options[typeSelect.selectedIndex]?.text || '-';
            document.getElementById('summarySubject').textContent = document.getElementById('articleSubject').value || '-';
            document.getElementById('summaryTitleTR').textContent = document.getElementById('titleTR').value || '-';
            document.getElementById('summaryTitleEN').textContent = document.getElementById('titleEN').value || '-';
            document.getElementById('summaryKeywordsTR').textContent = document.getElementById('keywordsTR').value || '-';
            document.getElementById('summaryKeywordsEN').textContent = document.getElementById('keywordsEN').value || '-';

            // Referansları topla
            let references = [];
            const selectedMethod = document.querySelector('input[name="referenceMethod"]:checked').value;
            
            if (selectedMethod === 'single') {
                const singleRefs = document.querySelectorAll('.reference-input');
                singleRefs.forEach(ref => {
                    if (ref.value.trim()) {
                        references.push(ref.value.trim());
                    }
                });
            } else {
                const bulkText = document.getElementById('bulkReferences').value.trim();
                if (bulkText) {
                    references = bulkText.split('\n').filter(line => line.trim().length > 0);
                }
            }

            // Özeti güncelle
            const summaryList = document.getElementById('summaryReferences');
            summaryList.innerHTML = '';

            if (references.length === 0) {
                summaryList.innerHTML = '<li>-</li>';
            } else {
                references.forEach((ref, index) => {
                    const li = document.createElement('li');
                    li.textContent = ref;
                    summaryList.appendChild(li);
                });
            }
        }

        // Character counters
        document.getElementById('titleTR').addEventListener('input', function() {
            document.getElementById('charCountTR').textContent = this.value.length;
        });

        document.getElementById('titleEN').addEventListener('input', function() {
            document.getElementById('charCountEN').textContent = this.value.length;
        });

        // Word counters
        document.getElementById('abstractTR').addEventListener('input', function() {
            document.getElementById('wordCountTR').textContent = countWords(this.value);
        });

        document.getElementById('abstractEN').addEventListener('input', function() {
            document.getElementById('wordCountEN').textContent = countWords(this.value);
        });

        // Keyword counters
        document.getElementById('keywordsTR').addEventListener('input', function() {
            document.getElementById('keywordCountTR').textContent = countKeywords(this.value);
        });

        document.getElementById('keywordsEN').addEventListener('input', function() {
            document.getElementById('keywordCountEN').textContent = countKeywords(this.value);
        });

        // REFERANS YÖNETİMİ
        function initializeMethodSelection() {
            const methodOptions = document.querySelectorAll('.method-option');
            const methodRadios = document.querySelectorAll('input[name="referenceMethod"]');
            
            methodOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const method = this.dataset.method;
                    document.getElementById(`method${method.charAt(0).toUpperCase() + method.slice(1)}`).checked = true;
                    showReferenceMethod(method);
                });
            });
            
            methodRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    showReferenceMethod(this.value);
                });
            });
        }

        function showReferenceMethod(method) {
            document.querySelectorAll('.method-option').forEach(option => {
                option.classList.remove('border-primary');
                option.classList.add('border-light');
            });
            
            document.querySelector(`.method-option[data-method="${method}"]`).classList.add('border-primary');
            document.querySelector(`.method-option[data-method="${method}"]`).classList.remove('border-light');
            
            if (method === 'single') {
                document.getElementById('singleReferenceUI').classList.remove('d-none');
                document.getElementById('bulkReferenceUI').classList.add('d-none');
            } else {
                document.getElementById('singleReferenceUI').classList.add('d-none');
                document.getElementById('bulkReferenceUI').classList.remove('d-none');
            }
            
            // Validasyonu tetikle
            setTimeout(() => validateReferences(), 100);
        }

        function initializeAutoResize() {
            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('auto-resize') || e.target.classList.contains('reference-input')) {
                    autoResizeTextarea(e.target);
                }
            });
            
            document.querySelectorAll('.auto-resize, .reference-input').forEach(textarea => {
                autoResizeTextarea(textarea);
            });
        }

        function autoResizeTextarea(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = (textarea.scrollHeight) + 'px';
        }

        function initializeBulkReferenceCounter() {
            const bulkTextarea = document.getElementById('bulkReferences');
            if (bulkTextarea) {
                bulkTextarea.addEventListener('input', function() {
                    const lines = this.value.split('\n').filter(line => line.trim().length > 0);
                    document.getElementById('bulkReferenceCount').textContent = lines.length;
                    
                    if (lines.length > REFERENCE_RULES.maxReferences) {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                    }
                    
                    setTimeout(() => validateReferences(), 100);
                });
            }
        }

        function addNewReference() {
            const currentReferences = document.querySelectorAll('.reference-input');
            if (currentReferences.length >= REFERENCE_RULES.maxReferences) {
                alert(`En fazla ${REFERENCE_RULES.maxReferences} referans ekleyebilirsiniz.`);
                return;
            }

            referenceCount++;
            const container = document.getElementById('referencesContainer');

            const newReferenceDiv = document.createElement('div');
            newReferenceDiv.className = 'reference-item mb-3';
            newReferenceDiv.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label mb-0">Referans #${referenceCount}</label>
                    <small class="text-muted"><span class="char-count">0</span> karakter</small>
                </div>
                <div class="input-group">
                    <textarea class="form-control reference-input auto-resize" rows="1" name="referanslar[]"
                            placeholder="Smith, J. (2023). Yapay Zeka ve Eğitim. Journal of Educational Technology, 15(2), 45-67."></textarea>
                    <button type="button" class="btn btn-outline-danger" onclick="removeReference(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;

            container.appendChild(newReferenceDiv);
            
            const newInput = newReferenceDiv.querySelector('.reference-input');
            const charCount = newReferenceDiv.querySelector('.char-count');
            
            newInput.addEventListener('input', function() {
                charCount.textContent = this.value.length;
                autoResizeTextarea(this);
                validateReferenceField(this);
                updateSingleReferenceCounter();
                setTimeout(() => validateReferences(), 100);
            });
            
            updateReferenceNumbers();
            updateSingleReferenceCounter();
        }

        function validateReferenceField(field) {
            const value = field.value.trim();
            
            if (value === '') {
                field.classList.remove('is-valid', 'is-invalid');
                return false;
            }
            
            if (value.length < 20) {
                field.classList.remove('is-valid');
                field.classList.add('is-invalid');
                return false;
            }
            
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            return true;
        }

        function removeReference(button) {
            const referenceItem = button.closest('.reference-item');
            referenceItem.remove();
            updateReferenceNumbers();
            updateSingleReferenceCounter();
            hideReferenceError();
            setTimeout(() => validateReferences(), 100);
        }

        function updateReferenceNumbers() {
            const items = document.querySelectorAll('.reference-item');
            referenceCount = items.length;

            items.forEach((item, index) => {
                const label = item.querySelector('label');
                label.textContent = `Referans #${index + 1}`;

                const deleteBtn = item.querySelector('.btn-outline-danger');
                if (items.length === 1) {
                    deleteBtn.disabled = true;
                } else {
                    deleteBtn.disabled = false;
                }
            });
        }

        function updateSingleReferenceCounter() {
            const references = document.querySelectorAll('.reference-input');
            const validReferences = Array.from(references).filter(ref => ref.value.trim().length >= 20).length;
            document.getElementById('singleReferenceCount').textContent = validReferences;
        }
    </script>
</body>
</html>