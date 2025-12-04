<?php

namespace App\Services;

/**
 * ORCID API Entegrasyon Servisi
 *
 * ORCID Public API ile yazar bilgilerini getir
 * - Public API kullanır (authentication gerektirmez)
 * - Cache mekanizması ile performans optimizasyonu
 * - Rate limiting koruması
 *
 * ORCID API Dokümantasyonu:
 * https://info.orcid.org/documentation/api-tutorials/api-tutorial-read-data-on-a-record/
 */
class OrcidService
{
    /**
     * ORCID Public API base URL
     */
    private $apiBaseUrl = 'https://pub.orcid.org/v3.0/';

    /**
     * Cache dizini
     */
    private $cacheDir = __DIR__ . '/../../storage/cache/orcid/';

    /**
     * Cache süresi (saniye) - 24 saat
     */
    private $cacheTtl = 86400;

    /**
     * Request timeout (saniye)
     */
    private $timeout = 10;

    public function __construct()
    {
        // Cache dizinini oluştur
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    /**
     * ORCID'den yazar bilgilerini getir
     *
     * @param string $orcid ORCID ID (örn: 0000-0001-2345-6789)
     * @return array|null Yazar bilgileri veya null
     */
    public function getAuthorInfo($orcid)
    {
        // ORCID formatını validate et
        if (!$this->validateOrcid($orcid)) {
            return null;
        }

        // Cache'den kontrol et
        $cached = $this->getFromCache($orcid);
        if ($cached !== null) {
            return $cached;
        }

        // API'ye istek at
        $data = $this->fetchFromApi($orcid);

        if ($data) {
            // Cache'e kaydet
            $this->saveToCache($orcid, $data);
            return $data;
        }

        return null;
    }

    /**
     * ORCID formatını validate et
     *
     * Format: 0000-0001-2345-6789
     * - 4 grup rakam
     * - Her grup tire ile ayrılmış
     * - Son karakter X olabilir (checksum)
     *
     * @param string $orcid
     * @return bool
     */
    public function validateOrcid($orcid)
    {
        // Format: 0000-0001-2345-6789
        return preg_match('/^\d{4}-\d{4}-\d{4}-\d{3}[0-9X]$/', $orcid) === 1;
    }

    /**
     * ORCID API'sinden veri çek
     *
     * @param string $orcid
     * @return array|null
     */
    private function fetchFromApi($orcid)
    {
        $url = $this->apiBaseUrl . $orcid;

        $headers = [
            'Accept: application/json',
            'User-Agent: AMDS-PHP/1.0'
        ];

        $response = $this->makeRequest($url, $headers);

        if ($response) {
            return $this->parseOrcidResponse($response, $orcid);
        }

        return null;
    }

    /**
     * HTTP request yap
     *
     * @param string $url
     * @param array $headers
     * @return string|null Response body
     */
    private function makeRequest($url, $headers = [])
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        // Hata kontrolü
        if ($error) {
            error_log("ORCID API Error: $error");
            return null;
        }

        // HTTP status kontrolü
        if ($httpCode !== 200) {
            error_log("ORCID API HTTP Error: $httpCode");
            return null;
        }

        return $response;
    }

    /**
     * ORCID API response'unu parse et ve normalize et
     *
     * @param string $response JSON response
     * @param string $orcid
     * @return array Normalize edilmiş yazar bilgileri
     */
    private function parseOrcidResponse($response, $orcid)
    {
        $data = json_decode($response, true);

        if (!$data) {
            return null;
        }

        // ORCID API response yapısı karmaşık olabilir
        // Basit bir normalizasyon yapalım
        $result = [
            'orcid' => $orcid,
            'name' => $this->extractName($data),
            'email' => $this->extractEmail($data),
            'institution' => $this->extractInstitution($data),
            'country' => $this->extractCountry($data),
            'biography' => $this->extractBiography($data),
            'raw_data' => $data // Tam veri için
        ];

        return $result;
    }

    /**
     * İsim bilgisini çıkar
     */
    private function extractName($data)
    {
        $givenName = $data['person']['name']['given-names']['value'] ?? '';
        $familyName = $data['person']['name']['family-name']['value'] ?? '';

        return trim($givenName . ' ' . $familyName);
    }

    /**
     * Email bilgisini çıkar
     */
    private function extractEmail($data)
    {
        $emails = $data['person']['emails']['email'] ?? [];

        foreach ($emails as $email) {
            if (isset($email['email'])) {
                return $email['email'];
            }
        }

        return '';
    }

    /**
     * Kurum bilgisini çıkar
     */
    private function extractInstitution($data)
    {
        $employments = $data['activities-summary']['employments']['affiliation-group'] ?? [];

        if (!empty($employments)) {
            $employment = $employments[0]['summaries'][0]['employment-summary'] ?? null;
            if ($employment) {
                return $employment['organization']['name'] ?? '';
            }
        }

        return '';
    }

    /**
     * Ülke bilgisini çıkar
     */
    private function extractCountry($data)
    {
        $employments = $data['activities-summary']['employments']['affiliation-group'] ?? [];

        if (!empty($employments)) {
            $employment = $employments[0]['summaries'][0]['employment-summary'] ?? null;
            if ($employment) {
                return $employment['organization']['address']['country'] ?? '';
            }
        }

        // Alternatif: addresses
        $addresses = $data['person']['addresses']['address'] ?? [];
        if (!empty($addresses)) {
            return $addresses[0]['country']['value'] ?? '';
        }

        return '';
    }

    /**
     * Biografi bilgisini çıkar
     */
    private function extractBiography($data)
    {
        return $data['person']['biography']['content'] ?? '';
    }

    /**
     * Cache'den veri oku
     *
     * @param string $orcid
     * @return array|null
     */
    private function getFromCache($orcid)
    {
        $cacheFile = $this->getCacheFilePath($orcid);

        if (!file_exists($cacheFile)) {
            return null;
        }

        // Cache süresi kontrolü
        $cacheTime = filemtime($cacheFile);
        if (time() - $cacheTime > $this->cacheTtl) {
            // Cache expired
            unlink($cacheFile);
            return null;
        }

        $content = file_get_contents($cacheFile);
        return json_decode($content, true);
    }

    /**
     * Cache'e veri yaz
     *
     * @param string $orcid
     * @param array $data
     */
    private function saveToCache($orcid, $data)
    {
        $cacheFile = $this->getCacheFilePath($orcid);
        file_put_contents($cacheFile, json_encode($data));
    }

    /**
     * Cache dosya yolunu al
     *
     * @param string $orcid
     * @return string
     */
    private function getCacheFilePath($orcid)
    {
        // ORCID'yi dosya adı olarak kullan (tire'leri kaldır)
        $filename = str_replace('-', '', $orcid) . '.json';
        return $this->cacheDir . $filename;
    }

    /**
     * Cache'i temizle
     *
     * @param string|null $orcid Belirli bir ORCID için temizle, null ise tümü
     */
    public function clearCache($orcid = null)
    {
        if ($orcid !== null) {
            $cacheFile = $this->getCacheFilePath($orcid);
            if (file_exists($cacheFile)) {
                unlink($cacheFile);
            }
        } else {
            // Tüm cache'i temizle
            $files = glob($this->cacheDir . '*.json');
            foreach ($files as $file) {
                unlink($file);
            }
        }
    }

    /**
     * ORCID doğrulama yap (gelecek için)
     *
     * Bu metod OAuth2 authentication gerektirir
     * Şimdilik sadece yapı olarak bırakıyoruz
     *
     * @param string $orcid
     * @param string $accessToken
     * @return bool
     */
    public function verifyOrcid($orcid, $accessToken)
    {
        // TODO: ORCID OAuth2 ile doğrulama
        // Bu özellik için ORCID üyeliği ve client credentials gerekli
        return false;
    }
}
