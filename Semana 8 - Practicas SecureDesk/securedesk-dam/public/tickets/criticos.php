<?php
/**
 * Controlador: Vista rápida de tickets con prioridad crítica.
 */

session_start();

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/ticket_constants.php';

// Inicializar variables para la vista (evitar notices de filtros)
$status = '';
$priority = 'critica';
$titulo = "🔴 Tickets con prioridad crítica";

try {
    // Consulta SQL para tickets críticos
    $stmt = $db->prepare("
        SELECT t.*, u.username AS assigned_user
        FROM tickets t
        LEFT JOIN users u ON t.assigned_to = u.id
        WHERE t.priority = 'critica'
        ORDER BY t.created_at DESC
    ");
    $stmt->execute();
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al obtener tickets críticos: " . $e->getMessage());
}

// Incluir el menú común
require_once __DIR__ . '/../../views/partials/menu.php';

// Cargar la vista de listado
require_once __DIR__ . '/../../views/tickets/list.php';
