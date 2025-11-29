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
                                    <li>Makale türünü doğru seçmelisiniz.</li>
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
                            <div class="alert alert-info mb-4">
                                <i class="fas fa-info-circle me-2"></i>
                                Makalenizde kullandığınız referansları APA formatında giriniz. "Yeni Referans Ekle" butonu ile referans ekleyebilir, her referansın yanındaki çöp kutusu simgesi ile silebilirsiniz.
                            </div>

                            <!-- Referanslar Container -->
                            <div id="referencesContainer" class="mb-3">
                                <!-- İlk referans -->
                                <div class="reference-item mb-3">
                                    <label class="form-label">Referans #1</label>
                                    <div class="input-group">
                                        <textarea class="form-control reference-input" rows="3" name="referanslar[]"
                                                placeholder="Örnek: Smith, J. (2023). Yapay Zeka ve Eğitim. Journal of Educational Technology, 15(2), 45-67."></textarea>
                                        <button type="button" class="btn btn-outline-danger" onclick="removeReference(this)" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-success" onclick="addNewReference()">
                                <i class="fas fa-plus me-2"></i>Yeni Referans Ekle
                            </button>

                            <div class="form-text mt-2">
                                <i class="fas fa-lightbulb me-1"></i> APA formatını kullanın. Her referans için ayrı alan ekleyin.
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

    // Step navigation
    document.getElementById('nextBtn').addEventListener('click', function() {
        if (validateStep(currentStep)) {
            markStepCompleted(currentStep);
            showStep(currentStep + 1);
        }
    });

    document.getElementById('prevBtn').addEventListener('click', function() {
        showStep(currentStep - 1);
    });

    // Step links
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
        // Hide current step
        document.getElementById('step' + currentStep).classList.add('d-none');
        document.querySelector('[data-step="' + currentStep + '"]').classList.remove('active');

        // Show new step
        currentStep = step;
        document.getElementById('step' + currentStep).classList.remove('d-none');
        document.querySelector('[data-step="' + currentStep + '"]').classList.add('active');

        // Update buttons
        document.getElementById('prevBtn').disabled = currentStep === 0;
        document.getElementById('nextBtn').style.display = currentStep === totalSteps - 1 ? 'none' : 'block';

        // Update progress
        const progress = ((currentStep + 1) / totalSteps) * 100;
        document.getElementById('progressBar').style.width = progress + '%';
        document.getElementById('progressText').textContent = Math.round(progress) + '%';

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });

        // Update summary if on last step
        if (currentStep === totalSteps - 1) {
            updateSummary();
        }
    }

    function validateStep(step) {
        const stepEl = document.getElementById('step' + step);
        const inputs = stepEl.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value || (input.type === 'checkbox' && !input.checked)) {
                input.classList.add('is-invalid');
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
            }
        });

        // Custom validations
        if (step === 3) { // Title step
            const titleTR = document.getElementById('titleTR').value;
            const titleEN = document.getElementById('titleEN').value;
            if (titleTR.length < 10 || titleTR.length > 500) {
                document.getElementById('titleTR').classList.add('is-invalid');
                isValid = false;
            }
            if (titleEN.length < 10 || titleEN.length > 500) {
                document.getElementById('titleEN').classList.add('is-invalid');
                isValid = false;
            }
        }

        if (step === 4) { // Abstract step
            const wordCountTR = countWords(document.getElementById('abstractTR').value);
            const wordCountEN = countWords(document.getElementById('abstractEN').value);
            if (wordCountTR < 150 || wordCountTR > 250) {
                document.getElementById('abstractTR').classList.add('is-invalid');
                isValid = false;
            }
            if (wordCountEN < 150 || wordCountEN > 250) {
                document.getElementById('abstractEN').classList.add('is-invalid');
                isValid = false;
            }
        }

        if (step === 5) { // Keywords step
            const keywordCountTR = countKeywords(document.getElementById('keywordsTR').value);
            const keywordCountEN = countKeywords(document.getElementById('keywordsEN').value);
            if (keywordCountTR < 3 || keywordCountTR > 5) {
                document.getElementById('keywordsTR').classList.add('is-invalid');
                isValid = false;
            }
            if (keywordCountEN < 3 || keywordCountEN > 5) {
                document.getElementById('keywordsEN').classList.add('is-invalid');
                isValid = false;
            }
        }

        return isValid;
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

        // Referanslar listesini güncelle
        const references = document.querySelectorAll('.reference-input');
        const summaryList = document.getElementById('summaryReferences');
        summaryList.innerHTML = '';

        if (references.length === 0 || (references.length === 1 && !references[0].value.trim())) {
            summaryList.innerHTML = '<li>-</li>';
        } else {
            references.forEach((ref, index) => {
                if (ref.value.trim()) {
                    const li = document.createElement('li');
                    li.textContent = ref.value.trim();
                    summaryList.appendChild(li);
                }
            });
            if (summaryList.children.length === 0) {
                summaryList.innerHTML = '<li>-</li>';
            }
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



    // Referans yönetimi fonksiyonları
    let referenceCount = 1;

    function addNewReference() {
        referenceCount++;
        const container = document.getElementById('referencesContainer');

        const newReferenceDiv = document.createElement('div');
        newReferenceDiv.className = 'reference-item mb-3';
        newReferenceDiv.innerHTML = `
            <label class="form-label">Referans #${referenceCount}</label>
            <div class="input-group">
                <textarea class="form-control reference-input" rows="3" name="referanslar[]"
                        placeholder="Örnek: Smith, J. (2023). Yapay Zeka ve Eğitim. Journal of Educational Technology, 15(2), 45-67."></textarea>
                <button type="button" class="btn btn-outline-danger" onclick="removeReference(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;

        container.appendChild(newReferenceDiv);
        updateReferenceNumbers();
    }

    function removeReference(button) {
        const referenceItem = button.closest('.reference-item');
        referenceItem.remove();
        updateReferenceNumbers();
    }

    function updateReferenceNumbers() {
        const items = document.querySelectorAll('.reference-item');
        referenceCount = items.length;

        items.forEach((item, index) => {
            const label = item.querySelector('label');
            label.textContent = `Referans #${index + 1}`;

            // İlk referansın sil butonunu devre dışı bırak
            const deleteBtn = item.querySelector('.btn-outline-danger');
            if (items.length === 1) {
                deleteBtn.disabled = true;
            } else {
                deleteBtn.disabled = false;
            }
        });
    }
    </script>
</body>
</html>
