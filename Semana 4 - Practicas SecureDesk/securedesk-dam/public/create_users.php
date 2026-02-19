<?php

require_once __DIR__ . '/../app/config.php';

try {

    // Activar claves foráneas (por si acaso)
    $db->exec("PRAGMA foreign_keys = ON");

    // Usuarios a crear
    $users = [
        ['username' => 'admin2',   'password' => 'admin123',   'role' => 'admin'],
        ['username' => 'tecnico1', 'password' => 'tecnico123', 'role' => 'tecnico'],
        ['username' => 'lector1',  'password' => 'lector123',  'role' => 'lector'],
    ];

    foreach ($users as $user) {

        // Comprobar si ya existe
        $check = $db->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $check->execute([':username' => $user['username']]);

        if ($check->fetchColumn() > 0) {
            echo "⚠ Usuario {$user['username']} ya existe.<br>";
            continue;
        }

        $passwordHash = password_hash($user['password'], PASSWORD_DEFAULT);

        $insert = $db->prepare("
            INSERT INTO users (username, password_hash, role)
            VALUES (:username, :password, :role)
        ");

        $insert->execute([
            ':username' => $user['username'],
            ':password' => $passwordHash,
            ':role'     => $user['role']
        ]);

        echo "✅ Usuario {$user['username']} creado correctamente.<br>";
    }

    echo "<br>🎯 Script ejecutado correctamente.";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
