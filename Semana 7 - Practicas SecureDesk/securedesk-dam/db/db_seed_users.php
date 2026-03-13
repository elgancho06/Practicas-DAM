<?php

// Cargamos la configuración de la base de datos
require_once __DIR__ . '/../app/config.php';

// (Opcional pero recomendable) Cargamos los roles definidos
require_once __DIR__ . '/../app/roles.php';

try {
    // Definimos los usuarios iniciales del sistema
    $users = [
        [
            'username' => 'admin',
            'password' => 'admin123',
            'role' => 'admin'
        ],
        [
            'username' => 'tecnico',
            'password' => 'tecnico123',
            'role' => 'tecnico'
        ],
        [
            'username' => 'lector',
            'password' => 'lector123',
            'role' => 'lector'
        ]
    ];

    // Preparamos la consulta SQL para insertar usuarios
    $stmt = $db->prepare("
        INSERT INTO users (username, password_hash, role, created_at)
        VALUES (:username, :password_hash, :role, :created_at)
    ");

    // Insertamos cada usuario en la base de datos
    foreach ($users as $user) {
        $stmt->execute([
            ':username' => $user['username'],
            ':password_hash' => password_hash($user['password'], PASSWORD_DEFAULT),
            ':role' => $user['role'],
            ':created_at' => date('Y-m-d H:i:s')
        ]);
    }

    echo "Usuarios iniciales creados correctamente ✅";

} catch (PDOException $e) {
    echo "Error al crear usuarios: " . $e->getMessage();
}
