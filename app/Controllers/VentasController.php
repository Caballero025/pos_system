<?php
namespace App\Controllers;
use Dompdf\Dompdf;
use App\Models\ProductoModel;
use App\Models\VentaModel;
use App\Models\DetalleVentaModel;
use App\Models\ClienteModel;
use App\Models\ConfiguracionModel;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
class VentasController extends BaseController
{
    protected $productoModel;
    protected $ventaModel;
    protected $detalleVentaModel;
    protected $clienteModel;

    public function __construct()
    {
    
        $this->productoModel = new ProductoModel();
        $this->ventaModel = new VentaModel();
        $this->detalleVentaModel = new DetalleVentaModel();
        $this->clienteModel = new ClienteModel();
    }

    // PUNTO DE VENTA
    public function puntoVenta()
    {
        $this->checkLogin();
        
    
        $productos = $this->productoModel->where('activo', 1)->findAll();
        $clientes = $this->clienteModel->findAll();

        $data = [
            'title' => 'Punto de Venta',
            'productos' => $productos,
            'clientes' => $clientes
        ];

        return view('ventas/punto_venta', $data);
    }

    // BUSCAR PRODUCTO POR CÓDIGO (SCANNER)
    public function buscarProducto($codigo)
    {
        $this->checkLogin();

        $producto = $this->productoModel->where('codigo', $codigo)
                                        ->where('activo', 1)
                                        ->first();

        if ($producto) {
            return $this->response->setJSON([
                'success' => true,
                'producto' => $producto
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Producto no encontrado'
            ]);
        }
    }

    // PROCESAR VENTA 
    public function procesarVenta()
{
    $this->checkLogin();

    if (!$this->request->is('post')) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Método no permitido'
        ]);
    }

    // Obtener datos
    $input = $this->request->getJSON(true) ?: $this->request->getPost();
    
    $carrito = $input['carrito'] ?? [];
    $cliente_id = $input['cliente_id'] ?? null;
    $efectivo = floatval($input['efectivo'] ?? 0);
    $total = floatval($input['total'] ?? 0);

    // Validaciones
    if (empty($carrito)) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'El carrito está vacío'
        ]);
    }

    if ($efectivo <= 0) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'El efectivo debe ser mayor a 0'
        ]);
    }

    if ($efectivo < $total) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'El efectivo recibido es menor al total'
        ]);
    }

    $cambio = $efectivo - $total;

    // Transacción
    $db = \Config\Database::connect();
    $db->transStart();

    try {
        $folio = 'V' . date('YmdHis') . rand(100, 999);

        $session = session();
        $user_id = $session->get('user_id') ?: 1;

        $ventaData = [
            'folio' => $folio,
            'cliente_id' => !empty($cliente_id) ? $cliente_id : null,
            'usuario_id' => $user_id,
            'total' => $total,
            'efectivo' => $efectivo,
            'cambio' => $cambio,
            'estado' => 'completada'
        ];

        $venta_id = $this->ventaModel->insert($ventaData);
        if (!$venta_id) {
            throw new \Exception('Error al crear la venta');
        }

        foreach ($carrito as $item) {
            if (!empty($item['materia_prima_id'])) {

    $materiaModel = new \App\Models\MateriaModel();
    $materia = $materiaModel->find($item['materia_prima_id']);

    if (!$materia) {
        throw new \Exception('Materia prima no encontrada');
    }

    if ($materia['cantidad'] < $item['cantidad']) {
        throw new \Exception('Cantidad insuficiente de ' . $materia['nombre']);
    }

    $materiaModel->update(
        $materia['id'],
        ['cantidad' => $materia['cantidad'] - $item['cantidad']]
    );
}

            $producto = $this->productoModel->find($item['id']);
            if (!$producto) {
                throw new \Exception('Producto no encontrado: ' . $item['id']);
            }

            // ✅ Verificar stock solo si es bebida (categoria_id = 2)
            if ($producto['categoria_id'] == 2 && $item['cantidad'] > $producto['stock']) {
                throw new \Exception('Stock insuficiente para: ' . $producto['nombre']);
            }

            $detalleData = [
                'venta_id' => $venta_id,
                'producto_id' => $item['id'],
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['precio'],
                'subtotal' => $item['subtotal']
            ];
            $this->detalleVentaModel->insert($detalleData);

            // ✅ Actualizar stock solo para bebidas
            if ($producto['categoria_id'] == 2) {
                $nuevoStock = $producto['stock'] - $item['cantidad'];
                $this->productoModel->update($item['id'], ['stock' => $nuevoStock]);
            }
        }

        $db->transCommit();

        return $this->response->setJSON([
            'success' => true,
            'venta_id' => $venta_id,
            'folio' => $folio,
            'cambio' => $cambio,
            'message' => 'Venta procesada correctamente'
        ]);

    } catch (\Exception $e) {
        $db->transRollback();
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error al procesar venta: ' . $e->getMessage()
        ]);
    }
}

    // HISTORIAL DE VENTAS
    public function historial()
    {
        $this->checkLogin();

        $fecha = $this->request->getGet('fecha');
        $folio = $this->request->getGet('folio');

        // [ESCRIBE AQUÍ] - Ajusta los joins según tus relaciones de base de datos
        $builder = $this->ventaModel->select('ventas.*, clientes.nombre as cliente_nombre')
                                  ->join('clientes', 'clientes.id = ventas.cliente_id', 'left');

        if (!empty($fecha)) {
            $builder->where('DATE(fecha_venta)', $fecha);
        }

        if (!empty($folio)) {
            $builder->like('folio', $folio);
        }

        $ventas = $builder->orderBy('fecha_venta', 'DESC')->findAll();

        $data = [
            'title' => 'Historial de Ventas',
            'ventas' => $ventas,
            'fecha' => $fecha,
            'folio' => $folio
        ];

        return view('ventas/historial', $data);
    }

    // DETALLE DE VENTA
    public function detalle($id)
    {
        $this->checkLogin();

        // Ajusta los campos según tus tablas
        $venta = $this->ventaModel->select('ventas.*, clientes.nombre as cliente_nombre, clientes.telefono, clientes.direccion')
                                 ->join('clientes', 'clientes.id = ventas.cliente_id', 'left')
                                 ->find($id);

        if (!$venta) {
            return redirect()->to('/ventas/historial')->with('error', 'Venta no encontrada');
        }

        $detalles = $this->detalleVentaModel->select('detalle_ventas.*, productos.nombre as producto_nombre, productos.codigo as producto_codigo')
                                           ->join('productos', 'productos.id = detalle_ventas.producto_id')
                                           ->where('venta_id', $id)
                                           ->findAll();

        $data = [
            'title' => 'Detalle de Venta',
            'venta' => $venta,
            'detalles' => $detalles
        ];

        return view('ventas/detalle', $data);
    }

public function imprimirTicket($id)
{
    $configModel = new ConfiguracionModel();
    $config = $configModel->first();

    $ventaModel = new VentaModel();
    $venta = $ventaModel->find($id);

    $detalleModel = new DetalleVentaModel();
    $detalles = $detalleModel
        ->select('detalle_ventas.*, productos.nombre as producto_nombre')
        ->join('productos', 'productos.id = detalle_ventas.producto_id')
        ->where('detalle_ventas.venta_id', $id)
        ->findAll();

   
    $connector = new WindowsPrintConnector("POS-58");// nombre de tu impresora (AQUI COLOCAR EL NOMBRE)
    $printer = new Printer($connector);

  
    $max_chars = 32; 


 
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->setEmphasis(true);
    $printer->text(($config['nombre_tienda'] ?? 'MI TIENDA') . "\n");
    $printer->setEmphasis(false);

    if(!empty($config['direccion'])) $printer->text($config['direccion']."\n");
    if(!empty($config['telefono'])) $printer->text('Tel: '.$config['telefono']."\n");
    if(!empty($config['rfc'])) $printer->text('RFC: '.$config['rfc']."\n");

    $printer->text(str_repeat("-", $max_chars)."\n");

    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("Folio: ".$venta['folio']."\n");
    $printer->text("Fecha: ".date('d/m/Y H:i', strtotime($venta['fecha_venta']))."\n");
    $printer->text("Cliente: ".($venta['cliente_nombre'] ?? 'Cliente general')."\n");
    $printer->text("Atendio: ".session()->get('user_name')."\n");

    $printer->text(str_repeat("-", $max_chars)."\n");

   
    $col_cant = 4;
    $col_precio = 8;
    $col_producto = $max_chars - $col_cant - $col_precio;

    $printer->setEmphasis(true);
    $printer->text(str_pad("Cant",$col_cant)." ".str_pad("Producto",$col_producto)." ".str_pad("Importe",$col_precio,' ',STR_PAD_LEFT)."\n");
    $printer->setEmphasis(false);


    function splitText($text, $length) {
        $lines = [];
        while (strlen($text) > $length) {
            $lines[] = substr($text, 0, $length);
            $text = substr($text, $length);
        }
        $lines[] = $text;
        return $lines;
    }

    foreach($detalles as $d){
        $producto_lines = splitText($d['producto_nombre'], $col_producto);
        $cant = str_pad($d['cantidad'], $col_cant, ' ', STR_PAD_LEFT);
        $precio = str_pad(number_format($d['subtotal'],2), $col_precio, ' ', STR_PAD_LEFT);

       
        $printer->text($cant." ".$producto_lines[0].$precio."\n");

       
        for($i = 1; $i < count($producto_lines); $i++){
            $printer->text(str_repeat(' ', $col_cant)." ".$producto_lines[$i]."\n");
        }
    }

    $printer->text(str_repeat("-", $max_chars)."\n");


    $printer->text(str_pad("Subtotal: $".number_format($venta['total'],2), $max_chars, ' ', STR_PAD_LEFT)."\n");
    $printer->text(str_pad("Efectivo: $".number_format($venta['efectivo'],2), $max_chars, ' ', STR_PAD_LEFT)."\n");
    $printer->text(str_pad("Cambio: $".number_format($venta['cambio'],2), $max_chars, ' ', STR_PAD_LEFT)."\n");

    $printer->setEmphasis(true);
    $printer->text(str_pad("TOTAL: $".number_format($venta['total'],2), $max_chars, ' ', STR_PAD_LEFT)."\n");
    $printer->setEmphasis(false);

    $printer->text("\n");
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->text($config['mensaje_ticket'] ?? '¡Gracias por su compra!'."\n");
    $printer->text('*** Venta '.strtoupper($venta['estado']).' ***'."\n");
    $printer->text('Powered by POS System'."\n");

    $printer->cut();
    $printer->close();
}

    // CANCELAR VENTA
    public function cancelarVenta($id)
    {
        $this->checkLogin();

        $venta = $this->ventaModel->find($id);

        if (!$venta) {
            return redirect()->to('/ventas/historial')->with('error', 'Venta no encontrada');
        }

        // Restaurar stock
        $detalles = $this->detalleVentaModel->where('venta_id', $id)->findAll();
        
        foreach ($detalles as $detalle) {
            $producto = $this->productoModel->find($detalle['producto_id']);
            $nuevoStock = $producto['stock'] + $detalle['cantidad'];
            $this->productoModel->update($detalle['producto_id'], ['stock' => $nuevoStock]);
        }

        $this->ventaModel->update($id, ['estado' => 'cancelada']);

        return redirect()->to('/ventas/historial')->with('success', 'Venta cancelada correctamente');
    }
}