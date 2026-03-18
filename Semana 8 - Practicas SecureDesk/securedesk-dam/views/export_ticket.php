<?php
/**
 * views/export_ticket.php - Informe HTML de un ticket.
 * Variables esperadas: $ticket, $comments, $historial, $attachments, $usuario_informe.
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe del Ticket #<?= htmlspecialchars($ticket['id']) ?> - SecureDesk</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 30px;
            line-height: 1.6;
            color: #333;
            background: #f5f5f5;
        }
        .informe {
            max-width: 1100px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .header {
            border-bottom: 2px solid #0056b3;
            padding-bottom: 15px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            color: #0056b3;
            font-size: 28px;
        }
        .header .meta {
            text-align: right;
            color: #666;
            font-size: 14px;
        }
        .ticket-info {
            background: #f0f7ff;
            border-left: 4px solid #0056b3;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 4px;
        }
        .ticket-info p {
            margin: 5px 0;
        }
        .label {
            font-weight: bold;
            display: inline-block;
            width: 140px;
            color: #333;
        }
        h2 {
            color: #0056b3;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            margin-top: 30px;
        }
        .comment, .change {
            background: #f9f9f9;
            border-left: 3px solid #0056b3;
            padding: 10px 15px;
            margin-bottom: 15px;
            border-radius: 0 4px 4px 0;
        }
        .comment-meta, .change-meta {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 5px;
        }
        .attachments ul {
            list-style: none;
            padding: 0;
        }
        .attachments li {
            background: #f9f9f9;
            padding: 8px 12px;
            margin-bottom: 5px;
            border-radius: 4px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        @media print {
            body { background: white; }
            .informe { box-shadow: none; padding: 0; }
            .header { border-bottom-color: #000; }
            .header h1 { color: #000; }
            h2 { color: #000; }
        }
    </style>
</head>
<body>
    <div class="informe">
        <div class="header">
            <h1>SecureDesk DAM</h1>
            <div class="meta">
                <p><strong>Informe generado:</strong> <?= date('d/m/Y H:i:s') ?></p>
                <p><strong>Generado por:</strong> <?= htmlspecialchars($usuario_informe) ?></p>
            </div>
        </div>

        <h2>Detalle del Ticket #<?= htmlspecialchars($ticket['id']) ?></h2>

        <div class="ticket-info">
            <p><span class="label">Título:</span> <?= htmlspecialchars($ticket['title']) ?></p>
            <p><span class="label">Estado:</span> <?= ucfirst(htmlspecialchars($ticket['status'] ?: 'Sin estado')) ?></p>
            <p><span class="label">Prioridad:</span> <?= ucfirst(htmlspecialchars($ticket['priority'] ?: 'Sin prioridad')) ?></p>
            <p><span class="label">Asignado a:</span> <?= htmlspecialchars($ticket['assigned_user'] ?? 'Sin asignar') ?></p>
            <p><span class="label">Creado:</span> <?= htmlspecialchars($ticket['created_at']) ?></p>
            <p><span class="label">Última actualización:</span> <?= htmlspecialchars($ticket['updated_at'] ?? '') ?></p>
            <p><span class="label">Descripción:</span><br><?= nl2br(htmlspecialchars($ticket['description'])) ?></p>
        </div>

        <h2>Comentarios</h2>
        <?php if (empty($comments)): ?>
            <p>No hay comentarios.</p>
        <?php else: ?>
            <?php foreach ($comments as $c): ?>
                <div class="comment">
                    <div class="comment-meta">
                        <strong><?= htmlspecialchars($c['username'] ?? 'Usuario') ?></strong> 
                        - <?= htmlspecialchars($c['created_at']) ?>
                    </div>
                    <p><?= nl2br(htmlspecialchars($c['comment'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <h2>Historial de cambios</h2>
        <?php if (empty($historial)): ?>
            <p>No hay cambios registrados.</p>
        <?php else: ?>
            <?php foreach ($historial as $h): ?>
                <div class="change">
                    <div class="change-meta">
                        <strong><?= htmlspecialchars($h['username'] ?? 'Usuario') ?></strong> 
                        - <?= htmlspecialchars($h['fecha_cambio']) ?>
                    </div>
                    <p>Cambió <strong><?= htmlspecialchars($h['campo_modificado']) ?></strong> 
                       de <span style="color:red;"><?= htmlspecialchars($h['valor_anterior'] ?? '—') ?></span> 
                       a <span style="color:green;"><?= htmlspecialchars($h['valor_nuevo'] ?? '—') ?></span></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <h2>Adjuntos</h2>
        <?php if (empty($attachments)): ?>
            <p>No hay archivos adjuntos.</p>
        <?php else: ?>
            <div class="attachments">
                <ul>
                    <?php foreach ($attachments as $a): ?>
                        <li>
                            📎 <?= htmlspecialchars($a['filename']) ?> 
                            (<?= round($a['filesize'] / 1024, 2) ?> KB) - 
                            Subido por <?= htmlspecialchars($a['username'] ?? 'Desconocido') ?> 
                            el <?= htmlspecialchars($a['created_at']) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="footer">
            Informe generado por SecureDesk DAM - <?= date('Y') ?>
        </div>
    </div>
</body>
</html>