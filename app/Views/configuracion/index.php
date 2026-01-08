<?= $this->include('layouts/header') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/reportes.css') ?>">
<!-- Usamos el mismo CSS que reportes para consistencia -->

<div class="reportes-container">
    <div class="page-header">
        <h1>⚙️ Configuración del Sistema</h1>
        <a href="<?= base_url('dashboard') ?>" class="btn">🏠 Inicio</a>
    </div>

    <!-- Mensajes de éxito/error -->
    <?php if (session('success')): ?>
        <div class="alert-reporte alert-success">
            ✅ <?= session('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session('error')): ?>
        <div class="alert-reporte alert-danger">
            ❌ <?= session('error') ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Datos de la Tienda -->
        <div class="col-md-6">
            <div class="card-reporte">
                <div class="card-header-reporte">
                    🏪 Datos de la Tienda
                </div>
                <div class="card-body-reporte">
                    <form action="<?= base_url('admin/configuracion/guardar-tienda') ?>" method="post">
                        <div class="form-group" style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">Nombre de la Tienda:</label>
                            <input type="text" name="nombre_tienda" class="form-control-reporte" 
                                   value="<?= esc($configuracion['nombre_tienda'] ?? '') ?>" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">Dirección:</label>
                            <input type="text" name="direccion" class="form-control-reporte" 
                                   value="<?= esc($configuracion['direccion'] ?? '') ?>">
                        </div>
                        <div class="form-group" style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">Teléfono:</label>
                            <input type="text" name="telefono" class="form-control-reporte" 
                                   value="<?= esc($configuracion['telefono'] ?? '') ?>">
                        </div>
                        <div class="form-group" style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">Email:</label>
                            <input type="email" name="email" class="form-control-reporte" 
                                   value="<?= esc($configuracion['email'] ?? '') ?>">
                        </div>
                        <div class="form-group" style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">RFC:</label>
                            <input type="text" name="rfc" class="form-control-reporte" 
                                   value="<?= esc($configuracion['rfc'] ?? '') ?>">
                        </div>
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">Mensaje en Ticket:</label>
                            <textarea name="mensaje_ticket" class="form-control-reporte" rows="3" style="min-height: 80px;"><?= esc($configuracion['mensaje_ticket'] ?? '¡Gracias por su compra!') ?></textarea>
                            <small style="color: #666; font-size: 12px;">Este mensaje aparecerá en los tickets de venta</small>
                        </div>
                        <button type="submit" class="btn-reporte" style="background: #28a745; color: white; border: none; text-align: center;">
                            💾 Guardar Configuración
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Gestión de Usuarios -->
 <?php if (session()->get('user_role') === 'admin'): ?>

<!-- Gestión de Usuarios -->
<div class="col-md-6">
    <div class="card-reporte">
        <div class="card-header-reporte">
            👥 Gestión de Usuarios
        </div>
        <div class="card-body-reporte">
            <form action="<?= base_url('admin/configuracion/crear-usuario') ?>" method="post">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">Nombre:</label>
                    <input type="text" name="nombre" class="form-control-reporte" required>
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">Email:</label>
                    <input type="email" name="email" class="form-control-reporte" required>
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">Contraseña:</label>
                    <input type="password" name="password" class="form-control-reporte" required>
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">Rol:</label>
                    <select name="rol" class="form-control-reporte" required>
                        <option value="vendedor">Vendedor</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <button type="submit" class="btn-reporte" style="background: #667eea; color: white; border:none;text-align:center;">
                    👤 Crear Usuario
                </button>
            </form>

            <!-- Lista de Usuarios Existentes -->
            <div style="margin-top: 30px;">
                <h4 style="margin-bottom: 15px; color: #333;">Usuarios Existentes</h4>
                <div class="lista-reportes">
                    <?php foreach($usuarios as $usuario): ?>
                        <div class="item-reporte">
                            <div>
                                <strong style="color: #333;"><?= esc($usuario['nombre']) ?></strong>
                                <div style="font-size: 12px; color: #666;"><?= esc($usuario['email']) ?></div>
                            </div>
                            <div>
                                <span class="badge-reporte <?= $usuario['rol'] == 'admin' ? 'badge-primary' : 'badge-info' ?>" style="margin-right: 5px;">
                                    <?= $usuario['rol'] ?>
                                </span>
                                <span class="badge-reporte <?= $usuario['activo'] ? 'badge-success' : 'badge-danger' ?>">
                                    <?= $usuario['activo'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

<?php endif; ?>

    </div>
</div>


<?= $this->include('layouts/footer') ?>