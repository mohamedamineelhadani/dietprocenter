<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db.php';

function respond(bool $success, string $message): void {
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, "Méthode non autorisée.");
}

$firstname = trim($_POST['firstname'] ?? '');
$lastname  = trim($_POST['lastname']  ?? '');
$email     = trim($_POST['email']     ?? '');
$phone     = trim($_POST['phone']     ?? '');
$type      = trim($_POST['consultation_type'] ?? '');
$date      = trim($_POST['preferred_date']    ?? '');
$message   = trim($_POST['message']   ?? '');

$allowed_types = ['bilan_initial', 'suivi', 'sportif', 'pediatrique'];

if ($firstname === '' || $lastname === '' || $email === '' || $phone === '' || $type === '' || $date === '') {
    respond(false, "Merci de remplir tous les champs obligatoires.");
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, "Adresse email invalide.");
}
if (!in_array($type, $allowed_types, true)) {
    respond(false, "Type de consultation invalide.");
}
$dateObj = DateTime::createFromFormat('Y-m-d', $date);
if (!$dateObj) {
    respond(false, "Date invalide.");
}

try {
    $pdo = get_db();
    $stmt = $pdo->prepare(
        'INSERT INTO consultations (firstname, lastname, email, phone, consultation_type, preferred_date, message)
         VALUES (:firstname, :lastname, :email, :phone, :type, :date, :message)'
    );
    $stmt->execute([
        ':firstname' => $firstname,
        ':lastname'  => $lastname,
        ':email'     => $email,
        ':phone'     => $phone,
        ':type'      => $type,
        ':date'      => $dateObj->format('Y-m-d'),
        ':message'   => $message !== '' ? $message : null,
    ]);
    respond(true, "Merci, votre demande de rendez-vous a bien été envoyée.");
} catch (Throwable $e) {
    error_log('consultation_handler error: ' . $e->getMessage());
    respond(false, "Une erreur est survenue, merci de réessayer plus tard.");
}