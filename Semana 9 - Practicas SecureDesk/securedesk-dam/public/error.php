<?php
/**
 * public/error.php - Página genérica de error fatal.
 * Se muestra cuando ocurre un problema crítico (ej: fallo de base de datos).
 */
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Secure Desk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .error-container { max-width: 500px; text-align: center; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .error-icon { font-size: 64px; color: #dc3545; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">⚠️</div>
        <h1 class="h3 mb-3">Algo salió mal</h1>
        <p class="text-muted">Lo sentimos, la aplicación ha encontrado un error inesperado y no puede continuar.</p>
        <p class="text-muted">Por favor, inténtalo de nuevo más tarde o contacta con el administrador del sistema.</p>
        <hr>
        <a href="/securedesk-dam/public/index.php" class="btn btn-primary">Volver al inicio</a>
    </div>
</body>
</html>
