<?php
namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use Core\Router;
use Core\Database;

/**
 * AMDS - Taslak Controller
 * Makale taslak yönetim işlemleri
 * Faz 4: Taslak Kayıt Sistemi
 */
class TaslakController
{
    private $db;
    private $tenant;

    public function __construct()
    {
        try {
            $this->tenant = current_tenant();

            if ($this->tenant) {
                $this->db = Database::getTenantConnection($this->tenant->database_name);
            } else {
                error_log('TaslakController::__construct() - Tenant bulunamadı');
            }
        } catch (\Exception $e) {
            error_log('TaslakController::__construct() - Hata: ' . $e->getMessage());
            // Constructor'da exception fırlatmayalım, save() metodunda handle edelim
        }
    }

    /**
     * Otomatik/Manuel taslak kaydet
     * POST /api/drafts/save
     */
    public function save()
    {
        // Hemen başta output buffer'ı temizle - önceden yazılmış HTML olmasın
        while (ob_get_level()) {
            ob_end_clean();
        }

        // JSON response için header - EN ÖNCE
        header('Content-Type: application/json; charset=utf-8');

        try {
            // Session başlatılmış mı kontrol et
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Giriş kontrolü (JSON response ile)
            if (empty($_SESSION['user_id'])) {
                echo json_encode(['error' => 'Giriş yapmanız gerekiyor'], JSON_UNESCAPED_UNICODE);
                http_response_code(401);
                exit;
            }

            // Tenant kontrolü
            if (!$this->tenant) {
                echo json_encode(['error' => 'Tenant bulunamadı'], JSON_UNESCAPED_UNICODE);
                http_response_code(500);
                exit;
            }

            // Database bağlantısı kontrolü
            if (!$this->db) {
                echo json_encode(['error' => 'Veritabanı bağlantısı kurulamadı'], JSON_UNESCAPED_UNICODE);
                http_response_code(500);
                exit;
            }

            $userId = $_SESSION['user_id'];
        } catch (\Exception $e) {
            error_log('TaslakController::save() - Başlangıç hatası: ' . $e->getMessage());
            echo json_encode([
                'error' => 'Sunucu hatası: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], JSON_UNESCAPED_UNICODE);
            http_response_code(500);
            exit;
        }

        try {
            // JSON verisini al
            $input = file_get_contents('php://input');

            // JSON decode et
            $postData = json_decode($input, true);

            // JSON decode hatası varsa
            if (json_last_error() !== JSON_ERROR_NONE) {
                Router::json([
                    'error' => 'Geçersiz JSON formatı: ' . json_last_error_msg(),
                    'json_error_code' => json_last_error(),
                    'raw_input_preview' => substr($input, 0, 200)
                ], 400);
                return;
            }

            // Eğer JSON parse edilemezse, POST'tan al
            if (!$postData) {
                $postData = $_POST;
            }

            // Veri yoksa hata döndür
            if (empty($postData)) {
                Router::json(['error' => 'Veri gönderilmedi'], 400);
                return;
            }

            // Form verisini temizle - kontrol karakterlerini kaldır
            $cleanedData = $this->cleanDataForJson($postData['data'] ?? []);

            // Form verisini JSON encode et (hata kontrolü ile)
            $taslakVerisi = json_encode($cleanedData, JSON_UNESCAPED_UNICODE);

            // JSON encode başarısız olduysa
            if ($taslakVerisi === false) {
                Router::json([
                    'error' => 'Veri JSON formatına çevrilemedi: ' . json_last_error_msg(),
                    'json_error_code' => json_last_error()
                ], 400);
                return;
            }

            // Form verisini al
            $data = [
                'taslak_adi' => $postData['taslak_adi'] ?? 'İsimsiz Taslak',
                'son_adim' => (int)($postData['son_adim'] ?? 0),
                'taslak_verisi' => $taslakVerisi,
                'toplam_adim' => (int)($postData['toplam_adim'] ?? 13)
            ];

            // Mevcut taslak var mı kontrol et (kullanıcının aktif taslağı)
            $existingDraft = $this->findDraftByUser($userId);

            if ($existingDraft) {
                // Güncelle
                $this->updateDraft($existingDraft['id'], $data);
                Router::json([
                    'success' => true,
                    'message' => 'Taslak güncellendi',
                    'draft_id' => $existingDraft['id']
                ]);
            } else {
                // Yeni oluştur
                $draftId = $this->createDraft($userId, $data);
                Router::json([
                    'success' => true,
                    'message' => 'Taslak oluşturuldu',
                    'draft_id' => $draftId
                ]);
            }
        } catch (\Exception $e) {
            error_log('TaslakController::save() - Hata: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            Router::json([
                'error' => 'Taslak kaydedilemedi: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Taslak yükle
     * GET /api/drafts/{id}
     */
    public function load($id)
    {
        // JSON response için header
        header('Content-Type: application/json; charset=utf-8');

        // Session başlatılmış mı kontrol et
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Giriş kontrolü (JSON response ile)
        if (empty($_SESSION['user_id'])) {
            echo json_encode(['error' => 'Giriş yapmanız gerekiyor'], JSON_UNESCAPED_UNICODE);
            http_response_code(401);
            exit;
        }

        $userId = $_SESSION['user_id'];

        try {
            $draft = $this->findDraft($id, $userId);

            if (!$draft) {
                Router::json(['error' => 'Taslak bulunamadı'], 404);
                return;
            }

            Router::json([
                'success' => true,
                'draft' => [
                    'id' => $draft['id'],
                    'taslak_adi' => $draft['taslak_adi'],
                    'son_adim' => $draft['son_adim'],
                    'toplam_adim' => $draft['toplam_adim'],
                    'data' => json_decode($draft['taslak_verisi'], true),
                    'son_guncelleme' => $draft['son_guncelleme']
                ]
            ]);
        } catch (\Exception $e) {
            Router::json(['error' => 'Taslak yüklenemedi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Kullanıcının taslak listesi
     * GET /api/drafts
     */
    public function listDrafts()
    {
        // JSON response için header
        header('Content-Type: application/json; charset=utf-8');

        // Session başlatılmış mı kontrol et
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Giriş kontrolü (JSON response ile)
        if (empty($_SESSION['user_id'])) {
            echo json_encode(['error' => 'Giriş yapmanız gerekiyor'], JSON_UNESCAPED_UNICODE);
            http_response_code(401);
            exit;
        }

        $userId = $_SESSION['user_id'];

        try {
            $drafts = $this->getDraftsByUser($userId);

            Router::json([
                'success' => true,
                'drafts' => $drafts
            ]);
        } catch (\Exception $e) {
            Router::json(['error' => 'Taslaklar yüklenemedi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Taslak sil
     * POST /api/drafts/{id}/delete
     */
    public function delete($id)
    {
        // JSON response için header
        header('Content-Type: application/json; charset=utf-8');

        // Session başlatılmış mı kontrol et
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Giriş kontrolü (JSON response ile)
        if (empty($_SESSION['user_id'])) {
            echo json_encode(['error' => 'Giriş yapmanız gerekiyor'], JSON_UNESCAPED_UNICODE);
            http_response_code(401);
            exit;
        }

        $userId = $_SESSION['user_id'];

        try {
            $result = $this->deleteDraft($id, $userId);

            if ($result) {
                Router::json([
                    'success' => true,
                    'message' => 'Taslak silindi'
                ]);
            } else {
                Router::json(['error' => 'Taslak silinemedi'], 400);
            }
        } catch (\Exception $e) {
            Router::json(['error' => 'Taslak silinemedi: ' . $e->getMessage()], 500);
        }
    }

    // ============================================
    // HELPER METODLAR
    // ============================================

    /**
     * JSON için veriyi temizle
     * Kontrol karakterlerini ve geçersiz UTF-8 karakterlerini kaldır
     */
    private function cleanDataForJson($data)
    {
        if (is_array($data)) {
            $cleaned = [];
            foreach ($data as $key => $value) {
                $cleaned[$key] = $this->cleanDataForJson($value);
            }
            return $cleaned;
        }

        if (is_string($data)) {
            // Kontrol karakterlerini temizle (tab, newline, carriage return hariç)
            $data = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/u', '', $data);

            // Geçersiz UTF-8 karakterlerini temizle
            $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');

            return $data;
        }

        return $data;
    }

    /**
     * Kullanıcının aktif taslağını bul
     */
    private function findDraftByUser($userId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM makale_taslaklari
            WHERE kullanici_id = ? AND durum = 'taslak'
            ORDER BY son_guncelleme DESC
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * ID ve kullanıcı ile taslak bul
     */
    private function findDraft($id, $userId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM makale_taslaklari
            WHERE id = ? AND kullanici_id = ?
        ");
        $stmt->execute([$id, $userId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Yeni taslak oluştur
     */
    private function createDraft($userId, $data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO makale_taslaklari
            (kullanici_id, taslak_adi, son_adim, taslak_verisi, durum, toplam_adim)
            VALUES (?, ?, ?, ?, 'taslak', ?)
        ");

        $stmt->execute([
            $userId,
            $data['taslak_adi'],
            $data['son_adim'],
            $data['taslak_verisi'],
            $data['toplam_adim']
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Taslak güncelle
     */
    private function updateDraft($id, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE makale_taslaklari
            SET taslak_adi = ?, son_adim = ?, taslak_verisi = ?, toplam_adim = ?
            WHERE id = ?
        ");

        return $stmt->execute([
            $data['taslak_adi'],
            $data['son_adim'],
            $data['taslak_verisi'],
            $data['toplam_adim'],
            $id
        ]);
    }

    /**
     * Kullanıcının tüm taslakları
     */
    private function getDraftsByUser($userId)
    {
        $stmt = $this->db->prepare("
            SELECT id, taslak_adi, son_adim, toplam_adim, durum, son_guncelleme, olusturma_tarihi
            FROM makale_taslaklari
            WHERE kullanici_id = ? AND durum = 'taslak'
            ORDER BY son_guncelleme DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Taslak sil
     */
    private function deleteDraft($id, $userId)
    {
        $stmt = $this->db->prepare("
            DELETE FROM makale_taslaklari
            WHERE id = ? AND kullanici_id = ?
        ");
        return $stmt->execute([$id, $userId]);
    }
}
