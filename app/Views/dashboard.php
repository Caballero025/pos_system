<?= $this->include('layouts/header') ?>

<?php
// Datos de ejemplo para las estadísticas
$stats = [
    'total_productos' => $total_productos ?? 0,
    'total_ventas' => $total_ventas ?? 0,
    'ventas_hoy' => $ventas_hoy ?? 0
];
?>

<!-- Estadísticas -->
<div class="stats">
    <div class="stat-card">
        <h3>Total Productos</h3>
        <div class="stat-number"><?= $stats['total_productos'] ?></div>
    </div>
    <div class="stat-card">
        <h3>Total Ventas</h3>
        <div class="stat-number"><?= $stats['total_ventas'] ?></div>
    </div>
    <div class="stat-card">
        <h3>Ventas Hoy</h3>
        <div class="stat-number"><?= $stats['ventas_hoy'] ?></div>
    </div>
    
</div>

<!-- Acciones Rápidas -->
<div class="quick-actions">
    <h2>Acciones Rápidas</h2>
    <?php if (session()->get('user_role') === 'admin'): ?>

    <div class="action-buttons">

        <button class="action-btn btn-primary" onclick="location.href='<?= base_url('admin/productos') ?>'">
            <span class="icon">📦</span> Gestionar Productos
        </button>
        <button class="action-btn btn-success" onclick="location.href='<?= base_url('ventas') ?>'">
            <span class="icon">💰</span> Nueva Venta
        </button>
        <button class="action-btn btn-warning" onclick="location.href='<?= base_url('admin/caja') ?>'">
            <span class="icon">💵</span> Caja Registradora
        </button>
        <button class="action-btn btn-info" onclick="location.href='<?= base_url('admin/reportes') ?>'">
            <span class="icon">📈</span> Ver Reportes
        </button>
    </div>
     <?php else: ?>
        <div class="action-buttons">
        <button class="action-btn btn-success" onclick="location.href='<?= base_url('ventas') ?>'">
            <span class="icon">💰</span> Nueva Venta
        </button>
      
    </div>
     <?php endif; ?>
</div>

</div><!-- content-area -->
</div><!-- main-content -->
</body>
</html>