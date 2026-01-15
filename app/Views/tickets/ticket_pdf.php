<style>
body { font-family: monospace; font-size: 10px; }
.line { border-top: 1px dashed #000; margin: 6px 0; }
.right { text-align: right; }
.center { text-align: center; }
</style>

<div class="center">
    <strong><?= $config['nombre_tienda'] ?></strong><br>
    <?= $config['direccion'] ?><br>
    Tel: <?= $config['telefono'] ?>
</div>

<div class="line"></div>

Folio: <?= $venta['folio'] ?><br>
Fecha: <?= date('d/m/Y H:i', strtotime($venta['fecha_venta'])) ?><br>

<div class="line"></div>

<?php foreach($detalles as $d): ?>
<?= $d['cantidad'] ?> <?= $d['producto_nombre'] ?>
<div class="right">$<?= number_format($d['subtotal'],2) ?></div>
<?php endforeach; ?>

<div class="line"></div>

<div class="right">
TOTAL: $<?= number_format($venta['total'],2) ?>
</div>
