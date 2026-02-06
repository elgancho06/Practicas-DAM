<?php

session_start();
require_once __DIR__ . '/../app/auth.php';

//Requiere login y todos los roles
requireLogin();
requireRole(['admin', 'tecnico', 'lector']);

require_once __DIR__ . '/../views/partials/menu.php';

echo "<h1>Listado de Tickets</h1>";

// Control visual de acciones
if ($_SESSION['role'] !== 'lector') {
    echo "<p>Este usuario puede crear o editar tickets.</p>";
}
?>