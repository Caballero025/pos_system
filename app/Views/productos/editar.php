<?= $this->include('layouts/header') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/productos.css') ?>">

<div class="page-header">
    <h1>Editar Producto</h1>
    <a href="<?= base_url('admin/productos') ?>" class="btn btn-cancel">← Volver</a>
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
    <form method="post" action="<?= base_url('admin/productos/actualizar/' . $producto['id']) ?>" enctype="multipart/form-data">
        <div class="form-group">
            <label class="form-label" for="codigo">Código del Producto *</label>
            <input type="text" id="codigo" name="codigo" class="form-input" 
                   value="<?= old('codigo', $producto['codigo']) ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="nombre">Nombre del Producto *</label>
            <input type="text" id="nombre" name="nombre" class="form-input" 
                   value="<?= old('nombre', $producto['nombre']) ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" class="form-input" 
                      rows="3"><?= old('descripcion', $producto['descripcion']) ?></textarea>
        </div>

        <!-- CAMPO DE IMAGEN ACTUALIZADO -->
        <div class="form-group">
            <label class="form-label">Imagen Actual</label>
            <div class="mb-2">
                <?php if(!empty($producto['imagen'])): ?>
                    <img src="admin//uploads/productos/<?= $producto['imagen'] ?>" 
                         alt="Imagen actual" 
                         style="max-width: 200px; max-height: 200px; border: 1px solid #ddd; border-radius: 4px;" 
                         class="img-thumbnail">
                    <br>
                    <small class="text-muted"><?= $producto['imagen'] ?></small>
                <?php else: ?>
                    <span class="text-muted">No hay imagen cargada</span>
                <?php endif; ?>
            </div>
            
            <label class="form-label" for="imagen">Cambiar Imagen (opcional)</label>
            <input type="file" id="imagen" name="imagen" class="form-input" accept="image/*">
            <small class="form-text text-muted">Dejar vacío para mantener la imagen actual</small>
        </div>

        <input type="hidden" name="imagen_actual" value="<?= $producto['imagen'] ?>">

        <div class="form-row" style="display: flex; gap: 15px;">
            <div class="form-group" style="flex: 1;">
                <label class="form-label" for="precio">Precio de Venta *</label>
                <input type="number" id="precio" name="precio" class="form-input" 
                       value="<?= old('precio', $producto['precio']) ?>" step="0.01" min="0" required>
            </div>

            <div class="form-group" style="flex: 1;">
                <label class="form-label" for="costo">Costo de Proveedor</label>
                <input type="number" id="costo" name="costo" class="form-input" 
                       value="<?= old('costo', $producto['costo']) ?>" step="0.01" min="0">
            </div>
        </div>

        <div class="form-row" style="display: flex; gap: 15px;">
            <div class="form-group" style="flex: 1;">
                <label class="form-label" for="stock">Stock Actual *</label>
                <input type="number" id="stock" name="stock" class="form-input" 
                       value="<?= old('stock', $producto['stock']) ?>" min="0" required>
            </div>

            <div class="form-group" style="flex: 1;">
                <label class="form-label" for="stock_minimo">Stock Mínimo</label>
                <input type="number" id="stock_minimo" name="stock_minimo" class="form-input" 
                       value="<?= old('stock_minimo', $producto['stock_minimo']) ?>" min="0">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="categoria_id">Categoría</label>
            <select id="categoria_id" name="categoria_id" class="form-input">
                <option value="">Seleccionar categoría</option>
                <?php foreach($categorias as $categoria): ?>
                    <option value="<?= $categoria['id'] ?>" 
                        <?= (old('categoria_id', $producto['categoria_id']) == $categoria['id']) ? 'selected' : '' ?>>
                        <?= esc($categoria['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Estado</label>
            <div>
                <label style="display: inline-flex; align-items: center; margin-right: 20px;">
                    <input type="radio" name="activo" value="1" 
                        <?= (old('activo', $producto['activo']) == 1) ? 'checked' : '' ?>> 
                    <span style="margin-left: 5px;">Activo</span>
                </label>
                <label style="display: inline-flex; align-items: center;">
                    <input type="radio" name="activo" value="0" 
                        <?= (old('activo', $producto['activo']) == 0) ? 'checked' : '' ?>> 
                    <span style="margin-left: 5px;">Inactivo</span>
                </label>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= base_url('admin/productos') ?>" class="btn-cancel">Cancelar</a>
            <button type="submit" class="btn btn-primary">💾 Actualizar Producto</button>
        </div>
    </form>
</div>

</div><!-- content-area -->
</div><!-- main-content -->
</body>
</html>