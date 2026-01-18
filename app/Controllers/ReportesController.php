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

    $this->ventaModel    = new \App\Models\VentaModel();
    $this->materiaModel  = new \App\Models\MateriaModel();
    $this->entradaModel  = new \App\Models\EntradaModel();
    $this->productoModel = new \App\Models\ProductoModel();

    // ───────────────────────────────
    // Meses en español
    // ───────────────────────────────
    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
    ];

    // ───────────────────────────────
    // Año, mes y semana actual
    // ───────────────────────────────
    $anioActual   = date('Y');
    $mesActual    = date('n');
    $hoy          = new \DateTime();
    $primerDiaMes = new \DateTime("$anioActual-$mesActual-01");
    $diffDias     = (int)$primerDiaMes->diff($hoy)->days;
    $diaPrimer    = (int)$primerDiaMes->format('N'); // 1=Lunes, 7=Domingo
    $semanaActual = (int)floor(($diffDias + $diaPrimer - 1) / 7) + 1;

    // ───────────────────────────────
    // Obtener filtros enviados por GET
    // ───────────────────────────────
    $anio   = $this->request->getGet('anio') ?? $anioActual;
    $mes    = $this->request->getGet('mes') ?? $mesActual;
    $semana = $this->request->getGet('semana') ?? $semanaActual;

    // ───────────────────────────────
    // Número de semanas del mes seleccionado
    // ───────────────────────────────
    $primerDiaMes = new \DateTime("$anio-$mes-01");
    $ultimoDiaMes = (clone $primerDiaMes)->modify('last day of this month');
    $diaSemanaPrimer = (int)$primerDiaMes->format('N'); // 1=Lunes
    $diaMesUltimo    = (int)$ultimoDiaMes->format('d');
    $semanasMes = ceil(($diaSemanaPrimer - 1 + $diaMesUltimo) / 7);

    // ───────────────────────────────
    // Calcular lunes y sábado de la semana seleccionada
    // ───────────────────────────────
// ───────────────────────────────
// Calcular lunes y domingo de la semana seleccionada
// ───────────────────────────────
$lunes = (clone $primerDiaMes)->modify('+'.(($semana - 1) * 7).' days');
$diaSemana = (int)$lunes->format('N');
$lunes->modify('-'.($diaSemana - 1).' days'); // ajustar al lunes
$domingo = (clone $lunes)->modify('+6 days');

$fechaInicio = $lunes->format('Y-m-d');
$fechaFin    = $domingo->format('Y-m-d');


    // ───────────────────────────────
    // Ventas del período
    // ───────────────────────────────
    $ventas = $this->ventaModel
        ->where('fecha_venta >=', $fechaInicio)
        ->where('fecha_venta <=', $fechaFin . ' 23:59:59')
        ->findAll();

    // ───────────────────────────────
    // Top y menos vendidos
    // ───────────────────────────────
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

    // ───────────────────────────────
    // Productos bajo stock
    // ───────────────────────────────
    $productosBajoStock = $this->materiaModel
        ->select('materias_primas.*, categorias_prima.nombre AS categoria_nombre')
        ->join('categorias_prima', 'categorias_prima.id = materias_primas.categoria_id')
        ->whereIn('materias_primas.categoria_id', [5, 6])
        ->where('materias_primas.cantidad <=', 5)
        ->where('materias_primas.activo', 1)
        ->findAll();

    // ───────────────────────────────
    // Productos inversión
    // ───────────────────────────────
$productosInversion = $this->entradaModel
    ->select('materia_entradas.materia_id, materias_primas.nombre, SUM(materia_entradas.cantidad) AS cantidad_total, SUM(materia_entradas.total) AS total_inversion')
    ->join('materias_primas', 'materias_primas.id = materia_entradas.materia_id')
    ->groupBy('materia_entradas.materia_id, materias_primas.nombre')
    ->orderBy('materias_primas.nombre', 'ASC')
    ->findAll();



    // ───────────────────────────────
    // Ventas e inversión por día (lunes a sábado)
    // ───────────────────────────────
    $ventasDia = $this->ventaModel
        ->select('DATE(fecha_venta) AS fecha, SUM(total) AS total')
        ->where('fecha_venta >=', $fechaInicio)
        ->where('fecha_venta <=', $fechaFin . ' 23:59:59')
        ->groupBy('DATE(fecha_venta)')
        ->orderBy('fecha', 'ASC')
        ->findAll();

    $inversionesDia = $this->entradaModel
        ->select('DATE(fecha) AS fecha, SUM(total) AS total')
        ->where('fecha >=', $fechaInicio)
        ->where('fecha <=', $fechaFin . ' 23:59:59')
        ->groupBy('DATE(fecha)')
        ->orderBy('fecha', 'ASC')
        ->findAll();


    // ───────────────────────────────
    // Llenar datos por día
    // ───────────────────────────────
$diasSemana = [
    'Monday'    => 'Lunes',
    'Tuesday'   => 'Martes',
    'Wednesday' => 'Miércoles',
    'Thursday'  => 'Jueves',
    'Friday'    => 'Viernes',
    'Saturday'  => 'Sábado',
    'Sunday'    => 'Domingo',
];


    $datosDia = [];
    $periodo = new \DatePeriod(new \DateTime($fechaInicio), new \DateInterval('P1D'), (new \DateTime($fechaFin))->modify('+1 day'));

    foreach ($periodo as $fecha) {
        $diaEn = $fecha->format('l');
        if (!isset($diasSemana[$diaEn])) continue;
        $datosDia[$fecha->format('Y-m-d')] = [
            'label' => $diasSemana[$diaEn],
            'ingreso' => 0,
            'inversion' => 0,
        ];
    }

    foreach ($ventasDia as $v) {
        $fechaKey = (new \DateTime($v['fecha']))->format('Y-m-d');
        if (isset($datosDia[$fechaKey])) $datosDia[$fechaKey]['ingreso'] = (float)$v['total'];
    }

    foreach ($inversionesDia as $i) {
        $fechaKey = (new \DateTime($i['fecha']))->format('Y-m-d');
        if (isset($datosDia[$fechaKey])) $datosDia[$fechaKey]['inversion'] = (float)$i['total'];
    }

    $labelsDia    = array_column($datosDia, 'label');
    $ingresosDia  = array_column($datosDia, 'ingreso');
    $inversionDia = array_column($datosDia, 'inversion');

    // Totales
    $totalIngresos  = array_sum($ingresosDia);
    $totalInversion = array_sum($inversionDia);
    $totalGanancias = $totalIngresos - $totalInversion;

    // ───────────────────────────────
    // Enviar datos a la vista
    // ───────────────────────────────
    return view('reportes/index', [
        'title'                  => 'Reportes',
        'ventas'                 => $ventas,
        'topProductos'           => $topProductos,
        'productosMenosVendidos' => $productosMenosVendidos,
        'productosBajoStock'     => $productosBajoStock,
        'productosInversion'     => $productosInversion,
        'fecha_inicio'           => $fechaInicio,
        'fecha_fin'              => $fechaFin,
        'labelsDia'              => $labelsDia,
        'ingresosDia'            => $ingresosDia,
        'inversionDia'           => $inversionDia,
        'gananciaDia'            => array_map(function($i,$inv){ return $i-$inv; }, $ingresosDia,$inversionDia),
        'totalIngresos'          => $totalIngresos,
        'totalInversion'         => $totalInversion,
        'totalGanancias'         => $totalGanancias,
        'anio'                   => $anio,
        'mes'                    => $mes,
        'semana'                 => $semana,
        'meses'                  => $meses,
        'semanasMes'             => $semanasMes,
    ]);
}



public function ventas()
{
    $this->checkLogin();

    // ───────────────────────────────
    // Filtros
    // ───────────────────────────────
    $anio   = $this->request->getGet('anio');
    $mes    = $this->request->getGet('mes');
    $semana = $this->request->getGet('semana');

    $fechaInicio = $this->request->getGet('fecha_inicio');
    $fechaFin    = $this->request->getGet('fecha_fin');

    // ───────────────────────────────
    // 🟢 FILTRO POR SEMANA (PRIORIDAD)
    // ───────────────────────────────
    if ($anio && $mes && $semana) {

        $primerDiaMes = new \DateTime("$anio-$mes-01");

        // Calcular lunes de la semana seleccionada
        $lunes = (clone $primerDiaMes)->modify('+'.(($semana - 1) * 7).' days');
        $diaSemana = (int)$lunes->format('N');
        $lunes->modify('-'.($diaSemana - 1).' days');

        $domingo = (clone $lunes)->modify('+6 days');


        $fechaInicio = $lunes->format('Y-m-d');
        $fechaFin = $domingo->format('Y-m-d');

    }

    // ───────────────────────────────
    // 🔵 FILTRO POR RANGO (fallback)
    // ───────────────────────────────
    if (empty($fechaInicio)) {
        $fechaInicio = date('Y-m-01');
    }

    if (empty($fechaFin)) {
        $fechaFin = date('Y-m-d');
    }

    // ───────────────────────────────
    // Ventas filtradas
    // ───────────────────────────────
    $ventas = $this->ventaModel
        ->select('ventas.*, clientes.nombre AS cliente_nombre')
        ->join('clientes', 'clientes.id = ventas.cliente_id', 'left')
        ->where('fecha_venta >=', $fechaInicio . ' 00:00:00')
        ->where('fecha_venta <=', $fechaFin . ' 23:59:59')
        ->orderBy('fecha_venta', 'DESC')
        ->findAll();

    return view('reportes/ventas', [
        'title'        => 'Reporte de Ventas',
        'ventas'       => $ventas,
        'fecha_inicio' => $fechaInicio,
        'fecha_fin'    => $fechaFin,
        'anio'         => $anio,
        'mes'          => $mes,
        'semana'       => $semana
    ]);
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

    // ───────────────────────────────
    // Filtros recibidos
    // ───────────────────────────────
    $anio   = $this->request->getGet('anio');
    $mes    = $this->request->getGet('mes');
    $semana = $this->request->getGet('semana');

    $fechaInicio = null;
    $fechaFin    = null;

    // ───────────────────────────────
    // 🟢 FILTRO POR SEMANA
    // ───────────────────────────────
    if ($anio && $mes && $semana) {

        $primerDiaMes = new \DateTime("$anio-$mes-01");

        $lunes = (clone $primerDiaMes)->modify('+'.(($semana - 1) * 7).' days');
        $diaSemana = (int)$lunes->format('N');
        $lunes->modify('-'.($diaSemana - 1).' days');

        $sabado = (clone $lunes)->modify('+5 days');

        $fechaInicio = $lunes->format('Y-m-d');
        $fechaFin    = $sabado->format('Y-m-d');
    }

    // ───────────────────────────────
    // Query base
    // ───────────────────────────────
    $builder = $this->clienteModel
        ->select('clientes.*,
                  COUNT(ventas.id) AS total_compras,
                  COALESCE(SUM(ventas.total), 0) AS total_gastado')
        ->join('ventas', 'ventas.cliente_id = clientes.id', 'left');

    // ───────────────────────────────
    // Aplicar filtro por fecha si existe
    // ───────────────────────────────
    if ($fechaInicio && $fechaFin) {
        $builder->where('ventas.fecha_venta >=', $fechaInicio . ' 00:00:00')
                ->where('ventas.fecha_venta <=', $fechaFin . ' 23:59:59');
    }

    $clientes = $builder
        ->groupBy('clientes.id')
        ->orderBy('total_gastado', 'DESC')
        ->findAll();

    return view('reportes/clientes', [
        'title'        => 'Reporte de Clientes',
        'clientes'     => $clientes,
        'anio'         => $anio,
        'mes'          => $mes,
        'semana'       => $semana,
        'fecha_inicio' => $fechaInicio,
        'fecha_fin'    => $fechaFin
    ]);
}

}