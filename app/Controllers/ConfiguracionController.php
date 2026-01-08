<?php
namespace App\Controllers;

use App\Models\ConfiguracionModel;
use App\Models\UsuarioModel;

class ConfiguracionController extends BaseController
{
    protected $configuracionModel;
    protected $usuarioModel;

    public function __construct()
    {
        $this->configuracionModel = new ConfiguracionModel();
        $this->usuarioModel = new UsuarioModel();
    }

    public function index()
    {
        $this->checkLogin();
        
        $configuracion = $this->configuracionModel->first();
        $usuarios = $this->usuarioModel->findAll();
        
        $data = [
            'title' => 'Configuración',
            'configuracion' => $configuracion,
            'usuarios' => $usuarios
        ];
        
        return view('configuracion/index', $data);
    }

    public function guardarTienda()
    {
        $this->checkLogin();
        
        // DEPURACIÓN: Ver datos recibidos
        log_message('debug', 'Datos recibidos para guardar tienda: ' . print_r($this->request->getPost(), true));
        
        $data = [
            'nombre_tienda' => $this->request->getPost('nombre_tienda'),
            'direccion' => $this->request->getPost('direccion'),
            'telefono' => $this->request->getPost('telefono'),
            'email' => $this->request->getPost('email'),
            'rfc' => $this->request->getPost('rfc'),
            'mensaje_ticket' => $this->request->getPost('mensaje_ticket')
        ];
        
        // Verificar que la tabla existe
        try {
            $configExistente = $this->configuracionModel->first();
            
            log_message('debug', 'Configuración existente: ' . ($configExistente ? print_r($configExistente, true) : 'No hay'));
            
            if ($configExistente) {
                $result = $this->configuracionModel->update($configExistente['id'], $data);
                log_message('debug', 'Resultado de actualización: ' . ($result ? 'true' : 'false'));
            } else {
                $result = $this->configuracionModel->insert($data);
                log_message('debug', 'Resultado de inserción: ' . ($result ? 'true' : 'false'));
            }
            
            if ($result) {
                return redirect()->to('admin/configuracion')->with('success', 'Configuración guardada correctamente');
            } else {
                $errors = $this->configuracionModel->errors();
                log_message('error', 'Error al guardar configuración: ' . print_r($errors, true));
                return redirect()->to('admin/configuracion')->with('error', 'Error al guardar: ' . implode(', ', $errors));
            }
        } catch (\Exception $e) {
            log_message('error', 'Excepción al guardar configuración: ' . $e->getMessage());
            return redirect()->to('admin/configuracion')->with('error', 'Error: ' . $e->getMessage());
        }
    }

public function crearUsuario()
{
    $this->checkLogin();

 
    log_message('debug', 'Datos para crear usuario: ' . print_r($this->request->getPost(), true));

   
    $email = $this->request->getPost('email');
    $usuarioExistente = $this->usuarioModel->where('email', $email)->first();
    if ($usuarioExistente) {
        return redirect()->to('admin/configuracion')->with('error', 'El email ya está registrado');
    }

$password = trim($this->request->getPost('password'));

    $data = [
        'nombre'   => $this->request->getPost('nombre'),
        'email'    => $email,
        'password' => $password,
        'rol'      => $this->request->getPost('rol'),
        'activo'   => 1
    ];



    log_message('debug', 'Datos a insertar en usuario: ' . print_r($data, true));

    try {
        $result = $this->usuarioModel->insert($data);

        if ($result) {
            log_message('info', 'Usuario creado con ID: ' . $this->usuarioModel->getInsertID());
            return redirect()->to('admin/configuracion')->with('success', 'Usuario creado correctamente');
        } else {
            $errors = $this->usuarioModel->errors();
            log_message('error', 'Error al crear usuario: ' . print_r($errors, true));
            return redirect()->to('admin/configuracion')->with('error', 'Error al crear usuario: ' . implode(', ', $errors));
        }
    } catch (\Exception $e) {
        log_message('error', 'Excepción al crear usuario: ' . $e->getMessage());
        return redirect()->to('admin/configuracion')->with('error', 'Error: ' . $e->getMessage());
    }
}

}