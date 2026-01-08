<?= $this->include('layouts/header') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/reportes.css') ?>">

<div class="reportes-container">
    <div class="page-header">
        <h1>🏷️ Reporte de Productos</h1>
        <a href="<?= base_url('admin/reportes') ?>" class="btn">🔙 Volver a Reportes</a>
    </div>

    <!-- Métricas de productos -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="metric-card success">
                <div class="metric-label">Total Productos</div>
                <div class="metric-value"><?= count($productos) ?></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="metric-card primary">
                <div class="metric-label">Ingresos Totales</div>
                <div class="metric-value">$<?= number_format(array_sum(array_column($productos, 'total_ingresos')), 2) ?></div>
            </div>
        </div>
    </div>

    <!-- Tabla de productos -->
    <div class="card-reporte">
        <div class="card-header-reporte">
            📦 Productos por Rendimiento
        </div>
        <div class="card-body-reporte">
            <table class="tabla-reportes">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Código</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Vendidos</th>
                        <th>Ingresos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($productos as $producto): ?>
                        <tr>
                            <td><strong><?= $producto['nombre'] ?></strong></td>
                            <td><span class="badge-reporte badge-info"><?= $producto['codigo'] ?></span></td>
                            <td>$<?= number_format($producto['precio'], 2) ?></td>
                            <td>
                                <span class="badge-reporte <?= $producto['stock'] > 10 ? 'badge-success' : ($producto['stock'] > 0 ? 'badge-warning' : 'badge-danger') ?>">
                                    <?= $producto['stock'] ?>
                                </span>
                            </td>
                            <td><?= $producto['total_vendido'] ?? 0 ?></td>
                            <td style="color: #28a745; font-weight: bold;">$<?= number_format($producto['total_ingresos'] ?? 0, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->include('layouts/footer') ?>