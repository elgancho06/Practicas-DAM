<?php
/**
 * public/account.php - Controlador para la gestión de la cuenta del usuario.
 */

session_start();

// Si el usuario no está autenticado, se le redirige al login
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

require_once __DIR__ . '/../app/core/config.php';
require_once __DIR__ . '/../app/modules/audit/audit.php';

$userId = $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

// 1. Obtener datos actuales del usuario
try {
    $stmt = $db->prepare("SELECT id, username, role, created_at, password_hash FROM users WHERE id = :id");
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Usuario no encontrado.");
    }
} catch (PDOException $e) {
    die("Error al obtener datos del usuario: " . $e->getMessage());
}

// 2. Procesar cambio de contraseña si se recibe POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_pw = $_POST['current_pw'] ?? '';
    $new_pw = $_POST['new_pw'] ?? '';
    $confirm_pw = $_POST['confirm_pw'] ?? '';

    if (empty($current_pw) || empty($new_pw) || empty($confirm_pw)) {
        $error_msg = "Todos los campos son obligatorios.";
    } elseif ($new_pw !== $confirm_pw) {
        $error_msg = "La nueva contraseña y su confirmación no coinciden.";
    } elseif (strlen($new_pw) < 8 || !preg_match('/[A-Za-z]/', $new_pw) || !preg_match('/[0-9]/', $new_pw) || !preg_match('/[^A-Za-z0-9]/', $new_pw)) {
        $error_msg = "La nueva contraseña debe tener al menos 8 caracteres, incluyendo letras, números y al menos un símbolo.";
    } elseif (!password_verify($current_pw, $user['password_hash'])) {
        $error_msg = "La contraseña actual es incorrecta.";
    } else {
        // Todo OK, procedemos a actualizar
        try {
            $new_hash = password_hash($new_pw, PASSWORD_DEFAULT);
            $update = $db->prepare("UPDATE users SET password_hash = :hash WHERE id = :id");
            $update->execute([':hash' => $new_hash, ':id' => $userId]);

            // Registrar en auditoría
            registrar_auditoria($db, $userId, 'PASSWORD_CHANGE', 'user', $userId, "El usuario cambió su contraseña.");

            $success_msg = "Contraseña actualizada correctamente.";
        } catch (PDOException $e) {
            $error_msg = "Error al actualizar la contraseña: " . $e->getMessage();
        }
    }
}

// 3. Incluir el menú y cargar la vista
require_once __DIR__ . '/../views/layout/menu.php';
require_once __DIR__ . '/../views/account.php';
