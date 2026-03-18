<?php
/**
 * Controlador para el Dashboard de métricas (KPIs)
 * Proporciona estadísticas generales sobre los tickets del sistema.
 */

// Iniciamos la sesión para verificar autenticación
session_start();

// Requerimos que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Incluimos la configuración de la base de datos y constantes
require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/ticket_constants.php';

try {
    // 1. Obtener el total de tickets
    $stmtTotal = $db->query("SELECT COUNT(*) FROM tickets");
    $totalTickets = $stmtTotal->fetchColumn();

    // 2. Obtener tickets agrupados por estado
    $stmtEstado = $db->query("SELECT status, COUNT(*) as count FROM tickets GROUP BY status");
    $resEstado = $stmtEstado->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Mapeo para asegurar que todos los estados definidos tengan un valor (aunque sea 0)
    $ticketsPorEstado = [];
    foreach (TICKET_STATUS as $status) {
        $ticketsPorEstado[$status] = $resEstado[$status] ?? 0;
    }

    // 3. Obtener tickets agrupados por prioridad
    $stmtPrioridad = $db->query("SELECT priority, COUNT(*) as count FROM tickets GROUP BY priority");
    $resPrioridad = $stmtPrioridad->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Mapeo para asegurar que todas las prioridades definidas tengan un valor
    $ticketsPorPrioridad = [];
    foreach (TICKET_PRIORITY as $priority) {
        $ticketsPorPrioridad[$priority] = $resPrioridad[$priority] ?? 0;
    }

    // 4. Obtener tickets agrupados por categoría
    $stmtCategoria = $db->query("SELECT category, COUNT(*) as count FROM tickets GROUP BY category ORDER BY count DESC");
    $ticketsPorCategoria = $stmtCategoria->fetchAll(PDO::FETCH_KEY_PAIR);

    // 5. Obtener conteo de tickets sin asignar
    $stmtSinAsignar = $db->query("SELECT COUNT(*) FROM tickets WHERE assigned_to IS NULL");
    $totalSinAsignar = $stmtSinAsignar->fetchColumn();

    // 6. Obtener conteo de tickets críticos (desde el array previo)
    $totalCriticos = $ticketsPorPrioridad['critica'] ?? 0;

    // 7. Fecha y hora de actualización
    $fechaActualizacion = date('d/m/Y H:i:s');

    // REGISTRO DE AUDITORÍA: Acceso al dashboard para trazabilidad mínima
    require_once __DIR__ . '/../../app/audit.php';
    registrar_auditoria($db, $_SESSION['user_id'], 'DASHBOARD_VIEW', 'dashboard', null, "Accedió al dashboard");

} catch (PDOException $e) {
    // En caso de error, podríamos registrarlo o mostrar un mensaje
    die("Error al obtener métricas: " . $e->getMessage());
}

// Incluimos el menú de navegación
require_once __DIR__ . '/../../views/partials/menu.php';

// Cargamos la vista del dashboard
require_once __DIR__ . '/../../views/dashboard.php';
