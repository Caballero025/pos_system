<?php
namespace App\Models;

use CodeIgniter\Model;

class VentaModel extends Model
{
    //  Nombre de tu tabla de ventas
    protected $table = 'ventas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['folio', 'cliente_id', 'usuario_id', 'total', 'efectivo', 'cambio', 'estado', 'fecha_venta'];
    
    protected $useTimestamps = false;
    
    //  Reglas de validación según necesites
    protected $validationRules = [
        'folio' => 'required',
        'usuario_id' => 'required',
        'total' => 'required|numeric',
        'efectivo' => 'required|numeric',
        'cambio' => 'required|numeric'
    ];
}