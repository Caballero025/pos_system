<?php
namespace App\Models;

use CodeIgniter\Model;

class DetalleVentaModel extends Model
{
    protected $table = 'detalle_ventas';
    protected $primaryKey = 'id';
    
    protected $allowedFields = ['venta_id', 'producto_id', 'cantidad', 'precio_unitario', 'subtotal','nombre'];
    
    protected $useTimestamps = false;
}