<?= $this->include('layouts/header') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/historial.css') ?>">

<div class="page-header">
    <h1>Detalle de Venta</h1>
    <div style="display: flex; gap: 10px;">
        <a href="<?= base_url('ventas/historial') ?>" class="btn btn-cancel">← Volver al Historial</a>
        <a href="<?= base_url('ventas/imprimir/' . $venta['id']) ?>" class="btn btn-secondary" target="_blank">🖨️ Imprimir Ticket</a>
        <?php if($venta['estado'] == 'completada'): ?>
            <a href="<?= base_url('ventas/cancelar/' . $venta['id']) ?>" class="btn btn-delete" onclick="return confirm('¿Estás seguro de cancelar esta venta? Se restaurará el stock.')">❌ Cancelar Venta</a>
        <?php endif; ?>
    </div>
</div>

<div class="details-container">
    <!-- Información de la Venta -->
    <div class="info-card">
        <h3>Información de la Venta</h3>
        <div class="info-grid">
            <div class="info-item">
                <strong>Folio:</strong> <?= esc($venta['folio']) ?>
            </div>
            <div class="info-item">
                <strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($venta['fecha_venta'])) ?>
            </div>
            <div class="info-item">
                <strong>Cliente:</strong> <?= esc($venta['cliente_nombre'] ?? 'Cliente general') ?>
            </div>
            <?php if($venta['telefono']): ?>
            <div class="info-item">
                <strong>Teléfono:</strong> <?= esc($venta['telefono']) ?>
            </div>
            <?php endif; ?>
            <?php if($venta['direccion']): ?>
            <div class="info-item">
                <strong>Dirección:</strong> <?= esc($venta['direccion']) ?>
            </div>
            <?php endif; ?>
            <div class="info-item">
                <strong>Estado:</strong> 
                <span class="status-badge <?= $venta['estado'] == 'completada' ? 'status-active' : 'status-inactive' ?>">
                    <?= ucfirst($venta['estado']) ?>
                </span>
            </div>
            <div class="info-item">
                <strong>Total:</strong> $<?= number_format($venta['total'], 2) ?>
            </div>
            <div class="info-item">
                <strong>Efectivo:</strong> $<?= number_format($venta['efectivo'], 2) ?>
            </div>
            <div class="info-item">
                <strong>Cambio:</strong> $<?= number_format($venta['cambio'], 2) ?>
            </div>
        </div>
    </div>

    <!-- Productos Vendidos -->
    <div class="products-card">
        <h3>Productos Vendidos</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Código</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($detalles as $detalle): ?>
                    <tr>
                        <td><?= esc($detalle['producto_nombre']) ?></td>
                        <td><?= esc($detalle['producto_codigo']) ?></td>
                        <td><?= $detalle['cantidad'] ?></td>
                        <td>$<?= number_format($detalle['precio_unitario'], 2) ?></td>
                        <td>$<?= number_format($detalle['subtotal'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right; font-weight: bold;">Total:</td>
                    <td style="font-weight: bold;">$<?= number_format($venta['total'], 2) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

</div><!-- content-area -->
</div><!-- main-content -->
</body>
</html>