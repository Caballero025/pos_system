<?= $this->include('layouts/header') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/productos.css') ?>">

<div class="page-header">
    <h1>Editar Usuario</h1>
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
 <form method="post" action="<?= base_url('admin/usuarios/actualizar/' . $usuario['id']) ?>">
    <?= csrf_field() ?>

    <!-- IMPORTANTE: rol oculto -->
    <input type="hidden" name="rol" value="vendedor">

    <div class="form-group">
        <label class="form-label" for="nombre">Nombre *</label>
        <input type="text" id="nombre" name="nombre" class="form-input"
               value="<?= old('nombre', $usuario['nombre']) ?>" required>
    </div>

    <div class="form-group">
        <label class="form-label" for="email">Correo *</label>
        <input type="email" id="email" name="email" class="form-input"
               value="<?= old('email', $usuario['email']) ?>" required>
    </div>

    <div class="form-group">
        <label class="form-label" for="password">Nueva Contraseña (opcional)</label>
        <input type="password" id="password" name="password" class="form-input">
        <small class="form-text text-muted">Dejar vacío para mantener la contraseña actual</small>
    </div>

    <div class="form-group">
        <label class="form-label">Estado</label>
        <div>
            <label style="display: inline-flex; align-items: center; margin-right: 20px;">
                <input type="radio" name="activo" value="1"
                    <?= (old('activo', $usuario['activo']) == 1) ? 'checked' : '' ?>>
                <span style="margin-left: 5px;">Activo</span>
            </label>

            <label style="display: inline-flex; align-items: center;">
                <input type="radio" name="activo" value="0"
                    <?= (old('activo', $usuario['activo']) == 0) ? 'checked' : '' ?>>
                <span style="margin-left: 5px;">Inactivo</span>
            </label>
        </div>
    </div>

    <div class="form-actions">
        <a href="<?= base_url('admin/usuarios') ?>" class="btn-cancel">Cancelar</a>
        <button type="submit" class="btn btn-primary">💾 Actualizar Usuario</button>
    </div>
</form>

</div>

</div><!-- content-area -->
</div><!-- main-content -->
</body>
</html>
