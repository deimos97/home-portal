<?php
// Cerrar sesión: solo por POST con token (un enlace o una imagen ajena no puede cerrarte la sesión).
$layout = 'none';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valid()) {
    portal_logout();
    header('Location: /login');
    exit;
}
header('Location: /');
exit;
