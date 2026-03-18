<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("No autorizado.");
}

require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/ticket_constants.php';
require_once __DIR__ . '/../../app/audit.php';

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Obtener filtros y búsqueda
$status = $_GET['status'] ?? '';
$priority = $_GET['priority'] ?? '';
$q = trim($_GET['q'] ?? '');

// Construir consulta con filtros
$where = [];
$params = [];

if (!empty($status)) {
    $where[] = "t.status = :status";
    $params[':status'] = $status;
}
if (!empty($priority)) {
    $where[] = "t.priority = :priority";
    $params[':priority'] = $priority;
}
if (!empty($q)) {
    $where[] = "(t.title LIKE :q OR t.description LIKE :q)";
    $params[':q'] = '%' . $q . '%';
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
$filtros = [];
if (!empty($status)) $filtros[] = "status=$status";
if (!empty($priority)) $filtros[] = "priority=$priority";
if (!empty($q)) $filtros[] = "busqueda=$q";
$descFiltros = empty($filtros) ? 'todos los tickets' : implode(', ', $filtros);
$details = "Exportó CSV con $descFiltros";
registrar_auditoria($db, $_SESSION['user_id'], 'EXPORT_CSV', 'ticket', null, $details);

// Generar CSV
$filename = 'tickets_' . date('Y-m-d_H-i-s') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
echo "\xEF\xBB\xBF";

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Título', 'Estado', 'Prioridad', 'Asignado a', 'Fecha de creación'], ';', '"');
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