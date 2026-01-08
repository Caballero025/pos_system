<?php
namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    // Nombre de tu tabla de clientes
    protected $table = 'clientes';
    protected $primaryKey = 'id';
    
    //  Campos permitidos
    protected $allowedFields = ['nombre'];
    
    protected $useTimestamps = false;
}