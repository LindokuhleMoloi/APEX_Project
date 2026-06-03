<?php
// Shared application utilities for SS_Portal

define('APP_BASE_PATH', __DIR__ . '/../');
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ss_portal');
define('LOG_DIR', APP_BASE_PATH . 'logs');
define('CACHE_DIR', APP_BASE_PATH . 'cache');
define('ASSETS_DIR', APP_BASE_PATH . 'assets/');
define('ASSETS_URL', 'assets');
define('ENABLE_IP_FILTERING', false);
define('ALLOWED_IPS', ['127.0.0.1', '::1']);
define('REQUEST_LOG_FILE', LOG_DIR . '/requests.log');
define('PERFORMANCE_LOG_FILE', LOG_DIR . '/performance.log');
define('ERROR_LOG_FILE', LOG_DIR . '/errors.log');

if (!is_dir(LOG_DIR)) {
    mkdir(LOG_DIR, 0755, true);
}
if (!is_dir(CACHE_DIR)) {
    mkdir(CACHE_DIR, 0755, true);
}
if (!is_dir(ASSETS_DIR)) {
    mkdir(ASSETS_DIR, 0755, true);
}
if (!is_dir(ASSETS_DIR . 'css')) {
    mkdir(ASSETS_DIR . 'css', 0755, true);
}

function app_start(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    enforce_ip_filtering();
    register_default_services();
    start_request_timer();
    register_shutdown_function('end_request_timer');
}

function db_connect(): mysqli
{
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_errno) {
        die('Failed to connect to MySQL: ' . $mysqli->connect_error);
    }
    $mysqli->set_charset('utf8mb4');
    return $mysqli;
}

function is_user_logged_in(): bool
{
    app_start();
    return !empty($_SESSION['user_id']);
}

function is_admin_logged_in(): bool
{
    app_start();
    return !empty($_SESSION['admin_logged_in']);
}

function require_user(): void
{
    app_start();
    if (!is_user_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function require_admin(): void
{
    app_start();
    if (!is_admin_logged_in()) {
        header('Location: admin_login.php');
        exit;
    }
}

function logout_user(): void
{
    app_start();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function start_request_timer(): void
{
    if (!isset($GLOBALS['_app_request_timer'])) {
        $GLOBALS['_app_request_timer'] = microtime(true);
    }
}

function end_request_timer(): void
{
    if (isset($GLOBALS['_app_request_timer'])) {
        $elapsed = microtime(true) - $GLOBALS['_app_request_timer'];
        log_performance(sprintf('elapsed=%.4f', $elapsed));
        log_request(sprintf('elapsed=%.4f', $elapsed));
        unset($GLOBALS['_app_request_timer']);
    }
}

function log_request(string $message = ''): void
{
    $uri = $_SERVER['REQUEST_URI'] ?? 'unknown';
    $method = $_SERVER['REQUEST_METHOD'] ?? 'CLI';
    $line = sprintf('[%s] %s %s %s', date('Y-m-d H:i:s'), $method, $uri, $message);
    file_put_contents(REQUEST_LOG_FILE, $line . PHP_EOL, FILE_APPEND);
}

function log_performance(string $message = ''): void
{
    $uri = $_SERVER['REQUEST_URI'] ?? 'unknown';
    $method = $_SERVER['REQUEST_METHOD'] ?? 'CLI';
    $line = sprintf('[%s] %s %s %s', date('Y-m-d H:i:s'), $method, $uri, $message);
    file_put_contents(PERFORMANCE_LOG_FILE, $line . PHP_EOL, FILE_APPEND);
}

function log_error(string $message = ''): void
{
    $uri = $_SERVER['REQUEST_URI'] ?? 'unknown';
    $method = $_SERVER['REQUEST_METHOD'] ?? 'CLI';
    $line = sprintf('[%s] %s %s %s', date('Y-m-d H:i:s'), $method, $uri, $message);
    file_put_contents(ERROR_LOG_FILE, $line . PHP_EOL, FILE_APPEND);
}

function cache_get(string $key, int $ttl = 60)
{
    $cacheFile = CACHE_DIR . '/' . preg_replace('/[^a-z0-9_-]/i', '_', $key) . '.json';
    if (!is_file($cacheFile)) {
        return null;
    }
    $data = json_decode(file_get_contents($cacheFile), true);
    if (!is_array($data) || empty($data['expires']) || time() > $data['expires']) {
        @unlink($cacheFile);
        return null;
    }
    return $data['value'];
}

function cache_set(string $key, $value, int $ttl = 60): void
{
    $cacheFile = CACHE_DIR . '/' . preg_replace('/[^a-z0-9_-]/i', '_', $key) . '.json';
    $data = [
        'expires' => time() + $ttl,
        'value' => $value,
    ];
    file_put_contents($cacheFile, json_encode($data));
}

function html_escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function generate_csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token(?string $token): bool
{
    return !empty($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function csrf_input_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . html_escape(generate_csrf_token()) . '">';
}

function register_service(string $name, $service, bool $override = false): void
{
    $GLOBALS['_app_services'] = $GLOBALS['_app_services'] ?? [];
    if (isset($GLOBALS['_app_services'][$name]) && !$override) {
        return;
    }
    $GLOBALS['_app_services'][$name] = $service;
}

function has_service(string $name): bool
{
    return isset($GLOBALS['_app_services']) && array_key_exists($name, $GLOBALS['_app_services']);
}

function get_service(string $name)
{
    if (!has_service($name)) {
        throw new RuntimeException("Service '{$name}' is not registered.");
    }

    $service = $GLOBALS['_app_services'][$name];
    if (is_callable($service)) {
        $service = $service();
        $GLOBALS['_app_services'][$name] = $service;
    }
    return $service;
}

function register_default_services(): void
{
    register_service('logger', function () {
        return new class {
            public function log(string $message): void
            {
                log_request($message);
            }

            public function error(string $message): void
            {
                log_error($message);
            }
        };
    });

    register_service('db', function () {
        return db_connect();
    });

    register_service('cache', function () {
        return new class {
            public function get(string $key, int $ttl = 60)
            {
                return cache_get($key, $ttl);
            }

            public function set(string $key, $value, int $ttl = 60): void
            {
                cache_set($key, $value, $ttl);
            }
        };
    });
}

function site_base_url(): string
{
    $documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
    $appPath = realpath(APP_BASE_PATH) ?: '';
    $baseUrl = '';

    if ($documentRoot && $appPath && str_starts_with($appPath, $documentRoot)) {
        $relative = substr($appPath, strlen($documentRoot));
        $baseUrl = str_replace('\\', '/', $relative);
    }

    return rtrim($baseUrl ?: '', '/') ?: '';
}

function site_url(string $path): string
{
    $baseUrl = site_base_url();
    $path = '/' . ltrim($path, '/');
    return ($baseUrl ? '/' . ltrim($baseUrl, '/') : '') . $path;
}

function asset_url(string $path): string
{
    $baseUrl = site_base_url();
    $assetPath = trim(ASSETS_URL, '/') . '/' . ltrim($path, '/');
    return ($baseUrl ? '/' . ltrim($baseUrl, '/') : '') . '/' . $assetPath;
}

function render_stylesheet(string $stylesheet = 'css/style.css'): void
{
    echo '<link rel="stylesheet" href="' . html_escape(asset_url($stylesheet)) . '">';
}

function render_script(string $script): void
{
    echo '<script src="' . html_escape(asset_url($script)) . '" defer></script>';
}

function get_client_ip(): string
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function is_ip_allowed(): bool
{
    if (!ENABLE_IP_FILTERING) {
        return true;
    }

    return in_array(get_client_ip(), ALLOWED_IPS, true);
}

function enforce_ip_filtering(): void
{
    if (!is_ip_allowed()) {
        log_request('blocked_ip=' . get_client_ip());
        http_response_code(403);
        exit('Access denied.');
    }
}

function sanitize_string(?string $value): string
{
    return trim(filter_var($value ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
}

function render_navigation(): void
{
    $links = [
        'index.php' => 'Home',
        'courses.php' => 'Courses',
        'grades.php' => 'Grades',
        'quizzes.php' => 'Quizzes',
        'profile.php' => 'Profile',
    ];

    echo '<header class="topbar">';
    echo '<div class="brand">Learner Portal</div>';
    echo '<div class="nav-links">';

    foreach ($links as $href => $label) {
        echo '<a href="' . $href . '">' . htmlspecialchars($label) . '</a>';
    }

    $loggedIn = !empty($_SESSION['user_id']) || !empty($_SESSION['admin_logged_in']);
    if ($loggedIn) {
        echo '<a href="logout.php">Logout</a>';
    } else {
        echo '<a href="login.php">Login</a>';
    }

    echo '</div>';
    echo '</header>';
}

function render_admin_navigation(): void
{
    echo '<header class="topbar">';
    echo '<div class="brand">Admin Portal</div>';
    echo '<div class="nav-links">';
    echo '<a href="dashboard.php">Dashboard</a>';
    echo '<a href="admin_login.php">Admin Login</a>';
    echo '<a href="logout.php">Logout</a>';
    echo '</div>';
    echo '</header>';
}
