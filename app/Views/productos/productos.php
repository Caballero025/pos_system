<?= $this->include('layouts/header') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/productos.css') ?>">

<div class="page-header">
    <h1>Gestión de Productos</h1>
    <a href="<?= base_url("admin/productos/crear?categoria_id=$categoria_id") ?>" class="btn btn-primary">
    ➕ Agregar Producto
</a>
</div>

<!-- Filtros de búsqueda -->
<form method="get" action="<?= base_url('admin/productos') ?>" class="filter-form">
    <div class="form-row">
        <div class="form-group">
            <input type="text" name="search" value="<?= esc($search) ?>" placeholder="Buscar por nombre, código o descripción...">
        </div>
        <div class="form-group">
            <select name="categoria_id">
                <option value="">Todas las categorías</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= $categoria['id'] ?>" <?= $categoria_id == $categoria['id'] ? 'selected' : '' ?>>
                        <?= esc($categoria['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn">🔍 Buscar</button>
        <a href="<?= base_url('admin/productos') ?>" class="btn btn-secondary">Limpiar</a>
    </div>
</form>

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



<?php if ($categoria_id == 1): ?>

    <!-- TABLA PARA CATEGORÍA 1 -->
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Categoria</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                    <tr> 
                        <td><?= esc($producto['id']) ?></td>
                        <td>
                            <img src="<?= base_url('uploads/productos/' . $producto['imagen']) ?>" class="product-thumbnail">
                        </td>
                        <td><?= esc($producto['nombre']) ?></td>
                        <td>$<?= number_format($producto['precio'], 2) ?></td>
                     <td><?= $producto['categoria_id'] ?></td>
                        <td><?= $producto['activo'] ? 'Activo' : 'Inactivo' ?></td>
                                </td>
                    <td class="actions">
                      <a href="<?= base_url('admin/productos/editar/' . $producto['id'] . '?categoria_id=' . $producto['categoria_id']) ?>" 
   class="btn btn-edit" title="Editar producto">
   ✏️ Editar
</a>

                        <form action="<?= base_url('admin/productos/eliminar/' . $producto['id']) ?>" 
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

<?php else: ?>

    <!-- TABLA PARA OTRAS CATEGORÍAS -->
    <div class="table-container">
        <table class="table table-alt">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Categoría</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td><?= esc($producto['id']) ?></td>
                        <td>
                            <img src="<?= base_url('uploads/productos/' . $producto['imagen']) ?>" class="product-thumbnail">
                        </td>
                        <td><?= esc($producto['nombre']) ?></td>
                        <td>$<?= number_format($producto['precio'], 2) ?></td>
                        <td><?= number_format($producto['stock'], 0, '', '') ?></td>
                        <td><?= esc($producto['categoria_nombre']) ?></td>
                        <td><?= $producto['activo'] ? 'Activo' : 'Inactivo' ?></td>
                           </td>
                    <td class="actions">
                    <a href="<?= base_url('admin/productos/editar/' . $producto['id'] . '?categoria_id=' . $producto['categoria_id']) ?>" 
   class="btn btn-edit" title="Editar producto">
   ✏️ Editar
</a>

                    <a href="<?= base_url('admin/productos/eliminar/' . $producto['id'] . '?categoria_id=' . $producto['categoria_id']) ?>" 
   class="btn btn-delete" 
   onclick="return confirm('¿Seguro que quieres eliminar este producto?')">
   🗑️ Eliminar
</a>
                    </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php endif; ?>

</div><!-- content-area -->
</div><!-- main-content -->
</body>
</html>