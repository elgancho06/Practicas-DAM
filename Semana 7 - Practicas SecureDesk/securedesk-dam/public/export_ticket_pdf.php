<?php
/**
 * public/export_ticket_pdf.php - Genera un PDF del informe de un ticket.
 * Accesible solo para admin/técnico.
 */

session_start();

// Verificar autenticación y rol (solo admin/técnico)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'lector') {
    die("No autorizado.");
}

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/ticket_constants.php';
require_once __DIR__ . '/../app/audit.php';

// Cargar Dompdf
require_once __DIR__ . '/../libs/dompdf/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die("ID de ticket inválido.");
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
    die("Ticket no encontrado.");
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

// ============================================================
// Definir la variable que necesita la vista
// ============================================================
$usuario_informe = $_SESSION['username'] ?? 'Desconocido';

// Registrar auditoría
if (function_exists('registrar_auditoria')) {
    $details = "Exportó informe PDF del ticket #$id";
    registrar_auditoria($db, $_SESSION['user_id'], 'EXPORT_PDF', 'ticket', $id, $details);
}

// Generar el HTML del informe (reutilizamos la misma vista pero capturamos la salida)
ob_start();
include __DIR__ . '/../views/export_ticket.php';
$html = ob_get_clean();

// Configurar Dompdf
$options = new Options();
$options->set('isRemoteEnabled', false);
$options->set('defaultFont', 'Helvetica');
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Enviar el PDF al navegador para descarga
$filename = 'ticket_' . $id . '_informe_' . date('Y-m-d') . '.pdf';
$dompdf->stream($filename, array('Attachment' => 1));
exit;