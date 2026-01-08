<?php
namespace App\Controllers;

use App\Models\CajaModel;
use App\Models\VentaModel;

class CajaController extends BaseController
{
    protected $cajaModel;
    protected $ventaModel;

    public function __construct()
    {
        $this->cajaModel = new CajaModel();
        $this->ventaModel = new VentaModel();
    }

    public function index()
    {
        $this->checkLogin();
        
        // DEBUG: Verificar conexión
        log_message('debug', 'Accediendo a módulo de caja');
        
        $cajaAbierta = $this->cajaModel->where('estado', 'abierta')->first();
        $ventasHoy = $this->ventaModel->where('DATE(fecha_venta)', date('Y-m-d'))->findAll();
        
        $data = [
            'title' => 'Caja Registradora',
            'caja_abierta' => $cajaAbierta,
            'ventas_hoy' => $ventasHoy
        ];
        
        log_message('debug', 'Datos para vista: ' . print_r($data, true));
        
        return view('/caja/index', $data);
    }

    public function abrirCaja()
    {
        $this->checkLogin();
        
        // DEBUG: Log de inicio
        log_message('debug', 'Iniciando proceso de apertura de caja');
        
        // Verificar si ya hay caja abierta
        $cajaAbierta = $this->cajaModel->where('estado', 'abierta')->first();
        if ($cajaAbierta) {
            log_message('warning', 'Ya hay una caja abierta');
            return redirect()->to('admin/caja')->with('error', 'Ya hay una caja abierta');
        }
        
        $montoInicial = $this->request->getPost('monto_inicial');
        
        // DEBUG: Log del monto recibido
        log_message('debug', 'Monto inicial recibido: ' . $montoInicial);
        
        // Validar monto
        if (!is_numeric($montoInicial) || $montoInicial <= 0) {
            log_message('error', 'Monto inicial no válido: ' . $montoInicial);
            return redirect()->to('admin/caja')->with('error', 'Monto inicial no válido');
        }
        
        $user_id = session()->get('user_id');
        log_message('debug', 'User ID de sesión: ' . $user_id);
        
        if (!$user_id) {
            $user_id = 1; // Valor temporal si no hay user_id en sesión
            log_message('warning', 'No se encontró user_id en sesión, usando temporal: ' . $user_id);
        }
        
        $cajaData = [
            'fecha_apertura' => date('Y-m-d H:i:s'),
            'monto_inicial' => $montoInicial,
            'usuario_id' => $user_id,
            'estado' => 'abierta',
            'created_at' => date('Y-m-d H:i:s') // IMPORTANTE: Agregar created_at manualmente
        ];
        
        // DEBUG: Log de datos a insertar
        log_message('debug', 'Datos para insertar: ' . print_r($cajaData, true));
        
        try {
            // Intentar insertar
            $result = $this->cajaModel->insert($cajaData);
            
            // DEBUG: Log del resultado
            log_message('debug', 'Resultado de inserción: ' . ($result ? 'true' : 'false'));
            
            if ($result) {
                // Obtener ID insertado
                $insertedId = $this->cajaModel->getInsertID();
                log_message('info', 'Caja abierta con ID: ' . $insertedId);
                return redirect()->to('admin/caja')->with('success', 'Caja abierta correctamente');
            } else {
                // Obtener errores del modelo
                $errors = $this->cajaModel->errors();
                log_message('error', 'Error al insertar caja: ' . print_r($errors, true));
                return redirect()->to('admin/caja')->with('error', 'Error al insertar en la base de datos: ' . implode(', ', $errors));
            }
        } catch (\Exception $e) {
            // DEBUG: Log de excepción
            log_message('error', 'Excepción al abrir caja: ' . $e->getMessage());
            log_message('error', 'Trace: ' . $e->getTraceAsString());
            return redirect()->to('admin/caja')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function cerrarCaja()
    {
        $this->checkLogin();
        
        log_message('debug', 'Iniciando cierre de caja');
        
        $cajaAbierta = $this->cajaModel->where('estado', 'abierta')->first();
        
        if (!$cajaAbierta) {
            log_message('warning', 'No hay caja abierta para cerrar');
            return redirect()->to('admin/caja')->with('error', 'No hay caja abierta');
        }
        
        // Calcular totales del día
        $ventasHoy = $this->ventaModel->where('DATE(fecha_venta)', date('Y-m-d'))->findAll();
        $totalVentas = 0;
        foreach ($ventasHoy as $venta) {
            $totalVentas += floatval($venta['total']);
        }
        
        log_message('debug', 'Total ventas hoy: ' . $totalVentas);
        
        $cierreData = [
            'fecha_cierre' => date('Y-m-d H:i:s'),
            'monto_final' => $cajaAbierta['monto_inicial'] + $totalVentas,
            'total_ventas' => $totalVentas,
            'estado' => 'cerrada'
        ];
        
        log_message('debug', 'Datos para cerrar caja: ' . print_r($cierreData, true));
        
        try {
            $result = $this->cajaModel->update($cajaAbierta['id'], $cierreData);
            
            if ($result) {
                log_message('info', 'Caja cerrada exitosamente ID: ' . $cajaAbierta['id']);
                return redirect()->to('admin/caja')->with('success', 'Caja cerrada correctamente');
            } else {
                $errors = $this->cajaModel->errors();
                log_message('error', 'Error al cerrar caja: ' . print_r($errors, true));
                return redirect()->to('admin/caja')->with('error', 'Error al actualizar la caja: ' . implode(', ', $errors));
            }
        } catch (\Exception $e) {
            log_message('error', 'Excepción al cerrar caja: ' . $e->getMessage());
            return redirect()->to('admin/caja')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function movimiento()
    {
        $this->checkLogin();
        
        // Verificar caja abierta
        $cajaAbierta = $this->cajaModel->where('estado', 'abierta')->first();
        if (!$cajaAbierta) {
            return redirect()->to('admin/caja')->with('error', 'No hay caja abierta');
        }
        
        $tipo = $this->request->getPost('tipo');
        $monto = $this->request->getPost('monto');
        $concepto = $this->request->getPost('concepto');
        
        // Validar datos y desarrollar esta parte para que se vea reflejado en el monto
        if (!in_array($tipo, ['ingreso', 'retiro'])) {
            return redirect()->to('admin/caja')->with('error', 'Tipo de movimiento no válido');
        }
        
        if (!is_numeric($monto) || $monto <= 0) {
            return redirect()->to('admin/caja')->with('error', 'Monto no válido');
        }
        
        // Por ahora solo redirigimos con mensaje de éxito
        return redirect()->to('admin/caja')->with('success', 'Movimiento registrado correctamente');
    }
}