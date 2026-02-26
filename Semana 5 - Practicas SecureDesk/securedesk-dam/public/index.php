<?php

// Iniciamos la sesión para poder acceder a las variables de sesión
session_start();

// Comprobamos si el usuario está autenticado
// Si no existe user_id en sesión, redirigimos al login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// En este punto el usuario ya está autenticado

// Incluimos el menú común de navegación
// Este menú se mostrará en la Home y en cualquier otra página
// que incluya este archivo
require_once __DIR__ . '/../views/partials/menu.php';

// Cargamos la vista principal (Home)
// Esta vista ya no contiene el menú, solo el contenido
require_once __DIR__ . '/../views/home.php';
