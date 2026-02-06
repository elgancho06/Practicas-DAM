<?php

session_start();
require_once __DIR__ . '/../app/auth.php';

requireLogin();
requireRole(['admin', 'tecnico']);

require_once __DIR__ . '/../views/partials/menu.php';

echo "<h1>Zona de Tickets</h1>";
?>