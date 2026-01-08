<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #ffffffff 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h1 {
            color: #333;
            margin: 0;
            font-size: 24px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .form-group input:focus {
            border-color: #667eea;
            outline: none;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-login:hover {
            background: #764ba2;
        }
        .alert {
            padding: 10px;
            background: #f8d7da;
            color: #721c24;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>BIENVENIDO!!!</h1>
            <p>Iniciar Sesión</p>
        </div>
        
        <?php if(session()->getFlashdata('msg')): ?>
            <div class="alert">
                <?= session()->getFlashdata('msg') ?>
            </div>
        <?php endif; ?>
        
       <form action="/login/auth" method="post">
    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required value="admin@pos.com">
    </div>
    
    <div class="form-group">
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required value="admin123">
    </div>
    
    <button type="submit" class="btn-login">Ingresar</button>
</form>
        
        <div style="text-align: center; margin-top: 20px; color: #666;">
            <strong>Usuario demo:</strong> admin@pos.com / admin123
        </div>
    </div>
    
</body>
</html>