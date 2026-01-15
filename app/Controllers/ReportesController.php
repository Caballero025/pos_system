<?php
namespace App\Controllers;

use App\Models\VentaModel;
use App\Models\ProductoModel;
use App\Models\ClienteModel;
use App\Models\MateriaModel;

class ReportesController extends BaseController
{
    protected $ventaModel;
    protected $productoModel;
    protected $clienteModel;
    protected $materiaModel;


    public function __construct()
    {
        $this->ventaModel = new VentaModel();
        $this->productoModel = new ProductoModel();
        $this->clienteModel = new ClienteModel();
        $this->materiaModel = new MateriaModel();
    }

public function index()
{
    $this->checkLogin();


    $fechaInicio = $this->request->getGet('fecha_inicio') ?? date('Y-m-01');
    $fechaFin = $this->request->getGet('fecha_fin') ?? date('Y-m-d');

    $ventas = $this->ventaModel
        ->where('fecha_venta >=', $fechaInicio)
        ->where('fecha_venta <=', $fechaFin . ' 23:59:59')
        ->findAll();

    $topProductos = $this->productoModel
        ->select('productos.nombre, SUM(detalle_ventas.cantidad) AS total_vendido')
        ->join('detalle_ventas', 'detalle_ventas.producto_id = productos.id')
        ->join('ventas', 'ventas.id = detalle_ventas.venta_id')
        ->where('ventas.fecha_venta >=', $fechaInicio)
        ->where('ventas.fecha_venta <=', $fechaFin . ' 23:59:59')
        ->groupBy('productos.nombre')
        ->orderBy('total_vendido', 'DESC')
        ->limit(2)
        ->findAll();
    $productosMenosVendidos = $this->productoModel
        ->select('productos.nombre, SUM(detalle_ventas.cantidad) AS total_vendido')
        ->join('detalle_ventas', 'detalle_ventas.producto_id = productos.id')
        ->join('ventas', 'ventas.id = detalle_ventas.venta_id')
        ->where('ventas.fecha_venta >=', $fechaInicio)
        ->where('ventas.fecha_venta <=', $fechaFin . ' 23:59:59')
        ->groupBy('productos.nombre')
        ->orderBy('total_vendido', 'ASC')
        ->limit(2)
        ->findAll();
    $data = [
        'title' => 'Reportes',
        'ventas' => $ventas,
          'topProductos' => $topProductos,
        'productosMenosVendidos' => $productosMenosVendidos,
        'fecha_inicio' => $fechaInicio,
        'fecha_fin' => $fechaFin
    ];

    return view('reportes/index', $data);
}

    public function ventas()
    {
        $this->checkLogin();
        
        $fechaInicio = $this->request->getGet('fecha_inicio') ?? date('Y-m-01');
        $fechaFin = $this->request->getGet('fecha_fin') ?? date('Y-m-d');
        
        $ventas = $this->ventaModel->select('ventas.*, clientes.nombre as cliente_nombre')
                                  ->join('clientes', 'clientes.id = ventas.cliente_id', 'left')
                                  ->where('fecha_venta >=', $fechaInicio)
                                  ->where('fecha_venta <=', $fechaFin . ' 23:59:59')
                                  ->orderBy('fecha_venta', 'DESC')
                                  ->findAll();
        
        $data = [
            'title' => 'Reporte de Ventas',
            'ventas' => $ventas,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin
        ];
        
        return view('reportes/ventas', $data);
    }

    public function productos()
    {
        $this->checkLogin();
        
        $productos = $this->productoModel->select('productos.*, SUM(detalle_ventas.cantidad) as total_vendido, SUM(detalle_ventas.subtotal) as total_ingresos')
                                        ->join('detalle_ventas', 'detalle_ventas.producto_id = productos.id', 'left')
                                        ->groupBy('productos.id')
                                        ->orderBy('total_vendido', 'DESC')
                                        ->findAll();
        
        $data = [
            'title' => 'Reporte de Productos',
            'productos' => $productos
        ];
        
        return view('reportes/productos', $data);
    }

    public function clientes()
    {
        $this->checkLogin();
        
        $clientes = $this->clienteModel->select('clientes.*, COUNT(ventas.id) as total_compras, SUM(ventas.total) as total_gastado')
                                      ->join('ventas', 'ventas.cliente_id = clientes.id', 'left')
                                      ->groupBy('clientes.id')
                                      ->orderBy('total_gastado', 'DESC')
                                      ->findAll();
        
        $data = [
            'title' => 'Reporte de Clientes',
            'clientes' => $clientes
        ];
        
        return view('reportes/clientes', $data);
    }
}