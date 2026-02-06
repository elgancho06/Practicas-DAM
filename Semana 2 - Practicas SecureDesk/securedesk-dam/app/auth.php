<?php

// Este archivo contiene funciones de seguridad relacionadas con autenticación y roles

// Comprueba si el usuario ha iniciado sesión
function requireLogin()
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

// Comprueba si el usuario tiene uno de los roles permitidos
function requireRole(array $roles)
{
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles)) {
        header('Location: index.php');
        exit;
    }
}
?>