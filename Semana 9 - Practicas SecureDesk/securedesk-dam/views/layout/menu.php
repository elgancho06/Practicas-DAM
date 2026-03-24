<?php
// views/layout/menu.php

// Aseguramos que security.php esté incluido para el control de sesión en el servidor
require_once __DIR__ . '/../../app/core/security.php';

// Comprobamos que el usuario esté autenticado
// Si no hay rol en sesión, no mostramos el menú
if (!isset($_SESSION['role'])) {
    return;
}

// Obtenemos la ruta base
$baseUrl = "/securedesk-dam/public/";
?>

<style>
    .menu {
        background-color: #1e293b;
        padding: 15px;
        margin-bottom: 25px;
        border-radius: 8px;
    }

    .menu a {
        color: #f1f5f9;
        text-decoration: none;
        margin-right: 15px;
        font-weight: bold;
    }

    .menu a:hover {
        text-decoration: underline;
        color: #38bdf8;
    }

    .menu .right {
        float: right;
    }
</style>

<nav class="menu">
    <a href="<?= $baseUrl ?>index.php">🏠 Home</a>

    <a href="<?= $baseUrl ?>dashboard/dashboard.php">📊 Dashboard</a>

    <a href="<?= $baseUrl ?>tickets/tickets_view.php">🎫 Ver tickets</a>

    <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="<?= $baseUrl ?>admin/users.php">👤 Usuarios</a>
    <?php endif; ?>

    <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="<?= $baseUrl ?>admin/audit.php">📋 Auditoría</a>
    <?php endif; ?>

    <span class="right">
        <a href="<?= $baseUrl ?>account.php">⚙ Mi cuenta</a>
        <a href="<?= $baseUrl ?>auth/logout.php">🚪 Cerrar sesión</a>
    </span>
</nav>

<script>
/**
 * Script para el cierre de sesión automático por inactividad (Cliente)
 * Redirige al usuario al login si no se detecta actividad en 1 minuto.
 */
(function() {
    let timeout;
    const inactivityTime = 30 * 60 * 1000; // 30 minutos en milisegundos

    function resetTimer() {
        clearTimeout(timeout);
        timeout = setTimeout(logout, inactivityTime);
    }

    function logout() {
        alert("Tu sesión ha expirado por inactividad.");
        window.location.href = "<?= $baseUrl ?>auth/login.php?error=expired";
    }

    // Eventos que reinician el contador de actividad
    window.onload = resetTimer;
    window.onmousemove = resetTimer;
    window.onmousedown = resetTimer; // Clicks
    window.ontouchstart = resetTimer; // Pantallas táctiles
    window.onclick = resetTimer;     
    window.onkeypress = resetTimer;   
    window.addEventListener('scroll', resetTimer, true); 

    resetTimer();
})();
</script>
