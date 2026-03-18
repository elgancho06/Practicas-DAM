<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login · SecureDesk</title>
    <style>
        /* ============================================
           ESTILOS MODERNOS PARA LA PÁGINA DE LOGIN
           ============================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background: #0f172a; /* Fondo azul muy oscuro */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Tarjeta principal del login */
        .login-card {
            background: rgba(18, 30, 48, 0.9);
            backdrop-filter: blur(8px);
            border-radius: 32px;
            padding: 40px 35px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(66, 153, 225, 0.2);
            color: #e2e8f0;
        }

        /* Título con gradiente */
        .login-card h1 {
            font-size: 32px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 30px;
            background: linear-gradient(135deg, #90cdf4, #4299e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }

        /* Mensaje de error */
        .error-message {
            background-color: rgba(229, 62, 62, 0.15);
            border: 1px solid #e53e3e;
            color: #fc8181;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 14px;
            text-align: center;
            backdrop-filter: blur(4px);
        }

        /* Grupo de cada campo del formulario */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #90cdf4;
            margin-bottom: 8px;
        }

        /* Estilos para los campos de entrada */
        .form-group input {
            width: 100%;
            padding: 14px 18px;
            background: #1e2e44;
            border: 1px solid #2d4a6e;
            border-radius: 16px;
            color: #f0f4fa;
            font-size: 16px;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-group input:hover {
            border-color: #4299e1;
            background-color: #253c5c;
        }

        .form-group input:focus {
            border-color: #63b3ed;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.3);
        }

        /* Estilos específicos para el campo de contraseña */
        .form-group input[type="password"] {
            letter-spacing: 2px; /* Pequeño detalle visual */
        }

        /* Botón de envío */
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(145deg, #2563eb, #1e4b8f);
            color: white;
            border: none;
            border-radius: 40px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 18px -6px #1e3a8a;
            margin-top: 15px;
            letter-spacing: 0.5px;
        }

        .btn-login:hover {
            background: linear-gradient(145deg, #3b82f6, #2563eb);
            transform: translateY(-2px);
            box-shadow: 0 14px 22px -8px #1e4b8f;
        }

        /* Pequeño texto decorativo */
        .login-footer {
            text-align: center;
            margin-top: 25px;
            color: #718096;
            font-size: 13px;
        }

        .login-footer a {
            color: #90cdf4;
            text-decoration: none;
            transition: color 0.2s;
        }

        .login-footer a:hover {
            color: #63b3ed;
            text-decoration: underline;
        }

        /* Responsive: ajuste en móviles */
        @media (max-width: 480px) {
            .login-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h1>🔐 SecureDesk</h1>

        <?php
        // Mostrar mensaje de error si existe (por ejemplo, credenciales incorrectas)
        // La variable $error debe ser definida en el controlador (public/login.php)
        if (isset($error) && $error): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!--
            Formulario de inicio de sesión.
            - Utiliza método POST para no exponer datos en la URL.
            - Incluye un campo oculto con token CSRF para proteger contra ataques de falsificación de petición.
            - Los campos son obligatorios (required) para validación básica en cliente.
        -->
        <form method="POST" action="">
            <!-- Token CSRF: generado en el controlador y pasado a la vista.
                 Este token debe coincidir con el almacenado en sesión para que la petición sea válida. -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="form-group">
                <label for="username">👤 Usuario</label>
                <input type="text" id="username" name="username" placeholder="Tu nombre de usuario" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">🔑 Contraseña</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-login">Entrar</button>
        </form>

        <div class="login-footer">
            <p>Acceso exclusivo para personal autorizado</p>
        </div>
    </div>
</body>
</html>