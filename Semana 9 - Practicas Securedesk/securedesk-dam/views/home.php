<?php
/**
 * views/home.php - Vista de inicio del panel.
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio · Secure Desk</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', system-ui, sans-serif;
        }
        body {
            background-color: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
        }
        .hero-section {
            max-width: 1200px;
            margin: 80px auto;
            text-align: center;
            padding: 0 20px;
        }
        .hero-section h1 {
            font-size: 56px;
            font-weight: 800;
            margin-bottom: 20px;
            letter-spacing: -1.5px;
            background: linear-gradient(to right, #60a5fa, #3b82f6, #2563eb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-section p {
            font-size: 18px;
            color: #94a3b8;
            max-width: 600px;
            margin: 0 auto 40px;
            line-height: 1.6;
        }
        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 50px;
        }
        .action-card {
            background: rgba(30, 41, 59, 0.4);
            border: 1px solid rgba(148, 163, 184, 0.1);
            padding: 40px;
            border-radius: 24px;
            text-align: left;
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .action-card:hover {
            background: rgba(30, 41, 59, 0.8);
            border-color: #3b82f6;
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4);
        }
        .card-icon {
            width: 48px;
            height: 48px;
            background: #1e293b;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            font-size: 24px;
            border: 1px solid rgba(148, 163, 184, 0.2);
        }
        .action-card h3 {
            font-size: 20px;
            margin-bottom: 12px;
            color: white;
        }
        .action-card p {
            font-size: 14px;
            color: #94a3b8;
            line-height: 1.5;
        }
        .badge {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 700;
            padding: 4px 10px;
            background: rgba(148, 163, 184, 0.1);
            border-radius: 10px;
            color: #64748b;
        }
    </style>
</head>
<body>

    <div class="hero-section">
        <h1>Secure Desk DAM</h1>
        <p>Sistema centralizado de gestión de incidencias. Resuelve, colabora y haz un seguimiento de tus tickets en tiempo real.</p>

        <div class="action-grid">
            <a href="dashboard/dashboard.php" class="action-card">
                <div class="badge">Analítica</div>
                <div class="card-icon">📊</div>
                <h3>Dashboard</h3>
                <p>Visualiza KPIs y estadísticas generales del sistema en tiempo real.</p>
            </a>

            <a href="tickets/tickets_view.php" class="action-card">
                <div class="badge">Operaciones</div>
                <div class="card-icon">🎫</div>
                <h3>Gestión de Tickets</h3>
                <p>Listado completo de incidencias, filtros avanzados y búsqueda rápida.</p>
            </a>

            <?php if ($_SESSION['role'] !== 'lector'): ?>
            <a href="tickets/ticket_create.php" class="action-card">
                <div class="badge">Acción</div>
                <div class="card-icon">➕</div>
                <h3>Crear Ticket</h3>
                <p>Registra una nueva incidencia de forma rápida y sencilla.</p>
            </a>
            <?php endif; ?>

            <a href="account.php" class="action-card">
                <div class="badge">Perfil</div>
                <div class="card-icon">👤</div>
                <h3>Mi Perfil</h3>
                <p>Gestiona tus datos de acceso y configuración de seguridad.</p>
            </a>
        </div>
    </div>
</body>
</html>
