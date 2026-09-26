<?php
session_start();
if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../php/db.php';

$pdo = get_db();
$contacts = $pdo->query(
    'SELECT id, name, email, subject, message, created_at FROM contacts ORDER BY created_at DESC'
)->fetchAll();
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
    <title>Messages — Admin</title>
</head>
<body>
<div class="admin-wrapper">
    <header class="admin-header">
        <div class="logo">diet<span>pro</span>center</div>
        <div class="user">
            <span>Connecté(e) : <?= htmlspecialchars($_SESSION['admin_username']) ?></span>
            <a href="logout.php">Déconnexion</a>
        </div>
    </header>

    <nav class="admin-nav">
        <a href="index.php">Dashboard</a>
        <a href="consultations.php">Consultations</a>
        <a href="contacts.php" class="active">Messages</a>
    </nav>

    <h2 class="section-title">Messages reçus</h2>

    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Rechercher par nom, email ou sujet...">
    </div>

    <div class="table-wrap">
        <table id="contactsTable">
            <thead><tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Sujet</th>
                <th>Message</th>
                <th>Date</th>
                <th>Action</th>
            </tr></thead>
            <tbody>
            <?php if (empty($contacts)): ?>
                <tr><td colspan="6" class="text-center">Aucun message</td></tr>
            <?php else: ?>
                <?php foreach ($contacts as $m):
                    $replySubject = 'Re: ' . $m['subject'];
                    $quoted = "\n\n---\nLe " . $m['created_at'] . ", " . $m['name'] . " a écrit :\n> " . str_replace("\n", "\n> ", $m['message']);
                    $mailto = 'mailto:' . rawurlencode($m['email'])
                        . '?subject=' . rawurlencode($replySubject)
                        . '&body=' . rawurlencode("Bonjour " . $m['name'] . ",\n\n" . $quoted);
                ?>
                <tr>
                    <td><?= htmlspecialchars($m['name']) ?></td>
                    <td><?= htmlspecialchars($m['email']) ?></td>
                    <td><?= htmlspecialchars($m['subject']) ?></td>
                    <td style="max-width: 280px;"><?= htmlspecialchars(substr($m['message'], 0, 100)) . (strlen($m['message'])>100?'…':'') ?></td>
                    <td><?= htmlspecialchars($m['created_at']) ?></td>
                    <td><a class="btn-reply" href="<?= htmlspecialchars($mailto) ?>">Répondre</a></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
        <p id="noResults" class="empty-search" style="display:none;">Aucun résultat pour cette recherche.</p>
    </div>
</div>
<script src="script.js"></script>
</body>
</html>