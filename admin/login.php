<?php
session_start();
require_once __DIR__ . '/../php/db.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "Merci de renseigner vos identifiants.";
    } else {
        try {
            $pdo = get_db();
            $stmt = $pdo->prepare('SELECT id, username, password_hash FROM admins WHERE username = :u LIMIT 1');
            $stmt->execute([':u' => $username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['admin_id']       = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                header('Location: index.php');
                exit;
            }
            $error = "Identifiants incorrects.";
        } catch (Throwable $e) {
            error_log('login error: ' . $e->getMessage());
            $error = "Impossible de se connecter à la base de données.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
     <link rel="icon" type="image/png" href="icon.png" />
    <link rel="stylesheet" href="style.css">
    <title>Connexion — Dashboard DietProCenter</title>
</head>
<body class="login-body">
    <div class="login-card">
        <h1>diet<span>pro</span>center</h1>
        <p class="sub">Espace administrateur</p>
        <?php if ($error): ?>
            <div class="alert"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <label for="username">Identifiant</label>
            <input type="text" id="username" name="username" required autofocus>
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>