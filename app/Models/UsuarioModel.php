<?php
namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table = 'usuario';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nombre',
        'email', 
        'password', 
        'rol', 
        'activo'
    ];
    protected $useTimestamps = true;
    
    // Especificar los nombres exactos de tus columnas timestamp
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at'; 
    
    protected $validationRules = [
        'nombre' => 'required|min_length[3]',
        'email' => 'required|valid_email|is_unique[usuario.email]',
        'password' => 'required|min_length[6]',
        'rol' => 'required|in_list[admin,vendedor]'
    ];
    
    // Encriptar password antes de insertar
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];
    
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }
}