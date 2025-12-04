<?php

namespace App\Controllers;

use App\Services\LanguageService;

/**
 * Dil Yönetim Controller'ı
 *
 * API Endpoints:
 * - GET  /api/languages/available - Kullanılabilir dilleri listele
 * - POST /api/languages/switch    - Dil değiştir
 * - GET  /api/languages/current   - Mevcut dili getir
 * - POST /api/languages/import    - JSON'dan dil paketi içe aktar
 */
class LanguageController
{
    private $db;
    private $languageService;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * LanguageService instance'ını al veya oluştur
     */
    private function getLanguageService()
    {
        if (!$this->languageService) {
            $tenantId = $_SESSION['tenant_id'] ?? 1;
            $this->languageService = new LanguageService($this->db, $tenantId);
        }

        return $this->languageService;
    }

    /**
     * Kullanılabilir dilleri listele
     * GET /api/languages/available
     *
     * Response:
     * {
     *   "success": true,
     *   "languages": [
     *     {
     *       "code": "tr",
     *       "name": "Turkish",
     *       "native_name": "Türkçe",
     *       "direction": "ltr",
     *       "flag": "🇹🇷",
     *       "current": true
     *     }
     *   ]
     * }
     */
    public function getAvailable()
    {
        $lang = $this->getLanguageService();
        $languages = $lang->getAvailableLanguages();
        $current = $lang->getCurrentLanguage();

        // Current flag ekle
        foreach ($languages as $code => &$langInfo) {
            $langInfo['current'] = ($code === $current);
        }

        return $this->json([
            'success' => true,
            'languages' => array_values($languages),
            'current' => $current
        ]);
    }

    /**
     * Mevcut dili getir
     * GET /api/languages/current
     *
     * Response:
     * {
     *   "success": true,
     *   "language": {
     *     "code": "tr",
     *     "name": "Turkish",
     *     "native_name": "Türkçe",
     *     "direction": "ltr"
     *   }
     * }
     */
    public function getCurrent()
    {
        $lang = $this->getLanguageService();
        $current = $lang->getCurrentLanguage();
        $info = $lang->getLanguageInfo($current);

        return $this->json([
            'success' => true,
            'language' => $info
        ]);
    }

    /**
     * Dil değiştir
     * POST /api/languages/switch
     *
     * Request:
     * {
     *   "language": "en"
     * }
     *
     * Response:
     * {
     *   "success": true,
     *   "message": "Language switched to English",
     *   "language": "en"
     * }
     */
    public function switchLanguage()
    {
        $data = $this->getJsonInput();
        $langCode = $data['language'] ?? null;

        if (!$langCode) {
            return $this->json([
                'success' => false,
                'error' => 'Language code is required'
            ], 400);
        }

        $lang = $this->getLanguageService();
        $result = $lang->setLanguage($langCode);

        if (!$result) {
            return $this->json([
                'success' => false,
                'error' => 'Invalid language code or language not enabled'
            ], 400);
        }

        $info = $lang->getLanguageInfo($langCode);

        return $this->json([
            'success' => true,
            'message' => 'Language switched to ' . $info['name'],
            'language' => $langCode
        ]);
    }

    /**
     * JSON dil paketini içe aktar
     * POST /api/languages/import
     *
     * Request:
     * {
     *   "language": "tr",
     *   "page": "create_article"
     * }
     *
     * Response:
     * {
     *   "success": true,
     *   "message": "Language pack imported successfully"
     * }
     */
    public function importFromJson()
    {
        $data = $this->getJsonInput();
        $langCode = $data['language'] ?? null;
        $page = $data['page'] ?? null;

        if (!$langCode || !$page) {
            return $this->json([
                'success' => false,
                'error' => 'Language code and page are required'
            ], 400);
        }

        $lang = $this->getLanguageService();
        $result = $lang->importFromJson($langCode, $page);

        if (!$result) {
            return $this->json([
                'success' => false,
                'error' => 'Language pack file not found or invalid JSON'
            ], 404);
        }

        return $this->json([
            'success' => true,
            'message' => "Language pack imported successfully for {$langCode}/{$page}"
        ]);
    }

    /**
     * Dil değişkenini getir
     * GET /api/languages/translate?key=form.title&lang=tr
     *
     * Response:
     * {
     *   "success": true,
     *   "key": "form.title",
     *   "value": "Yeni Makale Başvurusu",
     *   "language": "tr"
     * }
     */
    public function translate()
    {
        $key = $_GET['key'] ?? null;
        $langCode = $_GET['lang'] ?? null;

        if (!$key) {
            return $this->json([
                'success' => false,
                'error' => 'Key is required'
            ], 400);
        }

        $lang = $this->getLanguageService();
        $value = $lang->get($key, $langCode);

        return $this->json([
            'success' => true,
            'key' => $key,
            'value' => $value,
            'language' => $langCode ?? $lang->getCurrentLanguage()
        ]);
    }

    /**
     * Sayfa için tüm dil değişkenlerini getir
     * GET /api/languages/page?page=create_article&lang=tr
     *
     * Response:
     * {
     *   "success": true,
     *   "page": "create_article",
     *   "language": "tr",
     *   "translations": {
     *     "page_title": "Yeni Makale Başvurusu",
     *     "form.article_type": "Makale Türü"
     *   }
     * }
     */
    public function getPageTranslations()
    {
        $page = $_GET['page'] ?? null;
        $langCode = $_GET['lang'] ?? null;

        if (!$page) {
            return $this->json([
                'success' => false,
                'error' => 'Page is required'
            ], 400);
        }

        $lang = $this->getLanguageService();
        $translations = $lang->getAll($page, $langCode);

        return $this->json([
            'success' => true,
            'page' => $page,
            'language' => $langCode ?? $lang->getCurrentLanguage(),
            'translations' => $translations
        ]);
    }

    /**
     * JSON response helper
     */
    private function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Get JSON input helper
     */
    private function getJsonInput()
    {
        $input = file_get_contents('php://input');
        return json_decode($input, true) ?? [];
    }
}
