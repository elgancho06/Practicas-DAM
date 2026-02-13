<?php

// Iniciamos la sesión para poder acceder a los datos del usuario autenticado
session_start();

// Si el usuario no está autenticado, se le redirige al login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Incluimos el menú común de navegación
// Este menú es el mismo que se muestra en la página principal
require_once __DIR__ . '/../views/partials/menu.php';

// A partir de aquí mostramos la información de la cuenta del usuario
?>

<h1>Mi cuenta</h1>

<p><strong>Usuario:</strong> <?php echo $_SESSION['username']; ?></p>
<p><strong>Rol:</strong> <?php echo $_SESSION['role']; ?></p>
