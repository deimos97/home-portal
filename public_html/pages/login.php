<?php
$error = '';

$redir_to = "/";

if (isset($_GET['redir_to'])) {
    // TODO: Only allow relative paths or specific domains for redirection
    $redir_to = $_GET['redir_to'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $users = json_decode(file_get_contents('/home/deimos97/private/users.json'), true);

    if (isset($users[$username]) && password_verify($password, $users[$username]['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['authenticated'] = true;
        $_SESSION['user'] = $username;
        
        header("Location: {$redir_to}");
        exit;
    }

    $error = 'Usuario o contraseña incorrectos';
}
?>
<!DOCTYPE html>
<html>
<body>
    <form method="post">
        <p><?= htmlspecialchars($error) ?></p>
        <label>Usuario: <input type="text" name="username"></label><br>
        <label>Contraseña: <input type="password" name="password"></label><br>
        <button type="submit">Entrar</button>
    </form>
</body>
</html>