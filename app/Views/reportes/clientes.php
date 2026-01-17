<?= $this->include('layouts/header') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/reportes.css') ?>">

<div class="reportes-container">
    <div class="page-header">
        <h1>👥 Reporte de Clientes</h1>
               <a href="<?= base_url('admin/reportes')
    . '?anio=' . $anio
    . '&mes=' . $mes
    . '&semana=' . $semana ?>"
   class="btn btn-secondary">
    🔙 Volver a Reportes
</a>
    </div>

    <!-- Métricas de clientes -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="metric-card primary">
                <div class="metric-label">Total Clientes</div>
                <div class="metric-value"><?= count($clientes) ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card success">
                <div class="metric-label">Total Gastado</div>
                <div class="metric-value">$<?= number_format(array_sum(array_column($clientes, 'total_gastado')), 2) ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card info">
                <div class="metric-label">Compras Totales</div>
                <div class="metric-value"><?= array_sum(array_column($clientes, 'total_compras')) ?></div>
            </div>
        </div>
    </div>

    <!-- Tabla de clientes -->
    <div class="card-reporte">
        <div class="card-header-reporte">
            🏆 Clientes por Consumo
        </div>
        <div class="card-body-reporte">
            <table class="tabla-reportes">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Total Compras</th>
                        <th>Total Gastado</th>
                        <th>Gasto Promedio</th>
                        <th>Valoración</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($clientes as $cliente): ?>
                        <tr>
                            <td><strong><?= $cliente['nombre'] ?></strong></td>
                            <td>
                                <span class="badge-reporte badge-primary"><?= $cliente['total_compras'] ?></span>
                            </td>
                            <td style="color: #28a745; font-weight: bold;">$<?= number_format($cliente['total_gastado'] ?? 0, 2) ?></td>
                            <td>$<?= $cliente['total_compras'] > 0 ? number_format($cliente['total_gastado'] / $cliente['total_compras'], 2) : '0.00' ?></td>
                            <td>
                                <?php if($cliente['total_gastado'] > 10000): ?>
                                    <span class="badge-reporte badge-success">💎 VIP</span>
                                <?php elseif($cliente['total_gastado'] > 5000): ?>
                                    <span class="badge-reporte badge-info">⭐ Frecuente</span>
                                <?php else: ?>
                                    <span class="badge-reporte badge-warning">👤 Básico</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->include('layouts/footer') ?>