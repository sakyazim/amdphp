<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= $_SESSION['_csrf_token'] ?? '' ?>">
    <title>Yeni Makale - AMDS</title>

    <!-- External CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link href="<?= base_url('assets/css/create-wizard.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/author-search.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/reviewer-manager.css') ?>" rel="stylesheet">
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
                                <i class="fas fa-users me-2"></i> <span>Yazarlar</span>
                                <span class="step-status"></span>
                            </a>
                            <a href="#" class="step-link" data-step="8">
                                <i class="fas fa-file-upload me-2"></i> <span>Dosyalar</span>
                                <span class="step-status"></span>
                            </a>
                            <a href="#" class="step-link" data-step="9">
                                <i class="fas fa-user-tie me-2"></i> <span>Hakemler</span>
                                <span class="step-status"></span>
                            </a>
                            <a href="#" class="step-link" data-step="10">
                                <i class="fas fa-envelope me-2"></i> <span>Editöre Not</span>
                                <span class="step-status"></span>
                            </a>
                            <a href="#" class="step-link" data-step="11">
                                <i class="fas fa-tasks me-2"></i> <span>Kontrol Listesi</span>
                                <span class="step-status"></span>
                            </a>
                            <a href="#" class="step-link" data-step="12">
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
                        <input type="hidden" name="_csrf_token" value="<?= $_SESSION['_csrf_token'] ?? '' ?>">

                        <!-- Current Step Tracker (for draft system) -->
                        <input type="hidden" name="current_step" id="current_step" value="0">

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
                                            placeholder="1. Smith, J. (2023). Yapay Zeka ve Eğitim. Journal of Educational Technology, 15(2), 45-67.&#10;2. Johnson, M. (2022). Makine Öğrenmesi. AI Review, 8(4), 123-145.&#10;3. Brown, K. (2021). Deep Learning. Science, 12(1), 10-20."></textarea>
                                    <div class="form-text">
                                        <span id="bulkReferenceCount">0</span> satır
                                    </div>
                                </div>

                                <button type="button" class="btn btn-primary" onclick="parseBulkReferences()">
                                    <i class="fas fa-check me-2"></i>Referansları İşle ve Parse Et
                                </button>

                                <!-- Parse sonuçları buraya eklenecek -->
                                <div id="bulkParsePreview"></div>
                            </div>
                        </div>

                        <!-- Step 7: Yazarlar -->
                        <div id="step7" class="step-content d-none">
                            <h3 class="step-title mb-4">
                                <i class="fas fa-users me-2"></i> Yazarlar
                                <span class="badge bg-primary ms-2 float-end" id="authorCount">0 Yazar</span>
                            </h3>

                            <!-- HIZLI YAZAR ARAMA -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-search me-2"></i>Hızlı Yazar Ekleme
                                    </h6>
                                    <small class="text-muted">Email veya ORCID ile yazar arayın, sistem otomatik bilgileri dolduracak</small>
                                </div>
                                <div class="card-body">
                                    <!-- Email ile Arama -->
                                    <div class="row mb-3">
                                        <div class="col-md-8">
                                            <label class="form-label">Email ile Yazar Ara</label>
                                            <div class="author-search-container">
                                                <input type="email" class="form-control" id="emailSearch" placeholder="yazar@universite.edu.tr">
                                                <div id="emailSearchResults" class="author-search-results"></div>
                                            </div>
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle"></i> Email girerken sistem otomatik arama yapacak
                                            </small>
                                        </div>
                                    </div>

                                    <!-- ORCID ile Arama -->
                                    <div class="row">
                                        <div class="col-md-8">
                                            <label class="form-label">ORCID ile Yazar Ara</label>
                                            <div class="author-search-container">
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fab fa-orcid"></i>
                                                    </span>
                                                    <input type="text" class="form-control" id="orcidSearch" placeholder="0000-0000-0000-0000" maxlength="19">
                                                    <div id="orcidSearchResults" class="author-search-results"></div>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle"></i> ORCID girerken sistem otomatik arama yapacak
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- YAZAR EKLEME FORMU -->
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0" id="authorFormTitle">Yeni Yazar Ekle</h5>
                                </div>
                                <div class="card-body">
                                    <div id="authorForm">
                                        <!-- Gizli ID alanı - düzenleme için -->
                                        <input type="hidden" id="authorId" value="">
                                        
                                        <!-- Kişisel Bilgiler -->
                                        <h6 class="form-section-title">Kişisel Bilgiler</h6>
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <label class="form-label">Ünvan <span class="text-danger">*</span></label>
                                                <select class="form-select" id="authorTitle">
                                                    <option value="">Seçiniz</option>
                                                    <option value="prof">Prof. Dr.</option>
                                                    <option value="assocprof">Doç. Dr.</option>
                                                    <option value="assistprof">Dr. Öğr. Üyesi</option>
                                                    <option value="dr">Dr.</option>
                                                    <option value="res">Arş. Gör.</option>
                                                    <option value="other">Diğer</option>
                                                </select>
                                                <div class="invalid-feedback">Lütfen bir ünvan seçin</div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Ad <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="authorFirstName">
                                                <div class="invalid-feedback">Lütfen yazarın adını girin</div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">İkinci Ad</label>
                                                <input type="text" class="form-control" id="authorMiddleName">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Soyad <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="authorLastName">
                                                <div class="invalid-feedback">Lütfen yazarın soyadını girin</div>
                                            </div>
                                        </div>

                                        <!-- İletişim Bilgileri -->
                                        <h6 class="form-section-title mt-4">İletişim Bilgileri</h6>
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Telefon</label>
                                                <input type="tel" class="form-control" id="authorPhone">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Email 1 <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="authorEmail1">
                                                <div class="invalid-feedback">Lütfen geçerli bir e-posta adresi girin</div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Email 2</label>
                                                <input type="email" class="form-control" id="authorEmail2">
                                            </div>
                                        </div>
                                        
                                        <!-- Kurum Bilgileri -->
                                        <h6 class="form-section-title mt-4">Kurum Bilgileri</h6>
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Departman</label>
                                                <input type="text" class="form-control" id="authorDepartment">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Kurum</label>
                                                <input type="text" class="form-control" id="authorInstitution">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Ülke</label>
                                                <select class="form-select" id="authorCountry">
                                                    <option value="">Seçiniz</option>
                                                    <option value="TR">Türkiye</option>
                                                    <option value="US">Amerika Birleşik Devletleri</option>
                                                    <option value="GB">Birleşik Krallık</option>
                                                    <option value="DE">Almanya</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <!-- Akademik Kimlik -->
                                        <h6 class="form-section-title mt-4">Akademik Kimlik</h6>
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label for="authorOrcidId" class="form-label">ORCID ID</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">https://orcid.org/</span>
                                                    <input type="text" class="form-control" id="authorOrcidId" 
                                                        pattern="[0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{4}"
                                                        placeholder="0000-0000-0000-0000">
                                                    <div class="invalid-feedback">Lütfen geçerli bir ORCID ID girin (0000-0000-0000-0000 formatında)</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Makale Bilgileri -->
                                        <h6 class="form-section-title mt-4">Makale Bilgileri</h6>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Yazar Sırası <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="authorOrder" min="1">
                                                <div class="invalid-feedback">Lütfen yazar sırası girin</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Yazar Tipi <span class="text-danger">*</span></label>
                                                <select class="form-select" id="authorType">
                                                    <option value="">Seçiniz</option>
                                                    <option value="primary">Birincil Yazar</option>
                                                    <option value="corresponding">Sorumlu Yazar</option>
                                                    <option value="contributor">Katkıda Bulunan</option>
                                                </select>
                                                <div class="invalid-feedback">Lütfen yazar tipini seçin</div>
                                            </div>
                                        </div>
                                        
                                        <!-- Form Butonları -->
                                        <div class="d-flex justify-content-between mt-4">
                                            <button type="button" class="btn btn-primary" id="authorSubmitBtn">
                                                <i class="fas fa-plus me-2"></i>Yazar Ekle
                                            </button>
                                            <button type="button" class="btn btn-secondary d-none" id="authorCancelBtn">
                                                <i class="fas fa-times me-2"></i>İptal
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- YAZARLAR TABLOSU -->
                            <div class="card">
                                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Yazarlar Listesi</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="authorsTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="5%">Sıra</th>
                                                    <th width="85%">Yazar Bilgileri</th>
                                                    <th width="10%">İşlem</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Yazarlar JavaScript ile buraya eklenecek -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 8: Dosyalar -->
                        <div id="step8" class="step-content d-none">
                            <h3 class="step-title">
                                <i class="fas fa-file-upload me-2"></i> Dosyalar
                            </h3>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Dosya yükleme bölümü - içerik yakında eklenecek
                            </div>
                        </div>

                        <!-- Step 9: Hakemler -->
                        <div id="step9" class="step-content d-none">
                            <h3 class="step-title">
                                <i class="fas fa-user-tie me-2"></i> Önerilen Hakemler
                            </h3>

                            <!-- Hakem Kuralları -->
                            <div class="alert alert-warning mb-4">
                                <h6><i class="fas fa-info-circle me-2"></i>Hakem Önerme Kuralları:</h6>
                                <ul class="mb-0">
                                    <li>En az <strong>3 hakem</strong> önermelisiniz</li>
                                    <li>Hakemler makalenizin konusunda <strong>uzman</strong> olmalıdır</li>
                                    <li>Çıkar çatışması olabilecek hakemler <strong>önermeyiniz</strong></li>
                                    <li>Ad, Soyad, Email ve Kurum alanları <strong>zorunludur</strong></li>
                                </ul>
                            </div>

                            <!-- Durum Göstergesi -->
                            <div id="reviewer-status" class="mb-4">
                                <!-- JavaScript ile güncellenecek -->
                            </div>

                            <!-- Hakem Ekleme Formu -->
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-plus-circle me-2"></i>Yeni Hakem Ekle
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div id="reviewer-form">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="reviewer-ad" class="form-label">
                                                    Ad <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" id="reviewer-ad" name="ad">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="reviewer-soyad" class="form-label">
                                                    Soyad <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" id="reviewer-soyad" name="soyad">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="reviewer-email" class="form-label">
                                                    Email <span class="text-danger">*</span>
                                                </label>
                                                <input type="email" class="form-control" id="reviewer-email" name="email">
                                                <small class="form-text text-muted">Geçerli bir email adresi giriniz</small>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="reviewer-kurum" class="form-label">
                                                    Kurum <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" id="reviewer-kurum" name="kurum">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="reviewer-uzmanlik" class="form-label">
                                                    Uzmanlık Alanı
                                                </label>
                                                <input type="text" class="form-control" id="reviewer-uzmanlik" name="uzmanlik_alani"
                                                       placeholder="Örn: Yapay Zeka, Makine Öğrenmesi">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="reviewer-ulke" class="form-label">
                                                    Ülke
                                                </label>
                                                <input type="text" class="form-control" id="reviewer-ulke" name="ulke"
                                                       placeholder="Örn: Türkiye">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label for="reviewer-orcid" class="form-label">
                                                    ORCID iD
                                                    <a href="https://orcid.org" target="_blank" class="text-muted">
                                                        <i class="fas fa-external-link-alt fa-sm"></i>
                                                    </a>
                                                </label>
                                                <input type="text" class="form-control" id="reviewer-orcid" name="orcid"
                                                       placeholder="0000-0001-2345-6789" pattern="\d{4}-\d{4}-\d{4}-\d{3}[\dX]">
                                                <small class="form-text text-muted">Format: 0000-0001-2345-6789</small>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label for="reviewer-notlar" class="form-label">
                                                    Notlar (Neden bu hakemi öneriyorsunuz?)
                                                </label>
                                                <textarea class="form-control" id="reviewer-notlar" name="notlar" rows="2"
                                                          placeholder="Bu hakem neden uygun? Uzmanlık alanı, yayınları, vb."></textarea>
                                            </div>
                                        </div>

                                        <div class="d-grid">
                                            <button type="button" class="btn btn-primary btn-lg" id="reviewer-submit-btn">
                                                <i class="fa fa-plus"></i> Hakem Ekle
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Eklenen Hakemler Listesi -->
                            <div class="card">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">
                                        <i class="fas fa-list me-2"></i>Eklenen Hakemler
                                    </h5>
                                    <h5 class="mb-0">
                                        <span id="reviewer-count" class="badge bg-secondary">0</span> / 3
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div id="reviewers-container">
                                        <!-- JavaScript ile hakemler buraya eklenecek -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 10: Editöre Not -->
                        <div id="step10" class="step-content d-none">
                            <h3 class="step-title">
                                <i class="fas fa-envelope me-2"></i> Editöre Not
                            </h3>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Editöre not bölümü - içerik yakında eklenecek
                            </div>
                        </div>

                        <!-- Step 11: Kontrol Listesi -->
                        <div id="step11" class="step-content d-none">
                            <h3 class="step-title">
                                <i class="fas fa-tasks me-2"></i> Kontrol Listesi
                            </h3>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Kontrol listesi bölümü - içerik yakında eklenecek
                            </div>
                        </div>

                        <!-- Step 12: Makaleyi Gönder -->
                        <div id="step12" class="step-content d-none">
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

                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Yazarlar</h6>
                                </div>
                                <div class="card-body">
                                    <div id="summaryAuthors">-</div>
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
                            <!-- Draft Save Status -->
                            <div class="mb-3 text-center">
                                <small id="save-status" class="text-muted">
                                    <i class="fa fa-clock"></i> Otomatik kayıt aktif (30 saniye)
                                </small>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-secondary btn-navigation" id="prevBtn" disabled>
                                    <i class="fas fa-arrow-left me-2"></i>Önceki
                                </button>

                                <!-- Manuel Kayıt Butonu -->
                                <button type="button" id="manual-save-btn" class="btn btn-outline-secondary">
                                    <i class="fa fa-save"></i> Taslak Kaydet
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

    <!-- Custom JS -->
    <script src="<?= base_url('assets/js/create-wizard.js') ?>"></script>
    <script src="<?= base_url('assets/js/authors-management.js') ?>"></script>
    <script src="<?= base_url('assets/js/author-search.js') ?>"></script>
    <script src="<?= base_url('assets/js/reference-manager.js') ?>"></script>
    <script src="<?= base_url('assets/js/reviewer-manager.js') ?>"></script>

    <script>
        // AuthorSearch modülünü başlat
        document.addEventListener('DOMContentLoaded', function() {
            // AuthorSearch instance'ı oluştur
            const authorSearchInstance = initAuthorSearch({
                apiBaseUrl: '<?= base_url('/api/authors') ?>',
                emailInput: document.getElementById('emailSearch'),
                orcidInput: document.getElementById('orcidSearch'),
                emailResultContainer: document.getElementById('emailSearchResults'),
                orcidResultContainer: document.getElementById('orcidSearchResults'),
                onSelect: function(author) {
                    // Yazar bilgilerini form alanlarına doldur
                    fillAuthorForm(author);
                }
            });

            // Yazar bilgilerini form alanlarına doldur
            function fillAuthorForm(author) {
                // Ünvan
                if (author.title) {
                    const titleSelect = document.getElementById('authorTitle');
                    const titleMap = {
                        'Prof. Dr.': 'prof',
                        'Doç. Dr.': 'assocprof',
                        'Dr. Öğr. Üyesi': 'assistprof',
                        'Dr.': 'dr',
                        'Arş. Gör.': 'res'
                    };
                    titleSelect.value = titleMap[author.title] || 'other';
                }

                // Ad/Soyad
                if (author.first_name) {
                    document.getElementById('authorFirstName').value = author.first_name;
                }
                if (author.last_name) {
                    document.getElementById('authorLastName').value = author.last_name;
                }
                // Eğer full name varsa ve ayrı ayrı yoksa parse et
                if (!author.first_name && !author.last_name && author.name) {
                    const nameParts = author.name.trim().split(' ');
                    if (nameParts.length > 0) {
                        document.getElementById('authorFirstName').value = nameParts[0];
                        if (nameParts.length > 1) {
                            document.getElementById('authorLastName').value = nameParts.slice(1).join(' ');
                        }
                    }
                }

                // Email
                if (author.email) {
                    document.getElementById('authorEmail1').value = author.email;
                }
                if (author.email2) {
                    document.getElementById('authorEmail2').value = author.email2;
                }

                // Telefon
                if (author.phone) {
                    document.getElementById('authorPhone').value = author.phone;
                }

                // Departman
                if (author.department) {
                    document.getElementById('authorDepartment').value = author.department;
                }

                // Kurum
                if (author.institution) {
                    document.getElementById('authorInstitution').value = author.institution;
                }

                // Ülke
                if (author.country) {
                    document.getElementById('authorCountry').value = author.country;
                }

                // ORCID
                if (author.orcid) {
                    document.getElementById('authorOrcidId').value = author.orcid;
                }

                // Success mesajı göster
                const emailInput = document.getElementById('emailSearch');
                const orcidInput = document.getElementById('orcidSearch');

                // Input'ları temizle
                if (emailInput) emailInput.value = '';
                if (orcidInput) orcidInput.value = '';

                // Form alanına focus
                document.getElementById('authorFirstName').focus();

                // Bildirim göster
                showNotification('Yazar bilgileri form alanlarına dolduruldu', 'success');
            }

            // Basit bildirim fonksiyonu
            function showNotification(message, type = 'info') {
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
                alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                alertDiv.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(alertDiv);

                // 3 saniye sonra otomatik kaldır
                setTimeout(() => {
                    alertDiv.remove();
                }, 3000);
            }
        });

        // ReferenceManager modülünü başlat
        initReferenceManager({
            apiBaseUrl: '<?= base_url('/api/references') ?>',
            maxReferences: 50
        });
    </script>

    <!-- Taslak Sistemi -->
    <script src="<?= base_url('assets/js/taslak-sistemi.js') ?>"></script>
    <script>
        // Taslak sistemini başlat
        document.addEventListener('DOMContentLoaded', () => {
            window.taslakSistemi = new TaslakSistemi({
                formSelector: '#wizardForm',
                apiBaseUrl: '<?= base_url('/api/drafts') ?>',
                autoSaveInterval: 30000, // 30 saniye
                autoSaveEnabled: true,
                totalSteps: 13
            });

            taslakSistemi.init();

            // Ana form submit kontrolü - sadece son adımda izin ver
            const wizardForm = document.getElementById('wizardForm');
            if (wizardForm) {
                wizardForm.addEventListener('submit', (e) => {
                    const currentStep = parseInt(document.getElementById('current_step')?.value || 0);
                    if (currentStep < 12) {
                        e.preventDefault();
                        console.log('⚠ Makale gönderimi engellendi - Son adıma ulaşılmadı (Şu an: Adım ' + currentStep + ')');
                        return false;
                    }

                    // Son adımdayız, form submit edilebilir
                    console.log('✓ Makale formu gönderiliyor...');
                    return true;
                });
            }

            // Adım değiştiğinde taslak sistemine bildir
            const originalNextBtn = document.getElementById('nextBtn');
            if (originalNextBtn) {
                originalNextBtn.addEventListener('click', () => {
                    setTimeout(() => {
                        const currentStepEl = document.querySelector('.step-link.active');
                        if (currentStepEl) {
                            const step = parseInt(currentStepEl.dataset.step || 0);
                            taslakSistemi.updateCurrentStep(step);
                        }
                    }, 100);
                });
            }
        });
    </script>
</body>
</html>