<?php
/**
 * ticket_detail.php - Muestra y procesa el detalle de un ticket.
 * Incluye la lógica de actualización con transacciones y registro de historial.
 */

// Iniciar sesión
session_start();

// Redirigir si no hay usuario logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Cargar configuración y constantes
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/ticket_constants.php';

// Configurar PDO para que lance excepciones en errores
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Obtener ID del ticket
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die("Ticket no especificado o ID inválido.");
}

// Obtener datos actuales del ticket
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

// Obtener adjuntos
$stmt = $db->prepare("
    SELECT a.*, u.username
    FROM attachments a
    LEFT JOIN users u ON a.uploaded_by = u.id
    WHERE a.ticket_id = :ticket_id
    ORDER BY a.created_at DESC
");
$stmt->execute([':ticket_id' => $id]);
$attachments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Normalizar valores para evitar warnings en la vista
$ticket['assigned_to'] = $ticket['assigned_to'] ?? null;
$ticket['updated_at']  = $ticket['updated_at'] ?? null;
$ticket['description'] = $ticket['description'] ?? '';
$ticket['status']      = $ticket['status'] ?? '';
$ticket['priority']    = $ticket['priority'] ?? '';

// =============================================================================
// PROCESAR EL FORMULARIO DE EDICIÓN (solo para admin/técnico)
// =============================================================================
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['edit_ticket']) &&
    $_SESSION['role'] !== 'lector'
) {
    // Recoger y limpiar datos
    $description = trim($_POST['description'] ?? '');
    $status      = $_POST['status'] ?? '';
    $priority    = $_POST['priority'] ?? '';
    $assigned_to = trim($_POST['assigned_to'] ?? '');

    // --- Validaciones ---
    // Permitimos el valor vacío ('') además de los valores definidos en las constantes
    $allowedStatuses   = TICKET_STATUS;
    $allowedPriorities = TICKET_PRIORITY;

    if ($status !== '' && !in_array($status, $allowedStatuses)) {
        die("Estado inválido.");
    }
    if ($priority !== '' && !in_array($priority, $allowedPriorities)) {
        die("Prioridad inválida.");
    }
    // assigned_to puede ser vacío (sin asignar), lo manejaremos como null

    // Convertir valores vacíos a null para la base de datos
    $dbStatus      = $status === '' ? null : $status;
    $dbPriority    = $priority === '' ? null : $priority;
    $dbAssignedTo  = $assigned_to === '' ? null : $assigned_to;

    // INICIAR TRANSACCIÓN
    $db->beginTransaction();

    try {
        // 1. Actualizar el ticket
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
            ':status'      => $dbStatus,
            ':priority'    => $dbPriority,
            ':assigned_to' => $dbAssignedTo,
            ':id'          => $id
        ]);

        // 2. Registrar cambios en el historial (solo si realmente hubo cambios)
        $camposCriticos = [
            'status'      => $status,
            'priority'    => $priority,
            'assigned_to' => $assigned_to
        ];

        foreach ($camposCriticos as $campo => $nuevoValor) {
            $valorAnterior = $ticket[$campo] ?? null;

            // Normalizar para comparación (tratar '' y null como equivalentes)
            if ($valorAnterior === '') $valorAnterior = null;
            if ($nuevoValor === '') $nuevoValor = null;

            // Si hay cambio real (usando != para considerar null == null)
            if ($valorAnterior != $nuevoValor) {
                $stmtHist = $db->prepare("
                    INSERT INTO historial_cambios 
                    (ticket_id, user_id, campo_modificado, valor_anterior, valor_nuevo)
                    VALUES (:ticket_id, :user_id, :campo, :anterior, :nuevo)
                ");
                $stmtHist->execute([
                    ':ticket_id' => $id,
                    ':user_id'   => $_SESSION['user_id'],
                    ':campo'     => $campo,
                    ':anterior'  => $valorAnterior,
                    ':nuevo'     => $nuevoValor
                ]);
            }
        }

        // Confirmar transacción
        $db->commit();

        // Redirigir para evitar reenvío del formulario y mostrar cambios
        header("Location: ticket_detail.php?id=" . $id);
        exit;

    } catch (Exception $e) {
        // Revertir cambios si algo falla
        $db->rollBack();
        // Registrar error y mostrar mensaje amigable
        error_log("Error al actualizar ticket ID $id: " . $e->getMessage());
        $error = "Ocurrió un error al guardar los cambios. Por favor, inténtalo de nuevo.";
    }
}

// =============================================================================
// OBTENER COMENTARIOS E HISTORIAL (fuera del POST, para mostrarlos siempre)
// =============================================================================

// Comentarios
$stmtComments = $db->prepare("
    SELECT c.*, u.username
    FROM ticket_comments c
    LEFT JOIN users u ON c.user_id = u.id
    WHERE c.ticket_id = :ticket_id
    ORDER BY c.created_at ASC
");
$stmtComments->execute([':ticket_id' => $id]);
$comments = $stmtComments->fetchAll(PDO::FETCH_ASSOC);

// Historial de cambios
$stmtHistorial = $db->prepare("
    SELECT h.*, u.username
    FROM historial_cambios h
    LEFT JOIN users u ON h.user_id = u.id
    WHERE h.ticket_id = :ticket_id
    ORDER BY h.fecha_cambio ASC
");
$stmtHistorial->execute([':ticket_id' => $id]);
$historial = $stmtHistorial->fetchAll(PDO::FETCH_ASSOC);

// Unificar timeline
$timeline = [];
foreach ($comments as $comment) {
    $timeline[] = [
        'type'       => 'comment',
        'created_at' => $comment['created_at'],
        'data'       => $comment
    ];
}
foreach ($historial as $change) {
    $timeline[] = [
        'type'       => 'change',
        'created_at' => $change['fecha_cambio'],
        'data'       => $change
    ];
}
// Ordenar cronológicamente
usort($timeline, function ($a, $b) {
    return strtotime($a['created_at']) <=> strtotime($b['created_at']);
});

// Obtener técnicos para el selector de asignación (solo si no es lector)
$technicians = [];
if ($_SESSION['role'] !== 'lector') {
    $stmtTech = $db->prepare("
        SELECT id, username 
        FROM users 
        WHERE role = 'tecnico'
        ORDER BY username ASC
    ");
    $stmtTech->execute();
    $technicians = $stmtTech->fetchAll(PDO::FETCH_ASSOC);
}

// ===============================
// MAPA DE USUARIOS (para mostrar nombres en lugar de IDs en el historial)
// ===============================
$userMap = [];
$stmtUsers = $db->prepare("SELECT id, username FROM users WHERE role IN ('admin', 'tecnico')");
$stmtUsers->execute();
while ($row = $stmtUsers->fetch(PDO::FETCH_ASSOC)) {
    $userMap[$row['id']] = $row['username'];
}

// Cargar vistas
require_once __DIR__ . '/../views/partials/menu.php';
require_once __DIR__ . '/../views/tickets/detail.php';