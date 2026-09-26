<?php
session_start();
if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../php/db.php';

$pdo = get_db();

// Counts
$totalConsultations = $pdo->query('SELECT COUNT(*) FROM consultations')->fetchColumn();
$newConsultations = $pdo->query("SELECT COUNT(*) FROM consultations WHERE status = 'nouveau'")->fetchColumn();
$totalContacts = $pdo->query('SELECT COUNT(*) FROM contacts')->fetchColumn();


// Recent consultations (last 5)
$recentConsultations = $pdo->query(
    'SELECT id, firstname, lastname, email, consultation_type, preferred_date, status 
     FROM consultations ORDER BY created_at DESC LIMIT 5'
)->fetchAll();

// Recent contacts (last 5)
$recentContacts = $pdo->query(
    'SELECT id, name, email, subject, created_at FROM contacts ORDER BY created_at DESC LIMIT 5'
)->fetchAll();

// Data for chart: count by consultation_type
$typeStats = $pdo->query(
    'SELECT consultation_type, COUNT(*) as cnt FROM consultations GROUP BY consultation_type'
)->fetchAll();
$typeLabels = [];
$typeCounts = [];
foreach ($typeStats as $row) {
    $typeLabels[] = htmlspecialchars($row['consultation_type']);
    $typeCounts[] = (int)$row['cnt'];
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Dashboard — DietProCenter</title>
</head>
<body>
<div class="admin-wrapper">
    <!-- Header -->
    <header class="admin-header">
        <div class="logo">diet<span>pro</span>center</div>
        <div class="user">
            <span>Connecté(e) : <?= htmlspecialchars($_SESSION['admin_username']) ?></span>
            <a href="logout.php">Déconnexion</a>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="admin-nav">
        <a href="index.php" class="active">Dashboard</a>
        <a href="consultations.php">Consultations</a>
        <a href="contacts.php">Messages</a>
    </nav>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="number"><?= $totalConsultations ?></div>
            <div class="label">Total consultations</div>
        </div>
        <div class="stat-card">
            <div class="number"><?= $newConsultations ?></div>
            <div class="label">Nouvelles</div>
        </div>
        <div class="stat-card">
            <div class="number"><?= $totalContacts ?></div>
            <div class="label">Messages reçus</div>
        </div>
        <div class="stat-card">
            <div class="number"><?= $totalContacts + $totalConsultations ?></div>
            <div class="label">Total des demandes</div>
        </div>
    </div>


    <!-- Chart -->
    <div style="max-width: 500px; margin-bottom: 40px;">
        <canvas id="typeChart"></canvas>
    </div>

    <!-- Recent Consultations -->
    <h2 class="section-title">Dernières consultations</h2>
    <div class="table-wrap">
        <table>
            <thead><tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Type</th>
                <th>Date souhaitée</th>
                <th>Statut</th>
            </tr></thead>
            <tbody>
            <?php if (empty($recentConsultations)): ?>
                <tr><td colspan="5" class="text-center">Aucune consultation</td></tr>
            <?php else: ?>
                <?php foreach ($recentConsultations as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['firstname'] . ' ' . $c['lastname']) ?></td>
                    <td><?= htmlspecialchars($c['email']) ?></td>
                    <td><?= htmlspecialchars($c['consultation_type']) ?></td>
                    <td><?= htmlspecialchars($c['preferred_date']) ?></td>
                    <td><span class="status-badge status-<?= $c['status'] ?>"><?= $c['status'] ?></span></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Recent Contacts -->
    <h2 class="section-title">Derniers messages</h2>
    <div class="table-wrap">
        <table>
            <thead><tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Sujet</th>
                <th>Date</th>
            </tr></thead>
            <tbody>
            <?php if (empty($recentContacts)): ?>
                <tr><td colspan="4" class="text-center">Aucun message</td></tr>
            <?php else: ?>
                <?php foreach ($recentContacts as $m): ?>
                <tr>
                    <td><?= htmlspecialchars($m['name']) ?></td>
                    <td><?= htmlspecialchars($m['email']) ?></td>
                    <td><?= htmlspecialchars($m['subject']) ?></td>
                    <td><?= htmlspecialchars($m['created_at']) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Chart
const ctx = document.getElementById('typeChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($typeLabels) ?>,
        datasets: [{
            label: 'Nombre de consultations',
            data: <?= json_encode($typeCounts) ?>,
            backgroundColor: '#7CA087',
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        }
    }
});
</script>
</body>
</html>