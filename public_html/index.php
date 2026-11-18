<?php
// nginx necesita que exista index.php para servir "/" (directiva `index`);
// su config reescribe /index.php → /server.php, pero por si acaso se delega igual.
require __DIR__ . '/server.php';
