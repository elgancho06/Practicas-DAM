<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard · SecureDesk</title>
    <style>
        /* ============================================
           ESTILOS MODERNOS PARA EL DASHBOARD
           ============================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background: #0f172a; /* Fondo azul muy oscuro */
            min-height: 100vh;
            color: #e2e8f0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Título con gradiente */
        h1 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #90cdf4, #4299e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -1px;
        }

        /* Badge para la fecha de actualización */
        .last-update {
            display: inline-block;
            background: rgba(148, 163, 184, 0.1);
            border: 1px solid rgba(148, 163, 184, 0.2);
            color: #94a3b8;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            margin-bottom: 30px;
        }

        /* Secciones de cuadrícula (Grid) */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        /* Tarjetas con efecto de cristal (Glassmorphism) */
        .card {
            background: rgba(30, 41, 59, 0.5);
            backdrop-filter: blur(8px);
            border-radius: 24px;
            padding: 30px;
            border: 1px solid rgba(66, 153, 225, 0.1);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .card:hover {
            transform: translateY(-5px);
            border-color: rgba(66, 153, 225, 0.3);
            background: rgba(30, 46, 68, 0.8);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5);
        }

        .card-title {
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #90cdf4;
            margin-bottom: 15px;
        }

        .card-value {
            font-size: 48px;
            font-weight: 800;
            color: #f1f5f9;
            margin-bottom: 5px;
        }

        .card-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }

        /* Colores específicos para prioridad */
        .priority-baja { border-left: 5px solid #22c55e; }
        .priority-media { border-left: 5px solid #eab308; }
        .priority-alta { border-left: 5px solid #f97316; }
        .priority-critica { border-left: 5px solid #ef4444; }

        /* Títulos de sección */
        h2 {
            font-size: 24px;
            margin: 40px 0 20px;
            color: #94a3b8;
            border-bottom: 1px solid rgba(148, 163, 184, 0.1);
            padding-bottom: 10px;
        }

        /* Estilos para distribución con barras de progreso */
        .distribution-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .distribution-card {
            background: rgba(30, 46, 68, 0.5);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(66, 153, 225, 0.1);
            border-radius: 24px;
            padding: 25px;
        }

        .dist-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .dist-label {
            width: 100px;
            font-size: 14px;
            color: #cbd5e0;
            text-transform: capitalize;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dist-bar-container {
            flex: 1;
            height: 12px;
            background: #0f172a;
            border-radius: 10px;
            overflow: hidden;
            margin: 0 15px;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .dist-bar {
            height: 100%;
            background: linear-gradient(90deg, #4299e1, #63b3ed);
            border-radius: 10px;
            width: 0%;
            transition: width 0.8s ease-out;
        }

        .dist-count {
            min-width: 45px;
            text-align: right;
            font-weight: 600;
            color: #e2e8f0;
            font-size: 14px;
        }

        /* Colores para estados */
        .status-nuevo .dist-bar { background: linear-gradient(90deg, #3182ce, #90cdf4); }
        .status-en_proceso .dist-bar { background: linear-gradient(90deg, #d69e2e, #fbd38d); }
        .status-resuelto .dist-bar { background: linear-gradient(90deg, #38a169, #9ae6b4); }
        .status-cerrado .dist-bar { background: linear-gradient(90deg, #718096, #cbd5e0); }
        
        /* Color para categorías */
        .category-item .dist-bar { background: linear-gradient(90deg, #4299e1, #63b3ed); }

        /* Responsive */
        @media (max-width: 640px) {
            h1 { font-size: 28px; }
            .stats-grid, .distribution-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Dashboard de Métricas</h1>
        <div class="last-update">Última actualización: <?= htmlspecialchars($fechaActualizacion) ?></div>

        <!-- KPI Principal: Total Tickets -->
        <div class="stats-grid">
            <div class="card" style="grid-column: 1 / -1; background: linear-gradient(135deg, rgba(37, 99, 235, 0.2), rgba(30, 41, 59, 0.5)); border: 1px solid rgba(37, 99, 235, 0.3);">
                <div class="card-icon">🎫</div>
                <div class="card-title">Total de Incidencias</div>
                <div class="card-value"><?= $totalTickets ?></div>
                <p style="color: #94a3b8; font-size: 14px;">Total de tickets registrados en el sistema</p>
            </div>
        </div>

        <!-- Nuevas secciones de distribución -->
        <div class="distribution-grid">
            
            <!-- Distribución por Estado -->
            <div class="distribution-card">
                <div class="card-title" style="margin-bottom: 20px; text-align: left;">Distribución por Estado</div>
                <?php 
                $estadosAMostrar = ['nuevo', 'en_proceso', 'resuelto', 'cerrado'];
                foreach ($estadosAMostrar as $est): 
                    $count = $ticketsPorEstado[$est] ?? 0;
                    $porcentaje = $totalTickets > 0 ? round(($count / $totalTickets) * 100) : 0;
                    $classEstado = "status-" . $est;
                ?>
                    <div class="dist-item <?= $classEstado ?>">
                        <div class="dist-label"><?= str_replace('_', ' ', $est) ?></div>
                        <div class="dist-bar-container">
                            <div class="dist-bar" style="width: <?= $porcentaje ?>%;"></div>
                        </div>
                        <div class="dist-count"><?= $count ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Distribución por Categoría -->
            <div class="distribution-card">
                <div class="card-title" style="margin-bottom: 20px; text-align: left;">Distribución por Categoría</div>
                <?php if (!empty($ticketsPorCategoria)): ?>
                    <?php foreach ($ticketsPorCategoria as $cat => $count): 
                        $porcentaje = $totalTickets > 0 ? round(($count / $totalTickets) * 100) : 0;
                    ?>
                        <div class="dist-item category-item">
                            <div class="dist-label" title="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></div>
                            <div class="dist-bar-container">
                                <div class="dist-bar" style="width: <?= $porcentaje ?>%;"></div>
                            </div>
                            <div class="dist-count"><?= $count ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #718096; font-size: 14px; text-align: center; margin-top: 20px;">No hay categorías registradas</p>
                <?php endif; ?>
            </div>

        </div>

        <!-- Acciones rápidas -->
        <h2>Acciones rápidas</h2>
        <div class="stats-grid">
            <a href="../tickets/criticos.php" style="text-decoration: none;">
                <div class="card priority-critica" style="cursor: pointer; background: rgba(239, 68, 68, 0.1);">
                    <div class="card-icon">🔴</div>
                    <div class="card-title">Prioridad Crítica</div>
                    <div class="card-value"><?= $totalCriticos ?></div>
                    <p style="color: #94a3b8; font-size: 13px; margin-top: 10px;">Ver tickets urgentes</p>
                </div>
            </a>

            <a href="../tickets/sin_asignar.php" style="text-decoration: none;">
                <div class="card" style="cursor: pointer; border-left: 5px solid #94a3b8; background: rgba(148, 163, 184, 0.1);">
                    <div class="card-icon">❓</div>
                    <div class="card-title">Sin asignar</div>
                    <div class="card-value"><?= $totalSinAsignar ?></div>
                    <p style="color: #94a3b8; font-size: 13px; margin-top: 10px;">Gestionar tickets huérfanos</p>
                </div>
            </a>
        </div>

        <!-- Sección por Estado (Tarjetas) -->
        <h2>Resumen por Estado</h2>
        <div class="stats-grid">
            <div class="card">
                <div class="card-icon">🆕</div>
                <div class="card-title">Nuevos</div>
                <div class="card-value"><?= $ticketsPorEstado['nuevo'] ?? 0 ?></div>
            </div>
            <div class="card">
                <div class="card-icon">⚙️</div>
                <div class="card-title">En Proceso</div>
                <div class="card-value"><?= $ticketsPorEstado['en_proceso'] ?? 0 ?></div>
            </div>
            <div class="card">
                <div class="card-icon">✅</div>
                <div class="card-title">Resueltos</div>
                <div class="card-value"><?= $ticketsPorEstado['resuelto'] ?? 0 ?></div>
            </div>
        </div>

        <!-- Sección por Prioridad (Tarjetas) -->
        <h2>Resumen por Prioridad</h2>
        <div class="stats-grid">
            <div class="card priority-baja">
                <div class="card-icon">🟢</div>
                <div class="card-title">Baja</div>
                <div class="card-value"><?= $ticketsPorPrioridad['baja'] ?? 0 ?></div>
            </div>
            <div class="card priority-media">
                <div class="card-icon">🟡</div>
                <div class="card-title">Media</div>
                <div class="card-value"><?= $ticketsPorPrioridad['media'] ?? 0 ?></div>
            </div>
            <div class="card priority-alta">
                <div class="card-icon">🟠</div>
                <div class="card-title">Alta</div>
                <div class="card-value"><?= $ticketsPorPrioridad['alta'] ?? 0 ?></div>
            </div>
            <div class="card priority-critica">
                <div class="card-icon">🔴</div>
                <div class="card-title">Crítica</div>
                <div class="card-value"><?= $ticketsPorPrioridad['critica'] ?? 0 ?></div>
            </div>
        </div>
    </div>
</body>
</html>
