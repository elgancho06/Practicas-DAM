<?php
/**
 * views/layout/no_access.php - Mensaje de error para acceso no autorizado.
 */
if (!isset($mensaje_error)) {
    $mensaje_error = "No tienes permisos para realizar esta acción o acceder a este recurso.";
}
?>
<div class="container mt-5">
    <div class="alert alert-warning shadow-sm border-warning">
        <div class="d-flex align-items-center">
            <div class="display-4 me-4">🚫</div>
            <div>
                <h4 class="alert-heading">Acceso Denegado</h4>
                <p><?php echo htmlspecialchars($mensaje_error); ?></p>
                <hr>
                <p class="mb-0">
                    Si crees que esto es un error, contacta con el administrador. 
                    <div class="mt-3">
                        <a href="/securedesk-dam/public/dashboard/dashboard.php" class="btn btn-primary btn-sm">Ir al Panel Principal</a>
                        <a href="/securedesk-dam/public/auth/login.php" class="btn btn-outline-secondary btn-sm ms-2">Entrar con otra cuenta</a>
                    </div>
                </p>
            </div>
        </div>
    </div>
</div>
