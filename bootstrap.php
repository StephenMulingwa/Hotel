<?php
declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);
date_default_timezone_set('Africa/Nairobi');

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('VIEW_PATH', APP_PATH . '/Views');
define('STORAGE_PATH', BASE_PATH . '/storage');

require_once BASE_PATH . '/config.php';

if (!is_dir(STORAGE_PATH)) {
	mkdir(STORAGE_PATH, 0777, true);
}

spl_autoload_register(function (string $className): void {
	$prefix = 'App\\';
	$baseDir = APP_PATH . '/';
	$len = strlen($prefix);
	if (strncmp($prefix, $className, $len) !== 0) {
		return;
	}
	$relativeClass = substr($className, $len);
	$file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
	if (file_exists($file)) {
		require $file;
	}
});

function db(): PDO {
	static $pdo = null;
	if ($pdo instanceof PDO) {
		return $pdo;
	}
	
	$config = CONFIG();
	$dbConfig = $config['database'];
	
	if ($dbConfig['driver'] === 'pgsql') {
		// PostgreSQL connection for production
		$dsn = sprintf(
			'pgsql:host=%s;port=%d;dbname=%s',
			$dbConfig['host'],
			$dbConfig['port'],
			$dbConfig['dbname']
		);
		$pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['password']);
	} else {
		// SQLite connection for development
		$databaseFile = STORAGE_PATH . '/' . ($config['database']['sqlite_file'] ?? 'database.sqlite');
		$dsn = 'sqlite:' . $databaseFile;
		$pdo = new PDO($dsn);
	}
	
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	return $pdo;
}

function currentUser(): ?array { return $_SESSION['user'] ?? null; }
function isAuthenticated(): bool { return currentUser() !== null; }

function requireAuth(): void {
	if (!isAuthenticated()) {
		header('Location: /login');
		exit;
	}
}
function userHasRole(array $roles): bool {
	$user = currentUser();
	if ($user === null) return false;
	return in_array($user['role'], $roles, true);
}
function requireRole(array $roles): void {
	// If not logged in, send to login first
	if (!isAuthenticated()) {
		redirect('/login');
	}
	// Logged in but lacks role → 403
	if (!userHasRole($roles)) {
		http_response_code(403);
		echo 'Forbidden';
		exit;
	}
}

function csrf_token(): string {
	if (empty($_SESSION['csrf_token'])) {
		$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
	}
	return $_SESSION['csrf_token'];
}
function csrf_field(): string {
	$token = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
	return '<input type="hidden" name="_token" value="' . $token . '">';
}
function verify_csrf(): void {
	$token = $_POST['_token'] ?? '';
	if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
		http_response_code(419);
		echo 'CSRF token mismatch';
		exit;
	}
}
function isPost(): bool { return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST'; }
function input(string $key, ?string $default = null): ?string {
	if (isset($_POST[$key])) return is_string($_POST[$key]) ? trim($_POST[$key]) : $default;
	if (isset($_GET[$key])) return is_string($_GET[$key]) ? trim($_GET[$key]) : $default;
	return $default;
}
function redirect(string $path): void { header('Location: ' . $path); exit; }
function now(): string { return (new DateTimeImmutable('now'))->format('Y-m-d H:i:s'); }
function today(): string { return (new DateTimeImmutable('today'))->format('Y-m-d'); }

function greetingForName(string $name): string {
	$hour = (int) (new DateTimeImmutable())->format('G');
	if ($hour < 12) return 'Good Morning ' . $name;
	if ($hour < 17) return 'Good Afternoon ' . $name;
	return 'Good Evening ' . $name;
}

require_once APP_PATH . '/Support/helpers.php';