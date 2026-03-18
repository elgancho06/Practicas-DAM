<?php
/**
 * public/audit.php - Panel de auditoría para administradores.
 * Controlador que procesa los filtros y obtiene los datos de la tabla audit_logs.
 */

// Iniciar sesión y verificar permisos
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Cargar configuración y funciones de auditoría
require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/audit.php';

// Configurar PDO para lanzar excepciones (opcional pero recomendado)
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ============================================================
// Obtener listas para los filtros (usuarios y acciones)
// ============================================================
$stmt = $db->query("SELECT id, username FROM users ORDER BY username");
$users = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // array [id => username]

$stmt = $db->query("SELECT DISTINCT action FROM audit_logs ORDER BY action");
$actions = $stmt->fetchAll(PDO::FETCH_COLUMN); // array de acciones

// ============================================================
// Construir consulta con filtros dinámicos
// ============================================================
$where = [];
$params = [];

if (!empty($_GET['user_id'])) {
    $where[] = "audit_logs.user_id = :user_id";
    $params[':user_id'] = $_GET['user_id'];
}
if (!empty($_GET['action'])) {
    $where[] = "audit_logs.action = :action";
    $params[':action'] = $_GET['action'];
}

$sql = "SELECT audit_logs.*, users.username 
        FROM audit_logs 
        LEFT JOIN users ON audit_logs.user_id = users.id";
if (!empty($where)) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY audit_logs.created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ============================================================
// Cargar la vista (solo presentación)
// ============================================================
require_once __DIR__ . '/../../views/audit.php';