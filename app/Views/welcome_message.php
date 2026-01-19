<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }
        
        .container {
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 800px;
            width: 90%;
        }
        
        .logo {
            font-size: 4em;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        
        h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            color: white;
        }
        
        .subtitle {
            font-size: 1.2em;
            opacity: 0.9;
            margin-bottom: 30px;
        }
        
        .status-badge {
            display: inline-block;
            background: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
            padding: 8px 20px;
            border-radius: 20px;
            margin: 20px 0;
            border: 2px solid #2ecc71;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
            text-align: left;
        }
        
        .info-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }
        
        .info-card h3 {
            color: #88c0d0;
            margin-bottom: 10px;
        }
        
        .actions {
            margin-top: 30px;
        }
        
        .btn {
            display: inline-block;
            background: white;
            color: #667eea;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
            margin: 0 10px;
            text-decoration: none;
            transition: transform 0.3s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            background: #f0f0f0;
        }
        
        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .electron-badge {
            position: fixed;
            top: 10px;
            right: 10px;
            background: #764ba2;
            color: white;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 0.8em;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
    </style>
</head>
<body>
    <?php if ($is_electron): ?>
    <div class="electron-badge">🖥️ Electron App</div>
    <?php endif; ?>
    
    <div class="container">
        <div class="logo">🛒</div>
        <h1><?= $app_name ?></h1>
        <div class="subtitle">Sistema de Gestión de Ventas y Inventario</div>
        
        <div class="status-badge">
            ✅ Sistema funcionando correctamente
        </div>
        
        <div class="info-grid">
            <div class="info-card">
                <h3>📊 Estado del Sistema</h3>
                <p>Servidor: <strong>En línea</strong></p>
                <p>Puerto: <strong>8585</strong></p>
                <p>Entorno: <strong><?= ENVIRONMENT ?></strong></p>
            </div>
            
            <div class="info-card">
                <h3>🗄️ Base de Datos</h3>
                <p>Motor: <strong>MySQL</strong></p>
                <p>Estado: <strong>Conectado</strong></p>
                <p>Versión: <strong><?= $version ?></strong></p>
            </div>
            
            <div class="info-card">
                <h3>⚡ Tecnologías</h3>
                <p>Backend: <strong>CodeIgniter 4</strong></p>
                <p>Frontend: <strong>Electron</strong></p>
                <p>PHP: <strong><?= phpversion() ?></strong></p>
            </div>
        </div>
        
        <div class="actions">
            <a href="/login" class="btn">🚀 Iniciar Sistema</a>
            <a href="/config" class="btn btn-secondary">⚙️ Configuración</a>
        </div>
    </div>
    
    <script>
        // Comunicación con Electron
        if (window.electronAPI) {
            console.log('Electron API disponible');
            
            // Obtener información de la aplicación
            window.electronAPI.getAppInfo().then(info => {
                console.log('App Info:', info);
            });
        }
        
        // Verificar estado del servidor cada 5 segundos
        setInterval(() => {
            fetch('/api/status')
                .then(response => response.json())
                .then(data => {
                    console.log('Server status:', data);
                })
                .catch(error => {
                    console.error('Server check failed:', error);
                });
        }, 5000);
    </script>
</body>
</html>