<?php
/**
 * ticket_create.php - Crear un nuevo ticket.
 * Solo accesible para admin/técnico.
 */

// Iniciamos sesión
session_start();

// Si no hay usuario logueado, redirigimos al login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Bloqueamos acceso a usuarios con rol lector
if ($_SESSION['role'] === 'lector') {
    require_once __DIR__ . '/../../views/layout/menu.php';
    $mensaje_error = "Tu cuenta tiene un rol de 'lector' y no tienes permisos para crear nuevos tickets.";
    require_once __DIR__ . '/../../views/layout/no_access.php';
    exit;
}

// Conexión a la base de datos y funciones de auditoría y seguridad
require_once __DIR__ . '/../../app/core/config.php';
require_once __DIR__ . '/../../app/modules/audit/audit.php';
require_once __DIR__ . '/../../app/modules/tickets/ticket_constants.php';
require_once __DIR__ . '/../../app/core/security.php'; // <-- NUEVO: funciones CSRF

// Configurar PDO para excepciones
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Generar token CSRF para el formulario
$csrf_token = generar_csrf_token();

// Variable para mostrar errores
$error = '';

// Comprobamos si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verificar token CSRF (lo primero)
    if (!isset($_POST['csrf_token']) || !verificar_csrf_token($_POST['csrf_token'])) {
        die("Error de validación CSRF. Intenta de nuevo.");
    }

    // Recogemos datos del formulario
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority = $_POST['priority'] ?? '';
    $category = $_POST['category'] ?? 'General';

    // --- Validaciones estrictas en servidor ---
    if (empty($title)) {
        $error = 'El título es obligatorio.';
    } elseif (empty($description)) {
        $error = 'La descripción no puede estar vacía.';
    } elseif (empty($priority)) {
        $error = 'Debes seleccionar una prioridad.';
    } elseif (!in_array($priority, TICKET_PRIORITY)) {
        $error = 'Prioridad no válida.';
    } else {
        // Todo correcto, proceder con la inserción
        try {
            $stmt = $db->prepare("
                INSERT INTO tickets 
                (title, description, status, priority, category, created_by, created_at, updated_at)
                VALUES
                (:title, :description, 'nuevo', :priority, :category, :created_by, datetime('now'), datetime('now'))
            ");

            $stmt->execute([
                ':title'       => strip_tags($title),
                ':description' => strip_tags($description),
                ':priority'    => $priority,
                ':category'    => $category,
                ':created_by'  => $_SESSION['user_id']
            ]);

            $ticketId = $db->lastInsertId();

            // Auditoría
            $details = "Creó el ticket #$ticketId: " . substr($title, 0, 50) . (strlen($title) > 50 ? '...' : '');
            registrar_auditoria($db, $_SESSION['user_id'], 'TICKET_CREATE', 'ticket', $ticketId, $details);

            // Redirigir al listado
            header('Location: tickets_view.php');
            exit;

        } catch (Exception $e) {
            $error = "Error al crear el ticket: " . $e->getMessage();
            error_log("Error en ticket_create: " . $e->getMessage());
        }
    }
}

// Cargamos el menú y la vista (pasamos el token)
require_once __DIR__ . '/../../views/layout/menu.php';
require_once __DIR__ . '/../../views/tickets/create.php';