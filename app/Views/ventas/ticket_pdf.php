<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Venta</title>
    <style>
        @page {
            margin: 0;
            size: 80mm auto;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            margin: 0;
            padding: 10px;
            width: 80mm;
            background: white;
        }
        
        .ticket {
            width: 100%;
            max-width: 80mm;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 16px;
            margin: 0;
            font-weight: bold;
        }
        
        .header p {
            margin: 2px 0;
            font-size: 10px;
        }
        
        .info {
            margin-bottom: 10px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10px;
        }
        
        .items-table th {
            text-align: left;
            border-bottom: 1px dashed #000;
            padding: 5px 0;
            font-weight: bold;
        }
        
        .items-table td {
            padding: 3px 0;
            border-bottom: 1px dashed #eee;
        }
        
        .items-table .quantity {
            text-align: center;
            width: 15%;
        }
        
        .items-table .price {
            text-align: right;
            width: 25%;
        }
        
        .totals {
            border-top: 1px dashed #000;
            padding-top: 10px;
            margin-bottom: 10px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .total-final {
            font-weight: bold;
            font-size: 14px;
        }
        
        .footer {
            text-align: center;
            margin-top: 15px;
            border-top: 1px dashed #000;
            padding-top: 10px;
            font-size: 10px;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <?php
            // Obtener configuración de la tienda
            $configModel = new \App\Models\ConfiguracionModel();
            $configuracion = $configModel->first();
            ?>
            <h1><?= esc($configuracion['nombre_tienda'] ?? 'MI TIENDA') ?></h1>
            <?php if (!empty($configuracion['direccion'])): ?>
                <p><?= esc($configuracion['direccion']) ?></p>
            <?php endif; ?>
            <?php if (!empty($configuracion['telefono'])): ?>
                <p>Tel: <?= esc($configuracion['telefono']) ?></p>
            <?php endif; ?>
            <?php if (!empty($configuracion['rfc'])): ?>
                <p>RFC: <?= esc($configuracion['rfc']) ?></p>
            <?php endif; ?>
        </div>

        <div class="info">
            <div class="info-row">
                <span><strong>Folio:</strong> <?= esc($venta['folio']) ?></span>
            </div>
            <div class="info-row">
                <span><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($venta['fecha_venta'])) ?></span>
            </div>
            <div class="info-row">
                <span><strong>Cliente:</strong> <?= esc($venta['cliente_nombre'] ?? 'Cliente general') ?></span>
            </div>
            <div class="info-row">
                <span><strong>Atendió:</strong> <?= session()->get('user_name') ?></span>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th class="quantity">Cant</th>
                    <th>Producto</th>
                    <th class="price">Importe</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($detalles as $detalle): ?>
                    <tr>
                        <td class="quantity"><?= $detalle['cantidad'] ?></td>
                        <td><?= esc($detalle['producto_nombre']) ?></td>
                        <td class="price">$<?= number_format($detalle['subtotal'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>$<?= number_format($venta['total'], 2) ?></span>
            </div>
            <div class="total-row">
                <span>Efectivo:</span>
                <span>$<?= number_format($venta['efectivo'], 2) ?></span>
            </div>
            <div class="total-row">
                <span>Cambio:</span>
                <span>$<?= number_format($venta['cambio'], 2) ?></span>
            </div>
            <div class="total-row total-final">
                <span>TOTAL:</span>
                <span>$<?= number_format($venta['total'], 2) ?></span>
            </div>
        </div>

        <div class="footer">
            <p><?= esc($configuracion['mensaje_ticket'] ?? '¡Gracias por su compra!') ?></p>
            <p>*** Venta <?= strtoupper($venta['estado']) ?> ***</p>
            <p>Powered by POS System</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
            setTimeout(function() {
                window.close();
            }, 500);
        };
    </script>
</body>
</html>