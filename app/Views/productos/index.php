<?= $this->include('layouts/header') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/productos.css') ?>">

<div class="page-header">
    <h1>Gestión de Productos</h1>
    <a href="<?= base_url('admin/productos/crear') ?>" class="btn btn-primary">➕ Agregar Producto</a>
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

<div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th>Imagen</th>
                <th>Código</th>
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
                    <td class="product-image-cell">
                        <div class="product-image">
                            <img src="<?= base_url('admin/uploads/productos/' . $producto['imagen']) ?>" 
                                 alt="<?= esc($producto['nombre']) ?>" 
                                 onerror="this.src='<?= base_url('admin/uploads/productos/default.png') ?>'"
                                 class="product-thumbnail">
                        </div>
                    </td>
                    <td><?= esc($producto['codigo']) ?></td>
                    <td>
                        <strong><?= esc($producto['nombre']) ?></strong>
                        <?php if (!empty($producto['descripcion'])): ?>
                            <br><small class="text-muted"><?= esc($producto['descripcion']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td class="text-right">$<?= number_format($producto['precio'], 2) ?></td>
                    <td>
                        <span class="stock-badge <?= $producto['stock'] <= $producto['stock_minimo'] ? 'stock-low' : 'stock-ok' ?>">
                            <?= $producto['stock'] ?>
                            <?php if ($producto['stock'] <= $producto['stock_minimo']): ?>
                                <br><small class="text-danger">¡Stock bajo!</small>
                            <?php endif; ?>
                        </span>
                    </td>
                    <td><?= esc($producto['categoria_nombre'] ?? 'Sin categoría') ?></td>
                    <td>
                        <span class="status-badge <?= $producto['activo'] ? 'status-active' : 'status-inactive' ?>">
                            <?= $producto['activo'] ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td class="actions">
                        <a href="<?= base_url('admin/productos/editar/' . $producto['id']) ?>" 
                           class="btn btn-edit" 
                           title="Editar producto">
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
            
            <?php if (empty($productos)): ?>
                <tr>
                    <td colspan="8" class="text-center">
                        <div class="empty-state">
                            <p>No se encontraron productos</p>
                            <a href="<?= base_url('admin/productos/crear') ?>" class="btn btn-primary">
                                ➕ Agregar primer producto
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</div><!-- content-area -->
</div><!-- main-content -->
</body>
</html>