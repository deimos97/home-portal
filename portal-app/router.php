<?php
/**
 * Enrutado: /algo → pages/algo.php, /a/b → pages/a/b.php (o pages/a_b.php).
 * Todas las páginas exigen sesión salvo las de PUBLIC_ROUTES.
 *
 * Una página puede definir, antes de imprimir nada:
 *   $layout = 'default' | 'auth' | 'none';   // 'none' = la página responde ella sola (JSON…)
 *   $title  = 'Título de la pestaña';
 */

require __DIR__ . '/bootstrap.php';

const PUBLIC_ROUTES = ['login'];

portal_start_session();

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

// Quitar la barra final (/algo/ → /algo). Solo el path, siempre con UNA barra al
// principio: así "//otro-sitio.com/" no se convierte en una redirección externa.
if ($path !== '/' && str_ends_with($path, '/')) {
    $clean = '/' . trim($path, '/');
    $qs = $_SERVER['QUERY_STRING'] ?? '';
    header('Location: ' . $clean . ($qs !== '' ? "?{$qs}" : ''), true, 301);
    exit;
}

$segments = array_values(array_filter(explode('/', $path), 'strlen'));

// Solo nombres simples: nada de "..", puntos ni caracteres raros en la ruta.
foreach ($segments as $s) {
    if (!preg_match('/^[a-z0-9][a-z0-9-]*$/i', $s)) {
        not_found();
    }
}

$directory = PAGES_PATH;
while ($segments && is_dir($directory . $segments[0])) {
    $directory .= array_shift($segments) . '/';
}
$route = $segments ? implode('_', $segments) : 'index';
$file = $directory . $route . '.php';

if (!is_file($file)) {
    not_found();
}

$user = portal_current_user();
$firstSegment = explode('/', trim($path, '/'))[0] ?: 'index';

if ($user === null && !in_array($firstSegment, PUBLIC_ROUTES, true)) {
    $back = $path . (($_SERVER['QUERY_STRING'] ?? '') !== '' ? '?' . $_SERVER['QUERY_STRING'] : '');
    header('Location: /login' . ($back !== '/' ? '?redir_to=' . rawurlencode($back) : ''));
    exit;
}

$layout = 'default';
$title = '';
$activeTab = $firstSegment;

ob_start();
require $file;
$bodyContent = ob_get_clean();

if ($layout === 'none') {
    echo $bodyContent;
    exit;
}

header('Content-Type: text/html; charset=utf-8');
header('Content-Language: es');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');

require LAYOUT_PATH . "{$layout}/header.php";
echo $bodyContent;
require LAYOUT_PATH . "{$layout}/footer.php";

function not_found(): never
{
    http_response_code(404);
    header('Content-Type: text/html; charset=utf-8');
    $layout = 'auth';
    $title = 'No encontrado';
    require LAYOUT_PATH . 'auth/header.php';
    echo '<div class="v2-auth-card"><span class="v2-label">Error 404</span>'
        . '<h1 class="v2-page-title">Aquí no hay nada</h1>'
        . '<p class="v2-muted">La página que buscas no existe.</p>'
        . '<a class="v2-btn v2-btn-primary" href="/"><i class="fa-solid fa-house"></i> Ir al inicio</a></div>';
    require LAYOUT_PATH . 'auth/footer.php';
    exit;
}
