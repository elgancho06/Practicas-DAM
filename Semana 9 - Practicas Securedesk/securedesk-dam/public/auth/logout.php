<?php
/**
 * logout.php - Cierra la sesión del usuario y registra la acción en auditoría.
 */

// Iniciamos la sesión para poder manipularla
session_start();

// Cargar configuración y funciones de auditoría
require_once __DIR__ . '/../../app/core/config.php';
require_once __DIR__ . '/../../app/modules/audit/audit.php';

// Registrar logout si hay un usuario autenticado
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $username = $_SESSION['username'] ?? 'Desconocido';
    $details = "Cierre de sesión del usuario: $username";

    // Llamar a la función de auditoría (asume que está definida en audit.php)
    // Parámetros: ($db, $user_id, $action, $entity, $entity_id, $details)
    registrar_auditoria($db, $userId, 'LOGOUT', 'user', $userId, $details);
}

// Eliminamos todas las variables de sesión
session_unset();

// Destruimos la sesión completamente
session_destroy();

// Redirigimos al usuario al login
header('Location: login.php');
exit;
?>