<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios - Secure Desk</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --accent-primary: #38bdf8;
            --accent-secondary: #818cf8;
            --danger: #ef4444;
            --success: #22c55e;
            --border-color: rgba(255, 255, 255, 0.1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        h1 {
            font-size: 2rem;
            background: linear-gradient(to right, var(--accent-primary), var(--accent-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
        }

        /* Alertas */
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        .alert-error {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        .alert-success {
            background-color: rgba(34, 197, 94, 0.1);
            color: var(--success);
            border: 1px solid rgba(34, 197, 94, 0.2);
        }

        /* Card / Glass Effect */
        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .form-section h2 {
            font-size: 1.25rem;
            margin-top: 0;
            margin-bottom: 20px;
            color: var(--text-primary);
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        label {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        input, select {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 10px;
            color: var(--text-primary);
            font-family: inherit;
        }

        input:focus, select:focus {
            outline: 2px solid var(--accent-primary);
            outline-offset: -1px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: inherit;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            color: white;
        }
        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn-danger {
            background-color: transparent;
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        .btn-danger:hover {
            background-color: var(--danger);
            color: white;
        }

        /* Tabla */
        .table-container {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            text-align: left;
            color: var(--text-secondary);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-color);
        }
        td {
            padding: 16px;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.95rem;
        }
        tr:hover {
            background-color: rgba(255, 255, 255, 0.03);
        }

        .badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-admin { background-color: rgba(129, 140, 248, 0.2); color: #818cf8; }
        .badge-tecnico { background-color: rgba(56, 189, 248, 0.2); color: #38bdf8; }
        .badge-lector { background-color: rgba(148, 163, 184, 0.2); color: #94a3b8; }

        .actions {
            display: flex;
            gap: 10px;
        }

        .role-edit-form {
            display: flex;
            gap: 5px;
            align-items: center;
        }
        .role-edit-form select {
            padding: 5px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include __DIR__ . '/../layout/menu.php'; ?>

        <div class="header">
            <h1>👤 Gestión de Usuarios</h1>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <span>❌ <?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <span>✅ <?= htmlspecialchars($success) ?></span>
            </div>
        <?php endif; ?>

        <!-- Formulario Crear Usuario -->
        <div class="glass-card">
            <div class="form-section">
                <h2>➕ Crear Nuevo Usuario</h2>
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?= generar_csrf_token() ?>">
                    <input type="hidden" name="action" value="create">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="username">Nombre de Usuario</label>
                            <input type="text" id="username" name="username" required placeholder="ej. jdoe">
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña (mín. 8 car., letras, núm, símb.)</label>
                            <input type="password" id="password" name="password" required 
                                   minlength="8" 
                                   pattern="(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}"
                                   title="Mínimo 8 caracteres, incluyendo letras, números y símbolos">
                        </div>
                        <div class="form-group">
                            <label for="role">Rol</label>
                            <select id="role" name="role" required>
                                <option value="lector">Lector</option>
                                <option value="tecnico">Técnico</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Registrar Usuario</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Listado de Usuarios -->
        <div class="glass-card">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Rol Actual</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td style="font-weight: 600;"><?= htmlspecialchars($user['username']) ?></td>
                                <td>
                                    <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                        <span class="badge badge-<?= $user['role'] ?>"><?= $user['role'] ?></span>
                                        <small style="display: block; color: var(--text-secondary); margin-top: 4px;">(Eres tú)</small>
                                    <?php else: ?>
                                        <form method="POST" action="" class="role-edit-form">
                                            <input type="hidden" name="csrf_token" value="<?= generar_csrf_token() ?>">
                                            <input type="hidden" name="action" value="update_role">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <select name="role" onchange="this.form.submit()">
                                                <option value="lector" <?= $user['role'] === 'lector' ? 'selected' : '' ?>>Lector</option>
                                                <option value="tecnico" <?= $user['role'] === 'tecnico' ? 'selected' : '' ?>>Técnico</option>
                                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                                            </select>
                                        </form>
                                    <?php endif; ?>
                                </td>
                                <td style="color: var(--text-secondary);"><?= $user['created_at'] ?></td>
                                <td>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <form method="POST" action="" onsubmit="return confirm('¿Estás seguro de que deseas eliminar a <?= htmlspecialchars($user['username']) ?>?');">
                                            <input type="hidden" name="csrf_token" value="<?= generar_csrf_token() ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <button type="submit" class="btn btn-danger" title="Eliminar Usuario">
                                                🗑️ Eliminar
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
