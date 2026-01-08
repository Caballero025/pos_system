<?= $this->include('layouts/header') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/reportes.css') ?>">

<div class="reportes-container">
    <div class="page-header">
        <h1>📊 Reportes del Sistema</h1>
        <a href="<?= base_url('dashboard') ?>" class="btn">🏠 Inicio</a>
    </div>

    <!-- Filtros -->
    <div class="filtros-reportes">
        <h3 style="margin-bottom: 20px; color: #333;">Filtros de Reportes</h3>
        <form method="get" class="row">
            <div class="col-md-4">
                <label style="display: block; margin-bottom: 5px; color: #666;">Fecha Inicio:</label>
                <input type="date" name="fecha_inicio" class="form-control-reporte" value="<?= $fecha_inicio ?>">
            </div>
            <div class="col-md-4">
                <label style="display: block; margin-bottom: 5px; color: #666;">Fecha Fin:</label>
                <input type="date" name="fecha_fin" class="form-control-reporte" value="<?= $fecha_fin ?>">
            </div>
            <div class="col-md-4">
                <label style="display: block; margin-bottom: 5px; color: transparent;">Filtrar</label>
                <button type="submit" class="btn-reporte" style="text-align: center; background: #667eea; color: white; border: none;">
                    🔍 Filtrar Reportes
                </button>
            </div>
        </form>
    </div>

    <!-- Métricas Principales -->
    <div class="row">
        <div class="col-md-4">
            <div class="metric-card primary">
                <div class="metric-label">Total Ventas</div>
                <div class="metric-value">$<?= number_format(array_sum(array_column($ventas, 'total')), 2) ?></div>
                <div class="metric-detail"><?= count($ventas) ?> ventas</div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="metric-card success">
                <div class="metric-label">Productos Vendidos</div>
                <div class="metric-value"><?= count($productos_vendidos) ?></div>
                <div class="metric-detail">En el período</div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="metric-card info">
                <div class="metric-label">Venta Promedio</div>
                <div class="metric-value">$<?= count($ventas) > 0 ? number_format(array_sum(array_column($ventas, 'total')) / count($ventas), 2) : '0.00' ?></div>
                <div class="metric-detail">Por transacción</div>
            </div>
        </div>
    </div>

    <!-- Reportes Específicos -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card-reporte">
                <div class="card-header-reporte">
                    📈 Productos Más Vendidos
                </div>
                <div class="card-body-reporte">
                    <div class="lista-reportes">
                        <?php foreach(array_slice($productos_vendidos, 0, 5) as $producto): ?>
                            <div class="item-reporte">
                                <strong style="color: #333;"><?= $producto['nombre'] ?></strong>
                                <span class="badge-reporte badge-success"><?= $producto['total_vendido'] ?? 0 ?> vendidos</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card-reporte">
                <div class="card-header-reporte">
                    🔗 Acceso Rápido
                </div>
                <div class="card-body-reporte">
                    <a href="<?= base_url('admin/reportes/ventas') . '?fecha_inicio=' . $fecha_inicio . '&fecha_fin=' . $fecha_fin ?>" 
                       class="btn-reporte">
                        📋 Reporte Detallado de Ventas
                    </a>
                    <a href="<?= base_url('admin/reportes/productos') ?>" class="btn-reporte">
                        🏷️ Reporte de Productos
                    </a>
                    <a href="<?= base_url('admin/reportes/clientes') ?>" class="btn-reporte">
                        👥 Reporte de Clientes
                    </a> 
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('layouts/footer') ?>