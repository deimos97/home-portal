<?php 
/**
 * 
 * Página evolucionada de server.php para manejar todas las peticiones de ficheros
 * 
 * @author Deimos97
 * @since  2023
 * 
 */

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => 'hotwheelsandbids.com',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax',
]);

session_start();


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define("PAGES_PATH"  , __DIR__ . "/pages/");
define("JSON_PATH"   , __DIR__ . "/json/");
define("WEBHOOK_PATH", __DIR__ . "/webhooks/");
define("USE_HTTPS"   , TRUE);

require(__DIR__ . "/inc/config.php");

// REDIRECT IF NEEDED:
if ( substr($_SERVER['REQUEST_URI'], -1) == '/' ) {
    $newStrpath = rtrim($_SERVER['REQUEST_URI'], '/'); // Quita la última barra del final
}

if ( !empty($newStrpath) ) {
    header("Location: {$newStrpath}", true, 301);
    die();
}

$urlPathArray = preg_split(
    '@/@', 
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
    -1, // no limit
    PREG_SPLIT_NO_EMPTY
);


$directory = PAGES_PATH;
// We open up the possibility for change
$layout = "default";

// Veamos si existe el directorio, o si se trata de un alias
while ( 
    !empty($urlPathArray) 
    && is_dir($directory . $urlPathArray[0]) 
) {
    $directory .= "{$urlPathArray[0]}/";
    array_splice($urlPathArray, 0, 1); // Eliminamos el primer elemento del array (ya usado)
}

$file = implode('_', $urlPathArray);       // $urlPathArray a string con '_' entre elementos
$file = preg_replace("/\?.*/", '', $file); // Quitamos QUERY STRING si existiese

if (!empty($urlPathArray[count($urlPathArray)-1]) && stripos($urlPathArray[count($urlPathArray)-1], ".json") !== false ) {
    $layout    = "json";
    $directory = str_replace(PAGES_PATH, JSON_PATH, $directory);
    $file      = str_replace(".json", "", $file);
}
    
if ( empty($urlPathArray) ) {
    $file .= 'index';     // Por defecto: index
} 

$requested_file_path = "{$directory}{$file}.php";

// Renderizamos la página
if ( empty($requested_file_path) || !file_exists($requested_file_path) ) {
    $code = 404;

    if ( !headers_sent() ) {
        header('HTTP/1.0 404 Not Found');
    }
    
    $errorPagesPath = PAGES_PATH . "error/{$code}.php";

    if ( file_exists($errorPagesPath) ) {
        require $errorPagesPath; // No depende de template
    } else {
        echo "<h1>Error {$code}</h1>"; // No depende de template
    }
    die();
}

ob_start();
require $requested_file_path;
$bodyContent = ob_get_clean();

if (!empty($_SESSION["user"]) && isset($_SESSION["user"]["is_active"]) && $_SESSION["user"]["is_active"] !== 1) {
    header("Location: /logout");
    exit;
}

header('Content-Type: text/html; charset=utf-8');
header('Content-Language: es');

require_once __DIR__ . "/layout/{$layout}/header.php";
echo $bodyContent;
require_once __DIR__ . "/layout/{$layout}/footer.php";