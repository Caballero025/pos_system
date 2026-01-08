<?= $this->include('layouts/header') ?>

<div class="page-header">
    <h1>➕ Crear Nuevo Cliente</h1>
    <a href="<?= base_url('admin/clientes') ?>" class="btn btn-secondary">🔙 Volver</a>
</div>

<form action="<?= base_url('admin/clientes/guardar') ?>" method="post">
    <div class="form-group">
        <label>Nombre *</label>
        <input type="text" name="nombre" class="form-control" required>
    </div>
    <div class="form-group">
        <label>Teléfono</label>
        <input type="text" name="telefono" class="form-control">
    </div>
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control">
    </div>
    <div class="form-group">
        <label>Dirección</label>
        <textarea name="direccion" class="form-control"></textarea>
    </div>
    <div class="form-group">
        <label>RFC</label>
        <input type="text" name="rfc" class="form-control">
    </div>
    <button type="submit" class="btn btn-primary">💾 Guardar Cliente</button>
</form>

<?= $this->include('layouts/footer') ?>