<?php
namespace App\Models;

use CodeIgniter\Model;

class MateriaModel extends Model
{
    protected $table = 'materias_primas';
    protected $primaryKey = 'id';
    
    
protected $allowedFields = [
    'nombre',
    'categoria_id',
    'precio',
    'activo',
    'medida_id',
    'cantidad'
];    
    protected $useTimestamps = false;

    
    protected $validationRules = [

        'nombre' => 'required|min_length[3]|max_length[200]',
        'precio' => 'required|decimal',
    ];
    
    protected $validationMessages = [
  
        'nombre' => [
            'required' => 'El nombre del producto es obligatorio',
            'min_length' => 'El nombre debe tener al menos 3 caracteres',
            'max_length' => 'El nombre no puede exceder 200 caracteres'
        ],
        'precio' => [
            'required' => 'El precio es obligatorio',
            'decimal' => 'El precio debe ser un número decimal válido'
        ]
        
    ];
}