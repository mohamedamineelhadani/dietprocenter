<?php
session_start();
if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../php/db.php';

$id = (int)($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';

$allowed = ['nouveau', 'confirme', 'annule'];
if ($id > 0 && in_array($status, $allowed)) {
    try {
        $pdo = get_db();
        $stmt = $pdo->prepare('UPDATE consultations SET status = :status WHERE id = :id');
        $stmt->execute([':status' => $status, ':id' => $id]);
    } catch (Throwable $e) {
        error_log('update_status error: ' . $e->getMessage());
    }
}
header('Location: consultations.php');
exit;