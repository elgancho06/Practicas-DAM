<?php
// config.php

try {
    $db = new PDO('sqlite:' . __DIR__ . '/../db/securedesk.sqlite');
    // Configuramos para que devuelva errores como excepciones
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Conexión a SQLite exitosa ✅";
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>