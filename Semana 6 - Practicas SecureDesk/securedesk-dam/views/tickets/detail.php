<?php
/**
 * views/tickets/detail.php
 * =============================================================================
 * Vista de detalle de un ticket.
 * Muestra la información del ticket, comentarios, adjuntos e historial.
 * Incluye formularios de edición y comentario con protección CSRF.
 * 
 * @package SecureDesk
 * @subpackage Views/Tickets
 * @version 2.0 (con estilos modernos)
 * 
 * Variables esperadas desde el controlador (public/ticket_detail.php):
 *   - $ticket        : array con los datos del ticket
 *   - $attachments   : array de adjuntos
 *   - $comments      : array de comentarios
 *   - $timeline      : array unificado de actividad (comentarios + cambios)
 *   - $technicians   : array de técnicos para el selector de asignación
 *   - $userMap       : array [id => username] para traducir IDs en historial
 *   - $csrf_token    : token CSRF para los formularios
 *   - $error         : (opcional) mensaje de error a mostrar
 */
?>

<!-- =========================================================================
     ESTILOS CSS DE LA PÁGINA
     ========================================================================= -->
<style>
    /* ============================================
       RESETEO Y ESTILOS GLOBALES
       ============================================ */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    body {
        background-color: #0f172a; /* Fondo azul oscuro general */
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Contenedor principal que envuelve toda la vista */
    .detail-wrapper {
        max-width: 1200px;
        margin: 30px auto;
        padding: 0 20px;
    }

    /* ============================================
       TARJETA PRINCIPAL (efecto cristal)
       ============================================ */
    .detail-card {
        background: rgba(18, 30, 48, 0.9);
        backdrop-filter: blur(8px);
        border-radius: 32px;
        padding: 40px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(66, 153, 225, 0.2);
        color: #e2e8f0;
        margin-bottom: 30px;
    }

    /* Título de sección */
    .section-title {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 25px;
        background: linear-gradient(135deg, #90cdf4, #4299e1);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: -0.5px;
        border-bottom: 2px solid #2d4a6e;
        padding-bottom: 8px;
        display: inline-block;
    }

    /* ============================================
       GRID DE INFORMACIÓN DEL TICKET
       ============================================ */
    .detail-grid {
        display: grid;
        grid-template-columns: 150px 1fr;
        gap: 15px 20px;
        margin-bottom: 30px;
    }

    .detail-grid label {
        font-weight: 600;
        color: #90cdf4;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        align-self: center;
    }

    .detail-grid div {
        background: #1e2e44;
        border: 1px solid #2d4a6e;
        border-radius: 14px;
        padding: 12px 16px;
        color: #f0f4fa;
        font-size: 15px;
    }

    /* Estilo especial para campos editables dentro del grid (solo formulario) */
    .detail-grid select,
    .detail-grid textarea {
        background: #1e2e44;
        border: 1px solid #2d4a6e;
        border-radius: 14px;
        padding: 12px 16px;
        color: #f0f4fa;
        font-size: 15px;
        outline: none;
        transition: all 0.2s ease;
        width: 100%;
    }

    .detail-grid select:hover,
    .detail-grid textarea:hover {
        border-color: #4299e1;
        background-color: #253c5c;
    }

    .detail-grid select:focus,
    .detail-grid textarea:focus {
        border-color: #63b3ed;
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.3);
    }

    /* Personalización del selector (flecha) */
    .detail-grid select {
        appearance: none;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2390cdf4' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'/></svg>");
        background-repeat: no-repeat;
        background-position: right 18px center;
        background-size: 14px;
        cursor: pointer;
    }

    /* ============================================
       BOTONES
       ============================================ */
    .btn-primary {
        padding: 12px 24px;
        background: linear-gradient(145deg, #2563eb, #1e4b8f);
        color: white;
        border: none;
        border-radius: 40px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 8px 18px -6px #1e3a8a;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary:hover {
        background: linear-gradient(145deg, #3b82f6, #2563eb);
        transform: translateY(-2px);
        box-shadow: 0 14px 22px -8px #1e4b8f;
    }

    .btn-secondary {
        padding: 12px 24px;
        background: #2d3748;
        color: #cbd5e0;
        border: 1px solid #4a5568;
        border-radius: 40px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-secondary:hover {
        background: #4a5568;
        color: white;
        transform: translateY(-2px);
        border-color: #718096;
    }

    .btn-back {
        margin-left: 15px;
        color: #94a3b8;
        text-decoration: none;
        transition: color 0.2s;
    }

    .btn-back:hover {
        color: #63b3ed;
        text-decoration: underline;
    }

    /* ============================================
       MENSAJE DE ERROR
       ============================================ */
    .error-message {
        background-color: rgba(229, 62, 62, 0.15);
        border: 1px solid #e53e3e;
        color: #fc8181;
        padding: 12px 16px;
        border-radius: 16px;
        margin-bottom: 25px;
        font-size: 14px;
        text-align: center;
        backdrop-filter: blur(4px);
    }

    /* ============================================
       SECCIÓN DE COMENTARIOS (TIMELINE)
       ============================================ */
    .comments-section {
        margin-top: 40px;
    }

    .timeline {
        position: relative;
        margin-top: 25px;
        padding-left: 30px;
    }

    .timeline::before {
        content: "";
        position: absolute;
        left: 10px;
        top: 0;
        width: 2px;
        height: 100%;
        background: #2d4a6e;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 30px;
        padding-left: 20px;
    }

    .timeline-marker {
        position: absolute;
        left: -2px;
        top: 6px;
        width: 12px;
        height: 12px;
        background: #4299e1;
        border-radius: 50%;
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2);
    }

    .timeline-latest .timeline-marker {
        background: #22c55e;
        width: 14px;
        height: 14px;
        box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.2);
    }

    .timeline-badge {
        background: #22c55e;
        color: #0f172a;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 20px;
        margin-left: 10px;
        font-weight: 600;
    }

    .comment-content {
        background: #1e2e44;
        border: 1px solid #2d4a6e;
        padding: 18px;
        border-radius: 18px;
        transition: all 0.2s ease;
    }

    .comment-content:hover {
        border-color: #4299e1;
        background: #253c5c;
    }

    .comment-header {
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .comment-author {
        font-weight: 700;
        color: #90cdf4;
    }

    .comment-date {
        font-size: 12px;
        color: #94a3b8;
    }

    .comment-text {
        line-height: 1.6;
        color: #e0e7ff;
    }

    /* ============================================
       FORMULARIO DE NUEVO COMENTARIO
       ============================================ */
    .comment-form {
        margin-top: 30px;
    }

    .comment-form textarea {
        width: 100%;
        padding: 16px;
        background: #1e2e44;
        border: 1px solid #2d4a6e;
        border-radius: 18px;
        color: #f0f4fa;
        font-size: 15px;
        outline: none;
        transition: all 0.2s ease;
        resize: vertical;
        min-height: 100px;
        margin-bottom: 15px;
    }

    .comment-form textarea:hover {
        border-color: #4299e1;
        background-color: #253c5c;
    }

    .comment-form textarea:focus {
        border-color: #63b3ed;
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.3);
    }

    /* ============================================
       ADJUNTOS
       ============================================ */
    .attachments-list {
        margin: 20px 0;
    }

    .attachment-card {
        background: #1e2e44;
        border: 1px solid #2d4a6e;
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s ease;
    }

    .attachment-card:hover {
        border-color: #4299e1;
        background: #253c5c;
    }

    .file-info {
        flex: 1;
    }

    .file-name {
        font-weight: 600;
        color: #90cdf4;
        margin-bottom: 4px;
    }

    .file-meta {
        font-size: 12px;
        color: #94a3b8;
    }

    .btn-download {
        padding: 8px 16px;
        background: #2d3748;
        color: #cbd5e0;
        border: 1px solid #4a5568;
        border-radius: 40px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-download:hover {
        background: #4a5568;
        color: white;
        border-color: #718096;
    }

    /* Formulario de subida de archivos */
    .upload-form {
        display: flex;
        gap: 15px;
        align-items: center;
        flex-wrap: wrap;
        margin-top: 15px;
    }

    .file-input {
        background: #1e2e44;
        border: 1px solid #2d4a6e;
        border-radius: 40px;
        padding: 10px 18px;
        color: #e2e8f0;
        font-size: 14px;
        flex: 1;
        min-width: 200px;
    }

    /* ============================================
       HISTORIAL DE ACTIVIDAD
       ============================================ */
    .history-card {
        margin-top: 30px;
        padding: 25px;
        background: #1e2e44;
        border-radius: 24px;
        border: 1px solid #2d4a6e;
    }

    .history-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .history-item {
        display: flex;
        gap: 15px;
        align-items: flex-start;
        border-left: 3px solid #4299e1;
        padding-left: 18px;
    }

    .history-date {
        min-width: 140px;
        font-size: 12px;
        color: #94a3b8;
    }

    .history-content {
        flex: 1;
        color: #e0e7ff;
        line-height: 1.5;
    }

    .history-box {
        margin-top: 8px;
        padding: 12px;
        background: #132337;
        border-radius: 14px;
        border: 1px solid #2d4a6e;
    }

    .old-value {
        color: #fc8181;
        background: rgba(229, 62, 62, 0.1);
        padding: 2px 6px;
        border-radius: 6px;
    }

    .new-value {
        color: #4cd137;
        background: rgba(72, 187, 120, 0.1);
        padding: 2px 6px;
        border-radius: 6px;
    }

    .empty-history {
        color: #94a3b8;
        font-style: italic;
        text-align: center;
        padding: 20px;
    }

    /* ============================================
       RESPONSIVE
       ============================================ */
    @media (max-width: 768px) {
        .detail-card {
            padding: 25px;
        }
        .detail-grid {
            grid-template-columns: 1fr;
            gap: 10px;
        }
        .detail-grid label {
            margin-top: 10px;
        }
        .history-item {
            flex-direction: column;
            gap: 5px;
        }
        .history-date {
            min-width: auto;
        }
        .attachment-card {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
        .btn-download {
            align-self: flex-end;
        }
    }
</style>

<!-- =========================================================================
     CONTENIDO PRINCIPAL
     ========================================================================= -->
<div class="detail-wrapper">

    <!-- TARJETA PRINCIPAL DEL TICKET -->
    <div class="detail-card">

        <h1 class="section-title">📄 Detalle del Ticket #<?= htmlspecialchars($ticket['id']) ?></h1>

        <?php if (isset($error) && $error): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- =========================================
             VISTA SEGÚN EL ROL DEL USUARIO
             ========================================= -->
        <?php if ($_SESSION['role'] === 'lector'): ?>
            <!-- MODO SOLO LECTURA PARA LECTOR -->
            <div class="detail-grid">
                <label>Título</label>
                <div><?= htmlspecialchars($ticket['title']) ?></div>

                <label>Estado</label>
                <div><?= ucfirst($ticket['status'] ?: 'Sin estado') ?></div>

                <label>Prioridad</label>
                <div><?= ucfirst($ticket['priority'] ?: 'Sin prioridad') ?></div>

                <label>Asignado a</label>
                <div><?= htmlspecialchars($ticket['assigned_user'] ?? 'Sin asignar') ?></div>

                <label>Descripción</label>
                <div><?= nl2br(htmlspecialchars($ticket['description'])) ?></div>

                <label>Creado</label>
                <div><?= htmlspecialchars($ticket['created_at']) ?></div>

                <label>Última actualización</label>
                <div><?= htmlspecialchars($ticket['updated_at']) ?></div>
            </div>

            <a href="tickets_view.php" class="btn-back">← Volver al listado</a>

        <?php else: ?>
            <!-- MODO EDICIÓN PARA ADMIN/TÉCNICO -->
            <form method="POST" action="">
                <!-- Token CSRF (generado en el controlador) -->
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <input type="hidden" name="edit_ticket" value="1">

                <div class="detail-grid">
                    <label>Título</label>
                    <div><?= htmlspecialchars($ticket['title']) ?></div>

                    <label>Estado *</label>
                    <select name="status" required>
                        <option value="" <?= $ticket['status'] === '' || $ticket['status'] === null ? 'selected' : '' ?>>
                            — Sin estado —
                        </option>
                        <?php foreach (TICKET_STATUS as $s): ?>
                            <option value="<?= $s ?>" <?= $ticket['status'] === $s ? 'selected' : '' ?>>
                                <?= ucfirst($s) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label>Prioridad *</label>
                    <select name="priority" required>
                        <option value="" <?= $ticket['priority'] === '' || $ticket['priority'] === null ? 'selected' : '' ?>>
                            — Sin prioridad —
                        </option>
                        <?php foreach (TICKET_PRIORITY as $p): ?>
                            <option value="<?= $p ?>" <?= $ticket['priority'] === $p ? 'selected' : '' ?>>
                                <?= ucfirst($p) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label>Asignado a</label>
                    <select name="assigned_to">
                        <option value="">— Sin asignar —</option>
                        <?php foreach ($technicians as $tech): ?>
                            <option value="<?= $tech['id'] ?>" <?= $ticket['assigned_to'] == $tech['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tech['username']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label>Descripción *</label>
                    <textarea name="description" rows="4" required><?= htmlspecialchars($ticket['description']) ?></textarea>

                    <label>Creado</label>
                    <div><?= htmlspecialchars($ticket['created_at']) ?></div>

                    <label>Última actualización</label>
                    <div><?= htmlspecialchars($ticket['updated_at']) ?></div>
                </div>

                <div style="display: flex; gap: 15px; align-items: center; margin-top: 20px;">
                    <button type="submit" class="btn-primary">💾 Guardar cambios</button>
                    <a href="tickets_view.php" class="btn-back">← Volver al listado</a>
                </div>
            </form>
        <?php endif; ?>

    </div> <!-- /.detail-card -->

    <!-- =============================================
         SECCIÓN DE COMENTARIOS (TIMELINE)
         ============================================= -->
    <div class="detail-card">
        <h2 class="section-title">💬 Comentarios</h2>

        <?php if (!empty($comments)): ?>
            <div class="timeline">
                <?php
                $totalComments = count($comments);
                foreach ($comments as $index => $c):
                    $isLast = ($index === $totalComments - 1);
                    $username = $c['username'] ?? 'Usuario';
                    $date = new DateTime($c['created_at']);
                    $now = new DateTime();
                    $diff = $now->diff($date);

                    if ($diff->d > 0) {
                        $timeAgo = "hace " . $diff->d . " día(s)";
                    } elseif ($diff->h > 0) {
                        $timeAgo = "hace " . $diff->h . " hora(s)";
                    } elseif ($diff->i > 0) {
                        $timeAgo = "hace " . $diff->i . " minuto(s)";
                    } else {
                        $timeAgo = "justo ahora";
                    }

                    $exactDate = $date->format('d/m/Y H:i');
                ?>
                    <div class="timeline-item <?= $isLast ? 'timeline-latest' : '' ?>">
                        <div class="timeline-marker"></div>
                        <div class="comment-content">
                            <div class="comment-header">
                                <span class="comment-author"><?= htmlspecialchars($username) ?></span>
                                <span class="comment-date"><?= $timeAgo ?> · <?= $exactDate ?></span>
                                <?php if ($isLast): ?>
                                    <span class="timeline-badge">Última actualización</span>
                                <?php endif; ?>
                            </div>
                            <div class="comment-text">
                                <?= nl2br(htmlspecialchars($c['comment'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color: #94a3b8;">📭 No hay comentarios todavía.</p>
        <?php endif; ?>

        <!-- Formulario para añadir comentarios (solo admin/técnico) -->
        <?php if ($_SESSION['role'] !== 'lector'): ?>
            <form method="POST" action="" class="comment-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <input type="hidden" name="add_comment" value="1">
                <textarea name="comment" placeholder="Escribe un comentario..." required></textarea>
                <button type="submit" class="btn-primary">💬 Añadir comentario</button>
            </form>
        <?php endif; ?>
    </div> <!-- /.detail-card -->

    <!-- =============================================
         SECCIÓN DE ADJUNTOS
         ============================================= -->
    <div class="detail-card">
        <h2 class="section-title">📎 Adjuntos</h2>

        <?php if (empty($attachments)): ?>
            <p style="color: #94a3b8;">No hay archivos adjuntos.</p>
        <?php else: ?>
            <div class="attachments-list">
                <?php foreach ($attachments as $file): ?>
                    <div class="attachment-card">
                        <div class="file-info">
                            <div class="file-name">📄 <?= htmlspecialchars($file['filename']) ?></div>
                            <div class="file-meta">
                                <?= round($file['filesize'] / 1024, 2) ?> KB ·
                                <?= htmlspecialchars($file['created_at']) ?> ·
                                Subido por <?= htmlspecialchars($file['username'] ?? 'Desconocido') ?>
                            </div>
                        </div>
                        <div class="file-actions">
                            <a href="download_attachment.php?id=<?= $file['id'] ?>" class="btn-download">Descargar</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Formulario de subida (solo admin/técnico) -->
        <?php if ($_SESSION['role'] !== 'lector'): ?>
            <h3 style="margin: 25px 0 15px; color: #90cdf4;">📤 Subir nuevo archivo</h3>
            <form action="upload_attachment.php" method="POST" enctype="multipart/form-data" class="upload-form">
                <!-- Campo CSRF añadido -->
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
                <input type="file" name="attachment" required class="file-input">
                <button type="submit" class="btn-primary">Subir archivo</button>
            </form>
        <?php endif; ?>
    </div> <!-- /.detail-card -->

    <!-- =============================================
         HISTORIAL DE ACTIVIDAD (TIMELINE COMPLETO)
         ============================================= -->
    <div class="detail-card">
        <h2 class="section-title">📜 Historial de actividad</h2>

        <?php if (empty($timeline)): ?>
            <p class="empty-history">No hay actividad registrada aún.</p>
        <?php else: ?>
            <div class="history-list">
                <?php foreach ($timeline as $item): ?>
                    <div class="history-item">
                        <div class="history-date"><?= htmlspecialchars($item['created_at']) ?></div>
                        <div class="history-content">
                            <?php if ($item['type'] === 'comment'): ?>
                                <p>
                                    <strong><?= htmlspecialchars($item['data']['username']) ?></strong> comentó:
                                </p>
                                <div class="history-box">
                                    <?= nl2br(htmlspecialchars($item['data']['comment'])) ?>
                                </div>
                            <?php elseif ($item['type'] === 'change'):
                                $campo = $item['data']['campo_modificado'];
                                $oldRaw = $item['data']['valor_anterior'];
                                $newRaw = $item['data']['valor_nuevo'];

                                if ($campo === 'assigned_to' && isset($userMap)) {
                                    $oldDisplay = (is_numeric($oldRaw) && isset($userMap[$oldRaw])) ? $userMap[$oldRaw] : ($oldRaw ?: '—');
                                    $newDisplay = (is_numeric($newRaw) && isset($userMap[$newRaw])) ? $userMap[$newRaw] : ($newRaw ?: '—');
                                } else {
                                    $oldDisplay = $oldRaw ?: '—';
                                    $newDisplay = $newRaw ?: '—';
                                }
                            ?>
                                <p>
                                    <strong><?= htmlspecialchars($item['data']['username']) ?></strong>
                                    cambió
                                    <strong><?= htmlspecialchars($campo) ?></strong>
                                    de <span class="old-value"><?= htmlspecialchars($oldDisplay) ?></span>
                                    a <span class="new-value"><?= htmlspecialchars($newDisplay) ?></span>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div> <!-- /.detail-card -->

</div> <!-- /.detail-wrapper -->