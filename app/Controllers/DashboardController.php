<?php
namespace App\Controllers;

use App\Models\ProductoModel;
use App\Models\VentaModel;
use App\Models\EntradaModel;

class DashboardController extends BaseController
{
public function index()
{
    $this->checkLogin();

    $productoModel = new ProductoModel();
    $ventaModel    = new VentaModel();
    $entradaModel  = new EntradaModel();

    // =========================
    // Fechas actuales
    // =========================
    $hoy = new \DateTime();
    $diaSemana = (int)$hoy->format('N'); // 1=Lunes
    $lunes = (clone $hoy)->modify('-'.($diaSemana-1).' days');
    $sabado = (clone $lunes)->modify('+5 days');

    $inicioSemana = $lunes->format('Y-m-d');
    $finSemana    = $sabado->format('Y-m-d');

    $mesActual  = date('n');
    $anioActual = date('Y');

    // =========================
    // Ganancias totales (TODO el tiempo)
    // =========================
    $totalIngresos = $ventaModel->select('SUM(total) AS total')->first()['total'] ?? 0;
    $totalInversion = $entradaModel->select('SUM(total) AS total')->first()['total'] ?? 0;

    $gananciasTotales = (float)$totalIngresos - (float)$totalInversion;

    // =========================
    // Ganancias por Semana (Lun-Sab)
    // =========================
    $ventasSemana = $ventaModel
        ->select('DATE(fecha_venta) AS fecha, SUM(total) AS total')
        ->where('fecha_venta >=', $inicioSemana)
        ->where('fecha_venta <=', $finSemana . ' 23:59:59')
        ->groupBy('DATE(fecha_venta)')
        ->orderBy('fecha')
        ->findAll();

    $inversionSemana = $entradaModel
        ->select('DATE(fecha) AS fecha, SUM(total) AS total')
        ->where('fecha >=', $inicioSemana)
        ->where('fecha <=', $finSemana . ' 23:59:59')
        ->groupBy('DATE(fecha)')
        ->orderBy('fecha')
        ->findAll();

    $labelsSemana = ['Lun','Mar','Mié','Jue','Vie','Sáb'];
    $gananciasSemana = [0,0,0,0,0,0];

    foreach ($ventasSemana as $v) {
        $dia = (new \DateTime($v['fecha']))->format('N') - 1; // 0=Lunes
        if ($dia < 6) {
            $gananciasSemana[$dia] += (float)$v['total'];
        }
    }

    foreach ($inversionSemana as $i) {
        $dia = (new \DateTime($i['fecha']))->format('N') - 1;
        if ($dia < 6) {
            $gananciasSemana[$dia] -= (float)$i['total'];
        }
    }

    // =========================
    // Ganancias por Mes (día a día)
    // =========================
    $ventasMes = $ventaModel
        ->select('DAY(fecha_venta) AS dia, SUM(total) AS total')
        ->where('MONTH(fecha_venta)', $mesActual)
        ->where('YEAR(fecha_venta)', $anioActual)
        ->groupBy('DAY(fecha_venta)')
        ->orderBy('dia')
        ->findAll();

    $inversionMes = $entradaModel
        ->select('DAY(fecha) AS dia, SUM(total) AS total')
        ->where('MONTH(fecha)', $mesActual)
        ->where('YEAR(fecha)', $anioActual)
        ->groupBy('DAY(fecha)')
        ->orderBy('dia')
        ->findAll();

    $diasMes = (int)date('t'); // días del mes actual
    $labelsMes = [];
    $gananciasMes = [];

    for ($d=1; $d <= $diasMes; $d++) {
        $labelsMes[] = $d;
        $gananciasMes[$d] = 0;
    }

    foreach ($ventasMes as $v) {
        $gananciasMes[$v['dia']] += (float)$v['total'];
    }

    foreach ($inversionMes as $i) {
        $gananciasMes[$i['dia']] -= (float)$i['total'];
    }

    // Convertir a array numérico
    $gananciasMes = array_values($gananciasMes);

    // =========================
    // Ganancias por Año (mes a mes)
    // =========================
    $ventasAnio = $ventaModel
        ->select('MONTH(fecha_venta) AS mes, SUM(total) AS total')
        ->where('YEAR(fecha_venta)', $anioActual)
        ->groupBy('MONTH(fecha_venta)')
        ->orderBy('mes')
        ->findAll();

    $inversionAnio = $entradaModel
        ->select('MONTH(fecha) AS mes, SUM(total) AS total')
        ->where('YEAR(fecha)', $anioActual)
        ->groupBy('MONTH(fecha)')
        ->orderBy('mes')
        ->findAll();

    $labelsAnio = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    $gananciasAnio = array_fill(0, 12, 0);

    foreach ($ventasAnio as $v) {
        $gananciasAnio[$v['mes'] - 1] += (float)$v['total'];
    }

    foreach ($inversionAnio as $i) {
        $gananciasAnio[$i['mes'] - 1] -= (float)$i['total'];
    }

    // =========================
    // Datos generales
    // =========================
    $data = [
        'title'             => 'Inicio',
        'user_name'         => session()->get('user_name'),
        'total_productos'   => $productoModel->countAll(),
        'total_ventas'      => $ventaModel->countAll(),
        'ventas_hoy'        => $ventaModel->where('DATE(fecha_venta)', date('Y-m-d'))->countAllResults(),
        'ganancias'         => $gananciasTotales,

        'labelsSemana'      => $labelsSemana,
        'gananciasSemana'   => $gananciasSemana,

        'labelsMes'         => $labelsMes,
        'gananciasMes'      => $gananciasMes,

        'labelsAnio'        => $labelsAnio,
        'gananciasAnio'     => $gananciasAnio,
    ];

    return view('dashboard', $data);
}



}