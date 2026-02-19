<style>
/* ===============================
   CONTENEDOR PRINCIPAL
   =============================== */

.detail-container {
    max-width: 1000px;
    margin: 40px auto;
    background: #1e293b;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    color: #f1f5f9;
}

.detail-container h1 {
    margin-bottom: 25px;
    font-size: 24px;
}

/* ===============================
   GRID INFORMACIÓN TICKET
   =============================== */

.detail-grid {
    display: grid;
    grid-template-columns: 150px 1fr;
    gap: 15px 20px;
    margin-bottom: 25px;
}

.detail-grid label {
    font-weight: bold;
    color: #94a3b8;
}

.detail-grid div {
    background: #0f172a;
    padding: 8px 12px;
    border-radius: 6px;
}

/* ===============================
   INPUTS Y TEXTAREAS
   =============================== */

.detail-container input,
.detail-container select,
.detail-container textarea {
    width: 100%;
    padding: 8px;
    border-radius: 6px;
    border: none;
    background: #0f172a;
    color: #f1f5f9;
}

.detail-container textarea {
    resize: vertical;
}

/* ===============================
   BOTONES GENERALES
   =============================== */

.btn-save {
    margin-top: 20px;
    padding: 10px 18px;
    background: #3b82f6;
    border: none;
    border-radius: 6px;
    color: white;
    font-weight: bold;
    cursor: pointer;
    transition: 0.2s ease;
}

.btn-save:hover {
    background: #2563eb;
}

.btn-back {
    display: inline-block;
    margin-top: 20px;
    margin-left: 15px;
    color: #94a3b8;
    text-decoration: none;
}

/* ===============================
   SECCIÓN COMENTARIOS
   =============================== */

.comments-section {
    margin-top: 40px;
}

/* ===== Timeline ===== */

.timeline {
    position: relative;
    margin-top: 20px;
    padding-left: 30px;
}

.timeline::before {
    content: "";
    position: absolute;
    left: 10px;
    top: 0;
    width: 2px;
    height: 100%;
    background: #334155;
}

.timeline-item {
    position: relative;
    margin-bottom: 25px;
    padding-left: 20px;
}

.timeline-marker {
    position: absolute;
    left: -2px;
    top: 6px;
    width: 10px;
    height: 10px;
    background: #94a3b8;
    border-radius: 50%;
}

/* Último comentario destacado */

.timeline-latest .timeline-marker {
    background: #22c55e;
    width: 12px;
    height: 12px;
}

.timeline-badge {
    background: #22c55e;
    color: #0f172a;
    font-size: 11px;
    padding: 2px 6px;
    border-radius: 4px;
    margin-left: 10px;
    font-weight: 600;
}

/* Contenido comentario */

.comment-content {
    background: #0f172a;
    border: 1px solid #334155;
    padding: 15px;
    border-radius: 10px;
    transition: 0.2s ease;
}

.comment-content:hover {
    border-color: #3b82f6;
    background: #162036;
}

.comment-header {
    margin-bottom: 5px;
}

.comment-author {
    font-weight: bold;
    color: #f1f5f9;
}

.comment-date {
    font-size: 13px;
    color: #94a3b8;
    margin-left: 8px;
}

.comment-text {
    margin-top: 8px;
    line-height: 1.5;
}

/* Botón añadir comentario */

.btn-comment {
    margin-top: 15px;
    padding: 10px 18px;
    background: #10b981;
    border: none;
    border-radius: 6px;
    color: white;
    font-weight: bold;
    cursor: pointer;
    transition: 0.2s ease;
}

.btn-comment:hover {
    background: #059669;
    transform: translateY(-1px);
}

/* ==============================
   HISTORIAL DE ACTIVIDAD
================================= */

.card-history {
    margin-top: 30px;
    padding: 20px;
    background-color: #1e1e2f;
    border-radius: 10px;
}

.section-title {
    margin-bottom: 15px;
    font-size: 18px;
    font-weight: 600;
    color: #ffffff;
}

.empty-history {
    color: #999;
    font-style: italic;
}

.history-list {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.history-item {
    display: flex;
    gap: 15px;
    align-items: flex-start;
    border-left: 3px solid #3a82f7;
    padding-left: 15px;
}

.history-date {
    min-width: 140px;
    font-size: 12px;
    color: #aaa;
}

.history-content {
    flex: 1;
    color: #ddd;
}

.history-box {
    margin-top: 6px;
    padding: 10px;
    border-radius: 6px;
}

.comment-box {
    background-color: #2a2a3f;
}

.old-value {
    color: #ff6b6b;
}

.new-value {
    color: #4cd137;
}

/* Estilo para mensaje de error */
.error-message {
    background-color: #fee2e2;
    border: 1px solid #ef4444;
    color: #b91c1c;
    padding: 10px;
    border-radius: 6px;
    margin: 15px 0;
    font-weight: bold;
}
</style>

<div class="detail-container">

    <!-- DETALLE TICKET -->
    <h1>Detalle del Ticket</h1>

    <?php if ($_SESSION['role'] === 'lector'): ?>

        <!-- Vista solo lectura para lector -->
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
            <div><?= $ticket['created_at'] ?></div>

            <label>Última actualización</label>
            <div><?= $ticket['updated_at'] ?></div>
        </div>

        <a href="tickets_view.php" class="btn-back">← Volver</a>

    <?php else: ?>

        <!-- Formulario de edición para admin/técnico -->
        <form method="POST">
            <input type="hidden" name="edit_ticket" value="1">

            <div class="detail-grid">
                <label>Título</label>
                <div><?= htmlspecialchars($ticket['title']) ?></div>

                <label>Estado</label>
                <select name="status">
                    <!-- Opción para "sin estado" (valor vacío) -->
                    <option value="" <?= $ticket['status'] === '' || $ticket['status'] === null ? 'selected' : '' ?>>
                        — Sin estado —
                    </option>
                    <?php foreach (TICKET_STATUS as $s): ?>
                        <option value="<?= $s ?>" <?= $ticket['status'] === $s ? 'selected' : '' ?>>
                            <?= ucfirst($s) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Prioridad</label>
                <select name="priority">
                    <!-- Opción para "sin prioridad" (valor vacío) -->
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
                        <option value="<?= $tech['id'] ?>"
                            <?= $ticket['assigned_to'] == $tech['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tech['username']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Descripción</label>
                <textarea name="description" rows="4"><?= htmlspecialchars($ticket['description']) ?></textarea>

                <label>Creado</label>
                <div><?= $ticket['created_at'] ?></div>

                <label>Última actualización</label>
                <div><?= $ticket['updated_at'] ?></div>
            </div>

            <?php if (isset($error)): ?>
                <div class="error-message">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn-save">Guardar cambios</button>
            <a href="tickets_view.php" class="btn-back">← Volver</a>
        </form>

    <?php endif; ?>


    <!-- ===============================
        SECCIÓN COMENTARIOS
    ================================ -->
    <div class="comments-section">

        <h3>Comentarios</h3>
        <hr>

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
            <p><em>No hay comentarios todavía.</em></p>
        <?php endif; ?>

    </div>

    <!-- Formulario para añadir comentarios (solo Admin/Técnico) -->
    <?php if ($_SESSION['role'] !== 'lector'): ?>
        <form method="POST">
            <input type="hidden" name="add_comment" value="1">
            <textarea name="comment" rows="4" placeholder="Escribe un comentario..." required></textarea>
            <button type="submit" class="btn-comment">💬 Añadir comentario</button>
        </form>
    <?php else: ?>
        <p><em>No tienes permisos para añadir comentarios.</em></p>
    <?php endif; ?>


    <!-- ===============================
         ADJUNTOS
         =============================== -->
    <h3>Adjuntos</h3>
    <hr>

    <?php if (empty($attachments)): ?>
        <p>No hay archivos adjuntos.</p>
    <?php else: ?>
        <div class="attachments-list">
            <?php foreach ($attachments as $file): ?>
                <div class="attachment-card">
                    <div class="file-info">
                        <div class="file-name">📎 <?= htmlspecialchars($file['filename']) ?></div>
                        <div class="file-meta">
                            <?= round($file['filesize'] / 1024, 2) ?> KB · 
                            <?= $file['created_at'] ?> · 
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


    <!-- Subida de archivos (solo Admin/Técnico) -->
    <?php if ($_SESSION['role'] !== 'lector'): ?>
        <h4>Subir nuevo archivo</h4>
        <form action="upload_attachment.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
            <div style="display:flex; gap:15px; align-items:center; flex-wrap:wrap;">
                <input type="file" name="attachment" required class="file-input">
                <button type="submit" class="btn-save">Subir archivo</button>
            </div>
        </form>
    <?php endif; ?>


    <!-- ===============================
         HISTORIAL DE ACTIVIDAD
         =============================== -->
    <div class="card-history">
        <h3 class="section-title">Historial de actividad</h3>

        <?php if (empty($timeline)): ?>
            <p class="empty-history">No hay actividad aún.</p>
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
                                <div class="history-box comment-box">
                                    <?= nl2br(htmlspecialchars($item['data']['comment'])) ?>
                                </div>

                            <?php elseif ($item['type'] === 'change'): 
                                // Traducir assigned_to a nombre si es necesario
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
                                    de
                                    <span class="old-value"><?= htmlspecialchars($oldDisplay) ?></span>
                                    a
                                    <span class="new-value"><?= htmlspecialchars($newDisplay) ?></span>
                                </p>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>