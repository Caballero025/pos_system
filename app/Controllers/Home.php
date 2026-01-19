<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // Verificar si estamos en Electron
        $isElectron = isset($_SERVER['HTTP_USER_AGENT']) && 
                     strpos($_SERVER['HTTP_USER_AGENT'], 'Electron') !== false;
        
        $data = [
            'title' => 'Sistema POS - Inicio',
            'is_electron' => $isElectron,
            'app_name' => 'Sistema Punto de Venta',
            'version' => '1.0.0'
        ];
        
        return view('welcome_message', $data);
    }
    
    public function dashboard()
    {
        // Verificar sesión
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
        
        $data = [
            'title' => 'Dashboard - Sistema POS',
            'user' => session()->get('user'),
            'is_electron' => true
        ];
        
        return view('dashboard', $data);
    }
    
    public function apiStatus()
    {
        return $this->response->setJSON([
            'status' => 'online',
            'timestamp' => date('Y-m-d H:i:s'),
            'port' => 8585,
            'environment' => ENVIRONMENT
        ]);
    }
}