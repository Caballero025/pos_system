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

    $this->ventaModel = new \App\Models\VentaModel();
    $this->materiaModel = new \App\Models\MateriaModel();

    // --- Ventas totales ---
    $ventas = $this->ventaModel
        ->where('fecha_venta >=', $fechaInicio)
        ->where('fecha_venta <=', $fechaFin . ' 23:59:59')
        ->findAll();

    // --- Top y menos vendidos ---
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

    // --- Productos inversión y bajo stock ---
    $productosBajoStock = $this->materiaModel
        ->select('materias_primas.*, categorias_prima.nombre AS categoria_nombre')
        ->join('categorias_prima', 'categorias_prima.id = materias_primas.categoria_id')
        ->whereIn('materias_primas.categoria_id', [5,6])
        ->where('materias_primas.cantidad <=', 5)
        ->where('materias_primas.activo', 1)
        ->findAll();

    $productosInversion = $this->materiaModel
        ->select('materias_primas.*, categorias_prima.nombre AS categoria_nombre')
        ->join('categorias_prima', 'categorias_prima.id = materias_primas.categoria_id')
        ->where('materias_primas.activo', 1)
        ->orderBy('categorias_prima.nombre', 'ASC')
        ->findAll();

    // --- Datos por día (lunes a domingo) ---
    $diasSemana = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
    $labelsDia = $diasSemana;

    $ingresosDia = array_fill(0, 7, 0);
    $gananciaDia = array_fill(0, 7, 0);

    $ventasDia = $this->ventaModel
        ->select('DAYOFWEEK(fecha_venta) as dia_semana, SUM(total) as total_ingresos')
        ->where('fecha_venta >=', $fechaInicio)
        ->where('fecha_venta <=', $fechaFin . ' 23:59:59')
        ->groupBy('dia_semana')
        ->orderBy('dia_semana')
        ->findAll();

    // --- Costo total materias primas ---
    $totalMateriaPrima = 0;
    foreach ($this->materiaModel->findAll() as $m) {
        $totalMateriaPrima += $m['precio'] * $m['cantidad'];
    }

    $totalIngresos = 0;
    foreach ($ventasDia as $v) {
        $dia = (int)$v['dia_semana'] - 1; // ajustar 1-7 a 0-6
        $ingresosDia[$dia] = (float)$v['total_ingresos'];
        $totalIngresos += $v['total_ingresos'];
    }

    // --- Ganancia por día ---
    foreach ($ingresosDia as $i => $monto) {
        $proporcion = $totalIngresos > 0 ? $monto / $totalIngresos : 0;
        $gananciaDia[$i] = round($monto - ($totalMateriaPrima * $proporcion), 2);
    }

    // --- Inversión distribuida por día ---
    $inversionDia = array_fill(0, 7, round($totalMateriaPrima / 7, 2));

    // --- Totales ---
    $totalGanancias = array_sum($gananciaDia);

    // --- Preparar datos para la vista ---
    $data = [
        'title' => 'Reportes',
        'ventas' => $ventas,
        'topProductos' => $topProductos,
        'productosMenosVendidos' => $productosMenosVendidos,
        'productosBajoStock' => $productosBajoStock,
        'productosInversion' => $productosInversion,
        'fecha_inicio' => $fechaInicio,
        'fecha_fin' => $fechaFin,
        'labelsDia' => $labelsDia,
        'ingresosDia' => $ingresosDia,
        'gananciaDia' => $gananciaDia,
        'inversionDia' => $inversionDia,
        'totalIngresos' => $totalIngresos,
        'totalGanancias' => $totalGanancias
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