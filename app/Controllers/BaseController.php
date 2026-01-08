<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class BaseController extends Controller
{
    protected $helpers = ['form', 'url', 'html'];

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }
    
    protected function checkLogin()
    {
        $session = session();
        if(!$session->get('logged_in')){
            return redirect()->to('/login');
        }
        
        // Asegurarse de que tenemos un user_id válido
        if (!$session->get('user_id')) {
            // [ESCRIBE AQUÍ] - Intentar obtener de otras posibles claves de sesión
            $possibleKeys = ['id', 'user_id', 'usuario_id', 'userId'];
            foreach ($possibleKeys as $key) {
                if ($value = $session->get($key)) {
                    $session->set('user_id', $value);
                    break;
                }
            }
            
            
            // CAMBIA ESTE NÚMERO POR EL ID DE TU USUARIO PRINCIPAL EN LA BASE DE DATOS
            if (!$session->get('user_id')) {
                $default_user_id = 1; // ← CAMBIA ESTE VALOR
                $session->set('user_id', $default_user_id);
            }
        }
    }
}