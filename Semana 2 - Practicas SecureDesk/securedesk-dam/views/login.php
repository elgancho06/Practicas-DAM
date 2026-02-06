<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Definimos la codificación de caracteres -->
    <meta charset="UTF-8">

    <!-- Título que se mostrará en la pestaña del navegador -->
    <title>Login | SecureDesk</title>
</head>
<body>

    <!-- Título principal de la página -->
    <h1>Acceso a SecureDesk</h1>

    <?php
    // Comprobamos si existe la variable $error
    // Esta variable se define en el archivo de lógica cuando las credenciales son incorrectas
    if (isset($error)):
    ?>
        <!-- Mostramos el mensaje de error en color rojo -->
        <p style="color:red;">
            <?= $error ?>
        </p>
    <?php endif; ?>

    <!--
        Formulario de login
        Usamos el método POST para no enviar datos sensibles por la URL
    -->
    <form method="POST">

        <!-- Campo para introducir el nombre de usuario -->
        <label>
            Usuario:
            <input type="text" name="username" required>
        </label>

        <br><br>

        <!-- Campo para introducir la contraseña -->
        <label>
            Contraseña:
            <input type="password" name="password" required>
        </label>

        <br><br>

        <!-- Botón para enviar el formulario -->
        <button type="submit">Entrar</button>
    </form>

</body>
</html>
