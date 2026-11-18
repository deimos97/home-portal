<?php
// Subpetición interna de nginx (auth_request) para los subdominios protegidos.
// 200 = sesión válida del portal; 401 = no (nginx redirige al login).
require dirname(__DIR__) . '/portal-app/bootstrap.php';

portal_start_session();
http_response_code(portal_current_user() !== null ? 200 : 401);
