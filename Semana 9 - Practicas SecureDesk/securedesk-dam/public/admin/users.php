<?php
// public/admin/users.php

require_once __DIR__ . '/../../app/core/config.php';
require_once __DIR__ . '/../../app/core/security.php';
require_once __DIR__ . '/../../app/modules/audit/audit.php';

// 1. Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

// 2. Verificar rol admin
if ($_SESSION['role'] !== 'admin') {
    require_once __DIR__ . '/../../views/layout/menu.php';
    $mensaje_error = "Este panel de administración de usuarios es exclusivo para administradores.";
    require_once __DIR__ . '/../../views/layout/no_access.php';
    exit;
}

$error = '';
$success = '';

// 2. Procesar acciones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    if (!verificar_csrf_token($csrf_token)) {
        $error = "Error de seguridad: Token CSRF inválido.";
    } else {
        if ($action === 'create') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? '';

            // Validaciones
            if (empty($username) || empty($password) || empty($role)) {
                $error = "Todos los campos son obligatorios.";
            } elseif (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^A-Za-z0-9]/', $password)) {
                $error = "La contraseña debe tener al menos 8 caracteres, incluyendo letras, números y al menos un símbolo.";
            } elseif (!in_array($role, ['admin', 'tecnico', 'lector'])) {
                $error = "Rol no válido.";
            } else {
                // Verificar si el usuario ya existe
                $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
                $stmt->execute([$username]);
                if ($stmt->fetch()) {
                    $error = "El nombre de usuario ya está en uso.";
                } else {
                    // Crear usuario
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
                    $stmt->execute([$username, $password_hash, $role]);
                    $new_id = $db->lastInsertId();

                    registrar_auditoria($db, $_SESSION['user_id'], 'USER_CREATE', 'users', $new_id, "Usuario creado: $username con rol $role");
                    $success = "Usuario '$username' creado correctamente.";
                }
            }
        } elseif ($action === 'update_role') {
            $user_id = (int)($_POST['user_id'] ?? 0);
            $new_role = $_POST['role'] ?? '';

            if ($user_id === (int)$_SESSION['user_id']) {
                $error = "No puedes cambiar tu propio rol.";
            } elseif (!in_array($new_role, ['admin', 'tecnico', 'lector'])) {
                $error = "Rol no válido.";
            } else {
                // Obtener rol anterior para auditoría
                $stmt = $db->prepare("SELECT username, role FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    $old_role = $user['role'];
                    $stmt = $db->prepare("UPDATE users SET role = ? WHERE id = ?");
                    $stmt->execute([$new_role, $user_id]);

                    registrar_auditoria($db, $_SESSION['user_id'], 'USER_UPDATE_ROLE', 'users', $user_id, "Cambio de rol para {$user['username']}: $old_role -> $new_role");
                    $success = "Rol de '{$user['username']}' actualizado a $new_role.";
                } else {
                    $error = "Usuario no encontrado.";
                }
            }
        } elseif ($action === 'delete') {
            $user_id = (int)($_POST['user_id'] ?? 0);

            if ($user_id === (int)$_SESSION['user_id']) {
                $error = "No puedes eliminar tu propio usuario.";
            } else {
                // Verificar si tiene tickets asociados
                $stmt = $db->prepare("SELECT COUNT(*) FROM tickets WHERE created_by = ? OR assigned_to = ?");
                $stmt->execute([$user_id, $user_id]);
                $count = $stmt->fetchColumn();

                if ($count > 0) {
                    $error = "No se puede eliminar el usuario porque tiene $count tickets asociados (como creador o asignado).";
                } else {
                    $stmt = $db->prepare("SELECT username FROM users WHERE id = ?");
                    $stmt->execute([$user_id]);
                    $username = $stmt->fetchColumn();

                    if ($username) {
                        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
                        $stmt->execute([$user_id]);

                        registrar_auditoria($db, $_SESSION['user_id'], 'USER_DELETE', 'users', $user_id, "Usuario eliminado: $username");
                        $success = "Usuario '$username' eliminado correctamente.";
                    } else {
                        $error = "Usuario no encontrado.";
                    }
                }
            }
        }
    }
}

// 3. Obtener listado de usuarios
$stmt = $db->query("SELECT id, username, role, created_at FROM users ORDER BY id DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 4. Cargar la vista
include __DIR__ . '/../../views/admin/users.php';
