<?php
session_start();
if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../php/db.php';

$pdo = get_db();
$consultations = $pdo->query(
    'SELECT id, firstname, lastname, email, phone, consultation_type, preferred_date, status, created_at
     FROM consultations ORDER BY created_at DESC'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Consultations — Admin</title>
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
        <a href="consultations.php" class="active">Consultations</a>
        <a href="contacts.php">Messages</a>
    </nav>

    <h2 class="section-title">Toutes les consultations</h2>

    <div class="filter-export-bar">
        <form method="get" action="export_consultations.php" class="filter-form">
            <label>Du : <input type="date" name="start_date"></label>
            <label>Au : <input type="date" name="end_date"></label>
            <button type="submit" name="scope" value="filtered" class="btn-export">Excel (filtré)</button>
            <button type="submit" name="scope" value="all" class="btn-export" style="background:#7CA087;">Tout exporter</button>
        </form>
        <p class="filter-hint">Le filtre s'applique à la date de rendez-vous souhaitée. Laissez les dates vides ou cliquez sur « Tout exporter » pour exporter toutes les consultations.</p>
    </div>

    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Rechercher par nom, email, téléphone ou type...">
    </div>

    <div class="table-wrap">
        <table id="consultationsTable">
            <thead><tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Type</th>
                <th>Date souhaitée</th>
                <th>Statut</th>
                <th>Action</th>
            </tr></thead>
            <tbody>
            <?php if (empty($consultations)): ?>
                <tr><td colspan="7" class="text-center">Aucune consultation</td></tr>
            <?php else: ?>
                <?php foreach ($consultations as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['firstname'] . ' ' . $c['lastname']) ?></td>
                    <td><?= htmlspecialchars($c['email']) ?></td>
                    <td><?= htmlspecialchars($c['phone']) ?></td>
                    <td><?= htmlspecialchars($c['consultation_type']) ?></td>
                    <td><?= htmlspecialchars($c['preferred_date']) ?></td>
                    <td><span class="status-badge status-<?= $c['status'] ?>"><?= $c['status'] ?></span></td>
                    <td>
                        <form method="post" action="update_status.php" class="status-form">
                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                            <select name="status">
                                <option value="nouveau" <?= $c['status']==='nouveau'?'selected':'' ?>>Nouveau</option>
                                <option value="confirme" <?= $c['status']==='confirme'?'selected':'' ?>>Confirmé</option>
                                <option value="annule" <?= $c['status']==='annule'?'selected':'' ?>>Annulé</option>
                            </select>
                            <button type="submit">Mettre à jour</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
        <p id="noResults" class="empty-search" style="display:none;">Aucun résultat pour cette recherche.</p>
    </div>
</div>
<script src="script.js"></script>
<script>
    
</script>
</body>
</html>