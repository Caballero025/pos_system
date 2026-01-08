<?php
namespace App\Models;

use CodeIgniter\Model;

class ProductoModel extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id';
    
    
protected $allowedFields = [
    'codigo',
    'nombre',
    'descripcion',
    'precio',
    'costo',
    'stock',
    'stock_minimo',
    'categoria_id',
    'imagen',
    'activo'
];    
    protected $useTimestamps = false;

    
    protected $validationRules = [
        'codigo' => 'required|is_unique[productos.codigo,id,{id}]',
        'nombre' => 'required|min_length[3]|max_length[200]',
        'precio' => 'required|decimal',
        'stock' => 'required|integer',
        'costo' => 'decimal',
        'stock_minimo' => 'integer'
    ];
    
    protected $validationMessages = [
        'codigo' => [
            'required' => 'El código del producto es obligatorio',
            'is_unique' => 'Este código ya está registrado'
        ],
        'nombre' => [
            'required' => 'El nombre del producto es obligatorio',
            'min_length' => 'El nombre debe tener al menos 3 caracteres',
            'max_length' => 'El nombre no puede exceder 200 caracteres'
        ],
        'precio' => [
            'required' => 'El precio es obligatorio',
            'decimal' => 'El precio debe ser un número decimal válido'
        ],
        'stock' => [
            'required' => 'El stock es obligatorio',
            'integer' => 'El stock debe ser un número entero'
        ]
    ];
}