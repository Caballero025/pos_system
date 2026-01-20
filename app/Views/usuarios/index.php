<?= $this->include('layouts/header') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/productos.css') ?>">

<div class="page-header">
    <h1>Gestión de Personal</h1>
    <a href="<?= base_url("admin/usuarios/crear") ?>" class="btn btn-primary">
        ➕ Agregar Vendedor
    </a>
</div>

<!-- Mensajes -->
<?php if (session('success')): ?>
    <div class="alert alert-success">
        <?= session('success') ?>
    </div>
<?php endif; ?>

<?php if (session('error')): ?>
    <div class="alert alert-error">
        <?= session('error') ?>
    </div>
<?php endif; ?>

<?php if (session('debug')): ?>
    <div class="alert alert-warning">
        <strong>Debug:</strong> <?= session('debug') ?>
    </div>
<?php endif; ?>

<div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?= esc($usuario['id']) ?></td>
                    <td><?= esc($usuario['nombre']) ?></td>
                    <td><?= esc($usuario['email']) ?></td>
                    <td><?= esc($usuario['rol']) ?></td>
                    <td><?= $usuario['activo'] ? 'Activo' : 'Inactivo' ?></td>
                    <td class="actions">

                    <a href="#"
   class="btn btn-add"
   onclick="abrirModalPago(<?= $usuario['id'] ?>, '<?= esc($usuario['nombre']) ?>')">
   💰 Registrar Pago
</a>

                        <a href="<?= base_url('admin/usuarios/editar/' . $usuario['id']) ?>" class="btn btn-edit" title="Editar vendedor">
                            ✏️ Editar
                        </a>

                        <form action="<?= base_url('admin/usuarios/eliminar/' . $usuario['id']) ?>" method="post" class="delete-form" onsubmit="return confirm('¿Estás seguro de eliminar este vendedor?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-delete" title="Eliminar vendedor">
                                🗑️ Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</div><!-- content-area -->

<div id="modalPago" class="modal">
    <div class="modal-content">
        <h3 id="modalTituloPago"></h3>

        <form method="post" id="formPago">
            <?= csrf_field() ?>

            <label>Monto a pagar</label>
            <input type="number" step="0.01" name="monto" required>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="cerrarModalPago()">Cancelar</button>
                <button type="submit" class="btn-save">Guardar</button>
            </div>
        </form>
    </div>
</div>
<script>
function abrirModalPago(id, nombre) {
    document.getElementById('modalPago').style.display = 'flex';
    document.getElementById('modalTituloPago').innerText =
        'Registrar pago – ' + nombre;

    document.getElementById('formPago').action =
        '<?= base_url("admin/usuarios/guardar-pago/") ?>' + id;
}

function cerrarModalPago() {
    document.getElementById('modalPago').style.display = 'none';
}
</script>

</div><!-- main-content -->
</body>
</html>
