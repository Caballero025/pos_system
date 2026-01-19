<?php
namespace App\Controllers;

use App\Models\UsuarioModel;

class LoginController extends BaseController
{
public function index()
{
    if (session()->get('logged_in')) {

        // Verifica el rol del usuario
        $rol = session()->get('user_role'); // <-- CORRECTO

        if ($rol == 'admin') {
            return redirect()->to('/dashboard');
        } else {
            return redirect()->to('/ventas'); // <-- ruta del punto de venta
        }

    }

    return view('login');
}



   public function auth()
{
    $session = session();
    $model = new UsuarioModel();
    
    $email = $this->request->getPost('email');
    $password = $this->request->getPost('password');
    
    $user = $model->where('email', $email)->first();

    if($user){
        if(password_verify($password, $user['password'])){
            $ses_data = [
                'user_id' => $user['id'],
                'user_name' => $user['nombre'],
                'user_email' => $user['email'],
                'user_role' => $user['rol'],
                'logged_in' => TRUE
            ];
            $session->set($ses_data);

            // REDIRECCIÓN POR ROL
            if ($user['rol'] == 'admin') {
                return redirect()->to('/dashboard');
            } else {
                return redirect()->to('/ventas');
            }

        }else{
            $session->setFlashdata('msg', 'Contraseña incorrecta');
            return redirect()->to('/login');
        }
    }else{
        $session->setFlashdata('msg', 'Email no encontrado');
        return redirect()->to('/login');
    }
}


    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/login');
    }
}