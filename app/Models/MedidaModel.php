<?php
namespace App\Models;

use CodeIgniter\Model;

class MedidaModel extends Model
{
    protected $table = 'unidades_medida';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nombre', 'activo'];

    
    protected $validationRules = [
        'nombre' => 'required|min_length[3]|max_length[100]'
    ];
    
    protected $validationMessages = [
        'nombre' => [
            'required' => 'El nombre de la categoría es obligatorio',
            'min_length' => 'El nombre debe tener al menos 3 caracteres',
            'max_length' => 'El nombre no puede exceder 100 caracteres'
        ]
    ];
}