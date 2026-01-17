<?= $this->include('layouts/header') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/dashboard-dark.css') ?>">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="dashboard-container animate">

    <div class="page-header">
        <h1>📊 Dashboard</h1>
        <button id="toggleTheme" class="btn-toggle">🌙 Modo oscuro</button>
    </div>

    <!-- MÉTRICAS -->
    <div class="row">
        <div class="metric-card primary">
            <div class="metric-label">Total Productos</div>
            <div class="metric-value"><?= $total_productos ?? 0 ?></div>
        </div>
        <div class="metric-card success">
            <div class="metric-label">Total Ventas</div>
            <div class="metric-value"><?= $total_ventas ?? 0 ?></div>
        </div>
        <div class="metric-card info">
            <div class="metric-label">Ventas Hoy</div>
            <div class="metric-value"><?= $ventas_hoy ?? 0 ?></div>
        </div>
        <div class="metric-card warning">
            <div class="metric-label">Ganancias</div>
            <div class="metric-value">$<?= number_format($ganancias ?? 0, 2) ?></div>
        </div>
    </div>

    <!-- GRÁFICAS -->
<div class="row mt-4">
    <!-- Mes -->
    <div class="card-reporte col-12 col-md-6">
        <h3>📈 Ganancias (Mes)</h3>
        <canvas id="graficaGananciasMes"></canvas>
    </div>

    <!-- Año -->
    <div class="card-reporte col-12 col-md-6">
        <h3>📈 Ganancias (Año)</h3>
        <canvas id="graficaGananciasAnio"></canvas>
    </div>
</div>


    <!-- ACCIONES RÁPIDAS -->
    <div class="card-reporte mt-4">
        <h2>⚡ Acciones Rápidas</h2>

        <?php if (session()->get('user_role') === 'admin'): ?>
        <div class="row">
            <div class="metric-card primary clickable" onclick="location.href='<?= base_url('admin/productos') ?>'">
                📦 Gestión Productos
            </div>
            <div class="metric-card success clickable" onclick="location.href='<?= base_url('ventas') ?>'">
                💰 Nueva Venta
            </div>
            <div class="metric-card warning clickable" onclick="location.href='<?= base_url('admin/materias') ?>'">
                🥩 Materias primas
            </div>
            <div class="metric-card info clickable" onclick="location.href='<?= base_url('admin/reportes') ?>'">
                📊 Reportes
            </div>
        </div>
        <?php else: ?>
        <div class="row">
            <div class="metric-card success clickable" onclick="location.href='<?= base_url('ventas') ?>'">
                💰 Nueva Venta
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>


    // Mes
    new Chart(document.getElementById('graficaGananciasMes'), {
        type: 'line',
        data: {
            labels: <?= json_encode($labelsMes) ?>,
            datasets: [{
                label: 'Ganancias',
                data: <?= json_encode($gananciasMes) ?>,
                borderColor: 'rgba(54,162,235,1)',
                backgroundColor: 'rgba(54,162,235,0.2)',
                fill: true
            }]
        }
    });

    // Año
    new Chart(document.getElementById('graficaGananciasAnio'), {
        type: 'line',
        data: {
            labels: <?= json_encode($labelsAnio) ?>,
            datasets: [{
                label: 'Ganancias',
                data: <?= json_encode($gananciasAnio) ?>,
                borderColor: 'rgba(255,193,7,1)',
                backgroundColor: 'rgba(255,193,7,0.2)',
                fill: true
            }]
        }
    });

    // Toggle theme
    const toggle = document.getElementById('toggleTheme');
    toggle.onclick = () => {
        document.body.classList.toggle('dark-mode');
    }
</script>

<?= $this->include('layouts/footer') ?>
