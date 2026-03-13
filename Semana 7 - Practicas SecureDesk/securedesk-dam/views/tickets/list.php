<?php
// Vista del listado de tickets
// Aquí SOLO mostramos información (no lógica de BD)
?>

<style>
    /* ============================================
       ESTILOS MODERNOS PARA LISTADO DE TICKETS
       ============================================ */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    body {
        background-color: #0f172a;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .container {
        max-width: 1200px;
        margin: 30px auto;
        padding: 30px;
        background: rgba(18, 30, 48, 0.9);
        backdrop-filter: blur(8px);
        border-radius: 24px;
        box-shadow: 0 20px 40px -10px rgba(0, 20, 40, 0.7);
        border: 1px solid rgba(66, 153, 225, 0.2);
        color: #e2e8f0;
    }

    /* Cabecera: título + botones */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 25px;
    }

    .header h1 {
        font-size: 28px;
        font-weight: 600;
        background: linear-gradient(135deg, #90cdf4 0%, #4299e1 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: -0.5px;
        border-bottom: 2px solid #2d4a6e;
        padding-bottom: 8px;
        margin: 0;
    }

    .header-actions {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }

    /* Botón primario (crear ticket) */
    .btn-create {
        padding: 12px 24px;
        background: linear-gradient(145deg, #2563eb, #1e4b8f);
        color: white;
        text-decoration: none;
        border-radius: 40px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        box-shadow: 0 8px 18px -6px #1e3a8a;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-create:hover {
        background: linear-gradient(145deg, #3b82f6, #2563eb);
        transform: translateY(-2px);
        box-shadow: 0 14px 22px -8px #1e4b8f;
    }

    /* Botón secundario (exportar CSV) */
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

    /* Filtros (sin cambios) */
    .filters {
        display: flex;
        gap: 18px;
        align-items: flex-end;
        margin-bottom: 35px;
        flex-wrap: wrap;
        background: rgba(10, 25, 41, 0.7);
        padding: 20px;
        border-radius: 18px;
        border: 1px solid #253c5c;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        flex: 1 1 200px;
    }

    .filter-group label {
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
        color: #90cdf4;
    }

    .filter-group select {
        background: #1e2e44;
        border: 1px solid #2d4a6e;
        border-radius: 14px;
        padding: 12px 16px;
        color: #f0f4fa;
        font-size: 14px;
        outline: none;
        transition: all 0.2s ease;
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2390cdf4' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'/></svg>");
        background-repeat: no-repeat;
        background-position: right 15px center;
        background-size: 14px;
    }

    .filter-group select:hover {
        border-color: #4299e1;
        background-color: #253c5c;
    }

    .filter-group select:focus {
        border-color: #63b3ed;
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.3);
    }

    .filters button {
        padding: 12px 28px;
        background: linear-gradient(145deg, #2563eb, #1e4b8f);
        color: white;
        border: none;
        border-radius: 40px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 8px 18px -6px #1e3a8a;
    }

    .filters button:hover {
        background: linear-gradient(145deg, #3b82f6, #2563eb);
        transform: translateY(-2px);
        box-shadow: 0 14px 22px -8px #1e4b8f;
    }

    /* Tabla (sin cambios) */
    .tickets-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
        margin-top: 20px;
    }

    .tickets-table thead tr {
        background: linear-gradient(90deg, #0e1e2f, #162b3a);
    }

    .tickets-table th {
        padding: 16px 18px;
        text-align: left;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #a0c8f0;
        border-bottom: 2px solid #2d4a6e;
        white-space: nowrap;
    }

    .tickets-table td {
        padding: 16px 18px;
        background: #132337;
        border-radius: 14px;
        font-size: 14px;
        color: #e0e7ff;
        box-shadow: 0 4px 12px rgba(0, 10, 20, 0.4);
        transition: all 0.25s ease;
        border: 1px solid transparent;
    }

    .tickets-table tbody tr:hover td {
        background: #1b3147;
        border-color: #3b6c9e;
        transform: scale(1.01);
        box-shadow: 0 12px 24px -8px #0b1a2a;
    }

    .tickets-table td a {
        color: #90cdf4;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
    }

    .tickets-table td a:hover {
        color: #63b3ed;
        text-decoration: underline;
    }

    .badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 40px;
        font-size: 12px;
        font-weight: 600;
        text-align: center;
        min-width: 80px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        text-transform: capitalize;
    }

    .status-nuevo {
        background: linear-gradient(145deg, #1e4b8f, #2563eb);
        color: white;
    }

    .status-en_proceso {
        background: linear-gradient(145deg, #b45309, #d97706);
        color: white;
    }

    .status-resuelto {
        background: linear-gradient(145deg, #065f46, #059669);
        color: white;
    }

    .status-cerrado {
        background: linear-gradient(145deg, #6b7280, #4b5563);
        color: white;
    }

    .priority-baja {
        background: linear-gradient(145deg, #065f46, #059669);
        color: white;
    }

    .priority-media {
        background: linear-gradient(145deg, #b45309, #d97706);
        color: white;
    }

    .priority-alta {
        background: linear-gradient(145deg, #b91c1c, #dc2626);
        color: white;
    }

    .priority-critica {
        background: linear-gradient(145deg, #7b1e7b, #a855f7);
        color: white;
    }

    .assigned {
        font-weight: 500;
        color: #cbd5e0;
    }

    .empty {
        text-align: center;
        padding: 50px 20px;
        color: #a0aec0;
        font-size: 16px;
        background: #132337;
        border-radius: 30px;
        margin-top: 20px;
        border: 1px dashed #2d4a6e;
    }

    @media (max-width: 768px) {
        .container {
            padding: 15px;
            margin: 15px;
        }
        .tickets-table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }
        .header {
            flex-direction: column;
            align-items: flex-start;
        }
        .header-actions {
            width: 100%;
            justify-content: flex-start;
        }
    }
</style>

<div class="container">

    <!-- Cabecera del listado -->
    <div class="header">
        <h1>📋 Listado de tickets</h1>

        <div class="header-actions">
            <?php
            // Botón "Nuevo ticket" solo a Admin y Técnico
            if ($_SESSION['role'] !== 'lector'): ?>
                <a href="ticket_create.php" class="btn-create">
                    + Nuevo ticket
                </a>
            <?php endif; ?>

            <!-- Botón de exportar CSV (visible para todos los autenticados) -->
            <a href="export_tickets_csv.php?status=<?= urlencode($status ?? '') ?>&priority=<?= urlencode($priority ?? '') ?>" class="btn-secondary">
                📥 Exportar CSV
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <form method="GET" class="filters">
        <div class="filter-group">
            <label for="status">📌 Estado</label>
            <select name="status" id="status">
                <option value="">Todos los estados</option>
                <?php foreach (TICKET_STATUS as $s): ?>
                    <option value="<?= $s ?>" <?= ($status === $s) ? 'selected' : '' ?>>
                        <?= ucfirst(str_replace('_', ' ', $s)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group">
            <label for="priority">⚡ Prioridad</label>
            <select name="priority" id="priority">
                <option value="">Todas las prioridades</option>
                <?php foreach (TICKET_PRIORITY as $p): ?>
                    <option value="<?= $p ?>" <?= ($priority === $p) ? 'selected' : '' ?>>
                        <?= ucfirst($p) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit">Filtrar</button>
    </form>

    <!-- Tabla de tickets -->
    <table class="tickets-table">
        <thead>
            <tr>
                <th>Título</th>
                <th>Estado</th>
                <th>Prioridad</th>
                <th>Asignado</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>

        <?php if (empty($tickets)): ?>
            <tr>
                <td colspan="5" class="empty">
                    ✨ No hay tickets que mostrar
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($tickets as $ticket): ?>
                <tr>
                    <td>
                        <a href="ticket_detail.php?id=<?= $ticket['id'] ?>">
                            <?= htmlspecialchars($ticket['title']) ?>
                        </a>
                    </td>

                    <td>
                        <span class="badge status-<?= $ticket['status'] ?>">
                            <?= ucfirst(str_replace('_', ' ', $ticket['status'])) ?>
                        </span>
                    </td>

                    <td>
                        <span class="badge priority-<?= $ticket['priority'] ?>">
                            <?= ucfirst($ticket['priority']) ?>
                        </span>
                    </td>

                    <td class="assigned">
                        <?= htmlspecialchars($ticket['assigned_user'] ?? 'Sin asignar') ?>
                    </td>
                    <td><?= htmlspecialchars($ticket['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>

        </tbody>
    </table>

</div>