<?= $this->include('layouts/header') ?>

<div class="page-header">
    <h1>👥 Gestión de Clientes</h1>
    <a href="<?= base_url('admin/clientes/crear') ?>" class="btn btn-primary">➕ Nuevo Cliente</a>
</div>

<?php if (session('success')): ?>
    <div class="alert alert-success"><?= session('success') ?></div>
<?php endif; ?>

<?php if (session('error')): ?>
    <div class="alert alert-danger"><?= session('error') ?></div>
<?php endif; ?>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>Dirección</th>
            <th>RFC</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($clientes as $cliente): ?>
            <tr>
                <td><?= $cliente['nombre'] ?></td>
                <td><?= $cliente['telefono'] ?></td>
                <td><?= $cliente['email'] ?></td>
                <td><?= $cliente['direccion'] ?></td>
                <td><?= $cliente['rfc'] ?></td>
                <td>
                    <a href="<?= base_url('admin/clientes/editar/' . $cliente['id']) ?>" class="btn btn-sm btn-warning">✏️ Editar</a>
                    <a href="<?= base_url('admin/clientes/eliminar/' . $cliente['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar cliente?')">🗑️ Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $this->include('layouts/footer') ?>