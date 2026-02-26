<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    die("No autorizado.");
}

require_once __DIR__ . '/../app/config.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    die("Archivo inválido.");
}

$stmt = $db->prepare("SELECT * FROM attachments WHERE id = :id");
$stmt->execute([':id' => $id]);
$file = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$file) {
    die("Archivo no encontrado.");
}

$path = __DIR__ . '/../' . $file['filepath'];

if (!file_exists($path)) {
    die("El archivo no existe en el servidor.");
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($file['filename']) . '"');
header('Content-Length: ' . filesize($path));

readfile($path);
exit;
