<?php
/**
 * public/upload_attachment.php - Subida de archivos adjuntos a un ticket.
 * Solo accesible para admin/técnico.
 */

session_start();
require_once __DIR__ . '/../../app/core/config.php';
require_once __DIR__ . '/../../app/modules/audit/audit.php';   // Opcional, para auditoría
require_once __DIR__ . '/../../app/core/security.php'; // Para verificar CSRF

// Verificar autenticación y permisos
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

if ($_SESSION['role'] === 'lector') {
    require_once __DIR__ . '/../../views/layout/menu.php';
    $mensaje_error = "Tu cuenta tiene un rol de 'lector' y no tienes permisos para subir archivos.";
    require_once __DIR__ . '/../../views/layout/no_access.php';
    exit;
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
        require_once __DIR__ . '/../../views/layout/menu.php';
        $mensaje_error = "Error de validación CSRF. Por favor, inténtalo de nuevo.";
        require_once __DIR__ . '/../../views/layout/no_access.php';
        exit;
    }

    // Obtener y validar el ID del ticket
    $ticket_id = filter_input(INPUT_POST, 'ticket_id', FILTER_VALIDATE_INT);
    if (!$ticket_id) {
        require_once __DIR__ . '/../../views/layout/menu.php';
        $mensaje_error = "ID de ticket no válido.";
        require_once __DIR__ . '/../../views/layout/no_access.php';
        exit;
    }

    // Comprobar que se ha enviado un archivo sin errores
    if (!isset($_FILES['attachment']) || $_FILES['attachment']['error'] !== UPLOAD_ERR_OK) {
        require_once __DIR__ . '/../../views/layout/menu.php';
        $mensaje_error = "Error al subir el archivo. Código de error: " . ($_FILES['attachment']['error'] ?? 'desconocido');
        require_once __DIR__ . '/../../views/layout/no_access.php';
        exit;
    }

    $file = $_FILES['attachment'];

    // Validar tamaño
    if ($file['size'] > MAX_FILE_SIZE) {
        require_once __DIR__ . '/../../views/layout/menu.php';
        $mensaje_error = "El archivo excede el tamaño máximo permitido de " . (MAX_FILE_SIZE / 1024 / 1024) . " MB.";
        require_once __DIR__ . '/../../views/layout/no_access.php';
        exit;
    }

    // Obtener tipo MIME real del archivo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    // Validar tipo MIME
    if (!in_array($mime, ALLOWED_MIME_TYPES)) {
        require_once __DIR__ . '/../../views/layout/menu.php';
        $mensaje_error = "El tipo de archivo no está permitido ($mime).";
        require_once __DIR__ . '/../../views/layout/no_access.php';
        exit;
    }

    // Nombre original del archivo (se guardará en BD)
    $originalName = basename($file['name']);

    // --- MEJORA DE SEGURIDAD: Validación de doble extensión ---
    // Si el nombre contiene más de un punto (ej: malware.php.pdf), se rechaza.
    if (substr_count($originalName, '.') > 1) {
        require_once __DIR__ . '/../../views/layout/menu.php';
        $mensaje_error = "Error: El nombre del archivo contiene múltiples extensiones.";
        require_once __DIR__ . '/../../views/layout/no_access.php';
        exit;
    }

    // Obtener extensión y normalizar a minúsculas
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    // --- CORRECCIÓN PRINCIPAL: Lista negra de extensiones peligrosas ---
    $forbidden_extensions = ['js', 'php', 'exe', 'sh', 'bat', 'cmd', 'py', 'rb', 'pl', 'html', 'htm', 'svg'];
    if (in_array($extension, $forbidden_extensions)) {
        require_once __DIR__ . '/../../views/layout/menu.php';
        $mensaje_error = "Error: Extensión de archivo (.$extension) no permitida por seguridad.";
        require_once __DIR__ . '/../../views/layout/no_access.php';
        exit;
    }

    // --- MEJORA: Validación cruzada Extensión vs MIME ---
    $mime_to_ext = [
        'image/jpeg'      => ['jpg', 'jpeg'],
        'image/png'       => ['png'],
        'image/gif'       => ['gif'],
        'application/pdf' => ['pdf'],
        'text/plain'      => ['txt'],
        'application/msword' => ['doc'],
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx']
    ];

    if (!isset($mime_to_ext[$mime]) || !in_array($extension, $mime_to_ext[$mime])) {
        require_once __DIR__ . '/../../views/layout/menu.php';
        $mensaje_error = "Error: La extensión del archivo (.$extension) no coincide con su contenido ($mime).";
        require_once __DIR__ . '/../../views/layout/no_access.php';
        exit;
    }

    // Generar nombre seguro (aleatorio + extensión validada)
    $safeName = bin2hex(random_bytes(16)) . '.' . $extension;

    // Crear carpeta por ticket si no existe
    $uploadDir = __DIR__ . '/../../uploads/ticket_' . $ticket_id . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Ruta completa de destino y ruta relativa para la BD
    $destination = $uploadDir . $safeName;
    $relativePath = 'ticket_' . $ticket_id . '/' . $safeName;

    // Mover el archivo a su ubicación definitiva
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        require_once __DIR__ . '/../../views/layout/menu.php';
        $mensaje_error = "Error al guardar el archivo en el servidor.";
        require_once __DIR__ . '/../../views/layout/no_access.php';
        exit;
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
    header("Location: ../tickets/ticket_detail.php?id=" . $ticket_id);
    exit;
}