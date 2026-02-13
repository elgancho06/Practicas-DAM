<?php

// Iniciamos la sesión para poder guardar datos del usuario
// Debe ir siempre al principio del archivo
session_start();

// Cargamos la configuración y la conexión a la base de datos
require_once __DIR__ . '/../app/config.php';

// Inicializamos la variable de error
// Se utilizará para mostrar mensajes en la vista si el login falla
$error = null;

// Comprobamos si el formulario se ha enviado mediante POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recogemos el nombre de usuario del formulario
    $username = $_POST['username'] ?? '';

    // Recogemos la contraseña del formulario
    $password = $_POST['password'] ?? '';

    // Preparamos la consulta SQL para buscar al usuario por su username
    // Se utiliza una consulta preparada para evitar inyecciones SQL
    $stmt = $db->prepare("
        SELECT * FROM users
        WHERE username = :username
        LIMIT 1
    ");

    // Ejecutamos la consulta pasando el nombre de usuario como parámetro
    $stmt->execute([
        ':username' => $username
    ]);

    // Obtenemos el usuario encontrado (si existe) como array asociativo
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Comprobamos que el usuario exista y que la contraseña sea correcta
    // password_verify compara la contraseña introducida con el hash guardado
    if ($user && password_verify($password, $user['password_hash'])) {

        // Guardamos los datos del usuario en la sesión
        // Esto permite mantener al usuario autenticado
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirigimos al usuario a la página principal dependiendo de su rol
        if ($user['role'] === 'admin') {
            header('Location: index.php');
        } elseif ($user['role'] === 'tecnico') {
            header('Location: tickets.php');
        } else {
            header('Location: tickets_view.php');
        }
        exit;

    } else {
        // Si las credenciales no son correctas, definimos el mensaje de error
        $error = 'Usuario o contraseña incorrectos';
    }
}

// Cargamos la vista del formulario de login
// La vista mostrará el formulario y el error si existe
require_once __DIR__ . '/../views/login.php';
?>