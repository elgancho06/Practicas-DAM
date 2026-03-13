<?php
/**
 * public/upload_attachment.php - Subida de archivos adjuntos a un ticket.
 * Solo accesible para admin/técnico.
 */

session_start();
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/audit.php';   // Opcional, para auditoría
require_once __DIR__ . '/../app/security.php'; // Para verificar CSRF

// Verificar autenticación y permisos
if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'lector') {
    die("No autorizado.");
}

// Configuración de límites (puede estar en un archivo de constantes)
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB
define('ALLOWED_MIME_TYPES', [
    'image/jpeg', 'image/png', 'image/gif',
    'application/pdf', 'text/plain',
    'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verificar token CSRF
    if (!isset($_POST['csrf_token']) || !verificar_csrf_token($_POST['csrf_token'])) {
        die("Error de validación CSRF.");
    }

    // Obtener y validar el ID del ticket
    $ticket_id = filter_input(INPUT_POST, 'ticket_id', FILTER_VALIDATE_INT);
    if (!$ticket_id) {
        die("ID de ticket inválido.");
    }

    // Comprobar que se ha enviado un archivo sin errores
    if (!isset($_FILES['attachment']) || $_FILES['attachment']['error'] !== UPLOAD_ERR_OK) {
        die("Error al subir archivo.");
    }

    $file = $_FILES['attachment'];

    // Validar tamaño
    if ($file['size'] > MAX_FILE_SIZE) {
        die("El archivo excede el tamaño máximo de " . (MAX_FILE_SIZE / 1024 / 1024) . " MB.");
    }

    // Obtener tipo MIME real del archivo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    // Validar tipo MIME
    if (!in_array($mime, ALLOWED_MIME_TYPES)) {
        die("Tipo de archivo no permitido.");
    }

    // Nombre original del archivo (se guardará en BD)
    $originalName = basename($file['name']);

    // Generar nombre seguro (aleatorio + extensión)
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $safeName = bin2hex(random_bytes(16)) . ($extension ? '.' . $extension : '');

    // Crear carpeta por ticket si no existe
    $uploadDir = __DIR__ . '/../uploads/ticket_' . $ticket_id . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Ruta completa de destino y ruta relativa para la BD
    $destination = $uploadDir . $safeName;
    $relativePath = 'ticket_' . $ticket_id . '/' . $safeName;

    // Mover el archivo a su ubicación definitiva
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        die("Error al guardar archivo.");
    }

    // Insertar registro en la base de datos
    $stmt = $db->prepare("
        INSERT INTO attachments (ticket_id, filename, filepath, mime_type, filesize, uploaded_by)
        VALUES (:ticket_id, :filename, :filepath, :mime_type, :filesize, :uploaded_by)
    ");
    $stmt->execute([
        ':ticket_id'   => $ticket_id,
        ':filename'    => $originalName,
        ':filepath'    => $relativePath,
        ':mime_type'   => $mime,
        ':filesize'    => $file['size'],
        ':uploaded_by' => $_SESSION['user_id']
    ]);

    // Auditoría (opcional)
    if (function_exists('registrar_auditoria')) {
        $details = "Subió archivo '$originalName' al ticket #$ticket_id";
        registrar_auditoria($db, $_SESSION['user_id'], 'ATTACHMENT_UPLOAD', 'ticket', $ticket_id, $details);
    }

    // Redirigir al detalle del ticket
    header("Location: ticket_detail.php?id=" . $ticket_id);
    exit;
}