<?php

// Iniciamos la sesión para poder manipularla
session_start();

// Eliminamos todas las variables de sesión
session_unset();

// Destruimos la sesión completamente
session_destroy();

// Redirigimos al usuario al login
header('Location: login.php');
exit;
?>