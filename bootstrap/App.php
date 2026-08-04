<?php

namespace Bootstrap;

use App\Helpers\LogHelper;
use App\Helpers\ResponseHelper;
use Bootstrap\Database;
use Bootstrap\SchemaIntegrityGuard;
use RuntimeException;
use Throwable;

class App
{
    private array $config = [];

    public function __construct(private readonly Router $router)
    {
    }

    public function bootstrap(): void
    {
        $this->loadConfiguration();
        $this->configureEnvironment();
        $this->loadRoutes();
    }

    public function run(): void
    {
        try {
            $this->assertSchemaIntegrity();
            $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
            $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

            $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
            $basePath = rtrim(str_replace('/index.php', '', $scriptName), '/');

            if ($basePath !== '' && str_starts_with($uri, $basePath)) {
                $uri = substr($uri, strlen($basePath)) ?: '/';
            }

            $this->router->dispatch($method, $uri);
        } catch (Throwable $throwable) {
            $this->handleThrowable($throwable);
        }
    }

    public function config(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->config;
        }

        $segments = explode('.', $key);
        $value = $this->config;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    private function loadConfiguration(): void
    {
        $configPath = dirname(__DIR__) . '/app/config';
        $files = glob($configPath . '/*.php') ?: [];

        foreach ($files as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            $this->config[$name] = require $file;
        }
    }

    private function configureEnvironment(): void
    {
        date_default_timezone_set($this->config('app.timezone', 'Asia/Manila'));

        if (session_status() !== PHP_SESSION_ACTIVE) {
            $sessionPath = $this->config('app.session_path');

            if (is_string($sessionPath) && $sessionPath !== '') {
                if (!is_dir($sessionPath)) {
                    mkdir($sessionPath, 0775, true);
                }

                session_save_path($sessionPath);
            }

            session_name($this->config('app.session_name', 'ebps_session'));
            session_start();
        }
    }

    private function loadRoutes(): void
    {
        require dirname(__DIR__) . '/routes/web.php';
        require dirname(__DIR__) . '/routes/api.php';
    }

    private function handleThrowable(Throwable $throwable): void
    {
        $message = $throwable->getMessage();

        LogHelper::error('Unhandled application exception.', [
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'CLI',
            'uri' => $_SERVER['REQUEST_URI'] ?? '',
        ], $throwable);

        if ($throwable instanceof RuntimeException && str_contains($message, 'Database connection failed:')) {
            $this->renderDatabaseSetupError($message);
            return;
        }

        if ($throwable instanceof RuntimeException && str_contains($message, 'Schema integrity check failed:')) {
            $this->renderSchemaIntegrityError($message);
            return;
        }

        ResponseHelper::abort(500, 'An unexpected error occurred. Please try again later.');
    }

    private function assertSchemaIntegrity(): void
    {
        SchemaIntegrityGuard::assertValid(
            Database::connection(),
            (string) $this->config('database.database', 'bps')
        );
    }

    private function renderDatabaseSetupError(string $message): void
    {
        http_response_code(500);

        $schemaPath = dirname(__DIR__) . '/database/schema.sql';
        $seedPath = dirname(__DIR__) . '/database/seed.sql';
        $dbName = (string) $this->config('database.database', 'bps');
        $host = (string) $this->config('database.host', '127.0.0.1');
        $port = (string) $this->config('database.port', '3306');
        $user = (string) $this->config('database.username', 'root');

        echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Database Setup Required</title><style>body{font-family:Segoe UI,sans-serif;background:#f8fafc;color:#0f172a;margin:0;padding:32px}.panel{max-width:900px;margin:0 auto;background:#fff;border-radius:16px;padding:24px;box-shadow:0 12px 30px rgba(15,23,42,.08)}code,pre{background:#f1f5f9;border-radius:8px;padding:2px 6px}pre{padding:14px;overflow:auto}h1{margin-top:0}</style></head><body><div class="panel">';
        echo '<h1>Database Setup Required</h1>';
        echo '<p>The application cannot connect to the MySQL database yet.</p>';
        echo '<p><strong>Configured database:</strong> ' . htmlspecialchars($dbName, ENT_QUOTES, 'UTF-8') . ' on ' . htmlspecialchars($host . ':' . $port, ENT_QUOTES, 'UTF-8') . ' as ' . htmlspecialchars($user, ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p><strong>Fix:</strong> import the schema first, then the seed data.</p>';
        echo '<pre>mysql -u ' . htmlspecialchars($user, ENT_QUOTES, 'UTF-8') . ' -p &lt; "' . htmlspecialchars($schemaPath, ENT_QUOTES, 'UTF-8') . "\"\nmysql -u " . htmlspecialchars($user, ENT_QUOTES, 'UTF-8') . ' -p ' . htmlspecialchars($dbName, ENT_QUOTES, 'UTF-8') . ' &lt; "' . htmlspecialchars($seedPath, ENT_QUOTES, 'UTF-8') . '"</pre>';
        echo '<p><strong>Files:</strong></p>';
        echo '<ul>';
        echo '<li>' . htmlspecialchars($schemaPath, ENT_QUOTES, 'UTF-8') . '</li>';
        echo '<li>' . htmlspecialchars($seedPath, ENT_QUOTES, 'UTF-8') . '</li>';
        echo '</ul>';

        if ($this->config('app.debug', true)) {
            echo '<p><strong>Connection error:</strong></p>';
            echo '<pre>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</pre>';
        }

        echo '</div></body></html>';
    }

    private function renderSchemaIntegrityError(string $message): void
    {
        http_response_code(500);

        $schemaPath = dirname(__DIR__) . '/database/schema.sql';
        $dbName = (string) $this->config('database.database', 'bps');

        echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Schema Validation Failed</title><style>body{font-family:Segoe UI,sans-serif;background:#f8fafc;color:#0f172a;margin:0;padding:32px}.panel{max-width:900px;margin:0 auto;background:#fff;border-radius:16px;padding:24px;box-shadow:0 12px 30px rgba(15,23,42,.08)}code,pre{background:#f1f5f9;border-radius:8px;padding:2px 6px}pre{padding:14px;overflow:auto}h1{margin-top:0}</style></head><body><div class="panel">';
        echo '<h1>Schema Validation Failed</h1>';
        echo '<p>The application refused to start because the database schema does not match the government-procurement-safe structure required by this build.</p>';
        echo '<p><strong>Database:</strong> ' . htmlspecialchars($dbName, ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p><strong>Expected schema file:</strong> ' . htmlspecialchars($schemaPath, ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p><strong>Validation error:</strong></p>';
        echo '<pre>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</pre>';
        echo '</div></body></html>';
    }
}
