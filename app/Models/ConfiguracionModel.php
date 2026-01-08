<?php
namespace App\Models;

use CodeIgniter\Model;

class ConfiguracionModel extends Model
{
    protected $table = 'configuracion';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nombre_tienda',
        'direccion', 
        'telefono', 
        'email', 
        'rfc', 
        'mensaje_ticket'
    ];
    protected $useTimestamps = false;
    
    protected $validationRules = [
        'nombre_tienda' => 'required|min_length[3]',
        'email' => 'valid_email'
    ];
    
    protected $validationMessages = [
        'nombre_tienda' => [
            'required' => 'El nombre de la tienda es obligatorio',
            'min_length' => 'El nombre debe tener al menos 3 caracteres'
        ],
        'email' => [
            'valid_email' => 'Debe ingresar un email válido'
        ]
    ];

    
    
}