<?php
// config.php

// Aseguramos el control de sesión por inactividad si no estamos en CLI
if (php_sapi_name() !== 'cli') {
    require_once __DIR__ . '/security.php';
}

try {
    $db = new PDO('sqlite:' . __DIR__ . '/../../db/securedesk.sqlite');
    // Configuramos para que devuelva errores como excepciones
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Registramos el error en el log del servidor
    error_log("Error de conexión PDO: " . $e->getMessage());
    // Redirigimos a una página de error amigable
    header('Location: /securedesk-dam/public/error.php');
    exit;
}
?>