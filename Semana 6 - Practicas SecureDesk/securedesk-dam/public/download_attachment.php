<?php
session_start();
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/audit.php'; // opcional

if (!isset($_SESSION['user_id'])) {
    die("No autorizado.");
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die("ID de archivo inválido.");
}

$stmt = $db->prepare("SELECT * FROM attachments WHERE id = :id");
$stmt->execute([':id' => $id]);
$file = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$file) {
    die("Archivo no encontrado.");
}

// Opcional: verificar que el usuario tiene acceso al ticket
// (depende de la lógica de permisos de tu aplicación)

$path = __DIR__ . '/../uploads/' . $file['filepath'];
if (!file_exists($path)) {
    die("El archivo ya no está disponible en el servidor.");
}

// Registrar auditoría (opcional)
if (function_exists('registrar_auditoria')) {
    $details = "Descargó archivo '" . $file['filename'] . "' del ticket #" . $file['ticket_id'];
    registrar_auditoria($db, $_SESSION['user_id'], 'ATTACHMENT_DOWNLOAD', 'ticket', $file['ticket_id'], $details);
}

// Enviar cabeceras
header('Content-Description: File Transfer');
header('Content-Type: ' . ($file['mime_type'] ?? 'application/octet-stream'));
header('Content-Disposition: attachment; filename="' . $file['filename'] . '"');
header('Content-Length: ' . filesize($path));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

readfile($path);
exit;