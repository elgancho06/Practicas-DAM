<style>
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #0f172a, #1e293b);
    color: #e2e8f0;
}

/* Contenedor general debajo del menú */
.main-content {
    display: flex;
    justify-content: center;
    padding: 60px 20px;
}

/* Tarjeta del formulario */
.container {
    background: rgba(30, 41, 59, 0.9);
    backdrop-filter: blur(12px);
    padding: 40px;
    border-radius: 16px;
    width: 100%;
    max-width: 600px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.5);
}

h1 {
    margin-bottom: 25px;
    font-weight: 600;
    text-align: center;
}

label {
    display: block;
    margin-bottom: 6px;
    font-size: 14px;
    color: #94a3b8;
}

input, textarea, select {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    border-radius: 8px;
    border: 1px solid #334155;
    background-color: #0f172a;
    color: #e2e8f0;
    font-size: 14px;
    transition: all 0.3s ease;
}

input:focus, textarea:focus, select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59,130,246,0.3);
}

button {
    width: 100%;
    padding: 14px;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    background: linear-gradient(135deg, #3b82f6, #6366f1);
    color: white;
    transition: all 0.3s ease;
}

button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(99,102,241,0.4);
}

.error-message {
    background-color: rgba(220, 38, 38, 0.15);
    color: #f87171;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
    border: 1px solid rgba(220, 38, 38, 0.4);
}
</style>

<div class="main-content">
    <div class="container">

        <h1>🎫 Crear Nuevo Ticket</h1>

        <?php if (!empty($error)): ?>
            <div class="error-message">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <label>Título *</label>
            <input type="text" name="title" placeholder="Ej: Problema con acceso VPN" required>

            <label>Descripción</label>
            <textarea name="description" rows="5" placeholder="Describe el problema con el mayor detalle posible..."></textarea>

            <label>Categoría</label>
            <input type="text" name="category" placeholder="Ej: Redes, Software, Seguridad">

            <label>Prioridad</label>
            <select name="priority">
                <option value="baja">🟢 Baja</option>
                <option value="media" selected>🟡 Media</option>
                <option value="alta">🟠 Alta</option>
                <option value="critica">🔴 Crítica</option>
            </select>

            <button type="submit">Crear ticket</button>

        </form>

    </div>
</div>
