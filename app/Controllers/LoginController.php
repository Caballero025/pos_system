<?php
namespace App\Controllers;

use App\Models\UsuarioModel;

class LoginController extends BaseController
{
    public function index()
    {
    
        if(session()->get('logged_in')){
            return redirect()->to('/dashboard');
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
            // Verificar contraseña (en la base de datos está hasheada)
            if(password_verify($password, $user['password'])){
                $ses_data = [
                    'user_id' => $user['id'],
                    'user_name' => $user['nombre'],
                    'user_email' => $user['email'],
                    'user_role' => $user['rol'],
                    'logged_in' => TRUE
                ];
                $session->set($ses_data);
                return redirect()->to('/dashboard');
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