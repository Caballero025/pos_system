<?php
namespace App\Controllers;

use App\Models\ProductoModel;
use App\Models\CategoriaModel;
use App\Models\DetalleVentaModel;
use App\Models\MedidaModel;
use App\Models\MateriaModel;
use App\Models\PrimaModel;


class ProductosController extends BaseController
{
    protected $productoModel;
    protected $categoriaModel;
    protected $medidaModel;
    protected $materiaModel;
    protected $primaModel;


    public function __construct()
    {
        $this->productoModel = new ProductoModel();
        $this->categoriaModel = new CategoriaModel();
        $this->medidaModel = new MedidaModel();
          $this->materiaModel = new MateriaModel();
            $this->primaModel = new PrimaModel();

    }

    public function index()
    {
     
        return view('productos/index');
    }

    public function productos($categoria_id = null)
{
    $this->checkLogin();

    $search = null; 

    $builder = $this->productoModel
        ->select('productos.*, categorias.nombre AS categoria_nombre, unidades_medida.nombre AS medida_nombre')
        ->join('categorias', 'categorias.id = productos.categoria_id', 'left')
         ->join('unidades_medida', 'unidades_medida.id = productos.medida_id', 'left');
 
    if ($categoria_id) {
        $builder->where('productos.categoria_id', $categoria_id);
    }

    $productos = $builder->findAll();
    $categorias = $this->categoriaModel->findAll();

    return view('productos/productos', [
        'productos'   => $productos,
        'categorias'  => $categorias,
        'search'      => $search,      
        'categoria_id'=> $categoria_id
    ]);
}


    public function crear()
    {
        $this->checkLogin();
        $categoria_id = $this->request->getGet('categoria_id');
        $categorias = $this->categoriaModel->findAll();
        $medidas = $this->medidaModel->findAll();

        $data = [
            'title' => 'Agregar Producto',
            'categorias' => $categorias,
            'categoria_id' => $categoria_id,
            'medidas' =>  $medidas,
        ];

        return view('productos/crear', $data);
    }

public function guardar()
{
    $this->checkLogin();

    $categoria_id = $this->request->getPost('categoria_id');
    $nombre       = $this->request->getPost('nombre');

    // ───────────────────────────────
    // VALIDACIÓN (SIN STOCK)
    // ───────────────────────────────
    $rules = [
        'nombre' => 'required',
        'precio' => 'required|decimal',
        'imagen' => 'max_size[imagen,2048]|is_image[imagen]'
    ];

    if (!$this->validate($rules)) {
        return redirect()->back()
            ->withInput()
            ->with('errors', $this->validator->getErrors());
    }
    
    $stock = 0;
    $costo = 0;

    $categoriaPrima = $this->primaModel
        ->where('nombre', $nombre)
        ->first();

  if ($categoriaPrima) {

    $resultado = $this->materiaModel
        ->select('SUM(cantidad) AS stock_total, AVG(precio) AS costo_unitario')
        ->where('categoria_id', $categoriaPrima['id'])
        ->first();

    $stock = (int) ($resultado['stock_total'] ?? 0);
    $costo = (float) ($resultado['costo_unitario'] ?? 0);
}

 

    // ───────────────────────────────
    // IMAGEN
    // ───────────────────────────────
    $file = $this->request->getFile('imagen');
    $imagenName = 'default.png';

    if ($file && $file->isValid() && !$file->hasMoved()) {
        $imagenName = $file->getRandomName();
        $file->move(ROOTPATH . 'public/uploads/productos', $imagenName);
    }

    // ───────────────────────────────
    // GUARDAR PRODUCTO
    // ───────────────────────────────
    $productoData = [
        'nombre'       => $nombre,
        'precio'       => $this->request->getPost('precio'),
        'categoria_id' => $categoria_id,
        'medida_id'    => $this->request->getPost('medida_id'),
        'imagen'       => $imagenName,
        'activo'       => 1,
        'stock'        => $stock,
        'costo'        => $costo
    ];

    $id = $this->productoModel->insert($productoData);

    if (!$id) {
        dd($this->productoModel->errors());
    }

    return redirect()->to("admin/productos/categoria/$categoria_id")
        ->with('success', 'Producto agregado correctamente');
}


    public function editar($id)
    {
        $this->checkLogin();
        $categoria_id = $this->request->getGet('categoria_id');
        $medidas = $this->medidaModel->findAll();

        $producto = $this->productoModel->find($id);
        $categorias = $this->categoriaModel->findAll();

        if (!$producto) {
            return redirect()->to('admin/productos')->with('error', 'Producto no encontrado');
        }

        $data = [
            'title' => 'Editar Producto',
            'producto' => $producto,
            'categorias' => $categorias,
            'categoria_id' => $categoria_id,
  'medidas' =>  $medidas,
        ];

        return view('productos/editar', $data);
    }

    public function actualizar($id)
    {

        $this->checkLogin();
    $categoria_id = $this->request->getPost('categoria_id');

        $rules = [
            'nombre' => 'required',
            'precio' => 'required|decimal',
            'imagen' => 'max_size[imagen,2048]|is_image[imagen]' // Reglas para imagen
        ];


    if ($categoria_id == 2) {
        $rules['stock'] = 'required|integer';
    } 

        // Verificar si el código es único (excluyendo el producto actual)
        $producto = $this->productoModel->find($id);

        if ($this->validate($rules)) {
            $file = $this->request->getFile('imagen');
            $imagenActual = $this->request->getPost('imagen_actual');
            $imagenName = $imagenActual;
            
            // Procesar nueva imagen si se subió
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $imagenName = $file->getRandomName();
                $file->move(ROOTPATH . 'public/uploads/productos', $imagenName);
                
                // Eliminar imagen anterior si existe y no es la default
                if ($imagenActual && $imagenActual != 'default.png' && file_exists(ROOTPATH . 'public/uploads/productos/' . $imagenActual)) {
                    unlink(ROOTPATH . 'public/uploads/productos/' . $imagenActual);
                }
            }
            
       
    $productoData = [
        'nombre' => $this->request->getPost('nombre'),
        'precio' => $this->request->getPost('precio'),
        'categoria_id' => $categoria_id,
        'medida_id' => $this->request->getPost('medida_id'),
        'imagen' => $imagenName,
        'activo' => 1,
        'stock' => 0,
        'costo' => 0
    ];

 if ($categoria_id == 2) {
    $productoData['stock'] = $this->request->getPost('stock');
    $productoData['costo'] = $this->request->getPost('costo'); 
}   $this->productoModel->update($id, $productoData);

            return redirect()->to("admin/productos/categoria/$categoria_id")->with('success', 'Producto actualizado correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
    }

public function eliminar($id)
{
    $this->checkLogin();


    $producto = $this->productoModel->find($id);
    $detalleModel = new DetalleVentaModel();

    if (!$producto) {
        return redirect()->to('admin/productos')->with('error', 'Producto no encontrado');
    }
$categoria_id = $producto['categoria_id']; // <- tomarla de la BD
    // Eliminar detalles de ventas asociados
    $detalleModel->where('producto_id', $id)->delete();

    // Eliminar imagen si existe y no es la default
    if ($producto['imagen'] && $producto['imagen'] != 'default.png' && file_exists(ROOTPATH . 'public/uploads/productos/' . $producto['imagen'])) {
        unlink(ROOTPATH . 'public/uploads/productos/' . $producto['imagen']);
    }

    // Eliminar producto
    $this->productoModel->delete($id);

    return redirect()->to("admin/productos/categoria/$categoria_id")
                     ->with('success', 'Producto eliminado correctamente');
}
}