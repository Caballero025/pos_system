<?php
namespace App\Controllers;

use App\Models\ProductoModel;
use App\Models\VentaModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $this->checkLogin();
        
        $productoModel = new ProductoModel();
        $ventaModel = new VentaModel();
        
        $data = [
            'title' => 'Inicio',
            'user_name' => session()->get('user_name'),
            'total_productos' => $productoModel->countAll(),
            'total_ventas' => $ventaModel->countAll(),
            'ventas_hoy' => $ventaModel->where('DATE(fecha_venta)', date('Y-m-d'))->countAllResults()
        ];
        
        return view('dashboard', $data);
    }
}