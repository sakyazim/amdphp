<?php
namespace Core;

/**
 * AMDS - Router Sınıfı
 * HTTP isteklerini yönlendirir
 */
class Router
{
    private $routes = [];
    private $tenant;

    public function __construct(?object $tenant = null)
    {
        $this->tenant = $tenant;
    }

    /**
     * GET route tanımla
     */
    public function get(string $path, $callback): void
    {
        $this->addRoute('GET', $path, $callback);
    }

    /**
     * POST route tanımla
     */
    public function post(string $path, $callback): void
    {
        $this->addRoute('POST', $path, $callback);
    }

    /**
     * PUT route tanımla
     */
    public function put(string $path, $callback): void
    {
        $this->addRoute('PUT', $path, $callback);
    }

    /**
     * DELETE route tanımla
     */
    public function delete(string $path, $callback): void
    {
        $this->addRoute('DELETE', $path, $callback);
    }

    /**
     * Route ekle
     */
    private function addRoute(string $method, string $path, $callback): void
    {
        $this->routes[$method][$path] = $callback;
    }

    /**
     * İsteği route'a yönlendir
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        // Query string'i kaldır
        $uri = parse_url($uri, PHP_URL_PATH);

        // Script path'i kaldır (XAMPP subdirectory desteği için)
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptName !== '/' && strpos($uri, $scriptName) === 0) {
            $uri = substr($uri, strlen($scriptName));
        }

        // Tenant prefix'i kaldır (eğer path-based routing kullanılıyorsa)
        if ($this->tenant && !empty($this->tenant->slug)) {
            $uri = preg_replace('#^/' . preg_quote($this->tenant->slug, '#') . '#', '', $uri);
        }

        // Trailing slash'i kaldır
        $uri = rtrim($uri, '/') ?: '/';

        // Exact match kontrolü
        if (isset($this->routes[$method][$uri])) {
            $this->executeCallback($this->routes[$method][$uri]);
            return;
        }

        // Parametreli route kontrolü
        foreach ($this->routes[$method] ?? [] as $route => $callback) {
            $pattern = $this->convertRouteToRegex($route);

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // İlk elemanı (tam eşleşmeyi) çıkar
                $this->executeCallback($callback, $matches);
                return;
            }
        }

        // 404
        $this->notFound();
    }

    /**
     * Route'u regex'e çevir
     * Örnek: /makale/{id} -> #^/makale/([^/]+)$#
     */
    private function convertRouteToRegex(string $route): string
    {
        $route = preg_replace('#\{([a-z_]+)\}#', '([^/]+)', $route);
        return '#^' . $route . '$#';
    }

    /**
     * Callback'i çalıştır
     */
    private function executeCallback($callback, array $params = []): void
    {
        if (is_callable($callback)) {
            // Anonymous function
            call_user_func_array($callback, $params);
        } elseif (is_string($callback) && strpos($callback, '@') !== false) {
            // Controller@method formatı
            [$controller, $method] = explode('@', $callback);

            $controllerClass = "App\\Controllers\\{$controller}";

            if (class_exists($controllerClass)) {
                // Controller'a tenant database connection'ı geç
                try {
                    $db = null;
                    if ($this->tenant) {
                        $db = \Core\Database::getTenantConnection($this->tenant->database_name);
                    }

                    // Constructor parametresi olup olmadığını kontrol et
                    $reflection = new \ReflectionClass($controllerClass);
                    $constructor = $reflection->getConstructor();

                    if ($constructor && $constructor->getNumberOfParameters() > 0) {
                        // Constructor parametre alıyorsa db'yi geç
                        $instance = new $controllerClass($db);
                    } else {
                        // Constructor parametre almıyorsa normal oluştur
                        $instance = new $controllerClass();
                    }
                } catch (\Exception $e) {
                    // Hata durumunda parametresiz dene
                    $instance = new $controllerClass();
                }

                if (method_exists($instance, $method)) {
                    call_user_func_array([$instance, $method], $params);
                } else {
                    $this->error("Method '{$method}' bulunamadi: {$controllerClass}");
                }
            } else {
                $this->error("Controller bulunamadi: {$controllerClass}");
            }
        } else {
            $this->error('Gecersiz callback');
        }
    }

    /**
     * 404 Not Found
     */
    private function notFound(): void
    {
        http_response_code(404);
        echo json_encode([
            'error' => true,
            'message' => 'Sayfa bulunamadi (404)'
        ]);
    }

    /**
     * Hata mesajı
     */
    private function error(string $message): void
    {
        http_response_code(500);
        echo json_encode([
            'error' => true,
            'message' => $message
        ]);
    }

    /**
     * Redirect
     */
    public static function redirect(string $url, int $statusCode = 302): void
    {
        header("Location: {$url}", true, $statusCode);
        exit;
    }

    /**
     * JSON response
     */
    public static function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}
