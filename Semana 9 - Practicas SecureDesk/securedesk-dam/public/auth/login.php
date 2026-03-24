<?php
session_start();

// Forzar zona horaria local (ajusta según tu ubicación)
date_default_timezone_set('Europe/Madrid');

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../app/core/config.php';
require_once __DIR__ . '/../../app/modules/audit/audit.php';
require_once __DIR__ . '/../../app/core/security.php';

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ==============================================
// CONTROL DE INTENTOS DE LOGIN
// ==============================================
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
if ($ip === '::1') $ip = '127.0.0.1'; // Normalizar IPv6 local

// Limpiar intentos antiguos usando la hora de SQLite
$stmt = $db->prepare("DELETE FROM login_attempts WHERE ip_address = :ip AND attempted_at < datetime('now', '-15 minutes', 'localtime')");
$stmt->execute([':ip' => $ip]);

// Contar intentos recientes y obtener el tiempo del bloqueo si aplica
$stmt = $db->prepare("SELECT COUNT(*) as total, MIN(attempted_at) as first_attempt FROM login_attempts WHERE ip_address = :ip");
$stmt->execute([':ip' => $ip]);
$attempt_data = $stmt->fetch(PDO::FETCH_ASSOC);
$intentos_recientes = $attempt_data['total'];

$max_intentos = 3;
$bloqueado = $intentos_recientes >= $max_intentos;
$minutos_restantes = 0;

if ($bloqueado) {
    // Calculamos los minutos basándonos en la hora de SQLite para evitar desajustes de zona horaria con PHP
    $stmt_time = $db->prepare("SELECT (strftime('%s', datetime('now', 'localtime')) - strftime('%s', :first_attempt)) / 60 as diff_min");
    $stmt_time->execute([':first_attempt' => $attempt_data['first_attempt']]);
    $diff_min = (int)$stmt_time->fetchColumn();
    
    $minutos_restantes = 15 - $diff_min;
    if ($minutos_restantes <= 0) {
        $bloqueado = false;
    }
}

// ==============================================
// TOKEN CSRF
// ==============================================
$csrf_token = generar_csrf_token();
$error = null;

// Manejar mensaje de sesión expirada (de security.php)
if (isset($_GET['error']) && $_GET['error'] === 'expired') {
    $error = "Tu sesión ha expirado por inactividad.";
}

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
        $error = "Demasiados intentos fallidos. Por seguridad, el acceso desde esta IP está bloqueado. Tiempo restante: $minutos_restantes minuto(s).";
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

            header('Location: ../dashboard/dashboard.php');
            exit;
        } else {
            // Login fallido
            $stmt = $db->prepare("INSERT INTO login_attempts (ip_address, username, attempted_at) VALUES (:ip, :user, datetime('now', 'localtime'))");
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
require_once __DIR__ . '/../../views/auth/login.php';