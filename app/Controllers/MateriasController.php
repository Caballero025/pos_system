<?php
namespace App\Controllers;

use App\Models\MateriaModel;
use App\Models\PrimaModel;
use App\Models\MedidaModel;
use App\Models\EntradaModel;


class MateriasController extends BaseController
{
    protected $materiaModel;
    protected $primaModel;
    protected $medidaModel;
    protected $entradaModel;

    public function __construct()
    {
        $this->materiaModel = new MateriaModel();
        $this->primaModel = new PrimaModel();
        $this->medidaModel = new MedidaModel();
        $this->entradaModel = new EntradaModel();
    }

public function materias()
{
    $this->checkLogin();

 $materias = $this->materiaModel
    ->select('materias_primas.*, categorias_prima.nombre AS categoria_nombre, unidades_medida.nombre AS medida_nombre')
    ->join('categorias_prima', 'categorias_prima.id = materias_primas.categoria_id', 'left')
    ->join('unidades_medida', 'unidades_medida.id = materias_primas.medida_id','left')
    ->findAll();

    return view('materias/materias_primas', [
        'materias' => $materias
    ]);
}


    public function crear()
    {
        $this->checkLogin();

        $categorias = $this->primaModel->findAll();
        $medidas = $this->medidaModel->findAll();


        $data = [
            'title' => 'Agregar Producto',
            'categorias' => $categorias,
            'medidas' =>  $medidas,
        ];

        return view('materias/crear', $data);
    }

public function guardar()
{
    $this->checkLogin();

    $rules = [
        'nombre' => 'required',
        'precio' => 'required|decimal'
    ];

    if (!$this->validate($rules)) {
        return redirect()->back()
            ->withInput()
            ->with('errors', $this->validator->getErrors());
    }
    
  

    $productoData = [
        'nombre' => $this->request->getPost('nombre'),
        'precio' => $this->request->getPost('precio'),
        'categoria_id' => $this->request->getPost('categoria_id'),
        'medida_id' => $this->request->getPost('medida_id'),
        'activo' => 1,
        'cantidad' => $this->request->getPost('cantidad'),

    ];

    $id = $this->materiaModel->insert($productoData);

    if (!$id) {
        dd($this->materiaModel->errors());
    }

    return redirect()->to('admin/materias')
        ->with('success', 'Producto agregado correctamente');
}


    public function editar($id)
    {
        $this->checkLogin();
        $materia = $this->materiaModel->find($id);
        $categorias = $this->primaModel->findAll();
        $medidas = $this->medidaModel->findAll();

        if (!$materia) {
            return redirect()->to('admin/materias')->with('error', 'Producto no encontrado');
        }

        $data = [
            'title' => 'Editar Materia',
            'materia' => $materia,
            'categorias' => $categorias,
            'medidas' =>  $medidas,
        ];

        return view('materias/editar', $data);
    }

public function actualizar($id)
{
    $this->checkLogin();

    $cantidadNueva = (int) $this->request->getPost('cantidad');
    $costoUnitarioNuevo = (float) $this->request->getPost('precio');

    $entrada = $this->entradaModel
        ->where('materia_id', $id)
        ->orderBy('id', 'DESC')
        ->first();

    if (!$entrada) {
        return redirect()->back()
            ->with('error', 'No existe entrada para esta materia prima');
    }

    $diferencia = $cantidadNueva - $entrada['cantidad'];

    $this->materiaModel->update($id, [
        'nombre' => $this->request->getPost('nombre'),
        'precio' => $costoUnitarioNuevo,
        'categoria_id' => $this->request->getPost('categoria_id'),
        'medida_id' => $this->request->getPost('medida_id'),
    ]);

    $this->entradaModel->update($entrada['id'], [
        'cantidad' => $cantidadNueva,
        'costo_unitario' => $costoUnitarioNuevo,
        'total' => $cantidadNueva * $costoUnitarioNuevo
    ]);

    $this->materiaModel
        ->set('cantidad', 'cantidad + ' . $diferencia, false)
        ->where('id', $id)
        ->update();

    return redirect()->to('admin/materias')
        ->with('success', 'Materia actualizada correctamente');
}

public function eliminar($id)
{
    $this->checkLogin();

    $materia = $this->materiaModel->find($id);

    if (!$materia) {
        return redirect()->to('admin/materias')
            ->with('error', 'Producto no encontrado');
    }

    $this->materiaModel->delete($id);

    return redirect()->to('admin/materias')
        ->with('success', 'Producto eliminado correctamente');
}
public function obtenerPorCategoria($categoriaId)
{
    // Buscar materias primas activas de esa categoría
    $materias = $this->materiaModel
        ->where('categoria_id', $categoriaId)
        ->where('activo', 1)
        ->findAll();

    return $this->response->setJSON($materias);
}

}
