<?php
/**
 * views/account.php - Vista de perfil de usuario y ajustes.
 * Recibe:
 * - $user: Datos del usuario (id, username, role, created_at)
 * - $success_msg: Mensaje de éxito (opcional)
 * - $error_msg: Mensaje de error (opcional)
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Cuenta · Secure Desk</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
        body {
            background-color: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
        }
        .account-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .profile-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            border: 1px solid rgba(148, 163, 184, 0.1);
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .profile-header {
            display: flex;
            align-items: center;
            gap: 25px;
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.1);
        }
        .avatar-circle {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 700;
            color: white;
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.5);
        }
        .user-info h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .user-info .role-badge {
            display: inline-block;
            padding: 4px 12px;
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        .detail-item label {
            display: block;
            font-size: 12px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }
        .detail-item p {
            font-size: 16px;
            font-weight: 500;
        }
        .password-section h2 {
            font-size: 18px;
            margin-bottom: 20px;
            color: #f1f5f9;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #cbd5e1;
        }
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 12px;
            color: white;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-group input:focus {
            border-color: #3b82f6;
        }
        .btn-save {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            width: 100%;
        }
        .btn-save:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }
        .alert {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 14px;
        }
        .alert-success { background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
        .alert-error { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
    </style>
</head>
<body>

    <div class="account-container">
        <div class="profile-card">
            <?php if (!empty($success_msg)): ?>
                <div class="alert alert-success">✅ <?= htmlspecialchars($success_msg) ?></div>
            <?php endif; ?>
            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-error">❌ <?= htmlspecialchars($error_msg) ?></div>
            <?php endif; ?>

            <div class="profile-header">
                <div class="avatar-circle">
                    <?= strtoupper(substr($user['username'], 0, 1)) ?>
                </div>
                <div class="user-info">
                    <h1><?= htmlspecialchars($user['username']) ?></h1>
                    <span class="role-badge"><?= htmlspecialchars($user['role']) ?></span>
                </div>
            </div>

            <div class="details-grid">
                <div class="detail-item">
                    <label>ID de Usuario</label>
                    <p>#<?= htmlspecialchars($user['id']) ?></p>
                </div>
                <div class="detail-item">
                    <label>Miembro desde</label>
                    <p><?= date('d/m/Y', strtotime($user['created_at'])) ?></p>
                </div>
            </div>

            <div class="password-section">
                <h2>Cambiar contraseña</h2>
                <form method="POST" action="account.php">
                    <div class="form-group">
                        <label for="current_pw">Contraseña actual</label>
                        <input type="password" name="current_pw" id="current_pw" required>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label for="new_pw">Nueva contraseña</label>
                            <input type="password" name="new_pw" id="new_pw" required 
                                   pattern="(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}"
                                   title="Mínimo 8 caracteres, incluyendo letras, números y símbolos">
                            <small style="color: #94a3b8; font-size: 11px; margin-top: 5px; display: block;">
                                Mínimo 8 caracteres, con letras, números y símbolos.
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="confirm_pw">Confirmar nueva contraseña</label>
                            <input type="password" name="confirm_pw" id="confirm_pw" required>
                        </div>
                    </div>
                    <button type="submit" class="btn-save">Actualizar seguridad</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
