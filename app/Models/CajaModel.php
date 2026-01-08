<?php
namespace App\Models;

use CodeIgniter\Model;

class CajaModel extends Model
{
    protected $table = 'caja';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'fecha_apertura',
        'fecha_cierre',
        'monto_inicial',
        'monto_final',
        'usuario_id',
        'estado',
        'total_ventas',
        'created_at'
    ];
    protected $useTimestamps = false; // IMPORTANTE: Desactivado porque manejamos manualmente created_at
    
    protected $validationRules = [
        'monto_inicial' => 'required|decimal',
        'usuario_id' => 'required|integer'
    ];
}