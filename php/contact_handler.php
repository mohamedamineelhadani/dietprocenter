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

$name    = trim($_POST['name']    ?? '');
$email   = trim($_POST['email']   ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $subject === '' || $message === '') {
    respond(false, "Merci de remplir tous les champs.");
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, "Adresse email invalide.");
}

try {
    $pdo = get_db();
    $stmt = $pdo->prepare(
        'INSERT INTO contacts (name, email, subject, message) VALUES (:name, :email, :subject, :message)'
    );
    $stmt->execute([
        ':name'    => $name,
        ':email'   => $email,
        ':subject' => $subject,
        ':message' => $message,
    ]);
    respond(true, "Merci, votre message a bien été envoyé.");
} catch (Throwable $e) {
    // Avoid leaking DB details to the client.
    error_log('contact_handler error: ' . $e->getMessage());
    respond(false, "Une erreur est survenue, merci de réessayer plus tard.");
}