<?php

namespace App\Services;

/**
 * Referans Parse Servisi
 *
 * Toplu referans metnini parse eder ve her birini ayrıştırır
 * - Çok satırlı metni satırlara böler
 * - Numaralandırmaları temizler
 * - Basit validasyon yapar
 */
class ReferenceParser
{
    /**
     * Minimum referans uzunluğu
     */
    private $minLength = 20;

    /**
     * Maksimum referans uzunluğu
     */
    private $maxLength = 5000;

    /**
     * Toplu referans metnini parse et
     *
     * @param string $text Çok satırlı referans metni
     * @return array Parsed references
     */
    public function parseBulkReferences($text)
    {
        $lines = explode("\n", $text);
        $references = [];
        $currentReference = '';
        $order = 0;

        foreach ($lines as $lineIndex => $line) {
            $line = trim($line);

            // Boş satırları atla
            if (empty($line)) {
                // Eğer bir referans biriktirilmişse, onu ekle
                if (!empty($currentReference)) {
                    $references[] = $this->processReference($currentReference, ++$order);
                    $currentReference = '';
                }
                continue;
            }

            // Satır bir yeni referans başlangıcı mı kontrol et
            if ($this->isNewReference($line)) {
                // Önceki referansı ekle
                if (!empty($currentReference)) {
                    $references[] = $this->processReference($currentReference, ++$order);
                }
                // Yeni referansa başla
                $currentReference = $line;
            } else {
                // Aynı referansın devamı
                $currentReference .= ' ' . $line;
            }
        }

        // Son referansı ekle
        if (!empty($currentReference)) {
            $references[] = $this->processReference($currentReference, ++$order);
        }

        return $references;
    }

    /**
     * Tek bir referansı işle
     *
     * @param string $text
     * @param int $order
     * @return array
     */
    private function processReference($text, $order)
    {
        $original = trim($text);
        $cleaned = $this->cleanReference($original);
        $validation = $this->validateReference($cleaned);

        return [
            'original' => $original,
            'cleaned' => $cleaned,
            'order' => $order,
            'valid' => $validation['valid'],
            'errors' => $validation['errors']
        ];
    }

    /**
     * Referansı temizle
     * - Numaralandırmayı kaldır
     * - Fazla boşlukları temizle
     * - Özel karakterleri düzelt
     *
     * @param string $text
     * @return string
     */
    private function cleanReference($text)
    {
        // Numaralandırmayı kaldır
        $text = $this->removeNumbering($text);

        // Fazla boşlukları tek boşluğa indir
        $text = preg_replace('/\s+/', ' ', $text);

        // Başındaki ve sonundaki boşlukları temizle
        $text = trim($text);

        return $text;
    }

    /**
     * Satırın yeni bir referans başlangıcı olup olmadığını kontrol et
     *
     * @param string $line
     * @return bool
     */
    private function isNewReference($line)
    {
        // Numaralandırma ile başlıyorsa yeni referans
        $patterns = [
            '/^\d+\.\s+/',      // 1.
            '/^\d+\)\s+/',      // 1)
            '/^\[\d+\]\s*/',    // [1]
            '/^\(\d+\)\s+/'     // (1)
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $line)) {
                return true;
            }
        }

        // Çok kısa satırlar devam satırı olabilir
        if (strlen($line) < 30) {
            return false;
        }

        // Nokta ile bitiyorsa ve sonraki satır varsa, muhtemelen bağımsız referans
        // Bu basit bir heuristic, her zaman doğru olmayabilir
        return true;
    }

    /**
     * Başındaki numaralandırmayı temizle
     *
     * Desteklenen formatlar:
     * - 1. Referans
     * - 1) Referans
     * - [1] Referans
     * - (1) Referans
     *
     * @param string $text
     * @return string
     */
    private function removeNumbering($text)
    {
        $patterns = [
            '/^\d+\.\s+/',      // 1.
            '/^\d+\)\s+/',      // 1)
            '/^\[\d+\]\s*/',    // [1]
            '/^\(\d+\)\s+/'     // (1)
        ];

        foreach ($patterns as $pattern) {
            $text = preg_replace($pattern, '', $text);
        }

        return trim($text);
    }

    /**
     * Referans validasyonu
     *
     * @param string $text
     * @return array ['valid' => bool, 'errors' => array]
     */
    private function validateReference($text)
    {
        $errors = [];

        // Uzunluk kontrolü
        $length = mb_strlen($text);
        if ($length < $this->minLength) {
            $errors[] = "Referans çok kısa (minimum {$this->minLength} karakter gerekli)";
        }

        if ($length > $this->maxLength) {
            $errors[] = "Referans çok uzun (maksimum {$this->maxLength} karakter)";
        }

        // En az bir nokta içermeli (cümle yapısı)
        if (strpos($text, '.') === false) {
            $errors[] = 'Referans en az bir nokta içermelidir';
        }

        // Harf içermeli (sadece noktalama değil)
        if (!preg_match('/[a-zA-ZçğıöşüÇĞİÖŞÜ]/', $text)) {
            $errors[] = 'Referans harf içermelidir';
        }

        // En az bir tarih/yıl içermeli (opsiyonel ama önerilir)
        if (!preg_match('/\d{4}/', $text)) {
            // Bu bir uyarı olarak eklenebilir, hata değil
            // $errors[] = 'Referans bir yıl içermelidir (örn: 2023)';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Referans istatistikleri
     *
     * @param array $references
     * @return array
     */
    public function getStatistics($references)
    {
        $total = count($references);
        $valid = count(array_filter($references, fn($ref) => $ref['valid']));
        $invalid = $total - $valid;

        return [
            'total' => $total,
            'valid' => $valid,
            'invalid' => $invalid,
            'percentage' => $total > 0 ? round(($valid / $total) * 100, 2) : 0
        ];
    }

    /**
     * Sadece geçerli referansları filtrele
     *
     * @param array $references
     * @return array
     */
    public function getValidReferences($references)
    {
        return array_filter($references, fn($ref) => $ref['valid']);
    }

    /**
     * Referansları APA formatında kontrol et (basit)
     *
     * @param string $text
     * @return array ['is_apa' => bool, 'confidence' => int]
     */
    public function checkAPAFormat($text)
    {
        $confidence = 0;

        // APA format kontrolleri
        // 1. Yıl parantez içinde var mı? (2023)
        if (preg_match('/\(\d{4}\)/', $text)) {
            $confidence += 30;
        }

        // 2. Italic/em ile dergi adı var mı? (basit kontrol)
        if (preg_match('/<em>|<i>|\*/', $text)) {
            $confidence += 20;
        }

        // 3. Nokta, virgül kombinasyonu var mı?
        if (preg_match('/[A-Z]\.,/', $text)) {
            $confidence += 20;
        }

        // 4. Cilt(Sayı), Sayfa formatı var mı? örn: 15(3), 123-145
        if (preg_match('/\d+\(\d+\),\s*\d+-\d+/', $text)) {
            $confidence += 30;
        }

        return [
            'is_apa' => $confidence >= 50,
            'confidence' => min($confidence, 100)
        ];
    }
}
