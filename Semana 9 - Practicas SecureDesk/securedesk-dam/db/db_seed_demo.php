<?php
/**
 * Script de seeding para entorno de demostración.
 * Este script limpia las tablas de tickets y usuarios y genera datos realistas.
 */

require_once __DIR__ . '/../app/core/config.php';
require_once __DIR__ . '/../app/modules/tickets/ticket_constants.php';
require_once __DIR__ . '/../app/core/roles.php';

try {
    // 1. Limpiar datos existentes (respetando integridad referencial)
    $db->exec("PRAGMA foreign_keys = OFF");
    $db->exec("DELETE FROM ticket_comments");
    $db->exec("DELETE FROM historial_cambios");
    $db->exec("DELETE FROM attachments");
    $db->exec("DELETE FROM tickets");
    $db->exec("DELETE FROM users");
    $db->exec("DELETE FROM audit_logs");
    $db->exec("DELETE FROM login_attempts");
    
    // Reiniciar contadores de autoincremento
    $db->exec("DELETE FROM sqlite_sequence WHERE name IN ('users', 'tickets', 'ticket_comments', 'historial_cambios', 'attachments', 'audit_logs', 'login_attempts')");
    $db->exec("PRAGMA foreign_keys = ON");

    echo "Base de datos limpia correctamente...<br>";

    // 2. Crear Usuarios
    $users = [
        ['username' => 'admin_central', 'password' => 'admin123', 'role' => 'admin'],
        ['username' => 'tech_roberto', 'password' => 'tech123', 'role' => 'tecnico'],
        ['username' => 'tech_lucia', 'password' => 'tech123', 'role' => 'tecnico'],
        ['username' => 'user_carlos', 'password' => 'user123', 'role' => 'lector'],
        ['username' => 'user_elena', 'password' => 'user123', 'role' => 'lector']
    ];

    $stmtUser = $db->prepare("INSERT INTO users (username, password_hash, role) VALUES (:u, :p, :r)");
    $userIds = [];

    foreach ($users as $u) {
        $stmtUser->execute([
            ':u' => $u['username'],
            ':p' => password_hash($u['password'], PASSWORD_DEFAULT),
            ':r' => $u['role']
        ]);
        $userIds[$u['username']] = $db->lastInsertId();
    }

    echo "Usuarios creados: " . count($userIds) . " ✅<br>";

    // 3. Crear Tickets Realistas
    $tickets = [
        [
            'title' => 'Fallo crítico en el servidor de archivos',
            'description' => 'No se puede acceder a la unidad compartida Z desde la oficina de Madrid. El servicio SMB parece caído.',
            'status' => 'en_proceso',
            'priority' => 'critica',
            'category' => 'red',
            'created_by' => $userIds['tech_roberto'],
            'assigned_to' => $userIds['tech_roberto']
        ],
        [
            'title' => 'Monitor no enciende tras tormenta',
            'description' => 'El monitor principal del puesto 14 no recibe corriente. Se ha probado con otro cable y sigue igual.',
            'status' => 'nuevo',
            'priority' => 'media',
            'category' => 'hardware',
            'created_by' => $userIds['user_carlos'],
            'assigned_to' => null
        ],
        [
            'title' => 'Error al exportar informes a PDF',
            'description' => 'La aplicación de gestión da un error 500 cuando intento generar el informe mensual de ventas.',
            'status' => 'nuevo',
            'priority' => 'alta',
            'category' => 'software',
            'created_by' => $userIds['user_elena'],
            'assigned_to' => $userIds['tech_lucia']
        ],
        [
            'title' => 'Instalación de impresora de etiquetas',
            'description' => 'Ha llegado la nueva Zebra para el almacén. Necesitamos configuración en el equipo del operario.',
            'status' => 'resuelto',
            'priority' => 'baja',
            'category' => 'hardware',
            'created_by' => $userIds['admin_central'],
            'assigned_to' => $userIds['tech_roberto']
        ],
        [
            'title' => 'Solicitud de acceso a repositorio Git',
            'description' => 'Necesito permisos de escritura en el repo de "securedesk-dam" para el nuevo desarrollador.',
            'status' => 'nuevo',
            'priority' => 'media',
            'category' => 'otros',
            'created_by' => $userIds['user_elena'],
            'assigned_to' => null
        ]
    ];

    $stmtTicket = $db->prepare("
        INSERT INTO tickets (title, description, status, priority, category, created_by, assigned_to, created_at)
        VALUES (:t, :d, :s, :p, :c, :cb, :at, :ca)
    ");

    foreach ($tickets as $t) {
        $stmtTicket->execute([
            ':t' => $t['title'],
            ':d' => $t['description'],
            ':s' => $t['status'],
            ':p' => $t['priority'],
            ':c' => $t['category'],
            ':cb' => $t['created_by'],
            ':at' => $t['assigned_to'],
            ':ca' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 10) . ' days'))
        ]);
    }

    echo "Tickets de prueba creados: " . count($tickets) . " ✅<br>";
    echo "Seed finalizado con éxito.";

} catch (PDOException $e) {
    echo "Error durante el seed: " . $e->getMessage();
}
