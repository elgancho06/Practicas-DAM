<?php
// app/modules/audit/audit.php
require_once __DIR__ . '/../../core/config.php';

function registrar_auditoria($db, $userId, $action, $entity, $entityId, $details) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    $stmt = $db->prepare("
        INSERT INTO audit_logs (user_id, action, entity, entity_id, details, ip_address, user_agent)
        VALUES (:user_id, :action, :entity, :entity_id, :details, :ip, :ua)
    ");
    $stmt->execute([
        ':user_id' => $userId,
        ':action' => $action,
        ':entity' => $entity,
        ':entity_id' => $entityId,
        ':details' => $details,
        ':ip' => $ip,
        ':ua' => $userAgent
    ]);
}