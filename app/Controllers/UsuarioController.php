<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;

class UsuarioController extends BaseController
{
    protected $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Vendedores',
            'usuarios' => $this->usuarioModel->where('rol', 'vendedor')->findAll()
        ];

        return view('usuarios/index', $data);
    }
    public function crear()
    {
        return view('usuarios/crear');
    }

    public function guardar()
    {
        $rules = $this->usuarioModel->getValidationRules();

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->usuarioModel->save([
            'nombre'   => $this->request->getPost('nombre'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'rol'      => 'vendedor', // siempre vendedor
            'activo'   => 1
        ]);

        return redirect()->to('/admin/usuarios')->with('success', 'Vendedor creado correctamente');
    }

    public function editar($id)
    {
        $usuario = $this->usuarioModel
                        ->where('id', $id)
                        ->where('rol', 'vendedor')
                        ->first();

        if (!$usuario) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Vendedor no encontrado');
        }

        return view('usuarios/editar', [
            'usuario' => $usuario
        ]);
    }

public function actualizar($id)
{
    $usuario = $this->usuarioModel->find($id);

    if (!$usuario) {
        return redirect()->to(base_url('admin/usuarios'))->with('error', 'Usuario no encontrado');
    }

    $emailRule = 'required|valid_email|is_unique[usuario.email,id,' . $id . ']';

    $validationRules = [
        'nombre' => 'required|min_length[3]',
        'email'  => $emailRule,
        'rol'    => 'required|in_list[admin,vendedor]'
    ];

    if ($this->request->getPost('password')) {
        $validationRules['password'] = 'permit_empty|min_length[6]';
    }

    if (!$this->validate($validationRules)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }

    $data = [
        'nombre' => $this->request->getPost('nombre'),
        'email'  => $this->request->getPost('email'),
        'rol'    => $this->request->getPost('rol'),
        'activo' => $this->request->getPost('activo')
    ];

    if ($this->request->getPost('password')) {
        $data['password'] = $this->request->getPost('password');
    }

    $this->usuarioModel->update($id, $data);

    return redirect()->to(base_url('admin/usuarios'))->with('success', 'Usuario actualizado correctamente');
}

    public function eliminar($id)
    {
        $usuario = $this->usuarioModel
                        ->where('id', $id)
                        ->where('rol', 'vendedor')
                        ->first();

        if (!$usuario) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Vendedor no encontrado');
        }

        $this->usuarioModel->delete($id);

        return redirect()->to('/admin/usuarios')->with('success', 'Vendedor eliminado correctamente');
    }


    public function guardarPago($id)
{
    $monto = $this->request->getPost('monto');
    $descripcion = $this->request->getPost('descripcion');

    $entradaModel = new \App\Models\EntradaModel();

    $entradaModel->save([
        'materia_id'     => 28,          
        'cantidad'       => 1,            
        'costo_unitario' => $monto,       
        'total'          => $monto,      
        'fecha'          => date('Y-m-d')
    ]);

    return redirect()->to('/admin/usuarios')->with('success', 'Pago registrado correctamente');
}

}
