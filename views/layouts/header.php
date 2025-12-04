<?php
// Dil servisini başlat
require_once __DIR__ . '/../../app/Services/LanguageService.php';

use App\Services\LanguageService;

// Veritabanı bağlantısı (mevcut bağlantınızı kullanın)
// $db zaten var olduğunu varsayıyoruz
$tenantId = $_SESSION['tenant_id'] ?? 1;
$lang = new LanguageService($db, $tenantId);

// Mevcut dili al
$currentLang = $lang->getCurrentLanguage();
$isRTL = $lang->isRTL();
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>" dir="<?= $isRTL ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang->get('page_title', 'AMDS - Journal Management System') ?></title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Dil Seçici CSS -->
    <link rel="stylesheet" href="/assets/css/language-switcher.css">

    <!-- Özel CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="<?= $isRTL ? 'rtl' : '' ?>">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="/">
            <i class="fa fa-graduation-cap"></i>
            AMDS
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/">
                        <i class="fa fa-home"></i>
                        <span data-lang-key="nav.home"><?= $lang->get('nav.home', 'Ana Sayfa') ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/articles/create">
                        <i class="fa fa-plus-circle"></i>
                        <span data-lang-key="nav.new_article"><?= $lang->get('nav.new_article', 'Yeni Makale') ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/articles">
                        <i class="fa fa-file-alt"></i>
                        <span data-lang-key="nav.my_articles"><?= $lang->get('nav.my_articles', 'Makalelerim') ?></span>
                    </a>
                </li>

                <!-- Dil Seçici -->
                <li class="nav-item ml-3">
                    <div id="language-switcher"></div>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<main class="container mt-4">
