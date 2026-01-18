<?= $this->include('layouts/header') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/reportes.css') ?>">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="reportes-container">
    <div class="filtros-reportes">
<form method="get" class="row">
    <!-- Año -->
    <div class="col">
        <label>Año</label>
        <select name="anio" id="anio" class="form-control-reporte">
            <?php 
                $anioInicio = 2020;
                $anioActual = date('Y');
                for($a = $anioInicio; $a <= $anioActual + 1; $a++): 
            ?>
                <option value="<?= $a ?>" <?= $anio == $a ? 'selected' : '' ?>><?= $a ?></option>
            <?php endfor; ?>
        </select>
    </div>

    <!-- Mes -->
    <div class="col">
        <label>Mes</label>
        <select name="mes" id="mes" class="form-control-reporte">
            <?php foreach($meses as $num => $nombre): ?>
                <option value="<?= $num ?>" <?= $mes == $num ? 'selected' : '' ?>><?= $nombre ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Semana -->
    <div class="col">
        <label>Semana</label>
        <select name="semana" id="semana" class="form-control-reporte">
            <!-- JS llenará las semanas automáticamente -->
        </select>
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
    <div class="metric-label">Inversión</div>
    <div class="metric-value">$<?= number_format($totalInversion, 2) ?></div>
    <div class="metric-detail">Efectivo</div>
</div>
        <div class="metric-card warning animate">
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
            <div class="metric-label">Promedio de ventas</div>
            <div class="metric-value">$<?= count($ventas) > 0 ? number_format(array_sum(array_column($ventas, 'total')) / count($ventas), 2) : '0.00' ?></div>
            <div class="metric-detail">Efectivo</div>
        </div>
 


    </div>

    <!-- GRÁFICAS -->
    <div class="row mt-4">
           <div class="card-reporte grafica animate">
        <div class="card-header-reporte">💸 Inversión vs Ingreso</div>
        <canvas id="inversionIngreso"></canvas>
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
        <div class="table-scroll">
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
                            <td><?= esc($p['cantidad_total']) ?></td>
                            <td>$<?= number_format($p['total_inversion'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
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
                    <a href="<?= base_url('admin/reportes/ventas')
    . '?anio=' . $anio
    . '&mes=' . $mes
    . '&semana=' . $semana ?>"
   class="btn-acceso">

                        📋 Reporte Detallado de Ventas
                    </a>
                    <a href="<?= base_url('admin/reportes/clientes')     . '?anio=' . $anio
    . '&mes=' . $mes
    . '&semana=' . $semana ?>"
   class="btn-acceso">
                        👥 Reporte de Clientes
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Función para calcular cuántas semanas tiene un mes (sin domingos)
function llenarSemanas() {
    const anio = parseInt(document.getElementById('anio').value);
    const mes  = parseInt(document.getElementById('mes').value);
    const selectSemana = document.getElementById('semana');

    // Si no encuentra el select, detener
    if (!selectSemana) {
        console.error("No se encontró el select de semanas");
        return;
    }

    selectSemana.innerHTML = '';

    const ultimoDia = new Date(anio, mes, 0).getDate();

    let semana = 1;
    let diasEnSemana = 0;

    for (let dia = 1; dia <= ultimoDia; dia++) {
        diasEnSemana++;

        if (diasEnSemana === 7) {
            semana++;
            diasEnSemana = 0;
        }
    }

    const totalSemanas = diasEnSemana > 0 ? semana : semana - 1;

    if (totalSemanas <= 0) {
        selectSemana.innerHTML = '<option value="">No hay semanas</option>';
        return;
    }

    // <-- AQUI tomamos la semana seleccionada desde PHP
    const semanaSeleccionada = <?= $semana ?? 1 ?>;

    for (let s = 1; s <= totalSemanas; s++) {
        const option = document.createElement('option');
        option.value = s;
        option.text = 'Semana ' + s;

        if (s == semanaSeleccionada) option.selected = true;
        selectSemana.appendChild(option);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    llenarSemanas();

    document.getElementById('anio').addEventListener('change', llenarSemanas);
    document.getElementById('mes').addEventListener('change', llenarSemanas);
});
// Inicializar al cargar la página
llenarSemanas();

// Actualizar cuando cambie año o mes
document.getElementById('anio').addEventListener('change', llenarSemanas);
document.getElementById('mes').addEventListener('change', llenarSemanas);
</script>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
new Chart(document.getElementById('inversionIngreso'), {
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
                label: 'Ingreso',
                data: <?= json_encode($ingresosDia) ?>,
                borderColor: 'rgba(54,162,235,1)',
                backgroundColor: 'rgba(54,162,235,0.2)',
                tension: 0.4,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { callback: value => '$' + value.toLocaleString() }
            }
        }
    }
});
</script>

<?= $this->include('layouts/footer') ?>
