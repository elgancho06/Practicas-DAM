<?php
/**
 * views/audit.php - Vista del panel de auditoría.
 * Muestra los logs con filtros. Espera las variables:
 * - $users: array [id => username] para el selector de usuario.
 * - $actions: array de acciones distintas.
 * - $logs: array de registros de auditoría.
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Auditoría · Secure Desk</title>
    <style>
        /* ============================================
           ESTILOS MODERNOS PARA AUDITORÍA (AZULES OSCUROS)
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
        .audit-container {
            max-width: 1300px;
            margin: 30px auto;
            padding: 30px;
            background: rgba(18, 30, 48, 0.9);
            backdrop-filter: blur(8px);
            border-radius: 24px;
            box-shadow: 0 20px 40px -10px rgba(0, 20, 40, 0.7);
            border: 1px solid rgba(66, 153, 225, 0.2);
            color: #e2e8f0;
        }
        .audit-container h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 25px;
            background: linear-gradient(135deg, #90cdf4 0%, #4299e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
            display: inline-block;
            border-bottom: 2px solid #2d4a6e;
            padding-bottom: 8px;
        }
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
        .btn-filter, .btn-clear {
            padding: 12px 24px;
            border: none;
            border-radius: 40px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            letter-spacing: 0.3px;
        }
        .btn-filter {
            background: linear-gradient(145deg, #2563eb, #1e4b8f);
            color: white;
            box-shadow: 0 8px 18px -6px #1e3a8a;
        }
        .btn-filter:hover {
            background: linear-gradient(145deg, #3b82f6, #2563eb);
            transform: translateY(-2px);
            box-shadow: 0 14px 22px -8px #1e4b8f;
        }
        .btn-clear {
            background: #2d3748;
            color: #cbd5e0;
            border: 1px solid #4a5568;
        }
        .btn-clear:hover {
            background: #4a5568;
            color: white;
            transform: translateY(-2px);
            border-color: #718096;
        }
        .audit-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
            margin-top: 20px;
        }
        .audit-table thead tr {
            background: linear-gradient(90deg, #0e1e2f, #162b3a);
        }
        .audit-table th {
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
        .audit-table td {
            padding: 16px 18px;
            background: #132337;
            border-radius: 14px;
            font-size: 14px;
            color: #e0e7ff;
            box-shadow: 0 4px 12px rgba(0, 10, 20, 0.4);
            transition: all 0.25s ease;
            border: 1px solid transparent;
        }
        .audit-table tbody tr:hover td {
            background: #1b3147;
            border-color: #3b6c9e;
            transform: scale(1.01);
            box-shadow: 0 12px 24px -8px #0b1a2a;
        }
        .no-results {
            text-align: center;
            padding: 50px 20px;
            color: #a0aec0;
            font-size: 16px;
            background: #132337;
            border-radius: 30px;
            margin-top: 20px;
            border: 1px dashed #2d4a6e;
        }
        @media (max-width: 900px) {
            .audit-container {
                padding: 15px;
                margin: 15px;
            }
            .audit-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/partials/menu.php'; ?>

    <div class="audit-container">
        <h1>📋 Auditoría del sistema</h1>

        <!-- Formulario de filtros -->
        <form method="GET" class="filters">
            <div class="filter-group">
                <label for="user_id">👤 Usuario</label>
                <select name="user_id" id="user_id">
                    <option value="">Todos los usuarios</option>
                    <?php foreach ($users as $id => $username): ?>
                        <option value="<?= $id ?>" <?= (isset($_GET['user_id']) && $_GET['user_id'] == $id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($username) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="action">⚡ Acción</label>
                <select name="action" id="action">
                    <option value="">Todas las acciones</option>
                    <?php foreach ($actions as $act): ?>
                        <option value="<?= htmlspecialchars($act) ?>" <?= (isset($_GET['action']) && $_GET['action'] === $act) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($act) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn-filter">🔍 Filtrar</button>
            <a href="audit.php" class="btn-clear">🗑️ Limpiar</a>
        </form>

        <!-- Resultados -->
        <?php if (empty($logs)): ?>
            <div class="no-results">
                ✨ No hay registros de auditoría que coincidan con los filtros.
            </div>
        <?php else: ?>
            <table class="audit-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Entidad</th>
                        <th>ID</th>
                        <th>Detalle</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= htmlspecialchars($log['created_at']) ?></td>
                            <td><?= htmlspecialchars($log['username'] ?? 'Sistema/Desconocido') ?></td>
                            <td data-action="<?= htmlspecialchars($log['action']) ?>"><?= htmlspecialchars($log['action']) ?></td>
                            <td><?= htmlspecialchars($log['entity']) ?></td>
                            <td><?= htmlspecialchars($log['entity_id'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($log['details']) ?></td>
                            <td><?= htmlspecialchars($log['ip_address'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>