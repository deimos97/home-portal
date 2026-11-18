<?php
$layout = 'auth';
$title = 'Entrar';

$redirTo = safe_redirect_target($_GET['redir_to'] ?? ($_POST['redir_to'] ?? '/'));

if (portal_current_user() !== null) {
    header('Location: ' . $redirTo);
    exit;
}

$error = '';
$blocked = login_blocked_for();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($blocked > 0) {
        $error = 'Demasiados intentos. Prueba de nuevo en ' . ceil($blocked / 60) . ' min.';
    } elseif (!csrf_valid()) {
        $error = 'La sesión del formulario ha caducado. Vuelve a intentarlo.';
    } else {
        $username = (string) ($_POST['username'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $users = portal_users();
        $u = $users[$username] ?? null;
        $ok = $u !== null
            && password_verify($password, (string) ($u['password_hash'] ?? ''))
            && (!isset($u['is_active']) || (int) $u['is_active'] === 1);

        if ($ok) {
            login_clear_fails();
            session_regenerate_id(true);   // id nuevo tras autenticarse (evita fijación de sesión)
            $_SESSION['authenticated'] = true;
            $_SESSION['user'] = $username;
            $_SESSION['login_at'] = time();
            unset($_SESSION['csrf']);
            header('Location: ' . $redirTo);
            exit;
        }

        login_register_fail();
        usleep(400000); // frena un poco los intentos automáticos
        $error = 'Usuario o contraseña incorrectos';
    }
}
?>
<form class="v2-auth-card" method="post" action="/login" autocomplete="on">
    <div class="v2-auth-brand">
        <span class="v2-logo-mark"><i class="fa-solid fa-tower-broadcast"></i></span>
        <span class="v2-brand-text">Babilonia 33</span>
    </div>
    <span class="v2-label">Acceso al portal</span>

    <?php if ($error): ?>
        <div class="v2-alert v2-alert-bad"><i class="fa-solid fa-circle-exclamation"></i> <?= e($error) ?></div>
    <?php endif; ?>

    <label class="v2-field">
        <span class="v2-label">Usuario</span>
        <input class="v2-input" type="text" name="username" autocomplete="username" autocapitalize="none" spellcheck="false" required autofocus>
    </label>
    <label class="v2-field">
        <span class="v2-label">Contraseña</span>
        <input class="v2-input" type="password" name="password" autocomplete="current-password" required>
    </label>

    <?= csrf_field() ?>
    <input type="hidden" name="redir_to" value="<?= e($redirTo) ?>">

    <button class="v2-btn v2-btn-primary v2-btn-block" type="submit">
        <i class="fa-solid fa-arrow-right-to-bracket"></i> Entrar
    </button>

    <?php if ($redirTo !== '/'): ?>
        <p class="v2-auth-foot"><span class="v2-label">Destino</span> <span class="v2-mono"><?= e(parse_url($redirTo, PHP_URL_HOST) ?: $redirTo) ?></span></p>
    <?php endif; ?>
</form>
