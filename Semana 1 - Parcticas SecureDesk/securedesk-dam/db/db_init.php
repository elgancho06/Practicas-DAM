<?php

// Incluimos la configuración de la base de datos
// Esto nos da acceso a $db, la conexión SQLite lista para usar
require_once __DIR__ . '/../app/config.php';

try {
    // Crear tabla "users" si no existe
    // Esta tabla almacena los usuarios del sistema
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            role TEXT NOT NULL,
            created_at TEXT NOT NULL
        )
    ");
    // id: identificador único y automático
    // username: nombre de usuario, obligatorio y único
    // password_hash: contraseña encriptada
    // role: rol del usuario (admin, técnico, etc.)
    // created_at: fecha de creación del usuario

    // ================================

    // Crear tabla "tickets" si no existe
    // Esta tabla almacena los tickets o incidencias
    $db->exec("
        CREATE TABLE IF NOT EXISTS tickets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT NOT NULL,
            status TEXT NOT NULL,
            priority TEXT NOT NULL,
            created_by INTEGER NOT NULL,
            created_at TEXT NOT NULL
        )
    ");
    // id: identificador único del ticket
    // title: título del ticket
    // description: descripción detallada del problema
    // status: estado actual del ticket (abierto, en proceso, cerrado)
    // priority: prioridad del ticket (baja, media, alta)
    // created_by: ID del usuario que creó el ticket
    // created_at: fecha de creación del ticket

    // Mensaje de confirmación si todo va bien
    echo 'Tablas creadas correctamente';

} catch (PDOException $e) {
    // Captura cualquier error en la creación de las tablas
    // $e->getMessage() devuelve la descripción del error
    echo 'Error al crear tablas: ' . $e->getMessage();
}
?>