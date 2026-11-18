<?php
/**
 * Front controller: nginx manda aquí todo lo que no sea un fichero estático.
 * El código de la aplicación vive fuera del webroot, en ../portal-app.
 *
 * @author Deimos97
 * @since  2023
 */
// Solo en local (php -S): los ficheros estáticos los sirve el propio servidor.
if (PHP_SAPI === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

require dirname(__DIR__) . '/portal-app/router.php';
