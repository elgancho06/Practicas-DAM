<?php
require_once 'app/core/config.php';
require_once 'app/core/auth.php';
require_once 'app/modules/audit/audit.php';

// Simular entorno de sesión para el admin
session_start();
$db = new PDO('sqlite:db/securedesk.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Obtener un admin (suponiendo que existe uno con ID 1 o buscándolo)
$stmt = $db->query("SELECT id, username FROM users WHERE role = 'admin' LIMIT 1");
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    die("No se encontró un usuario admin.");
}

$admin_id = $admin['id'];
$_SESSION['user_id'] = $admin_id;

echo "--- Iniciando simulación de auditoría ---\n";

// 1. Login
registrar_auditoria($db, $admin_id, 'LOGIN_SUCCESS', 'user', $admin_id, "Login exitoso (Simulado)");
echo "1. Login registrado.\n";

// 2. Logout
registrar_auditoria($db, $admin_id, 'LOGOUT', 'user', $admin_id, "Logout exitoso (Simulado)");
echo "2. Logout registrado.\n";

// 3. Login de nuevo
registrar_auditoria($db, $admin_id, 'LOGIN_SUCCESS', 'user', $admin_id, "Login re-ingreso (Simulado)");
echo "3. Re-login registrado.\n";

// 4. Crear un ticket nuevo
$ticket_title = "Prueba de Auditoria " . time();
$db->prepare("INSERT INTO tickets (title, description, status, priority, category, created_by) VALUES (?, ?, 'Abierto', 'Alta', 'Soporte', ?)")
   ->execute([$ticket_title, 'Descripción de prueba para auditoría', $admin_id]);
$ticket_id = $db->lastInsertId();
registrar_auditoria($db, $admin_id, 'TICKET_CREATE', 'ticket', $ticket_id, "Ticket creado: $ticket_title");
echo "4. Ticket creado (ID: $ticket_id).\n";

// 5. Editar ese ticket
$db->prepare("UPDATE tickets SET status = 'En Proceso', priority = 'Crítica', assigned_to = ? WHERE id = ?")
   ->execute([$admin_id, $ticket_id]);
registrar_auditoria($db, $admin_id, 'TICKET_UPDATE', 'ticket', $ticket_id, "Estado: En Proceso, Prioridad: Crítica, Asignado a: admin");
echo "5. Ticket editado.\n";

// 6. Añadir un comentario
$db->prepare("INSERT INTO ticket_comments (ticket_id, user_id, comment) VALUES (?, ?, ?)")
   ->execute([$ticket_id, $admin_id, 'Comentario de prueba de auditoría']);
registrar_auditoria($db, $admin_id, 'COMMENT_ADD', 'ticket', $ticket_id, "Nuevo comentario añadido.");
echo "6. Comentario añadido.\n";

// 7. Subir archivo
registrar_auditoria($db, $admin_id, 'ATTACHMENT_UPLOAD', 'ticket', $ticket_id, "Archivo subido: test_doc.pdf");
echo "7. Subida de archivo registrada.\n";

// 8. Descargar archivo
registrar_auditoria($db, $admin_id, 'ATTACHMENT_DOWNLOAD', 'ticket', $ticket_id, "Archivo descargado: test_doc.pdf");
echo "8. Descarga de archivo registrada.\n";

// 9. Exportar HTML
registrar_auditoria($db, $admin_id, 'EXPORT_HTML', 'ticket', $ticket_id, "Exportación HTML del ticket $ticket_id");
echo "9. Exportación HTML registrada.\n";

// 10. Exportar PDF
registrar_auditoria($db, $admin_id, 'EXPORT_PDF', 'ticket', $ticket_id, "Exportación PDF del ticket $ticket_id");
echo "10. Exportación PDF registrada.\n";

// 11. Exportar CSV
registrar_auditoria($db, $admin_id, 'EXPORT_CSV', 'ticket', null, "Exportación CSV del listado completo");
echo "11. Exportación CSV registrada.\n";

echo "--- Simulación completada ---\n";
