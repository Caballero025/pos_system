<?= $this->include('layouts/header') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/productos.css') ?>">

<div class="page-header">
    <h1>Agregar Nuevo Usuario</h1>
    <a href="<?= base_url('admin/usuarios') ?>" class="btn btn-cancel">← Volver</a>
</div>

<!-- Mensajes de error -->
<?php if (session('errors')): ?>
    <div class="alert alert-error">
        <ul>
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="form-card">
    <form method="post" action="<?= base_url('admin/usuarios/guardar') ?>">

        <div class="form-group">
            <label class="form-label" for="nombre">Nombre *</label>
            <input type="text" id="nombre" name="nombre" class="form-input" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Correo *</label>
            <input type="email" id="email" name="email" class="form-input" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Contraseña *</label>
            <input type="password" id="password" name="password" class="form-input" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="rol">Rol *</label>
            <select id="rol" name="rol" class="form-input" required>
                <option value="">Seleccionar rol</option>
                <option value="admin">Administrador</option>
                <option value="vendedor">Vendedor</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Estado</label>
            <div>
                <label style="display: inline-flex; align-items: center; margin-right: 20px;">
                    <input type="radio" name="activo" value="1" checked>
                    <span style="margin-left: 5px;">Activo</span>
                </label>

                <label style="display: inline-flex; align-items: center;">
                    <input type="radio" name="activo" value="0">
                    <span style="margin-left: 5px;">Inactivo</span>
                </label>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= base_url('admin/usuarios') ?>" class="btn btn-cancel">Cancelar</a>
            <button type="submit" class="btn btn-primary">💾 Guardar Usuario</button>
        </div>

    </form>
</div>

</div><!-- content-area -->
</div><!-- main-content -->
</body>
</html>
