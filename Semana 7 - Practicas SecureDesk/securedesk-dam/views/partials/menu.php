<?php
// Comprobamos que el usuario esté autenticado
// Si no hay rol en sesión, no mostramos el menú
if (!isset($_SESSION['role'])) {
    return;
}
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
    <a href="index.php">🏠 Home</a>

    <a href="tickets_view.php">🎫 Ver tickets</a>

    <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="users.php">👤 Usuarios</a>
    <?php endif; ?>

    <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="audit.php">📋 Auditoría</a>
    <?php endif; ?>

    <span class="right">
        <a href="account.php">⚙ Mi cuenta</a>
        <a href="logout.php">🚪 Cerrar sesión</a>
    </span>
</nav>
