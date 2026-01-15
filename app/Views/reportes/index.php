<?= $this->include('layouts/header') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/reportes.css') ?>">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="reportes-container">
    <div class="filtros-reportes">
        <form method="get" class="row">
            <div class="col">
                <label>Fecha inicio</label>
                <input type="date" name="fecha_inicio" class="form-control-reporte" value="<?= $fecha_inicio ?? '' ?>">
            </div>
            <div class="col">
                <label>Fecha fin</label>
                <input type="date" name="fecha_fin" class="form-control-reporte" value="<?= $fecha_fin ?? '' ?>">
            </div>
            <div class="col">
                <label style="opacity:0;">Filtrar</label>
                <button class="btn-reporte">🔍 Filtrar</button>
            </div>
        </form>
    </div>

    <div class="page-header">
        <h1>📊 Dashboard de Reportes</h1>
        <button class="btn-toggle" onclick="toggleDarkMode()">🌙 Modo oscuro</button>
    </div>

    <!-- MÉTRICAS -->
    <div class="row">
        <div class="metric-card primary animate">
            <div class="metric-label">Ingresos</div>
            <div class="metric-value">$<?= number_format($totalIngresos, 2) ?></div>
            <div class="metric-detail">Brutos</div>
        </div>

        <div class="metric-card success animate">
            <div class="metric-label">Ganancias</div>
            <div class="metric-value">$<?= number_format($totalGanancias, 2) ?></div>
            <div class="metric-detail">Netas</div>
        </div>

        <div class="metric-card info animate">
            <div class="metric-label">Ventas</div>
            <div class="metric-value">$<?= count($ventas) > 0 ? number_format(array_sum(array_column($ventas, 'total')) / count($ventas), 2) : '0.00' ?></div>
            <div class="metric-detail">Efectivo</div>
        </div>

    </div>

    <!-- GRÁFICAS -->
    <div class="row mt-4">
        <div class="card-reporte grafica animate">
            <div class="card-header-reporte">💸 Inversión vs Ganancia</div>
            <canvas id="inversionGanancia"></canvas>
        </div>

        <div class="card-reporte grafica animate">
            <div class="card-header-reporte">💰 Ingresos vs Ganancias</div>
            <canvas id="ingresosGanancias"></canvas>
        </div>

    
    </div>

    <!-- SECCIONES -->
    <div class="row mt-4">
        <div class="card-reporte animate">
            <div class="card-header-reporte">🔥 Productos más vendidos</div>
            <?php foreach ($topProductos as $p): ?>
                <div class="item-reporte">
                    <span><?= esc($p['nombre']) ?></span>
                    <span><?= $p['total_vendido'] ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="card-reporte animate">
            <div class="card-header-reporte">🐢 Productos menos vendidos</div>
            <?php foreach ($productosMenosVendidos as $p): ?>
                <div class="item-reporte">
                    <span><?= esc($p['nombre']) ?></span>
                    <span><?= $p['total_vendido'] ?></span>
                </div>
            <?php endforeach; ?>
        </div>
            <div class="card-reporte grafica animate">
            <div class="card-header-reporte">📈 Ventas por día</div>
            <canvas id="ventasDia"></canvas>
        </div>
    </div>

    <!-- PRODUCTOS DE INVERSIÓN Y BAJO STOCK -->
    <div class="row mt-4">
        <div class="card-reporte full animate">
            <div class="card-header-reporte">💼 Productos de Inversión</div>
            <?php if(!empty($productosInversion)): ?>
                <table class="tabla-reporte">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($productosInversion as $p): ?>
                            <tr>
                                <td><?= esc($p['nombre']) ?></td>
                                <td><?= esc($p['cantidad']) ?></td>
                                <td>$<?= number_format($p['precio'] * $p['cantidad'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="item-reporte">
                    <span>No hay productos registrados</span>
                    <span>-</span>
                </div>
            <?php endif; ?>
        </div>

        <div class="card-reporte full animate">
            <div class="card-header-reporte">📦 Productos con bajo stock</div>
            <?php if(!empty($productosBajoStock)): ?>
                <?php foreach($productosBajoStock as $p): ?>
                    <div class="item-reporte alert">
                        <span><?= esc($p['categoria_nombre']) ?> / <?= esc($p['nombre']) ?></span>
                        <span><?= esc($p['cantidad']) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="item-reporte">
                    <span>Todos los productos están en stock</span>
                    <span>-</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-md-6"> 
        <div class="card-reporte">
            <div class="card-header-reporte">🔗 Acceso Rápido</div> 
            <div class="card-body-reporte">
                <div class="acceso-rapido-buttons">
                    <a href="<?= base_url('admin/reportes/ventas') . '?fecha_inicio=' . $fecha_inicio . '&fecha_fin=' . $fecha_fin ?>" class="btn-acceso">
                        📋 Reporte Detallado de Ventas
                    </a>
                    <a href="<?= base_url('admin/reportes/productos') ?>" class="btn-acceso">
                        🏷️ Reporte de Productos
                    </a>
                    <a href="<?= base_url('admin/reportes/clientes') ?>" class="btn-acceso">
                        👥 Reporte de Clientes
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
}

// 🔹 Ventas por día
new Chart(document.getElementById('ventasDia'), {
    type: 'line',
    data: {
        labels: <?= json_encode($labelsDia) ?>,
        datasets: [{
            label: 'Ventas',
            data: <?= json_encode($ingresosDia) ?>,
            borderColor: 'rgba(54,162,235,1)',
            backgroundColor: 'rgba(54,162,235,0.2)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: { y: { beginAtZero: true, ticks: { callback: value => '$' + value.toLocaleString() } } }
    }
});

/* 💰 Ingresos vs Ganancias */
new Chart(document.getElementById('ingresosGanancias'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($labelsDia) ?>, // días de la semana
        datasets: [
            { 
                label: 'Ingresos', 
                data: <?= json_encode($ingresosDia) ?>,
                backgroundColor: 'rgba(54,162,235,0.8)'
            },
            { 
                label: 'Ganancias', 
                data: <?= json_encode($gananciaDia) ?>,
                backgroundColor: 'rgba(25,135,84,0.8)'
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: { 
            y: { 
                beginAtZero: true,
                ticks: {
                    callback: value => '$' + value.toLocaleString()
                }
            } 
        }
    }
});


// 💸 Inversión vs Ganancia
new Chart(document.getElementById('inversionGanancia'), {
    type: 'line',
    data: {
        labels: <?= json_encode($labelsDia) ?>,
        datasets: [
            {
                label: 'Inversión',
                data: <?= json_encode($inversionDia) ?>,
                borderColor: 'rgba(220,53,69,1)',
                backgroundColor: 'rgba(220,53,69,0.2)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Ganancia',
                data: <?= json_encode($gananciaDia) ?>,
                borderColor: 'rgba(25,135,84,1)',
                backgroundColor: 'rgba(25,135,84,0.2)',
                tension: 0.4,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } },
        scales: { y: { beginAtZero: true, ticks: { callback: value => '$' + value.toLocaleString() } } }
    }
});
</script>

<?= $this->include('layouts/footer') ?>
