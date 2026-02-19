<?php

// Iniciamos la sesión para poder usar los datos del usuario logueado
session_start();

// Si el usuario no está autenticado, se redirige al login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Incluimos la configuración de la base de datos (conexión a SQLite)
require_once __DIR__ . '/../app/config.php';
// Incluimos los valores válidos de estado y prioridad
require_once __DIR__ . '/../app/ticket_constants.php';

// Recogemos los filtros desde la URL (GET)
$status = $_GET['status'] ?? '';
$priority = $_GET['priority'] ?? '';

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
    $sql .= " AND status = :status";
    $params[':status'] = $status;
}

// Aplicamos filtro por prioridad si existe
if (!empty($priority)) {
    $sql .= " AND priority = :priority";
    $params[':priority'] = $priority;
}

// Ordenamos por fecha de creación (más recientes primero)
$sql .= " ORDER BY created_at DESC";

// Preparamos y ejecutamos la consulta
$stmt = $db->prepare($sql);
$stmt->execute($params);

// Guardamos los resultados
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Incluimos el menú común
require_once __DIR__ . '/../views/partials/menu.php';

// Cargamos la vista del listado
require_once __DIR__ . '/../views/tickets/list.php';
