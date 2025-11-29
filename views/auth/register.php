<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="AMDS - Akademik Makale Değerlendirme Sistemi Kayıt Sayfası">
    <meta property="og:title" content="AMDS Kayıt">
    <meta property="og:description" content="AMDS platformuna kayıt olun">
    <meta property="og:type" content="website">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AMDS Kayıt</title>

    <!-- External CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/kayit.css') ?>">
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
                        <a class="nav-link" href="<?= base_url('/') ?>"><i class="fas fa-home me-2"></i>Ana Sayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-book me-2"></i>Dergiler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-info-circle me-2"></i>Hakkımızda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-headset me-2"></i>İletişim</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container my-5">
        <div class="text-center mb-5">
            <h1 class="fw-bold mb-3">📚 AMDS'ye Kayıt Ol</h1>
            <p class="text-muted">Lütfen aşağıdaki bilgileri eksiksiz doldurunuz</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="login-box">
                    <?php if (isset($error) && !empty($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i><?= $error ?>
                        </div>
                    <?php endif; ?>

                    <form id="registrationForm" method="POST" action="<?= base_url('register') ?>" class="needs-validation" novalidate>
                        <input type="hidden" name="_csrf_token" value="<?= $_SESSION['_csrf_token'] ?? '' ?>">

                        <!-- Kişisel Bilgiler -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Kişisel Bilgiler</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="ad" name="ad" placeholder="Adınız" required>
                                            <label for="ad">Ad</label>
                                            <i class="fas fa-user input-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="soyad" name="soyad" placeholder="Soyadınız" required>
                                            <label for="soyad">Soyad</label>
                                            <i class="fas fa-user input-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Akademik Bilgiler -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Akademik Bilgiler</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="kurum" name="kurum" placeholder="Kurum Adı">
                                            <label for="kurum">Kurum Adı</label>
                                            <i class="fas fa-university input-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="unvan" name="unvan" placeholder="Unvan">
                                            <label for="unvan">Unvan</label>
                                            <i class="fas fa-award input-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- İletişim Bilgileri -->
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>İletişim Bilgileri</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="tel" class="form-control" id="telefon" name="telefon" placeholder="Telefon">
                                            <label for="telefon">Telefon</label>
                                            <i class="fas fa-phone input-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="ulke" name="ulke" placeholder="Ülke">
                                            <label for="ulke">Ülke</label>
                                            <i class="fas fa-globe-europe input-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="sehir" name="sehir" placeholder="Şehir">
                                            <label for="sehir">Şehir</label>
                                            <i class="fas fa-city input-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hesap Bilgileri -->
                        <div class="card mb-4">
                            <div class="card-header bg-warning">
                                <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Hesap Bilgileri</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="form-floating">
                                            <input type="email" class="form-control" id="email" name="email" placeholder="ornek@email.com" required>
                                            <label for="email">E-posta</label>
                                            <i class="fas fa-envelope input-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="password" class="form-control" id="sifre" name="sifre" required minlength="6" placeholder="Şifreniz">
                                            <label for="sifre">Şifre</label>
                                            <i class="fas fa-lock input-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="password" class="form-control" id="sifre_tekrar" name="sifre_tekrar" required placeholder="Şifre tekrar">
                                            <label for="sifre_tekrar">Şifre Tekrar</label>
                                            <i class="fas fa-lock input-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Kayıt Ol
                            </button>
                            <a href="<?= base_url('login') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Giriş Sayfasına Dön
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-3 footer-section">
                    <h5>Kurumsal</h5>
                    <ul>
                        <li><a href="#" class="footer-link">
                            <i class="fas fa-info-circle"></i>Hakkımızda
                        </a></li>
                        <li><a href="#" class="footer-link">
                            <i class="fas fa-briefcase"></i>Kariyer
                        </a></li>
                        <li><a href="#" class="footer-link">
                            <i class="fas fa-newspaper"></i>Blog
                        </a></li>
                    </ul>
                </div>

                <div class="col-md-3 footer-section">
                    <h5>Dergiler</h5>
                    <ul>
                        <li><a href="#" class="footer-link">
                            <i class="fas fa-graduation-cap"></i>Tüm Dergiler
                        </a></li>
                        <li><a href="#" class="footer-link">
                            <i class="fas fa-certificate"></i>Yayın İlkeleri
                        </a></li>
                        <li><a href="#" class="footer-link">
                            <i class="fas fa-chalkboard-teacher"></i>Yazarlar İçin
                        </a></li>
                    </ul>
                </div>

                <div class="col-md-3 footer-section">
                    <h5>Yardım</h5>
                    <ul>
                        <li><a href="#" class="footer-link">
                            <i class="fas fa-question-circle"></i>SSS
                        </a></li>
                        <li><a href="#" class="footer-link">
                            <i class="fas fa-headset"></i>Destek
                        </a></li>
                        <li><a href="#" class="footer-link">
                            <i class="fas fa-envelope"></i>İletişim
                        </a></li>
                    </ul>
                </div>

                <div class="col-md-3 footer-section">
                    <h5>Bizi Takip Edin</h5>
                    <div class="social-icons mt-4">
                        <a href="#" class="social-icon">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                    <p class="mt-3 small text-light">AMDS © 2025 | Tüm hakları saklıdır.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
