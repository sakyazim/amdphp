<?php

namespace App\Controllers;

/**
 * Hakem Yönetim Controller'ı
 *
 * API Endpoints:
 * - POST   /api/articles/{articleId}/reviewers        - Hakem ekle
 * - GET    /api/articles/{articleId}/reviewers        - Hakem listesi
 * - DELETE /api/reviewers/{id}                        - Hakem sil
 * - GET    /api/articles/{articleId}/reviewers/validate - Hakem sayısı kontrolü
 */
class ReviewerController
{
    private $db;
    private $minReviewers = 3; // Minimum hakem sayısı

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Hakem ekle
     * POST /api/articles/{articleId}/reviewers
     *
     * Required Fields:
     * - ad: Hakem adı
     * - soyad: Hakem soyadı
     * - email: Email adresi
     * - kurum: Kurum adı
     *
     * Optional Fields:
     * - uzmanlik_alani: Uzmanlık alanı
     * - ulke: Ülke
     * - orcid: ORCID ID
     * - hakem_turu: ana|yedek|dis (default: ana)
     * - notlar: Yazar notu
     */
    public function addReviewer($articleId)
    {
        header('Content-Type: application/json');

        // Makale ID kontrolü
        if (empty($articleId) || !is_numeric($articleId)) {
            return $this->json(['error' => 'Geçersiz makale ID'], 400);
        }

        // Form verilerini al
        $data = [
            'makale_id' => $articleId,
            'ad' => trim($_POST['ad'] ?? ''),
            'soyad' => trim($_POST['soyad'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'kurum' => trim($_POST['kurum'] ?? ''),
            'uzmanlik_alani' => trim($_POST['uzmanlik_alani'] ?? ''),
            'ulke' => trim($_POST['ulke'] ?? ''),
            'orcid' => trim($_POST['orcid'] ?? ''),
            'hakem_turu' => $_POST['hakem_turu'] ?? 'ana',
            'notlar' => trim($_POST['notlar'] ?? ''),
            'sira' => $this->getNextOrder($articleId)
        ];

        // Zorunlu alan kontrolü
        if (empty($data['ad']) || empty($data['soyad']) || empty($data['email']) || empty($data['kurum'])) {
            return $this->json(['error' => 'Ad, Soyad, Email ve Kurum alanları zorunludur'], 400);
        }

        // Email format kontrolü
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->json(['error' => 'Geçersiz email formatı'], 400);
        }

        // Aynı hakem daha önce eklendi mi?
        if ($this->isDuplicateReviewer($articleId, $data['email'])) {
            return $this->json(['error' => 'Bu email adresine sahip hakem zaten eklenmiş'], 400);
        }

        // ORCID formatını kontrol et (eğer girilmişse)
        if (!empty($data['orcid']) && !$this->validateOrcid($data['orcid'])) {
            return $this->json(['error' => 'Geçersiz ORCID formatı (örnek: 0000-0001-2345-6789)'], 400);
        }

        // Hakem türü kontrolü
        if (!in_array($data['hakem_turu'], ['ana', 'yedek', 'dis'])) {
            $data['hakem_turu'] = 'ana';
        }

        // Veritabanına ekle
        try {
            $reviewerId = $this->insertReviewer($data);

            return $this->json([
                'success' => true,
                'message' => 'Hakem başarıyla eklendi',
                'reviewer_id' => $reviewerId,
                'reviewer' => $this->getReviewerById($reviewerId)
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Hakem eklenirken hata oluştu: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Hakem listesi
     * GET /api/articles/{articleId}/reviewers
     */
    public function listReviewers($articleId)
    {
        header('Content-Type: application/json');

        if (empty($articleId) || !is_numeric($articleId)) {
            return $this->json(['error' => 'Geçersiz makale ID'], 400);
        }

        $reviewers = $this->getReviewers($articleId);
        $count = count($reviewers);

        return $this->json([
            'success' => true,
            'reviewers' => $reviewers,
            'count' => $count,
            'min_required' => $this->minReviewers,
            'is_valid' => $count >= $this->minReviewers,
            'message' => $count >= $this->minReviewers
                ? 'Hakem sayısı yeterli'
                : "En az {$this->minReviewers} hakem önermeniz gerekiyor (şu anda: {$count})"
        ]);
    }

    /**
     * Hakem sil
     * DELETE /api/reviewers/{id}
     */
    public function deleteReviewer($id)
    {
        header('Content-Type: application/json');

        if (empty($id) || !is_numeric($id)) {
            return $this->json(['error' => 'Geçersiz hakem ID'], 400);
        }

        try {
            $result = $this->deleteReviewerById($id);

            if ($result) {
                return $this->json([
                    'success' => true,
                    'message' => 'Hakem başarıyla silindi'
                ]);
            } else {
                return $this->json(['error' => 'Hakem bulunamadı veya silinemedi'], 404);
            }
        } catch (\Exception $e) {
            return $this->json(['error' => 'Hakem silinirken hata oluştu: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Hakem sayısı kontrolü (validasyon)
     * GET /api/articles/{articleId}/reviewers/validate
     */
    public function validate($articleId)
    {
        header('Content-Type: application/json');

        if (empty($articleId) || !is_numeric($articleId)) {
            return $this->json(['error' => 'Geçersiz makale ID'], 400);
        }

        $reviewers = $this->getReviewers($articleId);
        $count = count($reviewers);
        $isValid = $count >= $this->minReviewers;

        return $this->json([
            'success' => true,
            'valid' => $isValid,
            'count' => $count,
            'min_required' => $this->minReviewers,
            'message' => $isValid
                ? 'Hakem sayısı yeterli'
                : "En az {$this->minReviewers} hakem önermeniz gerekiyor (şu anda: {$count})"
        ]);
    }

    // ============================================
    // HELPER METODLARI
    // ============================================

    /**
     * Bir sonraki sıra numarasını al
     */
    private function getNextOrder($articleId)
    {
        $stmt = $this->db->prepare("
            SELECT MAX(sira) as max_order
            FROM makale_hakem_onerileri
            WHERE makale_id = ?
        ");
        $stmt->execute([$articleId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return ($result['max_order'] ?? 0) + 1;
    }

    /**
     * Aynı email'e sahip hakem var mı kontrol et
     */
    private function isDuplicateReviewer($articleId, $email)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM makale_hakem_onerileri
            WHERE makale_id = ? AND email = ?
        ");
        $stmt->execute([$articleId, strtolower($email)]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result['count'] > 0;
    }

    /**
     * Hakem ekle (veritabanına)
     */
    private function insertReviewer($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO makale_hakem_onerileri
            (makale_id, ad, soyad, email, kurum, uzmanlik_alani, ulke, orcid, hakem_turu, sira, notlar)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['makale_id'],
            $data['ad'],
            $data['soyad'],
            strtolower($data['email']),
            $data['kurum'],
            $data['uzmanlik_alani'] ?: null,
            $data['ulke'] ?: null,
            $data['orcid'] ?: null,
            $data['hakem_turu'],
            $data['sira'],
            $data['notlar'] ?: null
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Makaleye ait tüm hakemleri getir
     */
    private function getReviewers($articleId)
    {
        $stmt = $this->db->prepare("
            SELECT
                id,
                makale_id,
                ad,
                soyad,
                email,
                kurum,
                uzmanlik_alani,
                ulke,
                orcid,
                hakem_turu,
                sira,
                notlar,
                olusturma_tarihi
            FROM makale_hakem_onerileri
            WHERE makale_id = ?
            ORDER BY sira ASC
        ");

        $stmt->execute([$articleId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * ID'ye göre hakem getir
     */
    private function getReviewerById($id)
    {
        $stmt = $this->db->prepare("
            SELECT
                id,
                makale_id,
                ad,
                soyad,
                email,
                kurum,
                uzmanlik_alani,
                ulke,
                orcid,
                hakem_turu,
                sira,
                notlar,
                olusturma_tarihi
            FROM makale_hakem_onerileri
            WHERE id = ?
        ");

        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Hakem sil (veritabanından)
     */
    private function deleteReviewerById($id)
    {
        $stmt = $this->db->prepare("
            DELETE FROM makale_hakem_onerileri
            WHERE id = ?
        ");

        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * ORCID formatını validate et
     * Format: 0000-0001-2345-6789
     */
    private function validateOrcid($orcid)
    {
        // ORCID formatı: XXXX-XXXX-XXXX-XXXX (4 grup, her grup 4 rakam)
        return preg_match('/^\d{4}-\d{4}-\d{4}-\d{3}[\dX]$/', $orcid);
    }

    /**
     * JSON response helper
     */
    private function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
