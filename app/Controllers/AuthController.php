<?php
namespace App\Controllers;

use App\Models\User;
use App\Middleware\AuthMiddleware;
use Core\Router;

/**
 * AMDS - Auth Controller
 * Kimlik doğrulama işlemlerini yönetir
 */
class AuthController
{
    private $tenant;

    public function __construct()
    {
        $this->tenant = current_tenant();
    }

    /**
     * Login sayfasını gösterir
     */
    public function showLogin(): void
    {
        // Zaten giriş yapmışsa dashboard'a yönlendir
        if (AuthMiddleware::check()) {
            Router::redirect('/dashboard');
            return;
        }

        $this->view('auth/login', [
            'title' => 'Giriş Yap',
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null,
        ]);

        unset($_SESSION['error'], $_SESSION['success']);
    }

    /**
     * Login işlemini gerçekleştirir
     */
    public function login(): void
    {
        // CSRF token kontrolü
        AuthMiddleware::requireCsrfToken();

        if (!$this->tenant) {
            Router::json(['error' => true, 'message' => 'Tenant bulunamadı'], 400);
            return;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['sifre'] ?? '';

        // Validasyon
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email ve şifre gereklidir';
            Router::redirect('/login');
            return;
        }

        // Kullanıcıyı bul
        $user = User::findByEmail($email, $this->tenant->database_name);

        if (!$user || !User::verifyPassword($password, $user['sifre_hash'])) {
            $_SESSION['error'] = 'Email veya şifre hatalı';
            Router::redirect('/login');
            return;
        }

        // Rolleri getir
        $roles = User::getRoles($user['id'], $this->tenant->database_name);

        // Session'a kaydet
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_ad'] = $user['ad'];
        $_SESSION['user_soyad'] = $user['soyad'];
        $_SESSION['user_roles'] = $roles;

        // Son giriş zamanını güncelle
        User::updateLastLogin($user['id'], $this->tenant->database_name);

        // Rol bazlı yönlendirme
        $redirectTo = $this->getRoleBasedDashboard($roles);

        Router::redirect($redirectTo);
    }

    /**
     * Logout işlemini gerçekleştirir
     */
    public function logout(): void
    {
        // Session'ı temizle
        $_SESSION = [];

        // Session cookie'yi sil
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Session'ı yok et
        session_destroy();

        Router::redirect('/login');
    }

    /**
     * Register sayfasını gösterir
     */
    public function showRegister(): void
    {
        // Zaten giriş yapmışsa dashboard'a yönlendir
        if (AuthMiddleware::check()) {
            Router::redirect('/dashboard');
            return;
        }

        $this->view('auth/register', [
            'title' => 'Kayıt Ol',
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null,
        ]);

        unset($_SESSION['error'], $_SESSION['success']);
    }

    /**
     * Register işlemini gerçekleştirir
     */
    public function register(): void
    {
        // CSRF token kontrolü
        AuthMiddleware::requireCsrfToken();

        if (!$this->tenant) {
            Router::json(['error' => true, 'message' => 'Tenant bulunamadı'], 400);
            return;
        }

        // Form verilerini al
        $data = [
            'email' => $_POST['email'] ?? '',
            'sifre' => $_POST['sifre'] ?? '',
            'sifre_tekrar' => $_POST['sifre_tekrar'] ?? '',
            'ad' => $_POST['ad'] ?? '',
            'soyad' => $_POST['soyad'] ?? '',
            'kurum' => $_POST['kurum'] ?? '',
            'unvan' => $_POST['unvan'] ?? '',
            'telefon' => $_POST['telefon'] ?? '',
            'ulke' => $_POST['ulke'] ?? '',
            'sehir' => $_POST['sehir'] ?? '',
        ];

        // Validasyon
        $errors = [];

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Geçerli bir email adresi giriniz';
        }

        if (empty($data['sifre']) || strlen($data['sifre']) < 6) {
            $errors[] = 'Şifre en az 6 karakter olmalıdır';
        }

        if ($data['sifre'] !== $data['sifre_tekrar']) {
            $errors[] = 'Şifreler eşleşmiyor';
        }

        if (empty($data['ad']) || empty($data['soyad'])) {
            $errors[] = 'Ad ve soyad gereklidir';
        }

        // Email kontrolü
        if (User::emailExists($data['email'], $this->tenant->database_name)) {
            $errors[] = 'Bu email adresi zaten kullanılıyor';
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            Router::redirect('/register');
            return;
        }

        try {
            // Kullanıcıyı oluştur
            $userId = User::create($data, $this->tenant->database_name);

            // Varsayılan rol ata (Yazar)
            $defaultRoleId = User::getDefaultRoleId($this->tenant->database_name);
            User::assignRole($userId, $defaultRoleId, $this->tenant->database_name);

            $_SESSION['success'] = 'Kayıt başarılı! Şimdi giriş yapabilirsiniz.';
            Router::redirect('/login');
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Kayıt sırasında bir hata oluştu: ' . $e->getMessage();
            Router::redirect('/register');
        }
    }

    /**
     * Dashboard sayfasını gösterir
     */
    public function dashboard(): void
    {
        AuthMiddleware::requireAuth();

        $user = AuthMiddleware::user();

        $this->view('dashboard', [
            'title' => 'Dashboard',
            'user' => $user,
        ]);
    }

    /**
     * Rol bazlı dashboard URL'ini belirle
     */
    private function getRoleBasedDashboard(array $roles): string
    {
        // Roller öncelik sırasına göre kontrol edilir
        $rolePriority = [
            'Super Admin' => base_url('admin/dashboard'),
            'Dergi Yöneticisi' => base_url('yonetici/dashboard'),
            'Baş Editör' => base_url('editor/dashboard'),
            'Alan Editörü' => base_url('editor/dashboard'),
            'Hakem' => base_url('hakem/dashboard'),
            'Yazar' => base_url('yazar/dashboard'),
        ];

        // İlk bulunan role göre yönlendir
        foreach ($rolePriority as $role => $url) {
            if (in_array($role, $roles)) {
                return $url;
            }
        }

        // Varsayılan olarak yazar paneline yönlendir
        return base_url('yazar/dashboard');
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
