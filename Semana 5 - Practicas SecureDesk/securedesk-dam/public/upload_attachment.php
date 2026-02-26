<?php
session_start();
require_once __DIR__ . '/../app/config.php';

if (!isset($_SESSION['user_id'])) {
    die("No autorizado.");
}

if ($_SESSION['role'] === 'lector') {
    die("No tienes permiso para subir archivos.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $ticket_id = $_POST['ticket_id'];

    if (!isset($_FILES['attachment']) || $_FILES['attachment']['error'] !== 0) {
        die("Error al subir archivo.");
    }

    $file = $_FILES['attachment'];

    $originalName = basename($file['name']);
    $fileSize = $file['size'];

    // Generar nombre interno aleatorio
    $newName = uniqid() . "_" . $originalName;

    $uploadDir = __DIR__ . '/../uploads/';
    $destination = $uploadDir . $newName;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        die("Error al guardar archivo.");
    }

    // Guardar en BD
    $stmt = $db->prepare("
        INSERT INTO attachments (ticket_id, filename, filepath, filesize, uploaded_by)
        VALUES (:ticket_id, :filename, :filepath, :filesize, :uploaded_by)
    ");

    $stmt->execute([
        ':ticket_id' => $ticket_id,
        ':filename' => $originalName,
        ':filepath' => $newName,
        ':filesize' => $fileSize,
        ':uploaded_by' => $_SESSION['user_id']
    ]);

    header("Location: ticket_detail.php?id=" . $ticket_id);
    exit;
}
