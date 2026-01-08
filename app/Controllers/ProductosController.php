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
        $this->checkLogin();
        
        // Obtener parámetros de búsqueda
        $search = $this->request->getGet('search');
        $categoria_id = $this->request->getGet('categoria_id');

        // Construir consulta
        $builder = $this->productoModel->select('productos.*, categorias.nombre as categoria_nombre');
        $builder->join('categorias', 'categorias.id = productos.categoria_id', 'left');
        
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('productos.nombre', $search)
                    ->orLike('productos.codigo', $search)
                    ->orLike('productos.descripcion', $search)
                    ->groupEnd();
        }
        
        if (!empty($categoria_id)) {
            $builder->where('productos.categoria_id', $categoria_id);
        }

        $productos = $builder->findAll();
        $categorias = $this->categoriaModel->findAll();

        $data = [
            'title' => 'Gestión de Productos',
            'productos' => $productos,
            'categorias' => $categorias,
            'search' => $search,
            'categoria_id' => $categoria_id
        ];

        return view('productos/index', $data);
    }

    public function crear()
    {
        $this->checkLogin();
        
        $categorias = $this->categoriaModel->findAll();

        $data = [
            'title' => 'Agregar Producto',
            'categorias' => $categorias
        ];

        return view('productos/crear', $data);
    }

    public function guardar()
    {
        $this->checkLogin();

        $rules = [
            'codigo' => 'required|is_unique[productos.codigo]',
            'nombre' => 'required',
            'precio' => 'required|decimal',
            'stock' => 'required|integer',
            'imagen' => 'max_size[imagen,2048]|is_image[imagen]' // Reglas para imagen
        ];

        if ($this->validate($rules)) {
            $file = $this->request->getFile('imagen');
            $imagenName = 'default.png'; // Imagen por defecto
            
            // Procesar imagen si se subió
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $imagenName = $file->getRandomName();
                $file->move(ROOTPATH . 'public/uploads/productos', $imagenName);
                 print($imagenName);
            }
           
            $productoData = [
                'codigo' => $this->request->getPost('codigo'),
                'nombre' => $this->request->getPost('nombre'),
                'descripcion' => $this->request->getPost('descripcion'),
                'precio' => $this->request->getPost('precio'),
                'costo' => $this->request->getPost('costo'),
                'stock' => $this->request->getPost('stock'),
                'stock_minimo' => $this->request->getPost('stock_minimo'),
                'categoria_id' => $this->request->getPost('categoria_id'),
                'imagen' => $imagenName // Guardar nombre de imagen

            ];

            $this->productoModel->insert($productoData);

            return redirect()->to('admin/productos')->with('success', 'Producto agregado correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
    }

    public function editar($id)
    {
        $this->checkLogin();

        $producto = $this->productoModel->find($id);
        $categorias = $this->categoriaModel->findAll();

        if (!$producto) {
            return redirect()->to('admin/productos')->with('error', 'Producto no encontrado');
        }

        $data = [
            'title' => 'Editar Producto',
            'producto' => $producto,
            'categorias' => $categorias
        ];

        return view('productos/editar', $data);
    }

    public function actualizar($id)
    {
        $this->checkLogin();

        $rules = [
            'nombre' => 'required',
            'precio' => 'required|decimal',
            'stock' => 'required|integer',
            'imagen' => 'max_size[imagen,2048]|is_image[imagen]' // Reglas para imagen
        ];

        // Verificar si el código es único (excluyendo el producto actual)
        $producto = $this->productoModel->find($id);
        if ($producto['codigo'] != $this->request->getPost('codigo')) {
            $rules['codigo'] = 'required|is_unique[productos.codigo]';
        }

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
                'codigo' => $this->request->getPost('codigo'),
                'nombre' => $this->request->getPost('nombre'),
                'descripcion' => $this->request->getPost('descripcion'),
                'precio' => $this->request->getPost('precio'),
                'costo' => $this->request->getPost('costo'),
                'stock' => $this->request->getPost('stock'),
                'stock_minimo' => $this->request->getPost('stock_minimo'),
                'categoria_id' => $this->request->getPost('categoria_id'),
                'imagen' => $imagenName
            ];

            $this->productoModel->update($id, $productoData);

            return redirect()->to('admin/productos')->with('success', 'Producto actualizado correctamente');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
    }

    public function eliminar($id)
    {
        $this->checkLogin();

        $producto = $this->productoModel->find($id);
        $detalleModel = new DetalleVentaModel();

        $detalleModel->where('producto_id', $id)->delete();
        
        if (!$producto) {
            return redirect()->to('admin/productos')->with('error', 'Producto no encontrado');
        }

        // Eliminar imagen si existe y no es la default
        if ($producto['imagen'] && $producto['imagen'] != 'default.png' && file_exists(ROOTPATH . 'public/uploads/productos/' . $producto['imagen'])) {
            unlink(ROOTPATH . 'public/uploads/productos/' . $producto['imagen']);
        }

        $this->productoModel->delete($id);

        return redirect()->to('admin/productos')->with('success', 'Producto eliminado correctamente');
    }
}