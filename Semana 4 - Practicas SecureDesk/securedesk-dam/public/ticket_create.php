<?php

// Iniciamos sesión
session_start();

// Si no hay usuario logueado, redirigimos al login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Bloqueamos acceso a usuarios con rol lector
if ($_SESSION['role'] === 'lector') {
    die('Acceso denegado. No tienes permisos para crear tickets.');
}

// Conexión a la base de datos
require_once __DIR__ . '/../app/config.php';

// Variable para mostrar errores
$error = '';

// Comprobamos si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recogemos datos del formulario
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority = $_POST['priority'] ?? 'media';
    $category = $_POST['category'] ?? 'General';

    // Validación mínima
    if (empty($title)) {
        $error = 'El título es obligatorio';
    } else {

        // Preparamos la inserción del ticket
        $stmt = $db->prepare("
            INSERT INTO tickets 
            (title, description, status, priority, category, created_by, created_at, updated_at)
            VALUES
            (:title, :description, 'nuevo', :priority, :category, :created_by, datetime('now'), datetime('now'))
        ");

        // Ejecutamos la consulta
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':priority' => $priority,
            ':category' => $category,
            ':created_by' => $_SESSION['user_id']
        ]);

        // Redirigimos al listado de tickets
        header('Location: tickets_view.php');
        exit;
    }
}

// Cargamos el menú y la vista
require_once __DIR__ . '/../views/partials/menu.php';
require_once __DIR__ . '/../views/tickets/create.php';
