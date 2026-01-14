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
            <div class="metric-value">$0.00</div>
            <div class="metric-detail">Brutos</div>
        </div>

        <div class="metric-card success animate">
            <div class="metric-label">Ganancias</div>
            <div class="metric-value">$0.00</div>
            <div class="metric-detail">Netas</div>
        </div>

        <div class="metric-card info animate">
            <div class="metric-label">Ventas</div>
            <div class="metric-value">$<?= count($ventas) > 0 ? number_format(array_sum(array_column($ventas, 'total')) / count($ventas), 2) : '0.00' ?></div>
            <div class="metric-detail">efectivo</div>
        </div>

        <div class="metric-card warning animate">
            <div class="metric-label">Cancelaciones</div>
            <div class="metric-value">0</div>
            <div class="metric-detail">Registros</div>
        </div>
    </div>

    <!-- GRÁFICAS -->
    <div class="row mt-4">
        <div class="card-reporte grafica animate">
            <div class="card-header-reporte">📈 Ventas por día</div>
            <canvas id="ventasDia"></canvas>
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
            <div class="item-reporte"><span>Hamburguesa</span><span>50</span></div>
            <div class="item-reporte"><span>Tacos</span><span>40</span></div>
        </div>

        <div class="card-reporte animate">
            <div class="card-header-reporte">🐢 Productos menos vendidos</div>
            <div class="item-reporte"><span>Ensalada</span><span>2</span></div>
            <div class="item-reporte"><span>Sopa</span><span>3</span></div>
        </div>

        <div class="card-reporte animate">
            <div class="card-header-reporte">💳 Métodos de pago</div>
            <div class="item-reporte"><span>Efectivo</span><span>$500</span></div>
            <div class="item-reporte"><span>Tarjeta</span><span>$700</span></div>
        </div>

        <div class="card-reporte animate">
            <div class="card-header-reporte">⏰ Horas pico</div>
            <div class="item-reporte"><span>2pm - 4pm</span><span>35 ventas</span></div>
            <div class="item-reporte"><span>7pm - 9pm</span><span>50 ventas</span></div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="card-reporte animate">
            <div class="card-header-reporte">👨‍🍳 Ventas por empleado</div>
            <div class="item-reporte"><span>Juan</span><span>$500</span></div>
            <div class="item-reporte"><span>Ana</span><span>$650</span></div>
        </div>

        <div class="card-reporte full animate">
            <div class="card-header-reporte">📦 Productos con bajo stock</div>
            <div class="item-reporte alert"><span>Coca Cola</span><span>2</span></div>
            <div class="item-reporte alert"><span>Papas</span><span>3</span></div>
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

<script>
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
}

new Chart(document.getElementById('ventasDia'), {
    type: 'line',
    data: {
        labels: ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'],
        datasets: [{
            label: 'Ventas',
            data: [10,25,40,30,50,70,60],
            tension: 0.4,
            fill: true
        }]
    }
});

new Chart(document.getElementById('ingresosGanancias'), {
    type: 'bar',
    data: {
        labels: ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'],
        datasets: [
            { label: 'Ingresos', data: [500,700,800,650,900,1200,1100] },
            { label: 'Ganancias', data: [200,300,350,280,400,600,550] }
        ]
    }
});
</script>

<?= $this->include('layouts/footer') ?>