<?php
/**
 * public/export_ticket.php - Genera un informe HTML de un ticket.
 * Accesible solo para admin/técnico.
 */

session_start();

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once __DIR__ . '/../../app/core/config.php';
require_once __DIR__ . '/../../app/modules/tickets/ticket_constants.php';
require_once __DIR__ . '/../../app/modules/audit/audit.php';

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    require_once __DIR__ . '/../../views/layout/menu.php';
    $mensaje_error = "ID de ticket no válido.";
    require_once __DIR__ . '/../../views/layout/no_access.php';
    exit;
}

// Obtener datos del ticket
$stmt = $db->prepare("
    SELECT t.*, u.username AS assigned_user
    FROM tickets t
    LEFT JOIN users u ON t.assigned_to = u.id
    WHERE t.id = :id
");
$stmt->execute([':id' => $id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    require_once __DIR__ . '/../../views/layout/menu.php';
    $mensaje_error = "El ticket solicitado no existe.";
    require_once __DIR__ . '/../../views/layout/no_access.php';
    exit;
}

// Obtener comentarios
$stmtComments = $db->prepare("
    SELECT c.*, u.username
    FROM ticket_comments c
    LEFT JOIN users u ON c.user_id = u.id
    WHERE c.ticket_id = :ticket_id
    ORDER BY c.created_at ASC
");
$stmtComments->execute([':ticket_id' => $id]);
$comments = $stmtComments->fetchAll(PDO::FETCH_ASSOC);

// Obtener historial de cambios
$stmtHistorial = $db->prepare("
    SELECT h.*, u.username
    FROM historial_cambios h
    LEFT JOIN users u ON h.user_id = u.id
    WHERE h.ticket_id = :ticket_id
    ORDER BY h.fecha_cambio ASC
");
$stmtHistorial->execute([':ticket_id' => $id]);
$historial = $stmtHistorial->fetchAll(PDO::FETCH_ASSOC);

// Obtener adjuntos
$stmtAttachments = $db->prepare("
    SELECT a.*, u.username
    FROM attachments a
    LEFT JOIN users u ON a.uploaded_by = u.id
    WHERE a.ticket_id = :ticket_id
    ORDER BY a.created_at ASC
");
$stmtAttachments->execute([':ticket_id' => $id]);
$attachments = $stmtAttachments->fetchAll(PDO::FETCH_ASSOC);

// Obtener nombre del usuario que genera el informe
$usuario_informe = $_SESSION['username'] ?? 'Desconocido';

// Registrar auditoría
if (function_exists('registrar_auditoria')) {
    $details = "Exportó informe HTML del ticket #$id";
    registrar_auditoria($db, $_SESSION['user_id'], 'EXPORT_HTML', 'ticket', $id, $details);
}

// Cargar la vista de informe
require_once __DIR__ . '/../../views/tickets/export_ticket.php';