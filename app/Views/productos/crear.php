<?= $this->include('layouts/header') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/productos.css') ?>">

<div class="page-header">
    <h1>Agregar Nuevo Producto</h1>
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
    <form method="post" action="<?= base_url('admin/productos/guardar') ?>" enctype="multipart/form-data">
        <div class="form-group">
            <label class="form-label" for="codigo">Código del Producto *</label>
            <input type="text" id="codigo" name="codigo" class="form-input" 
                   value="<?= old('codigo') ?>" required 
                   placeholder="Ej: PROD001">
        </div>

        <div class="form-group">
            <label class="form-label" for="nombre">Nombre del Producto *</label>
            <input type="text" id="nombre" name="nombre" class="form-input" 
                   value="<?= old('nombre') ?>" required 
                   placeholder="Ej: Refresco">
        </div>

        <div class="form-group">
            <label class="form-label" for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" class="form-input" 
                      rows="3" placeholder="Descripción del producto..."><?= old('descripcion') ?></textarea>
        </div>

        <!-- NUEVO CAMPO PARA IMAGEN -->
        <div class="form-group">
            <label class="form-label" for="imagen">Imagen del Producto</label>
            <input type="file" id="imagen" name="imagen" class="form-input" accept="image/*">
            <small class="form-text text-muted">Formatos: JPG, PNG, GIF. Máx: 2MB</small>
        </div>

        <div class="form-row" style="display: flex; gap: 15px;">
            <div class="form-group" style="flex: 1;">
                <label class="form-label" for="precio">Precio de Venta *</label>
                <input type="number" id="precio" name="precio" class="form-input" 
                       value="<?= old('precio') ?>" step="0.01" min="0" required 
                       placeholder="0.00">
            </div>

            <div class="form-group" style="flex: 1;">
                <label class="form-label" for="costo">Costo de Proveedor</label>
                <input type="number" id="costo" name="costo" class="form-input" 
                       value="<?= old('costo') ?>" step="0.01" min="0" 
                       placeholder="0.00">
            </div>
        </div>

        <div class="form-row" style="display: flex; gap: 15px;">
            <div class="form-group" style="flex: 1;">
                <label class="form-label" for="stock">Stock Actual *</label>
                <input type="number" id="stock" name="stock" class="form-input" 
                       value="<?= old('stock', 0) ?>" min="0" required 
                       placeholder="0">
            </div>

            <div class="form-group" style="flex: 1;">
                <label class="form-label" for="stock_minimo">Stock Mínimo</label>
                <input type="number" id="stock_minimo" name="stock_minimo" class="form-input" 
                       value="<?= old('stock_minimo', 5) ?>" min="0" 
                       placeholder="5">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="categoria_id">Categoría</label>
            <select id="categoria_id" name="categoria_id" class="form-input">
                <option value="">Seleccionar categoría</option>
                <?php foreach($categorias as $categoria): ?>
                    <option value="<?= $categoria['id'] ?>" 
                        <?= old('categoria_id') == $categoria['id'] ? 'selected' : '' ?>>
                        <?= esc($categoria['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-actions">
            <a href="<?= base_url('admin/productos') ?>" class="btn-cancel">Cancelar</a>
            <button type="submit" class="btn btn-primary">💾 Guardar Producto</button>
        </div>
    </form>
</div>

</div><!-- content-area -->
</div><!-- main-content -->
</body>
</html>