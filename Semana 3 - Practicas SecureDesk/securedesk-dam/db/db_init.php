<?php

require_once __DIR__ . '/../app/config.php';

try {

    $db->exec("PRAGMA foreign_keys = ON");

    // ===================================================
    // TABLA USERS
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
    // TABLA TICKETS
    // ===================================================
    $db->exec("
        CREATE TABLE IF NOT EXISTS tickets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT NOT NULL,
            status TEXT NOT NULL,
            priority TEXT NOT NULL,
            created_by INTEGER NOT NULL,
            created_at TEXT NOT NULL DEFAULT (datetime('now')),
            category TEXT NOT NULL,
            updated_at TEXT NOT NULL DEFAULT (datetime('now')),
            assigned_to TEXT,
            FOREIGN KEY (created_by) REFERENCES users(id)
        )
    ");

    // ===================================================
    // TABLA ATTACHMENTS (ADJUNTOS)
    // ===================================================
    $db->exec("
        CREATE TABLE IF NOT EXISTS attachments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ticket_id INTEGER NOT NULL,
            filename TEXT NOT NULL,
            filepath TEXT NOT NULL,
            filesize INTEGER,
            uploaded_by INTEGER,
            created_at TEXT NOT NULL DEFAULT (datetime('now')),
            FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
            FOREIGN KEY (uploaded_by) REFERENCES users(id)
        )
    ");

    // ===================================================
    // CREAR USUARIO ADMIN POR DEFECTO
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
