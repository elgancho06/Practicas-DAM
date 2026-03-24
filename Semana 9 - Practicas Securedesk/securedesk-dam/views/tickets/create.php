<?php
/**
 * views/tickets/create.php
 * =============================================================================
 * Vista para la creación de un nuevo ticket.
 * Muestra un formulario con los campos necesarios y aplica los mismos estilos
 * modernos que el resto de la aplicación (login, listado, auditoría).
 * 
 * @package SecureDesk
 * @subpackage Views/Tickets
 * @version 1.0
 * 
 * Variables esperadas desde el controlador (public/ticket_create.php):
 *   - $error       : (string) Mensaje de error a mostrar, si existe.
 *   - $csrf_token  : (string) Token CSRF generado para proteger el formulario.
 */
?>

<!-- =========================================================================
     ESTILOS CSS DE LA PÁGINA
     =========================================================================
     Se mantienen dentro de la vista para seguir el patrón actual del proyecto.
     Más adelante podrían externalizarse a un archivo .css si se desea.
-->
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
        background-color: #0f172a; /* Azul muy oscuro de fondo */
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Contenedor principal que envuelve la tarjeta del formulario */
    .main-content {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px 20px;
        flex: 1;
    }

    /* ============================================
       TARJETA DEL FORMULARIO (efecto cristal)
       ============================================ */
    .container {
        max-width: 650px;
        width: 100%;
        background: rgba(18, 30, 48, 0.9); /* Azul oscuro semitransparente */
        backdrop-filter: blur(8px);
        border-radius: 32px;
        padding: 40px 35px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(66, 153, 225, 0.2);
        color: #e2e8f0;
    }

    /* Título con gradiente (coherente con login y auditoría) */
    .container h1 {
        font-size: 32px;
        font-weight: 600;
        text-align: center;
        margin-bottom: 30px;
        background: linear-gradient(135deg, #90cdf4, #4299e1);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: -0.5px;
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
       ESTILOS DE LOS CAMPOS DEL FORMULARIO
       ============================================ */
    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #90cdf4;
        margin-bottom: 8px;
    }

    /* Campos de entrada, textarea y select */
    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 14px 18px;
        background: #1e2e44;
        border: 1px solid #2d4a6e;
        border-radius: 16px;
        color: #f0f4fa;
        font-size: 16px;
        outline: none;
        transition: all 0.2s ease;
    }

    /* Efecto hover en campos */
    .form-group input:hover,
    .form-group textarea:hover,
    .form-group select:hover {
        border-color: #4299e1;
        background-color: #253c5c;
    }

    /* Efecto focus (cuando el campo está seleccionado) */
    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        border-color: #63b3ed;
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.3);
    }

    /* Ajuste específico para textarea */
    .form-group textarea {
        resize: vertical;
        min-height: 120px;
    }

    /* Estilo para el select (personalización de la flecha) */
    .form-group select {
        appearance: none;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2390cdf4' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'/></svg>");
        background-repeat: no-repeat;
        background-position: right 18px center;
        background-size: 14px;
        cursor: pointer;
    }

    /* ============================================
       BOTÓN DE ENVÍO (con gradiente y efecto hover)
       ============================================ */
    .btn-submit {
        width: 100%;
        padding: 16px;
        background: linear-gradient(145deg, #2563eb, #1e4b8f);
        color: white;
        border: none;
        border-radius: 40px;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 8px 18px -6px #1e3a8a;
        margin-top: 20px;
        letter-spacing: 0.5px;
    }

    .btn-submit:hover {
        background: linear-gradient(145deg, #3b82f6, #2563eb);
        transform: translateY(-2px);
        box-shadow: 0 14px 22px -8px #1e4b8f;
    }

    /* Pequeño texto indicando campos obligatorios */
    .required-note {
        text-align: right;
        font-size: 12px;
        color: #718096;
        margin-top: -10px;
        margin-bottom: 15px;
    }

    /* ============================================
       RESPONSIVE (móviles)
       ============================================ */
    @media (max-width: 600px) {
        .container {
            padding: 25px 20px;
        }
        .btn-submit {
            font-size: 16px;
            padding: 14px;
        }
    }
</style>

<!-- =========================================================================
     CONTENIDO PRINCIPAL (TARJETA DEL FORMULARIO)
     ========================================================================= -->
<div class="main-content">
    <div class="container">

        <!-- Título de la página con un icono (opcional) -->
        <h1>🎫 Crear Nuevo Ticket</h1>

        <!--
            Mostrar mensaje de error si existe (definido en el controlador).
            La variable $error se pasa desde public/ticket_create.php.
        -->
        <?php if (!empty($error)): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!--
            FORMULARIO DE CREACIÓN DE TICKET
            - Utiliza método POST para enviar los datos.
            - Incluye un campo oculto con el token CSRF (protección contra falsificación de petición).
            - Los campos obligatorios tienen el atributo "required" para validación en cliente,
              pero la validación real se hace en el servidor.
        -->
        <form method="POST" action="">

            <!-- Campo oculto con el token CSRF generado en el controlador -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <!-- Nota: Los campos marcados con * son obligatorios -->
            <div class="required-note">* Campos obligatorios</div>

            <!-- Campo: Título (obligatorio) -->
            <div class="form-group">
                <label for="title">📌 Título *</label>
                <input type="text" id="title" name="title" 
                       placeholder="Ej: Problema con acceso VPN" 
                       required 
                       autofocus>
                <!--
                    El atributo "required" fuerza la validación en el navegador.
                    El controlador también lo validará en el servidor.
                -->
            </div>

            <!-- Campo: Descripción (obligatoria, aunque el controlador también lo exige) -->
            <div class="form-group">
                <label for="description">📝 Descripción *</label>
                <textarea id="description" name="description" 
                          rows="5" 
                          placeholder="Describe el problema con el mayor detalle posible..." 
                          required></textarea>
            </div>

            <!-- Campo: Categoría (opcional, pero se puede dejar un valor por defecto) -->
            <div class="form-group">
                <label for="category">🏷️ Categoría</label>
                <input type="text" id="category" name="category" 
                       placeholder="Ej: Redes, Software, Hardware, Seguridad...">
            </div>

            <!-- Campo: Prioridad (obligatorio, con valor por defecto "media") -->
            <div class="form-group">
                <label for="priority">⚡ Prioridad *</label>
                <select id="priority" name="priority" required>
                    <option value="baja">🟢 Baja</option>
                    <option value="media" selected>🟡 Media</option>
                    <option value="alta">🟠 Alta</option>
                    <option value="critica">🔴 Crítica</option>
                </select>
                <!--
                    El atributo "selected" en la opción "media" establece ese valor por defecto.
                    El controlador también valida que el valor enviado esté dentro de TICKET_PRIORITY.
                -->
            </div>

            <!-- Botón de envío del formulario -->
            <button type="submit" class="btn-submit">✨ Crear ticket</button>
        </form>

        <!-- Pequeña nota de seguridad (opcional) -->
        <div style="text-align: center; margin-top: 20px; color: #4a5568; font-size: 12px;">
            Los datos se transmiten de forma segura | CSRF protegido
        </div>

    </div>
</div>