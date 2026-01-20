<?php
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'LoginController::index');
$routes->get('/login', 'LoginController::index');
$routes->post('/login/auth', 'LoginController::auth');
$routes->get('/logout', 'LoginController::logout');
$routes->get('/dashboard', 'DashboardController::index');
$routes->get('materias-primas/obtener/(:num)', 'MateriasController::obtenerPorCategoria/$1');
$routes->group('admin', ['filter' => 'admin'], function($routes) {
// Rutas de Productos
$routes->get('productos', 'ProductosController::index');
$routes->get(
    'productos/categoria/(:num)',
    'ProductosController::productos/$1'
);
$routes->get('productos/crear', 'ProductosController::crear');
$routes->post('productos/guardar', 'ProductosController::guardar');
$routes->get('productos/editar/(:num)', 'ProductosController::editar/$1');
$routes->post('productos/actualizar/(:num)', 'ProductosController::actualizar/$1');
$routes->get('productos/eliminar/(:num)', 'ProductosController::eliminar/$1'); // Para enlaces GET
$routes->delete('productos/eliminar/(:num)', 'ProductosController::eliminar/$1'); // Para formularios DELETE
// Rutas de materias
$routes->get('materias', 'MateriasController::materias');
$routes->get('materias/crear', 'MateriasController::crear');
$routes->post('materias/guardar', 'MateriasController::guardar');
$routes->get('materias/editar/(:num)', 'MateriasController::editar/$1');
$routes->post('materias/actualizar/(:num)', 'MateriasController::actualizar/$1');
$routes->get('materias/eliminar/(:num)', 'MateriasController::eliminar/$1'); // Para enlaces GET
$routes->delete('materias/eliminar/(:num)', 'MateriasController::eliminar/$1'); // Para formularios DELETE
// Rutas de Reportes
$routes->get('reportes', 'ReportesController::index');
$routes->get('reportes/ventas', 'ReportesController::ventas');
$routes->get('reportes/productos', 'ReportesController::productos');
$routes->get('reportes/clientes', 'ReportesController::clientes');

// Rutas de Configuración
$routes->get('configuracion', 'ConfiguracionController::index');
$routes->post('configuracion/guardar-tienda', 'ConfiguracionController::guardarTienda');
$routes->post('configuracion/crear-usuario', 'ConfiguracionController::crearUsuario');

// Rutas de Clientes
$routes->get('clientes', 'CliesntesController::index');
$routes->get('clientes/crear', 'ClientesController::crear');
$routes->post('clientes/guardar', 'ClientesController::guardar');
$routes->get('clientes/editar/(:num)', 'ClientesController::editar/$1');
$routes->post('clientes/actualizar/(:num)', 'ClientesController::actualizar/$1');
$routes->get('clientes/eliminar/(:num)', 'ClientesController::eliminar/$1');
$routes->get('clientes/buscar', 'ClientesController::buscar');
$routes->get('usuarios', 'UsuarioController::index');
$routes->get('usuarios/crear', 'UsuarioController::crear');
$routes->post('usuarios/guardar', 'UsuarioController::guardar');
$routes->get('usuarios/editar/(:num)', 'UsuarioController::editar/$1');
$routes->post('usuarios/actualizar/(:num)', 'UsuarioController::actualizar/$1');
$routes->get('usuarios/eliminar/(:num)', 'UsuarioController::eliminar/$1');

// ✅ RUTA DEL PAGO
$routes->post('usuarios/guardar-pago/(:num)', 'UsuarioController::guardarPago/$1');

});
// Rutas de Ventas
$routes->get('/ventas', 'VentasController::puntoVenta');
$routes->get('/ventas/historial', 'VentasController::historial');
$routes->post('/ventas/procesar', 'VentasController::procesarVenta');
$routes->get('/ventas/buscar-producto/(:any)', 'VentasController::buscarProducto/$1');
$routes->get('/ventas/detalle/(:num)', 'VentasController::detalle/$1');
$routes->get('/ventas/detalle_historial/(:num)', 'VentasController::detalle_historial/$1');
$routes->get('/ventas/imprimir/(:num)', 'VentasController::imprimirTicket/$1');
$routes->get('/ventas/cancelar/(:num)', 'VentasController::cancelarVenta/$1');


// Mostrar vista (si la usas, opcional)
$routes->get(
    'admin/materias/agregar-cantidad/(:num)',
    'MateriasController::agregarCantidad/$1'
);

// Guardar cantidad desde el modal
$routes->post(
    'admin/materias/guardar-cantidad/(:num)',
    'MateriasController::guardarCantidad/$1'
);




