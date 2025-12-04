<?php

namespace App\Controllers;

use App\Services\OrcidService;

/**
 * Yazar Yönetim Controller'ı
 *
 * API Endpoints:
 * - GET  /api/authors/search-by-email - Email ile yazar ara
 * - GET  /api/authors/search-by-orcid - ORCID ile yazar ara
 * - POST /api/authors/profile         - Yazar profili oluştur/güncelle
 * - POST /api/articles/{id}/authors   - Makaleye co-author ekle
 * - GET  /api/authors/{id}            - Yazar bilgilerini getir
 */
class AuthorController
{
    private $db;
    private $orcidService;

    public function __construct($db)
    {
        $this->db = $db;
        $this->orcidService = new OrcidService();
    }

    /**
     * Email ile yazar ara
     * GET /api/authors/search-by-email?email=xxx
     *
     * Response:
     * {
     *   "found": true,
     *   "source": "internal",
     *   "author": {
     *     "id": 123,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "title": "Prof. Dr.",
     *     "department": "Computer Science",
     *     "institution": "ABC University",
     *     "country": "USA",
     *     "orcid": "0000-0001-2345-6789"
     *   }
     * }
     */
    public function searchByEmail()
    {
        header('Content-Type: application/json');

        $email = $_GET['email'] ?? '';

        if (empty($email)) {
            echo json_encode([
                'success' => false,
                'message' => 'Email parametresi gerekli'
            ]);
            return;
        }

        // Önce kendi sistemimizde ara
        $user = $this->findUserByEmail($email);

        if ($user) {
            echo json_encode([
                'success' => true,
                'found' => true,
                'source' => 'internal',
                'author' => $user
            ]);
            return;
        }

        echo json_encode([
            'success' => true,
            'found' => false,
            'message' => 'Yazar bulunamadı'
        ]);
    }

    /**
     * ORCID ile yazar ara
     * GET /api/authors/search-by-orcid?orcid=0000-0001-2345-6789
     *
     * Response:
     * {
     *   "found": true,
     *   "source": "orcid",
     *   "author": {
     *     "orcid": "0000-0001-2345-6789",
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "affiliation": "ABC University",
     *     "country": "USA"
     *   }
     * }
     */
    public function searchByOrcid()
    {
        header('Content-Type: application/json');

        $orcid = $_GET['orcid'] ?? '';

        if (empty($orcid)) {
            echo json_encode([
                'success' => false,
                'message' => 'ORCID parametresi gerekli'
            ]);
            return;
        }

        // ORCID formatını validate et
        if (!$this->orcidService->validateOrcid($orcid)) {
            echo json_encode([
                'success' => false,
                'message' => 'Geçersiz ORCID formatı'
            ]);
            return;
        }

        // Önce kendi sistemimizde ara
        $user = $this->findUserByOrcid($orcid);

        if ($user) {
            echo json_encode([
                'success' => true,
                'found' => true,
                'source' => 'internal',
                'author' => $user
            ]);
            return;
        }

        // ORCID API'sinde ara
        $orcidData = $this->orcidService->getAuthorInfo($orcid);

        if ($orcidData) {
            echo json_encode([
                'success' => true,
                'found' => true,
                'source' => 'orcid',
                'author' => $orcidData
            ]);
            return;
        }

        echo json_encode([
            'success' => true,
            'found' => false,
            'message' => 'ORCID bulunamadı'
        ]);
    }

    /**
     * Yazar profili oluştur/güncelle
     * POST /api/authors/profile
     *
     * Body:
     * {
     *   "user_id": 123,
     *   "title": "Prof. Dr.",
     *   "department": "Computer Science",
     *   "institution": "ABC University",
     *   "country": "USA",
     *   "orcid": "0000-0001-2345-6789",
     *   "phone": "+90 555 555 55 55",
     *   "email2": "secondary@example.com",
     *   "bio": "Biography text",
     *   "website": "https://example.com",
     *   "google_scholar": "https://scholar.google.com/...",
     *   "scopus_author_id": "12345678900"
     * }
     */
    public function updateProfile()
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['user_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'User ID gerekli'
            ]);
            return;
        }

        $userId = $input['user_id'];

        // Profil var mı kontrol et
        $stmt = $this->db->prepare("SELECT id FROM kullanici_yazar_profilleri WHERE kullanici_id = ?");
        $stmt->execute([$userId]);
        $exists = $stmt->fetch();

        if ($exists) {
            // Güncelle
            $result = $this->updateAuthorProfile($userId, $input);
        } else {
            // Yeni oluştur
            $result = $this->createAuthorProfile($userId, $input);
        }

        echo json_encode($result);
    }

    /**
     * Makaleye co-author ekle
     * POST /api/articles/{id}/authors
     *
     * Body:
     * {
     *   "author_id": 123,
     *   "order": 2,
     *   "is_corresponding": false
     * }
     */
    public function addCoAuthor($articleId)
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['author_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Author ID gerekli'
            ]);
            return;
        }

        $authorId = $input['author_id'];
        $order = $input['order'] ?? 1;
        $isCorresponding = $input['is_corresponding'] ?? false;

        try {
            $stmt = $this->db->prepare("
                INSERT INTO makale_yazarlari
                (makale_id, kullanici_id, sira, sorumlu_yazar)
                VALUES (?, ?, ?, ?)
            ");

            $stmt->execute([
                $articleId,
                $authorId,
                $order,
                $isCorresponding ? 1 : 0
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Yazar eklendi',
                'author_id' => $authorId
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Yazar eklenirken hata oluştu: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Yazar bilgilerini getir
     * GET /api/authors/{id}
     */
    public function getAuthor($authorId)
    {
        header('Content-Type: application/json');

        $author = $this->findUserById($authorId);

        if ($author) {
            echo json_encode([
                'success' => true,
                'author' => $author
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Yazar bulunamadı'
            ]);
        }
    }

    /**
     * Email ile kullanıcı ara
     */
    private function findUserByEmail($email)
    {
        $stmt = $this->db->prepare("
            SELECT
                u.id,
                u.ad,
                u.soyad,
                u.email,
                p.unvan,
                p.telefon,
                p.email2,
                p.departman,
                p.kurum,
                p.ulke,
                p.orcid,
                p.bio,
                p.web_sitesi,
                p.google_scholar,
                p.scopus_author_id
            FROM kullanicilar u
            LEFT JOIN kullanici_yazar_profilleri p ON u.id = p.kullanici_id
            WHERE u.email = ? OR p.email2 = ?
            LIMIT 1
        ");

        $stmt->execute([$email, $email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($user) {
            return $this->formatAuthorData($user);
        }

        return null;
    }

    /**
     * ORCID ile kullanıcı ara
     */
    private function findUserByOrcid($orcid)
    {
        $stmt = $this->db->prepare("
            SELECT
                u.id,
                u.ad,
                u.soyad,
                u.email,
                p.unvan,
                p.telefon,
                p.email2,
                p.departman,
                p.kurum,
                p.ulke,
                p.orcid,
                p.bio,
                p.web_sitesi,
                p.google_scholar,
                p.scopus_author_id
            FROM kullanicilar u
            INNER JOIN kullanici_yazar_profilleri p ON u.id = p.kullanici_id
            WHERE p.orcid = ?
            LIMIT 1
        ");

        $stmt->execute([$orcid]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($user) {
            return $this->formatAuthorData($user);
        }

        return null;
    }

    /**
     * ID ile kullanıcı ara
     */
    private function findUserById($userId)
    {
        $stmt = $this->db->prepare("
            SELECT
                u.id,
                u.ad,
                u.soyad,
                u.email,
                p.unvan,
                p.telefon,
                p.email2,
                p.departman,
                p.kurum,
                p.ulke,
                p.orcid,
                p.bio,
                p.web_sitesi,
                p.google_scholar,
                p.scopus_author_id
            FROM kullanicilar u
            LEFT JOIN kullanici_yazar_profilleri p ON u.id = p.kullanici_id
            WHERE u.id = ?
            LIMIT 1
        ");

        $stmt->execute([$userId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($user) {
            return $this->formatAuthorData($user);
        }

        return null;
    }

    /**
     * Yazar profili oluştur
     */
    private function createAuthorProfile($userId, $data)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO kullanici_yazar_profilleri
                (kullanici_id, unvan, telefon, email2, departman, kurum, ulke, orcid, bio, web_sitesi, google_scholar, scopus_author_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $userId,
                $data['title'] ?? null,
                $data['phone'] ?? null,
                $data['email2'] ?? null,
                $data['department'] ?? null,
                $data['institution'] ?? null,
                $data['country'] ?? null,
                $data['orcid'] ?? null,
                $data['bio'] ?? null,
                $data['website'] ?? null,
                $data['google_scholar'] ?? null,
                $data['scopus_author_id'] ?? null
            ]);

            return [
                'success' => true,
                'message' => 'Profil oluşturuldu',
                'profile_id' => $this->db->lastInsertId()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Profil oluşturulurken hata: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Yazar profili güncelle
     */
    private function updateAuthorProfile($userId, $data)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE kullanici_yazar_profilleri
                SET
                    unvan = ?,
                    telefon = ?,
                    email2 = ?,
                    departman = ?,
                    kurum = ?,
                    ulke = ?,
                    orcid = ?,
                    bio = ?,
                    web_sitesi = ?,
                    google_scholar = ?,
                    scopus_author_id = ?
                WHERE kullanici_id = ?
            ");

            $stmt->execute([
                $data['title'] ?? null,
                $data['phone'] ?? null,
                $data['email2'] ?? null,
                $data['department'] ?? null,
                $data['institution'] ?? null,
                $data['country'] ?? null,
                $data['orcid'] ?? null,
                $data['bio'] ?? null,
                $data['website'] ?? null,
                $data['google_scholar'] ?? null,
                $data['scopus_author_id'] ?? null,
                $userId
            ]);

            return [
                'success' => true,
                'message' => 'Profil güncellendi'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Profil güncellenirken hata: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Yazar verisini formatla
     */
    private function formatAuthorData($user)
    {
        return [
            'id' => $user['id'],
            'name' => trim(($user['ad'] ?? '') . ' ' . ($user['soyad'] ?? '')),
            'first_name' => $user['ad'] ?? '',
            'last_name' => $user['soyad'] ?? '',
            'email' => $user['email'] ?? '',
            'email2' => $user['email2'] ?? '',
            'title' => $user['unvan'] ?? '',
            'phone' => $user['telefon'] ?? '',
            'department' => $user['departman'] ?? '',
            'institution' => $user['kurum'] ?? '',
            'country' => $user['ulke'] ?? '',
            'orcid' => $user['orcid'] ?? '',
            'bio' => $user['bio'] ?? '',
            'website' => $user['web_sitesi'] ?? '',
            'google_scholar' => $user['google_scholar'] ?? '',
            'scopus_author_id' => $user['scopus_author_id'] ?? ''
        ];
    }

    /**
     * JSON response helper
     */
    private function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
