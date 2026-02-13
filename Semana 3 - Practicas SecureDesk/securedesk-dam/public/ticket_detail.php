<?php


// Inicio de Sesion
session_start();

// Si no hay usuario logueado se redirigire
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// ===============================
// Conexión con la base de datos y añadimos las constantes 
// de status y priority
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/ticket_constants.php';

// ===============================
// Comprobar el ID del ticket
$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    die("Ticket no especificado o ID inválido.");
}

// ===============================
// Tras obtener el ID se hace una consulta a la BD
$stmt = $db->prepare("SELECT * FROM tickets WHERE id = :id");
$stmt->execute([':id' => $id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    die("Ticket no encontrado.");
}

// ===============================
// Obtener adjuntos del ticket
$stmt = $db->prepare("
    SELECT a.*, u.username
    FROM attachments a
    LEFT JOIN users u ON a.uploaded_by = u.id
    WHERE a.ticket_id = :ticket_id
    ORDER BY a.created_at DESC
");
$stmt->execute([':ticket_id' => $id]);
$attachments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ===============================
// Evitar los warnings
$ticket['assigned_to'] = $ticket['assigned_to'] ?? null;
$ticket['updated_at']  = $ticket['updated_at'] ?? null;
$ticket['description'] = $ticket['description'] ?? '';
$ticket['status']      = $ticket['status'] ?? '';
$ticket['priority']    = $ticket['priority'] ?? '';

// ===============================
// Procesa la edición dependiendo del rol
// permitiendo la eidición a todos excepto a lector
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['role'] !== 'lector') {

    $description = trim($_POST['description'] ?? '');
    $status      = $_POST['status'] ?? '';
    $priority    = $_POST['priority'] ?? '';
    $assigned_to = trim($_POST['assigned_to'] ?? '');

    // Validación básica
    if (!in_array($status, TICKET_STATUS)) {
        die("Estado inválido.");
    }

    if (!in_array($priority, TICKET_PRIORITY)) {
        die("Prioridad inválida.");
    }

    $stmt = $db->prepare("
        UPDATE tickets
        SET description = :description,
            status = :status,
            priority = :priority,
            assigned_to = :assigned_to,
            updated_at = datetime('now')
        WHERE id = :id
    ");

    $stmt->execute([
        ':description' => $description,
        ':status'      => $status,
        ':priority'    => $priority,
        ':assigned_to' => $assigned_to ?: null,
        ':id'          => $id
    ]);

    // PRG Pattern
    header("Location: ticket_detail.php?id=" . $id);
    exit;
}

// ===============================
// Cargar las vistas del menú y los propios detalles
require_once __DIR__ . '/../views/partials/menu.php';
require_once __DIR__ . '/../views/tickets/detail.php';
