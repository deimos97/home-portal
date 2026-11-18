<?php
/**
 * Arranque común del portal: configuración, sesión y utilidades de seguridad.
 * Lo cargan public_html/server.php (todas las páginas) y public_html/auth-check.php
 * (el auth_request de nginx para pihole., claudio., claudio-dev.).
 *
 * Este directorio vive FUERA del webroot (/home/deimos97/portal-app): nada de aquí
 * se puede pedir directamente por URL.
 */

declare(strict_types=1);

define('APP_PATH', __DIR__);
define('PAGES_PATH', APP_PATH . '/pages/');
define('LAYOUT_PATH', APP_PATH . '/layout/');
define('BASE_DOMAIN', 'hotwheelsandbids.com');
define('SESSION_LIFETIME', 43200); // 12 h

// Usuarios: /home/deimos97/private/users.json en la Raspberry (hermano de portal-app).
// En local se puede apuntar a un fichero de pruebas con PORTAL_USERS_FILE.
define('USERS_FILE', getenv('PORTAL_USERS_FILE') ?: dirname(APP_PATH) . '/private/users.json');

require APP_PATH . '/inc/config.php';

// Nunca mostrar errores al visitante: solo al log de PHP.
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

// ---------- sesión ----------
function portal_start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $host = strtolower($_SERVER['HTTP_HOST'] ?? '');
    $host = preg_replace('/:\d+$/', '', $host);
    $onDomain = $host === BASE_DOMAIN || str_ends_with($host, '.' . BASE_DOMAIN);
    $https = ($_SERVER['HTTPS'] ?? '') === 'on' || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';

    ini_set('session.gc_maxlifetime', (string) SESSION_LIFETIME);
    ini_set('session.use_strict_mode', '1');   // rechaza ids de sesión que no creó el servidor
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path'     => '/',
        // Cookie compartida con los subdominios (auth_request de pihole., claudio., …).
        // En local (localhost) sin dominio para poder probar.
        'domain'   => $onDomain ? BASE_DOMAIN : '',
        'secure'   => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function portal_users(): array
{
    if (!is_readable(USERS_FILE)) {
        return [];
    }
    $users = json_decode((string) file_get_contents(USERS_FILE), true);
    return is_array($users) ? $users : [];
}

/** Usuario autenticado (nombre) o null. Si el usuario ya no existe o está inactivo, cierra la sesión. */
function portal_current_user(): ?string
{
    if (empty($_SESSION['authenticated']) || !is_string($_SESSION['user'] ?? null)) {
        return null;
    }
    $users = portal_users();
    $u = $users[$_SESSION['user']] ?? null;
    if ($u === null || (isset($u['is_active']) && (int) $u['is_active'] !== 1)) {
        portal_logout();
        return null;
    }
    return $_SESSION['user'];
}

function portal_logout(): void
{
    $_SESSION = [];
    if (session_status() === PHP_SESSION_ACTIVE) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 3600, 'path' => $p['path'], 'domain' => $p['domain'],
            'secure' => $p['secure'], 'httponly' => true, 'samesite' => 'Lax',
        ]);
        session_destroy();
    }
}

// ---------- redirecciones seguras ----------
/**
 * Devuelve un destino de redirección seguro: rutas relativas del propio sitio
 * ("/algo", nunca "//otro" ni "/\otro") o URLs https de hotwheelsandbids.com y
 * sus subdominios. Cualquier otra cosa → "/".
 */
function safe_redirect_target(?string $target): string
{
    $target = trim((string) $target);
    if ($target === '' || preg_match('/[\x00-\x1f\x7f]/', $target)) {
        return '/';
    }
    if ($target[0] === '/') {
        return (isset($target[1]) && ($target[1] === '/' || $target[1] === '\\')) ? '/' : $target;
    }
    $parts = parse_url($target);
    if (!$parts || ($parts['scheme'] ?? '') !== 'https' || isset($parts['user']) || isset($parts['pass'])) {
        return '/';
    }
    $host = strtolower($parts['host'] ?? '');
    if ($host === BASE_DOMAIN || str_ends_with($host, '.' . BASE_DOMAIN)) {
        return $target;
    }
    return '/';
}

// ---------- CSRF ----------
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_valid(): bool
{
    $sent = $_POST['csrf'] ?? '';
    return is_string($sent) && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $sent);
}

// ---------- límite de intentos de login (por IP, en fichero temporal) ----------
const LOGIN_MAX_FAILS = 5;
const LOGIN_WINDOW = 600; // 10 min

function login_attempts_file(): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    return sys_get_temp_dir() . '/portal-login-' . hash('sha256', $ip);
}

/** Segundos que faltan para poder volver a intentarlo (0 = puede). */
function login_blocked_for(): int
{
    $f = login_attempts_file();
    if (!is_file($f)) {
        return 0;
    }
    $fails = array_filter(
        array_map('intval', explode("\n", (string) file_get_contents($f))),
        fn ($t) => $t > time() - LOGIN_WINDOW
    );
    if (count($fails) < LOGIN_MAX_FAILS) {
        return 0;
    }
    return max(0, min($fails) + LOGIN_WINDOW - time());
}

function login_register_fail(): void
{
    file_put_contents(login_attempts_file(), time() . "\n", FILE_APPEND | LOCK_EX);
}

function login_clear_fails(): void
{
    @unlink(login_attempts_file());
}

// ---------- utilidades ----------
function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}
