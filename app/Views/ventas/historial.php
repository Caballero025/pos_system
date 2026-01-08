<?= $this->include('layouts/header') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/historial.css') ?>">

<div class="page-header">
    <h1>Historial de Ventas</h1>
    <a href="<?= base_url('ventas') ?>" class="btn btn-primary">🛒 Nueva Venta</a>
</div>

<!-- Filtros -->
<div class="filters-card">
    <form method="get" action="<?= base_url('ventas/historial') ?>" class="filter-form">
        <div class="filter-row">
            <div class="filter-group">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" value="<?= esc($fecha) ?>">
            </div>
            <div class="filter-group">
                <label class="form-label">Folio</label>
                <input type="text" name="folio" class="form-control" value="<?= esc($folio) ?>" placeholder="Buscar por folio...">
            </div>
            <div class="filter-group">
                <button type="submit" class="btn btn-search">🔍 Buscar</button>
                <a href="<?= base_url('ventas/historial') ?>" class="btn btn-clear">🔄 Limpiar</a>
            </div>
        </div>
    </form>
</div>

<!-- Mensajes -->
<?php if (session('success')): ?>
    <div class="alert alert-success">
        <?= session('success') ?>
    </div>
<?php endif; ?>

<?php if (session('error')): ?>
    <div class="alert alert-error">
        <?= session('error') ?>
    </div>
<?php endif; ?>

<!-- Tabla de Ventas -->
<div class="table-card">
    <?php if (!empty($ventas)): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Efectivo</th>
                    <th>Cambio</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($ventas as $venta): ?>
                    <tr>
                        <td><strong><?= esc($venta['folio']) ?></strong></td>
                        <td><?= date('d/m/Y H:i', strtotime($venta['fecha_venta'])) ?></td>
                        <td><?= esc($venta['cliente_nombre'] ?? 'Cliente general') ?></td>
                        <td>$<?= number_format($venta['total'], 2) ?></td>
                        <td>$<?= number_format($venta['efectivo'], 2) ?></td>
                        <td>$<?= number_format($venta['cambio'], 2) ?></td>
                        <td>
                            <span class="status-badge <?= $venta['estado'] == 'completada' ? 'status-active' : 'status-inactive' ?>">
                                <?= ucfirst($venta['estado']) ?>
                            </span>
                        </td>
                        <td class="actions">
                            <a href="<?= base_url('ventas/detalle/' . $venta['id']) ?>" class="btn btn-info">👁️ Ver</a>
                            <a href="<?= base_url('ventas/imprimir/' . $venta['id']) ?>" class="btn btn-secondary" target="_blank">🖨️ Ticket</a>
                            <?php if($venta['estado'] == 'completada'): ?>
                                <a href="<?= base_url('ventas/cancelar/' . $venta['id']) ?>" class="btn btn-delete" onclick="return confirm('¿Estás seguro de cancelar esta venta? Se restaurará el stock.')">❌ Cancelar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <p>No se encontraron ventas</p>
        </div>
    <?php endif; ?>
</div>

</div><!-- content-area -->
</div><!-- main-content -->
</body>
</html>