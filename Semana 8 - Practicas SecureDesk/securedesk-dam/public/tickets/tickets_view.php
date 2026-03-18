<?php
// Iniciamos la sesión para poder usar los datos del usuario logueado
session_start();

// Si el usuario no está autenticado, se redirige al login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Incluimos la configuración de la base de datos (conexión a SQLite)
require_once __DIR__ . '/../../app/config.php';
// Incluimos los valores válidos de estado y prioridad
require_once __DIR__ . '/../../app/ticket_constants.php';

// Recogemos los filtros desde la URL (GET)
$status = $_GET['status'] ?? '';
$priority = $_GET['priority'] ?? '';
$q = trim($_GET['q'] ?? ''); // Término de búsqueda

// REGISTRO DE AUDITORÍA: Si hay una búsqueda activa, se registra la acción
if (!empty($q)) {
    require_once __DIR__ . '/../../app/audit.php';
    registrar_auditoria($db, $_SESSION['user_id'], 'TICKET_SEARCH', 'ticket', null, "Búsqueda: " . $q);
}

// Construimos la consulta base
$sql = "
    SELECT t.*, u.username AS assigned_user
    FROM tickets t
    LEFT JOIN users u ON t.assigned_to = u.id
    WHERE 1=1
";

$params = [];

// Aplicamos filtro por estado si existe
if (!empty($status)) {
    $sql .= " AND t.status = :status";
    $params[':status'] = $status;
}

// Aplicamos filtro por prioridad si existe
if (!empty($priority)) {
    $sql .= " AND t.priority = :priority";
    $params[':priority'] = $priority;
}

// Aplicamos búsqueda por texto si existe
if (!empty($q)) {
    $sql .= " AND (t.title LIKE :q OR t.description LIKE :q)";
    $params[':q'] = '%' . $q . '%';
}

// Ordenamos por fecha de creación (más recientes primero)
$sql .= " ORDER BY t.created_at DESC";

// Preparamos y ejecutamos la consulta
$stmt = $db->prepare($sql);
$stmt->execute($params);

// Guardamos los resultados
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Incluimos el menú común
require_once __DIR__ . '/../../views/partials/menu.php';

// Cargamos la vista del listado
require_once __DIR__ . '/../../views/tickets/list.php';