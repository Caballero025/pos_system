<?php
namespace App\Models;

use CodeIgniter\Model;

class EntradaModel extends Model
{
    protected $table = 'materia_entradas';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'materia_id',
        'cantidad',
        'costo_unitario',
        'total',
        'fecha'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'materia_id'     => 'required|integer',
        'cantidad'       => 'required|decimal',
        'costo_unitario' => 'required|decimal',
        'total'          => 'required|decimal',
    ];
}
