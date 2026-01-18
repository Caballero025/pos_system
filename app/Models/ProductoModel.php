<?php
namespace App\Models;

use CodeIgniter\Model;

class ProductoModel extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id';
    
    
protected $allowedFields = [
    'nombre',
    'descripcion',
    'precio',
    'costo',
    'stock',
    'categoria_id',
    'imagen',
    'activo',
    'medida_id'
];    
    protected $useTimestamps = false;

    
    protected $validationRules = [

        'nombre' => 'required|min_length[3]|max_length[200]',
        'precio' => 'required|decimal',
        'stock' => 'required|integer',
        'costo' => 'decimal',
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
        ],
        'stock' => [
            'required' => 'El stock es obligatorio',
            'integer' => 'El stock debe ser un número entero'
        ]
    ];


    
public function actualizarStockDesdeMateriaPrima($categoriaPrimaId)
{
    $materiaModel = new \App\Models\MateriaModel();
    $categoriaModel = new \App\Models\PrimaModel();

    // Obtener categoría prima
    $categoria = $categoriaModel->find($categoriaPrimaId);
    if (!$categoria) return false;

    // Sumar cantidades y calcular costo promedio
    $resultado = $materiaModel
        ->select('SUM(cantidad) AS stock_total, AVG(precio) AS costo_unitario')
        ->where('categoria_id', $categoriaPrimaId)
        ->first();

    $stock = (int) ($resultado['stock_total'] ?? 0);
    $costo = (float) ($resultado['costo_unitario'] ?? 0);

    // Actualizar producto que tenga el mismo nombre
    return $this->where('nombre', $categoria['nombre'])
                ->set([
                    'stock' => $stock,
                    'costo' => $costo
                ])
                ->update();
}

}
