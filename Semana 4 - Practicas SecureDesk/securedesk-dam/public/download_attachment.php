<?php
session_start();
require_once __DIR__ . '/../app/config.php';

if (!isset($_SESSION['user_id'])) {
    die("No autorizado.");
}

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Archivo no especificado.");
}

$stmt = $db->prepare("
    SELECT * FROM attachments WHERE id = :id
");
$stmt->execute([':id' => $id]);
$file = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$file) {
    die("Archivo no encontrado.");
}

$path = __DIR__ . '/../uploads/' . $file['filepath'];

if (!file_exists($path)) {
    die("Archivo no disponible.");
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $file['filename'] . '"');
header('Content-Length: ' . filesize($path));
readfile($path);
exit;
