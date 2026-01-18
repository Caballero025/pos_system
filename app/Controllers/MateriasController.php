<?php
namespace App\Controllers;

use App\Models\MateriaModel;
use App\Models\PrimaModel;
use App\Models\MedidaModel;
use App\Models\EntradaModel;
use App\Models\ProductoModel;


class MateriasController extends BaseController
{
    protected $materiaModel;
    protected $primaModel;
    protected $medidaModel;
    protected $entradaModel;
     protected $productoModel;

    public function __construct()
    {
        $this->materiaModel = new MateriaModel();
        $this->primaModel = new PrimaModel();
        $this->medidaModel = new MedidaModel();
        $this->entradaModel = new EntradaModel();
        $this->productoModel = new ProductoModel();
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
        'precio' => 'required|decimal',
        'cantidad' => 'required|integer'
    ];

    if (!$this->validate($rules)) {
        return redirect()->back()
            ->withInput()
            ->with('errors', $this->validator->getErrors());
    }

    $data = [
        'nombre'       => $this->request->getPost('nombre'),
        'precio'       => $this->request->getPost('precio'),
        'categoria_id' => $this->request->getPost('categoria_id'),
        'medida_id'    => $this->request->getPost('medida_id'),
        'cantidad'     => $this->request->getPost('cantidad'),
        'activo'       => 1
    ];

    $materiaId = $this->materiaModel->insert($data);

    // Registrar entrada
    $cantidad = $data['cantidad'];
    $costo    = $data['precio'];

    $this->entradaModel->insert([
        'materia_id'    => $materiaId,
        'cantidad'      => $cantidad,
        'costo_unitario'=> $costo,
        'total'         => $cantidad * $costo
    ]);

    // 🔥 Actualizar stock del producto
    $this->productoModel
        ->actualizarStockDesdeMateriaPrima($data['categoria_id']);

    return redirect()->to('admin/materias')
        ->with('success', 'Materia prima registrada correctamente');
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

    $materia = $this->materiaModel->find($id);
    if (!$materia) {
        return redirect()->back()->with('error', 'Materia no encontrada');
    }

    $cantidadNueva = (int) $this->request->getPost('cantidad');
    $precioNuevo   = (float) $this->request->getPost('precio');

    // Actualizar materia prima
    $this->materiaModel->update($id, [
        'nombre'       => $this->request->getPost('nombre'),
        'precio'       => $precioNuevo,
        'categoria_id' => $this->request->getPost('categoria_id'),
        'medida_id'    => $this->request->getPost('medida_id'),
        'cantidad'     => $cantidadNueva
    ]);

    // Actualizar entrada
    $entrada = $this->entradaModel
        ->where('materia_id', $id)
        ->orderBy('id', 'DESC')
        ->first();

    if ($entrada) {
        $this->entradaModel->update($entrada['id'], [
            'cantidad'       => $cantidadNueva,
            'costo_unitario' => $precioNuevo,
            'total'          => $cantidadNueva * $precioNuevo
        ]);
    }

    // 🔥 Recalcular stock del producto
    $this->productoModel
        ->actualizarStockDesdeMateriaPrima($materia['categoria_id']);

    return redirect()->to('admin/materias')
        ->with('success', 'Materia actualizada correctamente');
}


public function eliminar($id)
{
    $this->checkLogin();

    $materia = $this->materiaModel->find($id);
    if (!$materia) {
        return redirect()->to('admin/materias')
            ->with('error', 'Materia no encontrada');
    }

    $categoriaId = $materia['categoria_id'];

    $this->materiaModel->delete($id);

    // 🔥 Recalcular stock del producto
    $this->productoModel
        ->actualizarStockDesdeMateriaPrima($categoriaId);

    return redirect()->to('admin/materias')
        ->with('success', 'Materia eliminada correctamente');
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
