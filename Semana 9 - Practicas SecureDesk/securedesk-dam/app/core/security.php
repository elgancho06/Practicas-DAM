<?php
// app/core/security.php

// Inicia sesión si no está iniciada (para poder usar $_SESSION)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración de expiración de sesión
$timeout_duration = 30 * 60; 

// Control de expiración de sesión por inactividad
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout_duration)) {
        // Sesión expirada
        session_unset();
        session_destroy();
        
        // Redirigir al login con mensaje
        // Intentamos detectar la ruta base dinámicamente o usamos una relativa segura
        $current_page = $_SERVER['PHP_SELF'];
        if (strpos($current_page, '/auth/login.php') === false) {
            header("Location: /securedesk-dam/public/auth/login.php?error=expired");
            exit;
        }
    }
    // Actualizar el tiempo de última actividad solo si no hemos expirado ya
    $_SESSION['last_activity'] = time(); 
}

/**
 * Genera un token CSRF y lo guarda en sesión.
 * Si ya existe, lo devuelve (para no regenerar en cada carga).
 */
function generar_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica que el token recibido coincida con el de sesión.
 */
function verificar_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Elimina el token de sesión (opcional, para regenerar tras uso).
 */
function limpiar_csrf_token() {
    unset($_SESSION['csrf_token']);
}