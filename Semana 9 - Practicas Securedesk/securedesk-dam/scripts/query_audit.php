<?php
$db = new PDO('sqlite:db/securedesk.sqlite');
$query = "SELECT id, user_id, action, entity, entity_id, details, ip_address, created_at 
          FROM audit_logs 
          ORDER BY created_at DESC 
          LIMIT 30";
$stmt = $db->query($query);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

printf("| %-2s | %-7s | %-19s | %-10s | %-9s | %-40s | %-15s | %-19s |\n", 
       "ID", "User ID", "Action", "Entity", "Entity ID", "Details", "IP Address", "Created At");
printf("|----|---------|---------------------|------------|-----------|------------------------------------------|-----------------|---------------------|\n");

foreach ($rows as $row) {
    printf("| %-2d | %-7s | %-19s | %-10s | %-9s | %-40s | %-15s | %-19s |\n", 
           $row['id'], $row['user_id'], $row['action'], $row['entity'], $row['entity_id'], 
           substr($row['details'], 0, 40), $row['ip_address'], $row['created_at']);
}
