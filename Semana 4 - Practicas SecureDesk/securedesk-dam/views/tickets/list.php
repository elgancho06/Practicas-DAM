<?php
// Vista del listado de tickets
// Aquí SOLO mostramos información (no lógica de BD)
?>

<style>
    .container {
        max-width: 1100px;
        margin: 30px auto;
        padding: 25px;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }

    /* Cabecera: título + botón */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .header h1 {
        margin: 0;
    }

    .btn-create {
        padding: 10px 16px;
        background-color: #28a745;
        color: #fff;
        text-decoration: none;
        border-radius: 6px;
        font-weight: bold;
        transition: background-color 0.2s ease;
    }

    .btn-create:hover {
        background-color: #218838;
    }

    /* Filtros */
    .filters {
        display: flex;
        gap: 20px;
        margin-bottom: 25px;
        align-items: flex-end;
        flex-wrap: wrap;
    }

    .filters label {
        display: block;
        font-weight: bold;
        margin-bottom: 6px;
    }

    .filters select {
        padding: 7px;
        border-radius: 5px;
        border: 1px solid #ccc;
        min-width: 150px;
    }

    .filters button {
        padding: 9px 18px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
    }

    .filters button:hover {
        background-color: #0056b3;
    }

    /* Tabla */
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 15px;
    }

    table th {
        background-color: #f5f6f8;
        padding: 12px;
        text-align: left;
        border-bottom: 2px solid #ddd;
    }

    table td {
        padding: 12px;
        border-bottom: 1px solid #eee;
    }

    table tr:hover {
        background-color: #fafafa;
    }

    /* Estados */
    .status {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: bold;
        display: inline-block;
    }

    .status-nuevo {
        background-color: #e3f2fd;
        color: #0d6efd;
    }

    .status-en_proceso {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-resuelto {
        background-color: #d4edda;
        color: #155724;
    }

    /* Prioridades */
    .priority {
        font-weight: bold;
    }

    .priority-baja { color: #28a745; }
    .priority-media { color: #ffc107; }
    .priority-alta { color: #fd7e14; }
    .priority-critica { color: #dc3545; }

    .empty {
        text-align: center;
        padding: 25px;
        color: #666;
    }
</style>

<div class="container">

    <!-- Cabecera del listado -->
    <div class="header">
        <h1>Listado de tickets</h1>

        <?php
        // Mostramos el botón "Nuevo ticket" solo a Admin y Técnico
        if ($_SESSION['role'] !== 'lector'): ?>
            <a href="ticket_create.php" class="btn-create">
                + Nuevo ticket
            </a>
        <?php endif; ?>
    </div>

    <!-- Filtros -->
    <form method="GET" class="filters">

        <div>
            <label for="status">Estado</label>
            <select name="status" id="status">
                <option value="">Todos</option>
                <?php foreach (TICKET_STATUS as $s): ?>
                    <option value="<?= $s ?>" <?= ($status === $s) ? 'selected' : '' ?>>
                        <?= ucfirst(str_replace('_', ' ', $s)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="priority">Prioridad</label>
            <select name="priority" id="priority">
                <option value="">Todas</option>
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
    <table>
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
                    No hay tickets que mostrar
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($tickets as $ticket): ?>
                <tr>
                    <td><a href="ticket_detail.php?id=<?= $ticket['id'] ?>"> 
                        <?= htmlspecialchars($ticket['title']) ?> </a> 
                    </td>

                    <td>
                        <span class="status status-<?= $ticket['status'] ?>">
                            <?= ucfirst(str_replace('_', ' ', $ticket['status'])) ?>
                        </span>
                    </td>

                    <td class="priority priority-<?= $ticket['priority'] ?>">
                        <?= ucfirst($ticket['priority']) ?>
                    </td>

                    <td><?= htmlspecialchars($ticket['assigned_user'] ?? 'Sin asignar') ?></td>
                    <td><?= $ticket['created_at'] ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>

        </tbody>
    </table>

</div>
