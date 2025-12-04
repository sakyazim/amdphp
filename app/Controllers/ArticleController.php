<?php
namespace App\Controllers;

use App\Models\Article;
use App\Middleware\AuthMiddleware;
use Core\Router;

/**
 * AMDS - Article Controller
 * Makale yonetim islemleri
 */
class ArticleController
{
    private $tenant;

    public function __construct()
    {
        $this->tenant = current_tenant();
    }

    /**
     * Makale listesi sayfasi
     */
    public function index(): void
    {
        // Giriş kontrolü
        AuthMiddleware::requireAuth();

        if (!$this->tenant) {
            Router::json(['error' => true, 'message' => 'Tenant bulunamadı'], 400);
            return;
        }

        // Pagination parametreleri
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        // Filtreleme parametreleri
        $filters = [
            'durum' => $_GET['durum'] ?? '',
            'makale_turu' => $_GET['makale_turu'] ?? '',
            'search' => $_GET['search'] ?? '',
        ];

        // Makaleleri getir
        $makaleler = Article::getAll($this->tenant->database_name, $perPage, $offset, $filters);
        $totalCount = Article::count($this->tenant->database_name, $filters);
        $totalPages = ceil($totalCount / $perPage);

        // View'e gönder
        $this->view('articles/index', [
            'title' => 'Makaleler',
            'makaleler' => $makaleler,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalCount' => $totalCount,
            'filters' => $filters,
            'statusList' => Article::getStatusList(),
            'typeList' => Article::getTypeList(),
            'subjectList' => Article::getSubjectList(),
        ]);
    }

    /**
     * Makale detay sayfasi
     */
    public function show(int $id): void
    {
        // Giriş kontrolü
        AuthMiddleware::requireAuth();

        if (!$this->tenant) {
            Router::json(['error' => true, 'message' => 'Tenant bulunamadı'], 400);
            return;
        }

        // Makaleyi getir
        $makale = Article::findById($id, $this->tenant->database_name);

        if (!$makale) {
            $_SESSION['error'] = 'Makale bulunamadı';
            Router::redirect('/makaleler');
            return;
        }

        // View'e gönder
        $this->view('articles/show', [
            'title' => $makale['baslik_tr'],
            'makale' => $makale,
            'statusList' => Article::getStatusList(),
            'typeList' => Article::getTypeList(),
        ]);
    }

    /**
     * Yeni makale formu
     */
    public function create(): void
    {
        // Giriş kontrolü
        AuthMiddleware::requireAuth();

        if (!$this->tenant) {
            Router::json(['error' => true, 'message' => 'Tenant bulunamadı'], 400);
            return;
        }

        // View'e gönder
        $this->view('articles/create', [
            'title' => 'Yeni Makale',
            'typeList' => Article::getTypeList(),
            'subjectList' => Article::getSubjectList(),
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null,
        ]);

        unset($_SESSION['error'], $_SESSION['success']);
    }

    /**
     * Makale kaydetme
     */
    public function store(): void
    {
        // Giriş kontrolü
        AuthMiddleware::requireAuth();

        // CSRF kontrolü
        AuthMiddleware::requireCsrfToken();

        if (!$this->tenant) {
            Router::json(['error' => true, 'message' => 'Tenant bulunamadı'], 400);
            return;
        }

        // Form verilerini al
        $data = [
            'baslik_tr' => trim($_POST['baslik_tr'] ?? ''),
            'baslik_en' => trim($_POST['baslik_en'] ?? ''),
            'ozet_tr' => trim($_POST['ozet_tr'] ?? ''),
            'ozet_en' => trim($_POST['ozet_en'] ?? ''),
            'anahtar_kelimeler_tr' => trim($_POST['anahtar_kelimeler_tr'] ?? ''),
            'anahtar_kelimeler_en' => trim($_POST['anahtar_kelimeler_en'] ?? ''),
            'makale_turu' => $_POST['makale_turu'] ?? '',
            'makale_konusu' => $_POST['makale_konusu'] ?? '',
            'referanslar' => $_POST['referanslar'] ?? [],
        ];

        // Validasyon
        $errors = [];

        if (empty($data['baslik_tr'])) {
            $errors[] = 'Türkçe başlık gereklidir';
        }

        if (empty($data['baslik_en'])) {
            $errors[] = 'İngilizce başlık gereklidir';
        }

        if (empty($data['ozet_tr'])) {
            $errors[] = 'Türkçe özet gereklidir';
        }

        if (empty($data['ozet_en'])) {
            $errors[] = 'İngilizce özet gereklidir';
        }

        if (empty($data['anahtar_kelimeler_tr'])) {
            $errors[] = 'Türkçe anahtar kelimeler gereklidir';
        }

        if (empty($data['anahtar_kelimeler_en'])) {
            $errors[] = 'İngilizce anahtar kelimeler gereklidir';
        }

        if (empty($data['makale_turu'])) {
            $errors[] = 'Makale türü seçilmelidir';
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            Router::redirect('/makaleler/yeni');
            return;
        }

        try {
            // Makaleyi oluştur
            $makaleId = Article::create($data, $this->tenant->database_name);

            $_SESSION['success'] = 'Makale başarıyla oluşturuldu';
            Router::redirect('/makaleler/' . $makaleId);
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Makale oluşturulurken bir hata oluştu: ' . $e->getMessage();
            Router::redirect('/makaleler/yeni');
        }
    }

    /**
     * Makale düzenleme formu
     */
    public function edit(int $id): void
    {
        // Giriş kontrolü
        AuthMiddleware::requireAuth();

        if (!$this->tenant) {
            Router::json(['error' => true, 'message' => 'Tenant bulunamadı'], 400);
            return;
        }

        // Makaleyi getir
        $makale = Article::findById($id, $this->tenant->database_name);

        if (!$makale) {
            $_SESSION['error'] = 'Makale bulunamadı';
            Router::redirect('/makaleler');
            return;
        }

        // View'e gönder
        $this->view('articles/edit', [
            'title' => 'Makale Düzenle',
            'makale' => $makale,
            'typeList' => Article::getTypeList(),
            'subjectList' => Article::getSubjectList(),
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null,
        ]);

        unset($_SESSION['error'], $_SESSION['success']);
    }

    /**
     * Makale güncelleme
     */
    public function update(int $id): void
    {
        // Giriş kontrolü
        AuthMiddleware::requireAuth();

        // CSRF kontrolü
        AuthMiddleware::requireCsrfToken();

        if (!$this->tenant) {
            Router::json(['error' => true, 'message' => 'Tenant bulunamadı'], 400);
            return;
        }

        // Makaleyi kontrol et
        $makale = Article::findById($id, $this->tenant->database_name);

        if (!$makale) {
            $_SESSION['error'] = 'Makale bulunamadı';
            Router::redirect('/makaleler');
            return;
        }

        // Form verilerini al
        $data = [
            'baslik_tr' => trim($_POST['baslik_tr'] ?? ''),
            'baslik_en' => trim($_POST['baslik_en'] ?? ''),
            'ozet_tr' => trim($_POST['ozet_tr'] ?? ''),
            'ozet_en' => trim($_POST['ozet_en'] ?? ''),
            'anahtar_kelimeler_tr' => trim($_POST['anahtar_kelimeler_tr'] ?? ''),
            'anahtar_kelimeler_en' => trim($_POST['anahtar_kelimeler_en'] ?? ''),
            'makale_turu' => $_POST['makale_turu'] ?? '',
            'makale_konusu' => $_POST['makale_konusu'] ?? '',
            'referanslar' => $_POST['referanslar'] ?? [],
        ];

        // Validasyon
        $errors = [];

        if (empty($data['baslik_tr'])) {
            $errors[] = 'Türkçe başlık gereklidir';
        }

        if (empty($data['baslik_en'])) {
            $errors[] = 'İngilizce başlık gereklidir';
        }

        if (empty($data['ozet_tr'])) {
            $errors[] = 'Türkçe özet gereklidir';
        }

        if (empty($data['ozet_en'])) {
            $errors[] = 'İngilizce özet gereklidir';
        }

        if (empty($data['anahtar_kelimeler_tr'])) {
            $errors[] = 'Türkçe anahtar kelimeler gereklidir';
        }

        if (empty($data['anahtar_kelimeler_en'])) {
            $errors[] = 'İngilizce anahtar kelimeler gereklidir';
        }

        if (empty($data['makale_turu'])) {
            $errors[] = 'Makale türü seçilmelidir';
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            Router::redirect('/makaleler/' . $id . '/duzenle');
            return;
        }

        try {
            // Makaleyi güncelle
            Article::update($id, $data, $this->tenant->database_name);

            $_SESSION['success'] = 'Makale başarıyla güncellendi';
            Router::redirect('/makaleler/' . $id);
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Makale güncellenirken bir hata oluştu: ' . $e->getMessage();
            Router::redirect('/makaleler/' . $id . '/duzenle');
        }
    }

    /**
     * Makale silme
     */
    public function delete(int $id): void
    {
        // Giriş kontrolü
        AuthMiddleware::requireAuth();

        // CSRF kontrolü
        AuthMiddleware::requireCsrfToken();

        if (!$this->tenant) {
            Router::json(['error' => true, 'message' => 'Tenant bulunamadı'], 400);
            return;
        }

        // Makaleyi kontrol et
        $makale = Article::findById($id, $this->tenant->database_name);

        if (!$makale) {
            $_SESSION['error'] = 'Makale bulunamadı';
            Router::redirect('/makaleler');
            return;
        }

        try {
            // Makaleyi sil
            Article::delete($id, $this->tenant->database_name);

            $_SESSION['success'] = 'Makale başarıyla silindi';
            Router::redirect('/makaleler');
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Makale silinirken bir hata oluştu: ' . $e->getMessage();
            Router::redirect('/makaleler');
        }
    }

    /**
     * Makale durum güncelleme (AJAX)
     */
    public function updateStatus(int $id): void
    {
        // Giriş kontrolü
        AuthMiddleware::requireAuth();

        // CSRF kontrolü
        AuthMiddleware::requireCsrfToken();

        if (!$this->tenant) {
            Router::json(['error' => true, 'message' => 'Tenant bulunamadı'], 400);
            return;
        }

        $durum = $_POST['durum'] ?? '';

        if (empty($durum)) {
            Router::json(['error' => true, 'message' => 'Durum belirtilmelidir'], 400);
            return;
        }

        try {
            Article::updateStatus($id, $durum, $this->tenant->database_name);

            Router::json([
                'success' => true,
                'message' => 'Durum başarıyla güncellendi',
            ]);
        } catch (\Exception $e) {
            Router::json([
                'error' => true,
                'message' => 'Durum güncellenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View render helper
     */
    private function view(string $view, array $data = []): void
    {
        extract($data);
        $viewPath = __DIR__ . '/../../views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            echo "View not found: {$view}";
            return;
        }

        require $viewPath;
    }
}
