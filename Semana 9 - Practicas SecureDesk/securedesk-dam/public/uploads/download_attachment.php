<?php
session_start();
require_once __DIR__ . '/../../app/core/config.php';
require_once __DIR__ . '/../../app/modules/audit/audit.php'; // opcional

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    require_once __DIR__ . '/../../views/layout/menu.php';
    $mensaje_error = "ID de archivo inválido.";
    require_once __DIR__ . '/../../views/layout/no_access.php';
    exit;
}

$stmt = $db->prepare("SELECT * FROM attachments WHERE id = :id");
$stmt->execute([':id' => $id]);
$file = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$file) {
    require_once __DIR__ . '/../../views/layout/menu.php';
    $mensaje_error = "Archivo no encontrado.";
    require_once __DIR__ . '/../../views/layout/no_access.php';
    exit;
}

// Opcional: verificar que el usuario tiene acceso al ticket
// (depende de la lógica de permisos de tu aplicación)

$path = __DIR__ . '/../../uploads/' . $file['filepath'];
if (!file_exists($path)) {
    require_once __DIR__ . '/../../views/layout/menu.php';
    $mensaje_error = "El archivo ya no está disponible en el servidor.";
    require_once __DIR__ . '/../../views/layout/no_access.php';
    exit;
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