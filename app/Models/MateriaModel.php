<?php
namespace App\Models;

use CodeIgniter\Model;

class MateriaModel extends Model
{
    protected $table = 'materias_primas';
    protected $primaryKey = 'id';
    
    protected $afterInsert = ['syncStock'];
protected $afterUpdate = ['syncStock'];
protected $afterDelete = ['syncStock'];

protected function syncStock(array $data)
{
    if (!isset($data['data']['categoria_id'])) {
        return $data;
    }

    $categoriaId = $data['data']['categoria_id'];

    $resultado = $this->select('SUM(cantidad) AS stock_total, AVG(precio) AS costo_unitario')
                      ->where('categoria_id', $categoriaId)
                      ->first();

    $productoModel = new \App\Models\ProductoModel();

    $productoModel->where('categoria_id', $categoriaId)
                  ->set([
                      'stock' => (int) ($resultado['stock_total'] ?? 0),
                      'costo' => (float) ($resultado['costo_unitario'] ?? 0),
                  ])
                  ->update();

    return $data;
}

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