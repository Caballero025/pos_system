<?php
namespace App\Controllers;

use App\Models\VentaModel;
use App\Models\ProductoModel;
use App\Models\ClienteModel;
use App\Models\MateriaModel;
use App\Models\EntradaModel;

class ReportesController extends BaseController
{
    protected $ventaModel;
    protected $productoModel;
    protected $clienteModel;
    protected $materiaModel;
    protected $entradaModel;


    public function __construct()
    {
        $this->ventaModel = new VentaModel();
        $this->productoModel = new ProductoModel();
        $this->clienteModel = new ClienteModel();
        $this->materiaModel = new MateriaModel();
        $this->entradaModel = new EntradaModel();
    }

public function index()
{
    $this->checkLogin();

    $fechaInicio = $this->request->getGet('fecha_inicio') ?? date('Y-m-01');
    $fechaFin    = $this->request->getGet('fecha_fin') ?? date('Y-m-d');

    $this->ventaModel    = new \App\Models\VentaModel();
    $this->materiaModel  = new \App\Models\MateriaModel();
    $this->entradaModel  = new \App\Models\EntradaModel();
    $this->productoModel = new \App\Models\ProductoModel();

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

    $labelsDia    = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
    $ingresosDia  = array_fill(0, 7, 0);
    $inversionDia = array_fill(0, 7, 0);

    $ventasDia = $this->ventaModel
        ->select('DAYOFWEEK(fecha_venta) AS dia, SUM(total) AS total')
        ->where('fecha_venta >=', $fechaInicio)
        ->where('fecha_venta <=', $fechaFin . ' 23:59:59')
        ->groupBy('dia')
        ->findAll();

    foreach ($ventasDia as $v) {
        $index = (int)$v['dia'] - 1;
        $ingresosDia[$index] = (float)$v['total'];
    }
    $inversionesDia = $this->entradaModel
        ->select('DAYOFWEEK(fecha) AS dia, SUM(total) AS total')
        ->where('fecha >=', $fechaInicio)
        ->where('fecha <=', $fechaFin . ' 23:59:59')
        ->groupBy('dia')
        ->findAll();

    foreach ($inversionesDia as $i) {
        $index = (int)$i['dia'] - 1;
        $inversionDia[$index] = (float)$i['total'];
    }
$gananciaDia = [];

for ($i = 0; $i < 7; $i++) {
    $gananciaDia[$i] = round(
        $ingresosDia[$i] - $inversionDia[$i],
        2
    );

    $totalIngresos  = array_sum($ingresosDia);
    $totalInversion = array_sum($inversionDia);
    $totalGanancias = $totalIngresos - $totalInversion;

    return view('reportes/index', [
        'title'                  => 'Reportes',
        'ventas'                 => $ventas,
        'topProductos'           => $topProductos,
        'productosMenosVendidos' => $productosMenosVendidos,
        'productosBajoStock'     => $productosBajoStock,
        'productosInversion'     => $productosInversion,
        'fecha_inicio'           => $fechaInicio,
        'fecha_fin'              => $fechaFin,
        'labelsDia'     => $labelsDia,
    'ingresosDia'   => $ingresosDia,
    'inversionDia'  => $inversionDia,
    'gananciaDia'   => $gananciaDia,
    'totalIngresos' => $totalIngresos,
    'totalGanancias'=> array_sum($gananciaDia),
    ]);
}

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