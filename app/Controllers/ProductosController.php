<?php
namespace App\Controllers;

use App\Models\ProductoModel;
use App\Models\CategoriaModel;
use App\Models\DetalleVentaModel;


class ProductosController extends BaseController
{
    protected $productoModel;
    protected $categoriaModel;

    public function __construct()
    {
        $this->productoModel = new ProductoModel();
        $this->categoriaModel = new CategoriaModel();
    }

    public function index()
    {
     
        return view('productos/index');
    }

    public function productos($categoria_id = null)
{
    $this->checkLogin();

    $search = null; // 👈 IMPORTANTE

    $builder = $this->productoModel
        ->select('productos.*, categorias.nombre as categoria_nombre')
        ->join('categorias', 'categorias.id = productos.categoria_id', 'left');

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
        $categoria_id = $this->request->getGet('categoria_id'); // obtiene 2

        $categorias = $this->categoriaModel->findAll();

        $data = [
            'title' => 'Agregar Producto',
            'categorias' => $categorias,
            'categoria_id' => $categoria_id
        ];

        return view('productos/crear', $data);
    }

   public function guardar()
{
    $this->checkLogin();

    $categoria_id = $this->request->getPost('categoria_id');

    $rules = [
        'nombre' => 'required',
        'precio' => 'required|decimal',
        'imagen' => 'max_size[imagen,2048]|is_image[imagen]'
    ];

    if ($categoria_id == 2) {
        $rules['stock'] = 'required|integer';
    } 

    if (!$this->validate($rules)) {
        return redirect()->back()
            ->withInput()
            ->with('errors', $this->validator->getErrors());
    }

    $file = $this->request->getFile('imagen');
    $imagenName = 'default.png';

    if ($file && $file->isValid() && !$file->hasMoved()) {
        $imagenName = $file->getRandomName();
        $file->move(ROOTPATH . 'public/uploads/productos', $imagenName);
    }

    $productoData = [
        'nombre' => $this->request->getPost('nombre'),
        'precio' => $this->request->getPost('precio'),
        'categoria_id' => $categoria_id,
        'imagen' => $imagenName,
        'activo' => 1,
        'stock' => 0,
        'costo' => 0
    ];
if ($categoria_id == 2) {
    $productoData['stock'] = $this->request->getPost('stock');
    $productoData['costo'] = $this->request->getPost('costo'); // <- aquí
}
    $id = $this->productoModel->insert($productoData);

    if (!$id) {
        dd($this->productoModel->errors()); // depura si hubo errores
    }

    return redirect()->to("admin/productos/categoria/$categoria_id")
                 ->with('success', 'Producto agregado correctamente');
}

    public function editar($id)
    {
        $this->checkLogin();
        $categoria_id = $this->request->getGet('categoria_id'); // obtiene 2

        $producto = $this->productoModel->find($id);
        $categorias = $this->categoriaModel->findAll();

        if (!$producto) {
            return redirect()->to('admin/productos')->with('error', 'Producto no encontrado');
        }

        $data = [
            'title' => 'Editar Producto',
            'producto' => $producto,
            'categorias' => $categorias,
             'categoria_id' => $categoria_id
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
        'imagen' => $imagenName,
        'activo' => 1,
        'stock' => 0,
        'costo' => 0
    ];

 if ($categoria_id == 2) {
    $productoData['stock'] = $this->request->getPost('stock');
    $productoData['costo'] = $this->request->getPost('costo'); // <- aquí
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