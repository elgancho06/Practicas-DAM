<?php
// app/core/security.php

// Inicia sesión si no está iniciada (para poder usar $_SESSION)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Control de expiración de sesión por inactividad (30 minutos)
if (isset($_SESSION['user_id'])) {
    $timeout = 30 * 60; // 30 minutos en segundos
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        // Sesión expirada
        session_unset();
        session_destroy();
        
        // Redirigir al login con mensaje (usando sesión temporal o parámetro GET)
        // Como acabamos de destruir la sesión, usaremos un parámetro GET o iniciaremos una nueva brevemente
        header("Location: /securedesk-dam/public/auth/login.php?error=expired");
        exit;
    }
    $_SESSION['last_activity'] = time(); // Actualizar el tiempo de última actividad
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