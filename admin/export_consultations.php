<?php
session_start();
if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../php/db.php';

$scope     = $_GET['scope'] ?? 'filtered';
$startDate = trim($_GET['start_date'] ?? '');
$endDate   = trim($_GET['end_date'] ?? '');


$useDateFilter = ($scope === 'filtered') && ($startDate !== '' || $endDate !== '');

function is_valid_date(string $d): bool {
    return (bool) DateTime::createFromFormat('Y-m-d', $d);
}

$pdo = get_db();
$sql = 'SELECT firstname, lastname, email, phone, consultation_type, preferred_date, status, message, created_at
        FROM consultations';
$params = [];

if ($useDateFilter) {
    $conditions = [];
    if ($startDate !== '' && is_valid_date($startDate)) {
        $conditions[] = 'preferred_date >= :start_date';
        $params[':start_date'] = $startDate;
    }
    if ($endDate !== '' && is_valid_date($endDate)) {
        $conditions[] = 'preferred_date <= :end_date';
        $params[':end_date'] = $endDate;
    }
    if ($conditions) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }
}
$sql .= ' ORDER BY preferred_date ASC, created_at DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$typeLabels = [
    'bilan_initial' => 'Bilan initial',
    'suivi'         => 'Suivi',
    'sportif'       => 'Nutrition sportive',
    'pediatrique'   => 'Suivi pédiatrique',
];
$statusLabels = [
    'nouveau'  => 'Nouveau',
    'confirme' => 'Confirmé',
    'annule'   => 'Annulé',
];

// Build a filename that reflects what was actually exported.
$filenameParts = ['consultations'];
if ($useDateFilter) {
    $filenameParts[] = ($startDate !== '' ? $startDate : 'debut');
    $filenameParts[] = ($endDate !== '' ? $endDate : 'fin');
} else {
    $filenameParts[] = 'toutes';
}
$filename = implode('_', $filenameParts) . '.xls';

header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// A UTF-8 BOM keeps accented French characters (é, è, à...) readable when Excel opens the file.
echo "\xEF\xBB\xBF";

function h(?string $v): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<table border="1">
    <tr>
        <th>Prénom</th>
        <th>Nom</th>
        <th>Email</th>
        <th>Téléphone</th>
        <th>Type de consultation</th>
        <th>Date souhaitée</th>
        <th>Statut</th>
        <th>Message</th>
        <th>Reçu le</th>
    </tr>
    <?php foreach ($rows as $r): ?>
    <tr>
        <td><?= h($r['firstname']) ?></td>
        <td><?= h($r['lastname']) ?></td>
        <td><?= h($r['email']) ?></td>
        <td><?= h($r['phone']) ?></td>
        <td><?= h($typeLabels[$r['consultation_type']] ?? $r['consultation_type']) ?></td>
        <td><?= h($r['preferred_date']) ?></td>
        <td><?= h($statusLabels[$r['status']] ?? $r['status']) ?></td>
        <td><?= h($r['message']) ?></td>
        <td><?= h($r['created_at']) ?></td>
    </tr>
    <?php endforeach; ?>
</table>
