<?php

session_start();
require_once __DIR__ . '/../app/auth.php';

// Obligamos a que el usuario esté logueado
// Solo el rol admin puede acceder
requireLogin();
requireRole(['admin']);

// Incluimos el menú común
require_once __DIR__ . '/../views/partials/menu.php';

echo "<h1>Gestión de Usuarios</h1>";
?>