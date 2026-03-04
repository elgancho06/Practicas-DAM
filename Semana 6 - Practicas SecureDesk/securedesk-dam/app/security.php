<?php
// app/security.php

// Inicia sesión si no está iniciada (para poder usar $_SESSION)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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