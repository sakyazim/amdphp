<?php
/**
 * AMDS - Public Index
 * Tüm HTTP isteklerinin giriş noktası
 */

// Bootstrap
$app = require __DIR__ . '/../core/bootstrap.php';

// Router oluştur
$router = new \Core\Router($app['tenant']);

// Test route'ları (geliştirme aşamasında)
$router->get('/', function() use ($app) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'message' => 'AMDS - Akademik Makale Degerlendirme Sistemi',
        'version' => '1.0.0',
        'tenant' => $app['tenant'] ? [
            'slug' => $app['tenant']->slug,
            'name' => $app['tenant']->name,
            'database' => $app['tenant']->database_name,
        ] : null,
        'timestamp' => date('Y-m-d H:i:s'),
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
});

$router->get('/test', function() {
    \Core\Router::json([
        'success' => true,
        'message' => 'Test route calisiyor!',
        'php_version' => phpversion(),
        'extensions' => get_loaded_extensions(),
    ]);
});

$router->get('/phpinfo', function() {
    phpinfo();
});

// ============================================
// AUTH ROUTES
// ============================================

// Login sayfası
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');

// Register sayfası
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');

// Logout
$router->get('/logout', 'AuthController@logout');

// Dashboard (protected route - genel)
$router->get('/dashboard', 'AuthController@dashboard');

// ============================================
// ARTICLE (MAKALE) ROUTES
// ============================================

// Makale listesi
$router->get('/makaleler', 'ArticleController@index');

// Yeni makale formu
$router->get('/makaleler/yeni', 'ArticleController@create');

// Makale kaydetme
$router->post('/makaleler', 'ArticleController@store');

// Makale detay
$router->get('/makaleler/{id}', 'ArticleController@show');

// Makale düzenleme formu
$router->get('/makaleler/{id}/duzenle', 'ArticleController@edit');

// Makale güncelleme
$router->post('/makaleler/{id}', 'ArticleController@update');

// Makale silme
$router->post('/makaleler/{id}/sil', 'ArticleController@delete');

// Makale durum güncelleme (AJAX)
$router->post('/makaleler/{id}/durum', 'ArticleController@updateStatus');

// ============================================
// YAZAR PANEL ROUTES
// ============================================

$router->get('/yazar/dashboard', 'YazarController@dashboard');
$router->get('/yazar/makalelerim', 'YazarController@makalelerim');
$router->get('/yazar/yeni-makale', 'YazarController@yeniMakale');

// ============================================
// DATABASE TEST ENDPOINTS
// ============================================

$router->get('/db/test', function() {
    try {
        $pdo = \Core\Database::getCoreConnection();
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM tenants");
        $result = $stmt->fetch();

        \Core\Router::json([
            'success' => true,
            'message' => 'Database baglantisi basarili!',
            'tenant_count' => $result['count'],
        ]);
    } catch (\Exception $e) {
        \Core\Router::json([
            'success' => false,
            'error' => $e->getMessage(),
        ], 500);
    }
});

$router->get('/db/tenants', function() {
    try {
        $tenants = \Core\Database::fetchAll("SELECT id, slug, name, database_name, status FROM tenants");

        \Core\Router::json([
            'success' => true,
            'tenants' => $tenants,
        ]);
    } catch (\Exception $e) {
        \Core\Router::json([
            'success' => false,
            'error' => $e->getMessage(),
        ], 500);
    }
});

$router->get('/db/users', function() use ($app) {
    try {
        if (!$app['tenant']) {
            \Core\Router::json([
                'success' => false,
                'error' => 'Tenant bulunamadi',
            ], 404);
            return;
        }

        $pdo = \Core\Database::getTenantConnection($app['tenant']->database_name);
        $stmt = $pdo->query("
            SELECT k.id, k.email, k.ad, k.soyad, k.kurum, k.unvan, r.rol_adi_tr
            FROM kullanicilar k
            LEFT JOIN kullanici_roller kr ON k.id = kr.kullanici_id
            LEFT JOIN roller r ON kr.rol_id = r.id
            LIMIT 20
        ");
        $users = $stmt->fetchAll();

        \Core\Router::json([
            'success' => true,
            'tenant' => $app['tenant']->name,
            'users' => $users,
        ]);
    } catch (\Exception $e) {
        \Core\Router::json([
            'success' => false,
            'error' => $e->getMessage(),
        ], 500);
    }
});

$router->get('/db/articles', function() use ($app) {
    try {
        if (!$app['tenant']) {
            \Core\Router::json([
                'success' => false,
                'error' => 'Tenant bulunamadi',
            ], 404);
            return;
        }

        $pdo = \Core\Database::getTenantConnection($app['tenant']->database_name);
        $stmt = $pdo->query("
            SELECT m.*,
                   (SELECT GROUP_CONCAT(CONCAT(ad, ' ', soyad) SEPARATOR ', ')
                    FROM makale_yazarlari
                    WHERE makale_id = m.id
                    ORDER BY yazar_sirasi) as yazarlar
            FROM makaleler m
            LIMIT 20
        ");
        $articles = $stmt->fetchAll();

        \Core\Router::json([
            'success' => true,
            'tenant' => $app['tenant']->name,
            'articles' => $articles,
        ]);
    } catch (\Exception $e) {
        \Core\Router::json([
            'success' => false,
            'error' => $e->getMessage(),
        ], 500);
    }
});

// Route'ları çalıştır
$router->dispatch();
