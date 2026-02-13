<style>
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

.btn-save {
    margin-top: 20px;
    padding: 10px 18px;
    background: #3b82f6;
    border: none;
    border-radius: 6px;
    color: white;
    font-weight: bold;
    cursor: pointer;
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

.attachments-table {
    width: 100%;
    margin-top: 20px;
    border-collapse: collapse;
}

.attachments-table th,
.attachments-table td {
    padding: 10px;
    text-align: left;
}

.attachments-table th {
    background: #0f172a;
    color: #94a3b8;
}

.attachments-table tr {
    border-bottom: 1px solid #334155;
}

.attachments-table a {
    color: #3b82f6;
    text-decoration: none;
    font-weight: bold;
}

.attachments-table a:hover {
    color: #60a5fa;
}

.upload-box {
    margin-top: 25px;
    padding: 15px;
    background: #0f172a;
    border-radius: 8px;
}

.attachments-list {
    margin-top: 20px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.attachment-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #0f172a;
    padding: 15px 20px;
    border-radius: 10px;
    border: 1px solid #334155;
    transition: 0.2s ease;
}

.attachment-card:hover {
    background: #162036;
    border-color: #3b82f6;
}

.file-info {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.file-name {
    font-weight: bold;
    font-size: 15px;
    color: #f1f5f9;
}

.file-meta {
    font-size: 13px;
    color: #94a3b8;
}

.btn-download {
    padding: 8px 14px;
    background: #3b82f6;
    color: white;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    transition: 0.2s ease;
}

.btn-download:hover {
    background: #2563eb;
}

</style>

<div class="detail-container">

    <!-- DETALLE TICKET -->
    <h1>Detalle del Ticket</h1>

    <?php if ($_SESSION['role'] === 'lector'): ?>

        <div class="detail-grid">
            <label>Título</label>
            <div><?= htmlspecialchars($ticket['title']) ?></div>

            <label>Estado</label>
            <div><?= ucfirst($ticket['status']) ?></div>

            <label>Prioridad</label>
            <div><?= ucfirst($ticket['priority']) ?></div>

            <label>Asignado a</label>
            <div><?= htmlspecialchars($ticket['assigned_to'] ?? '-') ?></div>

            <label>Descripción</label>
            <div><?= nl2br(htmlspecialchars($ticket['description'])) ?></div>

            <label>Creado</label>
            <div><?= $ticket['created_at'] ?></div>

            <label>Última actualización</label>
            <div><?= $ticket['updated_at'] ?></div>
        </div>

        <a href="tickets_view.php" class="btn-back">← Volver</a>

    <?php else: ?>

        <form method="POST">
            <div class="detail-grid">
                <label>Título</label>
                <div><?= htmlspecialchars($ticket['title']) ?></div>

                <label>Estado</label>
                <select name="status">
                    <?php foreach (TICKET_STATUS as $s): ?>
                        <option value="<?= $s ?>" <?= $ticket['status'] === $s ? 'selected' : '' ?>>
                            <?= ucfirst($s) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Prioridad</label>
                <select name="priority">
                    <?php foreach (TICKET_PRIORITY as $p): ?>
                        <option value="<?= $p ?>" <?= $ticket['priority'] === $p ? 'selected' : '' ?>>
                            <?= ucfirst($p) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Asignado a</label>
                <input type="text" name="assigned_to"
                       value="<?= htmlspecialchars($ticket['assigned_to'] ?? '') ?>">

                <label>Descripción</label>
                <textarea name="description" rows="4"><?= htmlspecialchars($ticket['description']) ?></textarea>

                <label>Creado</label>
                <div><?= $ticket['created_at'] ?></div>

                <label>Última actualización</label>
                <div><?= $ticket['updated_at'] ?></div>
            </div>

            <button type="submit" class="btn-save">Guardar cambios</button>
            <a href="tickets_view.php" class="btn-back">← Volver</a>
        </form>

    <?php endif; ?>



    <!-- ADJUNTOS -->
    <h3>Adjuntos</h3>

<?php if (empty($attachments)): ?>
    <p>No hay archivos adjuntos.</p>
<?php else: ?>
    <table>
       <div class="attachments-list">

    <?php foreach ($attachments as $file): ?>
        <div class="attachment-card">

            <div class="file-info">
                <div class="file-name">
                    📎 <?= htmlspecialchars($file['filename']) ?>
                </div>

                <div class="file-meta">
                    <?= round($file['filesize'] / 1024, 2) ?> KB · 
                    <?= $file['created_at'] ?> · 
                    Subido por <?= htmlspecialchars($file['username'] ?? 'Desconocido') ?>
                </div>
            </div>

            <div class="file-actions">
                <a href="download_attachment.php?id=<?= $file['id'] ?>" class="btn-download">
                    Descargar
                </a>
            </div>

        </div>
    <?php endforeach; ?>

</div>
    </table>
<?php endif; ?>



    <!-- SUBIDA SOLO ADMIN / TECNICO -->
   <?php if ($_SESSION['role'] !== 'lector'): ?>

    <h4>Subir nuevo archivo</h4>

    <form action="upload_attachment.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">

        <div style="display:flex; gap:15px; align-items:center; flex-wrap:wrap;">

    <input type="file" name="attachment" required class="file-input">

    <button type="submit" class="btn-save">
        Subir archivo
    </button>

</div>

    </form>

    <?php endif; ?>

</div>
