<?php
// Comprobamos que el usuario esté autenticado
// Si no hay rol en sesión, no mostramos el menú
if (!isset($_SESSION['role'])) {
    return;
}

// Obtenemos la ruta base (suponiendo que estamos en el directorio raíz de la aplicación)
// En un entorno real, podrías usar una constante URL_BASE definida en app/config.php
// Para mantener la simplicidad y basándonos en la estructura dada:
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
