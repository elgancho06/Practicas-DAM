<?php
/**
 * public/export_tickets_csv.php - Exporta el listado de tickets a CSV.
 * Accesible para usuarios autenticados (admin/técnico/lector).
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    die("No autorizado.");
}

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/ticket_constants.php';
require_once __DIR__ . '/../app/audit.php';

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ============================================
// Construir consulta con filtros (igual que en tickets_view.php)
// ============================================
$where = [];
$params = [];

if (!empty($_GET['status'])) {
    $where[] = "t.status = :status";
    $params[':status'] = $_GET['status'];
}

if (!empty($_GET['priority'])) {
    $where[] = "t.priority = :priority";
    $params[':priority'] = $_GET['priority'];
}

$sql = "
    SELECT t.id, t.title, t.status, t.priority, 
           COALESCE(u.username, 'Sin asignar') as assigned_to,
           t.created_at
    FROM tickets t
    LEFT JOIN users u ON t.assigned_to = u.id
";

if (!empty($where)) {
    $sql .= " WHERE " . implode(' AND ', $where);
}

$sql .= " ORDER BY t.created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Registrar auditoría
if (function_exists('registrar_auditoria')) {
    $filtros = [];
    if (!empty($_GET['status'])) $filtros[] = 'status=' . $_GET['status'];
    if (!empty($_GET['priority'])) $filtros[] = 'priority=' . $_GET['priority'];
    $desc_filtros = $filtros ? ' con filtros: ' . implode(', ', $filtros) : '';
    $details = "Exportó listado de tickets a CSV" . $desc_filtros;
    registrar_auditoria($db, $_SESSION['user_id'], 'EXPORT_CSV', 'ticket_list', null, $details);
}

// ============================================
// Configurar cabeceras para descargar CSV
// ============================================
$filename = 'tickets_' . date('Y-m-d_H-i-s') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Añadir BOM (Byte Order Mark) para que Excel interprete UTF-8 correctamente
echo "\xEF\xBB\xBF";

// Abrir salida como archivo
$output = fopen('php://output', 'w');

// Escribir la fila de cabeceras con separador punto y coma
fputcsv($output, ['ID', 'Título', 'Estado', 'Prioridad', 'Asignado a', 'Fecha de creación'], ';', '"');

// Escribir cada ticket
foreach ($tickets as $ticket) {
    fputcsv($output, [
        $ticket['id'],
        $ticket['title'],
        $ticket['status'],
        $ticket['priority'],
        $ticket['assigned_to'],
        $ticket['created_at']
    ], ';', '"');
}

fclose($output);
exit;