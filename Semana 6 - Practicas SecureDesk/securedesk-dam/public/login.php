<?php
session_start();

// Forzar zona horaria local (ajusta según tu ubicación)
date_default_timezone_set('Europe/Madrid');

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/audit.php';
require_once __DIR__ . '/../app/security.php';

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ==============================================
// CONTROL DE INTENTOS DE LOGIN
// ==============================================
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
if ($ip === '::1') $ip = '127.0.0.1'; // Normalizar IPv6 local

// Limpiar intentos antiguos usando la hora de SQLite (más fiable, no depende de la zona horaria)
$stmt = $db->prepare("DELETE FROM login_attempts WHERE ip_address = :ip AND attempted_at < datetime('now', '-5 minutes')");
$stmt->execute([':ip' => $ip]);

// Contar intentos recientes
$stmt = $db->prepare("SELECT COUNT(*) as total FROM login_attempts WHERE ip_address = :ip");
$stmt->execute([':ip' => $ip]);
$intentos_recientes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$max_intentos = 5;
$bloqueado = $intentos_recientes >= $max_intentos;

// ==============================================
// TOKEN CSRF
// ==============================================
$csrf_token = generar_csrf_token();
$error = null;

// ==============================================
// PROCESAR FORMULARIO
// ==============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['csrf_token']) || !verificar_csrf_token($_POST['csrf_token'])) {
        die("Error de validación CSRF.");
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Usuario y contraseña son obligatorios.';
    } elseif ($bloqueado) {
        //Mensaje y bloqueo de POST por exceso de fallos
        $details = "Intento de login bloqueado por exceso de fallos (IP: $ip)";
        registrar_auditoria($db, null, 'LOGIN_BLOCKED', 'system', null, $details);
        $error = "Demasiados intentos fallidos. Espera 5 minutos.";
    } else {
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            // Login exitoso
            $stmt = $db->prepare("DELETE FROM login_attempts WHERE ip_address = :ip");
            $stmt->execute([':ip' => $ip]);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            $details = "Inicio de sesión exitoso para usuario: " . $user['username'];
            registrar_auditoria($db, $user['id'], 'LOGIN_SUCCESS', 'user', $user['id'], $details);

            if ($user['role'] === 'admin') {
                header('Location: index.php');
            } elseif ($user['role'] === 'tecnico') {
                header('Location: tickets.php');
            } else {
                header('Location: tickets_view.php');
            }
            exit;
        } else {
            // Login fallido
            $stmt = $db->prepare("INSERT INTO login_attempts (ip_address, username) VALUES (:ip, :user)");
            $stmt->execute([':ip' => $ip, ':user' => $username]);

            $details = "Intento fallido de inicio de sesión para usuario: " . $username . " (IP: $ip)";
            registrar_auditoria($db, null, 'LOGIN_FAILED', 'user', null, $details);

            $error = 'Usuario o contraseña incorrectos';
        }
    }
}

// ==============================================
// CARGAR VISTA
// ==============================================
require_once __DIR__ . '/../views/login.php';