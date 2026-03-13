<?php

require_once __DIR__ . '/../app/config.php';

try {

    // Activar claves foráneas en SQLite (necesario para que funcionen las restricciones)
    $db->exec("PRAGMA foreign_keys = ON");

    // ===================================================
    // TABLA USERS: almacena los usuarios del sistema
    // ===================================================
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            role TEXT NOT NULL CHECK(role IN ('admin','tecnico','lector')),
            created_at TEXT NOT NULL DEFAULT (datetime('now'))
        )
    ");

    // ===================================================
    // TABLA TICKETS: almacena las incidencias
    // ===================================================
    $db->exec("
        CREATE TABLE IF NOT EXISTS tickets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT NOT NULL,
            status TEXT NOT NULL,
            priority TEXT NOT NULL,
            category TEXT NOT NULL,
            created_by INTEGER NOT NULL,
            assigned_to INTEGER NULL,
            created_at TEXT NOT NULL DEFAULT (datetime('now')),
            updated_at TEXT NOT NULL DEFAULT (datetime('now')),

            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
        )
    ");

    // ===================================================
    // TABLA ATTACHMENTS: archivos adjuntos a los tickets
    // ===================================================
    $db->exec("
       CREATE TABLE IF NOT EXISTS attachments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ticket_id INTEGER NOT NULL,
            filename TEXT NOT NULL,
            filepath TEXT NOT NULL,
            mime_type TEXT NOT NULL,      
            filesize INTEGER,
            uploaded_by INTEGER,
            created_at TEXT NOT NULL DEFAULT (datetime('now')),
            FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
            FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
        );
    ");

    // ===================================================
    // TABLA TICKET_COMMENTS: comentarios en los tickets
    // ===================================================
    $db->exec("
        CREATE TABLE IF NOT EXISTS ticket_comments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ticket_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            comment TEXT NOT NULL,
            created_at TEXT NOT NULL DEFAULT (datetime('now')),

            FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");

    // ===================================================
    // TABLA HISTORIAL_CAMBIOS: registra cambios en campos críticos del ticket
    // ===================================================
    $db->exec("
        CREATE TABLE IF NOT EXISTS historial_cambios (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ticket_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            campo_modificado TEXT NOT NULL,
            valor_anterior TEXT,
            valor_nuevo TEXT,
            fecha_cambio TEXT NOT NULL DEFAULT (datetime('now')),

            FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");

    // ===================================================
    // TABLA AUDIT_LOGS: auditoría de acciones importantes
    // ===================================================
    $db->exec("
        CREATE TABLE IF NOT EXISTS audit_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            action VARCHAR(50) NOT NULL,
            entity VARCHAR(50) NOT NULL,
            entity_id INTEGER,
            details TEXT NOT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // ===================================================
    // TABLA LOGIN_ATTEMPTS: control de intentos fallidos de login (anti fuerza bruta)
    // ===================================================
    $db->exec("
        CREATE TABLE IF NOT EXISTS login_attempts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ip_address VARCHAR(45) NOT NULL,
            username VARCHAR(100),
            attempted_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    // Índice para acelerar consultas por IP y fecha (opcional pero recomendado)
    $db->exec("CREATE INDEX IF NOT EXISTS idx_login_attempts_ip ON login_attempts(ip_address)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_login_attempts_attempted_at ON login_attempts(attempted_at)");

    // ===================================================
    // CREAR USUARIO ADMIN POR DEFECTO (si no existe)
    // ===================================================
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
    $stmt->execute();
    $exists = $stmt->fetchColumn();

    if (!$exists) {
        $password = password_hash('admin123', PASSWORD_DEFAULT);

        $insert = $db->prepare("
            INSERT INTO users (username, password_hash, role)
            VALUES ('admin', :password, 'admin')
        ");

        $insert->execute([':password' => $password]);
    }

    echo "Base de datos creada correctamente ✅";

} catch (PDOException $e) {
    die("Error al inicializar la base de datos: " . $e->getMessage());
}