<?php
namespace App\Controllers;

use App\Models\ClienteModel;

class ClientesController extends BaseController
{
    protected $clienteModel;

    public function __construct()
    {
        $this->clienteModel = new ClienteModel();
    }

    // Listar clientes
    public function index()
    {
        $this->checkLogin();
        
        $clientes = $this->clienteModel->where('activo', 1)->findAll();
        
        $data = [
            'title' => 'Gestión de Clientes',
            'clientes' => $clientes
        ];
        
        return view('admin/clientes/index', $data);
    }

    // Formulario para crear cliente
    public function crear()
    {
        $this->checkLogin();
        
        $data = [
            'title' => 'Crear Cliente'
        ];
        
        return view('admin/clientes/crear', $data);
    }

    // Guardar cliente
    public function guardar()
    {
        $this->checkLogin();
        
        $data = [
            'nombre'    => $this->request->getPost('nombre'),
            'telefono'  => $this->request->getPost('telefono'),
            'direccion' => $this->request->getPost('direccion'),
            'email'     => $this->request->getPost('email'),
            'rfc'       => $this->request->getPost('rfc'),
            'activo'    => 1
        ];
        
        if ($this->clienteModel->insert($data)) {
            return redirect()->to('admin//clientes')->with('success', 'Cliente creado correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->clienteModel->errors());
        }
    }

    // Formulario para editar cliente
    public function editar($id)
    {
        $this->checkLogin();
        
        $cliente = $this->clienteModel->find($id);
        
        if (!$cliente) {
            return redirect()->to('admin//clientes')->with('error', 'Cliente no encontrado');
        }
        
        $data = [
            'title' => 'Editar Cliente',
            'cliente' => $cliente
        ];
        
        return view('admin/clientes/editar', $data);
    }

    // Actualizar cliente
    public function actualizar($id)
    {
        $this->checkLogin();
        
        $data = [
            'nombre'    => $this->request->getPost('nombre'),
            'telefono'  => $this->request->getPost('telefono'),
            'direccion' => $this->request->getPost('direccion'),
            'email'     => $this->request->getPost('email'),
            'rfc'       => $this->request->getPost('rfc')
        ];
        
        if ($this->clienteModel->update($id, $data)) {
            return redirect()->to('admin//clientes')->with('success', 'Cliente actualizado correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->clienteModel->errors());
        }
    }

    // Eliminar cliente (borrado lógico)
    public function eliminar($id)
    {
        $this->checkLogin();
        
        if ($this->clienteModel->update($id, ['activo' => 0])) {
            return redirect()->to('admin//clientes')->with('success', 'Cliente eliminado correctamente');
        } else {
            return redirect()->to('admin//clientes')->with('error', 'Error al eliminar el cliente');
        }
    }

    // Buscar cliente por nombre o teléfono (para autocompletar en ventas)
    public function buscar()
    {
        $this->checkLogin();
        
        $term = $this->request->getGet('term');
        
        $clientes = $this->clienteModel->select('id, nombre, telefono')
                                     ->like('nombre', $term)
                                     ->orLike('telefono', $term)
                                     ->where('activo', 1)
                                     ->findAll();
        
        return $this->response->setJSON($clientes);
    }
}