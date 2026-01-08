<?= $this->include('layouts/header') ?>

<link rel="stylesheet" href="<?= base_url('assets/css/caja.css') ?>">

<div class="page-header">
    <h1>💰 Caja Registradora</h1>
    <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">🏠 Inicio</a>
</div>

<!-- Mensaje de debug (puedes quitar esto después) -->
<?php if (isset($_GET['debug'])): ?>
<div class="alert alert-info">
    <h5>📊 Información de Debug:</h5>
    <p><strong>User ID en sesión:</strong> <?= session()->get('user_id') ?? 'No encontrado' ?></p>
    <p><strong>Fecha actual:</strong> <?= date('Y-m-d H:i:s') ?></p>
</div>
<?php endif; ?>

<?php if (session('success')): ?>
    <div class="alert alert-success"><?= session('success') ?></div>
<?php endif; ?>

<?php if (session('error')): ?>
    <div class="alert alert-danger"><?= session('error') ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Estado de Caja</h3>
            </div>
            <div class="card-body">
                <?php if ($caja_abierta): ?>
                    <div class="alert alert-success">
                        <h4>✅ Caja Abierta</h4>
                        <p><strong>Fecha apertura:</strong> <?= $caja_abierta['fecha_apertura'] ?></p>
                        <p><strong>Monto inicial:</strong> $<?= number_format($caja_abierta['monto_inicial'], 2) ?></p>
                        <p><strong>ID de caja:</strong> <?= $caja_abierta['id'] ?></p>
                    </div>
                    
                    <form action="<?= base_url('admin/caja/cerrar') ?>" method="post">
                        <button type="submit" class="btn btn-danger">🔒 Cerrar Caja</button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <h4>❌ Caja Cerrada</h4>
                        <p>La caja no está abierta actualmente</p>
                    </div>
                    
                    <form action="<?= base_url('admin/caja/abrir') ?>" method="post">
                        <div class="form-group">
                            <label>Monto Inicial:</label>
                            <input type="number" name="monto_inicial" class="form-control" step="0.01" min="0" value="0.00" required>
                        </div>
                        <button type="submit" class="btn btn-success">🔓 Abrir Caja</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Resumen del Día</h3>
            </div>
            <div class="card-body">
                <p><strong>Ventas hoy:</strong> <?= count($ventas_hoy) ?></p>
                <p><strong>Total vendido:</strong> $<?= number_format(array_sum(array_column($ventas_hoy, 'total')), 2) ?></p>
                <p><strong>Hora actual:</strong> <?= date('H:i:s') ?></p>
            </div>
        </div>
        
        <?php if ($caja_abierta): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h3>Movimientos</h3>
            </div>
            <div class="card-body">
                <form action="<?= base_url('admin/caja/movimiento') ?>" method="post">
                    <div class="form-group">
                        <label>Tipo:</label>
                        <select name="tipo" class="form-control" required>
                            <option value="ingreso">Ingreso</option>
                            <option value="retiro">Retiro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Monto:</label>
                        <input type="number" name="monto" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Concepto:</label>
                        <input type="text" name="concepto" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Registrar Movimiento</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->include('layouts/footer') ?>