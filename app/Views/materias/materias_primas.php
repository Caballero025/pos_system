<?= $this->include('layouts/header') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/productos.css') ?>">

<div class="page-header">
    <h1>Gestión de Materias Primas</h1>
<a href="<?= base_url('admin/materias/crear') ?>" class="btn btn-primary">
    ➕ Agregar Materia
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





    <!-- TABLA PARA CATEGORÍA 1 -->
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Categoria</th>
                    <th>Medida</th>
                    <th>Cantidad</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($materias as $materia): ?>
                    <tr> 
                        <td><?= esc($materia['id']) ?></td>
                        <td><?= esc($materia['nombre']) ?></td>
                        <td>$<?= number_format($materia['precio'], 2) ?></td>
                     <td><?= $materia['categoria_nombre'] ?></td>
                     <td><?= $materia['medida_nombre'] ?></td>
                     <td><?= number_format($materia['cantidad']) ?></td>
                        <td><?= $materia['activo'] ? 'Activo' : 'Inactivo' ?></td>
                                </td>
                    <td class="actions">
  <a href="<?= base_url('admin/materias/editar/' . $materia['id']) ?>" 
                           class="btn btn-edit" 
                           title="Editar materia">
                            ✏️ Editar
                        </a>
    
                        <form action="<?= base_url('admin/materias/eliminar/' . $materia['id']) ?>" 
                              method="post" 
                              class="delete-form"
                              onsubmit="return confirm('¿Estás seguro de eliminar este producto?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-delete" title="Eliminar producto">
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
</div><!-- main-content -->
</body>
</html>