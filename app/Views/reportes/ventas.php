<?= $this->include('layouts/header') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/reportes.css') ?>">

<div class="reportes-container">
    <div class="page-header">
        <h1>📋 Reporte Detallado de Ventas</h1>
        <a href="<?= base_url('admin/reportes') ?>" class="btn">🔙 Volver a Reportes</a>
    </div>

    <!-- Filtros -->
    <div class="filtros-reportes">
        <h3 style="margin-bottom: 20px; color: #333;">Filtrar Ventas</h3>
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
                    🔍 Filtrar Ventas
                </button>
            </div>
        </form>
    </div>

    <!-- Resumen -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="metric-card success">
                <div class="metric-label">Total Ventas</div>
                <div class="metric-value">$<?= number_format(array_sum(array_column($ventas, 'total')), 2) ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card info">
                <div class="metric-label">Total Ventas</div>
                <div class="metric-value"><?= count($ventas) ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card primary">
                <div class="metric-label">Promedio por Venta</div>
                <div class="metric-value">$<?= count($ventas) > 0 ? number_format(array_sum(array_column($ventas, 'total')) / count($ventas), 2) : '0.00' ?></div>
            </div>
        </div>
    </div>

    <!-- Tabla de ventas -->
    <div class="card-reporte">
        <div class="card-header-reporte">
            📊 Listado de Ventas
        </div>
        <div class="card-body-reporte">
            <table class="tabla-reportes">
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Efectivo</th>
                        <th>Cambio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($ventas as $venta): ?>
                        <tr>
                            <td><strong><?= $venta['folio'] ?></strong></td>
                            <td><?= date('d/m/Y H:i', strtotime($venta['fecha_venta'])) ?></td>
                            <td><?= $venta['cliente_nombre'] ?? 'Cliente general' ?></td>
                            <td style="color: #28a745; font-weight: bold;">$<?= number_format($venta['total'], 2) ?></td>
                            <td>$<?= number_format($venta['efectivo'], 2) ?></td>
                            <td>$<?= number_format($venta['cambio'], 2) ?></td>
                            <td>
                                <a href="<?= base_url('ventas/detalle/' . $venta['id']) ?>" 
                                   class="btn-reporte" 
                                   style="padding: 5px 10px; font-size: 12px; background: #17a2b8; color: white; border: none; text-align: center;">
                                    👁️ Ver
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->include('layouts/footer') ?>