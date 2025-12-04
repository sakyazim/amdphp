<?php
namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use App\Models\User;

/**
 * AMDS - Yazar Controller
 * Yazar paneli işlemlerini yönetir
 */
class YazarController
{
    private $tenant;

    public function __construct()
    {
        $this->tenant = current_tenant();
    }

    /**
     * Yazar Dashboard
     */
    public function dashboard(): void
    {
        AuthMiddleware::requireAuth();

        $user = AuthMiddleware::user();

        // Yazar rolü kontrolü
        if (!in_array('Yazar', $user['roles'])) {
            $_SESSION['error'] = 'Bu sayfaya erişim yetkiniz yok';
            redirect(base_url('dashboard'));
            return;
        }

        // Yazar istatistiklerini al
        $stats = $this->getYazarStats($user['id']);

        $this->view('yazar/dashboard', [
            'title' => 'Yazar Paneli',
            'user' => $user,
            'stats' => $stats,
        ]);
    }

    /**
     * Yazar istatistiklerini getir
     */
    private function getYazarStats(int $userId): array
    {
        $db = tenant_db();

        // Toplam makale sayısı
        $stmt = $db->prepare("
            SELECT COUNT(*) as toplam
            FROM makaleler
            WHERE yazar_id = ?
        ");
        $stmt->execute([$userId]);
        $toplamMakale = $stmt->fetch(\PDO::FETCH_ASSOC)['toplam'] ?? 0;

        // Duruma göre makale sayıları
        $stmt = $db->prepare("
            SELECT
                durum,
                COUNT(*) as sayi
            FROM makaleler
            WHERE yazar_id = ?
            GROUP BY durum
        ");
        $stmt->execute([$userId]);
        $durumlar = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $stats = [
            'toplam' => $toplamMakale,
            'beklemede' => 0,
            'degerlendirmede' => 0,
            'revizyon' => 0,
            'kabul' => 0,
            'red' => 0,
            'taslak' => 0,
        ];

        foreach ($durumlar as $durum) {
            $key = strtolower($durum['durum']);
            if (isset($stats[$key])) {
                $stats[$key] = (int)$durum['sayi'];
            }
        }

        return $stats;
    }

    /**
     * Makalelerim listesi
     */
    public function makalelerim(): void
    {
        AuthMiddleware::requireAuth();

        $user = AuthMiddleware::user();

        $this->view('yazar/makalelerim', [
            'title' => 'Makalelerim',
            'user' => $user,
        ]);
    }

    /**
     * Yeni makale gönder
     */
    public function yeniMakale(): void
    {
        AuthMiddleware::requireAuth();

        $user = AuthMiddleware::user();

        $this->view('yazar/yeni-makale', [
            'title' => 'Yeni Makale Gönder',
            'user' => $user,
        ]);
    }

    /**
     * Taslak makaleler listesi
     */
    public function taslaklar(): void
    {
        AuthMiddleware::requireAuth();

        $user = AuthMiddleware::user();

        // Yazar rolü kontrolü
        if (!in_array('Yazar', $user['roles'])) {
            $_SESSION['error'] = 'Bu sayfaya erişim yetkiniz yok';
            redirect(base_url('dashboard'));
            return;
        }

        $this->view('author/drafts', [
            'title' => 'Taslak Makalelerim',
            'user' => $user,
        ]);
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
