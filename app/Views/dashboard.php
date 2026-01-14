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
        <div class="card-reporte small">
            <h3>📈 Ganancias</h3>
            <canvas id="graficaGanancias"></canvas>
        </div>
        <div class="card-reporte small">
            <h3>💵 Ingresos</h3>
            <canvas id="graficaIngresos"></canvas>
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
            <div class="metric-card warning clickable" onclick="location.href='<?= base_url('admin/caja') ?>'">
                💵 Caja
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
const ctxG = document.getElementById('graficaGanancias');
new Chart(ctxG, {
    type: 'line',
    data: {
        labels: ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'],
        datasets: [{
            label: 'Ganancias',
            data: [120,190,300,250,220,320,400],
            tension: 0.4,
            fill: true
        }]
    }
});

const ctxI = document.getElementById('graficaIngresos');
new Chart(ctxI, {
    type: 'bar',
    data: {
        labels: ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'],
        datasets: [{
            label: 'Ingresos',
            data: [500,700,800,650,900,1000,1200]
        }]
    }
});

const toggle = document.getElementById('toggleTheme');
toggle.onclick = () => {
    document.body.classList.toggle('dark-mode');
}
</script>

<?= $this->include('layouts/footer') ?>

