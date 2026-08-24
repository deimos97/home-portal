<?php

// spl_autoload_register(
// 	function($class) {
// 		$classFile = __DIR__ . "/../class/{$class}.php";
//         if ( file_exists($classFile) ) {
// 			require_once $classFile;
//         }
//     }
// );

date_default_timezone_set('Europe/Madrid');

// TRATAMIENTO DE ERRORES:
if ( 
	true
) {
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
} else {
	error_reporting(0);
}

// require(__DIR__ . "/defines.php");
// require(__DIR__ . "/functions/main.php");