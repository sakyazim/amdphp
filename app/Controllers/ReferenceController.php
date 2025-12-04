<?php

namespace App\Controllers;

use App\Services\ReferenceParser;

/**
 * Referans Yönetim Controller'ı
 *
 * API Endpoints:
 * - POST /api/references/parse-bulk - Toplu referans parse et
 * - POST /api/references/validate    - Tek referans validate et
 */
class ReferenceController
{
    private $db;
    private $parser;

    public function __construct($db)
    {
        $this->db = $db;
        $this->parser = new ReferenceParser();
    }

    /**
     * Toplu referans parse et
     * POST /api/references/parse-bulk
     *
     * Body:
     * {
     *   "text": "1. Smith J. (2023)...\n2. Brown K. (2022)..."
     * }
     *
     * Response:
     * {
     *   "success": true,
     *   "count": 10,
     *   "statistics": {
     *     "total": 10,
     *     "valid": 8,
     *     "invalid": 2,
     *     "percentage": 80
     *   },
     *   "references": [...]
     * }
     */
    public function parseBulk()
    {
        header('Content-Type: application/json');

        // POST body'den veriyi al
        $input = json_decode(file_get_contents('php://input'), true);

        // Alternatif olarak form-data
        if (!$input) {
            $input = $_POST;
        }

        $text = $input['text'] ?? '';

        if (empty($text)) {
            echo json_encode([
                'success' => false,
                'message' => 'Text parametresi gerekli'
            ]);
            return;
        }

        // Parse et
        $references = $this->parser->parseBulkReferences($text);

        // İstatistikler
        $statistics = $this->parser->getStatistics($references);

        echo json_encode([
            'success' => true,
            'count' => count($references),
            'statistics' => $statistics,
            'references' => $references
        ]);
    }

    /**
     * Tek referans validate et
     * POST /api/references/validate
     *
     * Body:
     * {
     *   "text": "Smith J. (2023). Title. Journal, 15(3), 123-145."
     * }
     *
     * Response:
     * {
     *   "success": true,
     *   "valid": true,
     *   "errors": [],
     *   "apa_check": {
     *     "is_apa": true,
     *     "confidence": 80
     *   }
     * }
     */
    public function validate()
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            $input = $_POST;
        }

        $text = $input['text'] ?? '';

        if (empty($text)) {
            echo json_encode([
                'success' => false,
                'message' => 'Text parametresi gerekli'
            ]);
            return;
        }

        // Tek referans parse et
        $references = $this->parser->parseBulkReferences($text);

        if (empty($references)) {
            echo json_encode([
                'success' => false,
                'message' => 'Referans parse edilemedi'
            ]);
            return;
        }

        $reference = $references[0];

        // APA format kontrolü
        $apaCheck = $this->parser->checkAPAFormat($reference['cleaned']);

        echo json_encode([
            'success' => true,
            'valid' => $reference['valid'],
            'errors' => $reference['errors'],
            'cleaned' => $reference['cleaned'],
            'apa_check' => $apaCheck
        ]);
    }

    /**
     * Sadece geçerli referansları filtrele
     * POST /api/references/filter-valid
     */
    public function filterValid()
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            $input = $_POST;
        }

        $text = $input['text'] ?? '';

        if (empty($text)) {
            echo json_encode([
                'success' => false,
                'message' => 'Text parametresi gerekli'
            ]);
            return;
        }

        // Parse et
        $references = $this->parser->parseBulkReferences($text);

        // Sadece geçerli olanları al
        $validReferences = $this->parser->getValidReferences($references);

        echo json_encode([
            'success' => true,
            'count' => count($validReferences),
            'references' => array_values($validReferences) // Re-index array
        ]);
    }
}
