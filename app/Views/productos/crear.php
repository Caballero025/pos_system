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
<?php if ($categoria_id == 1): ?>
        <div class="form-group">
            <label class="form-label" for="nombre">Nombre del Producto *</label>
            <input type="text" id="nombre" name="nombre" class="form-input" 
                   value="<?= old('nombre') ?>" required 
                   placeholder="Ej: Tacos campechanos">
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

                     <div class="form-group">
            <label class="form-label" for="medida_id">Unidad de medida</label>
            <select id="medida_id" name="medida_id" class="form-input">
                <option value="">Seleccionar medida</option>
                <?php foreach($medidas as $medida): ?>
                    <option value="<?= $medida['id'] ?>" 
                        <?= old('medida_id') == $medida['id'] ? 'selected' : '' ?>>
                        <?= esc($medida['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-actions">
            <a href="<?= base_url("admin/productos/categoria/$categoria_id") ?>" class="btn-cancel">Cancelar</a>
            <button type="submit" class="btn btn-primary">💾 Guardar Producto</button>
        </div>
          <?php else: ?>
        <div class="form-group">
            <label class="form-label" for="nombre">Nombre del Producto *</label>
            <input type="text" id="nombre" name="nombre" class="form-input" 
                   value="<?= old('nombre') ?>" required 
                   placeholder="Ej: Refresco">
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

        
                     <div class="form-group">
            <label class="form-label" for="medida_id">Unidad de medida</label>
            <select id="medida_id" name="medida_id" class="form-input">
                <option value="">Seleccionar medida</option>
                <?php foreach($medidas as $medida): ?>
                    <option value="<?= $medida['id'] ?>" 
                        <?= old('medida_id') == $medida['id'] ? 'selected' : '' ?>>
                        <?= esc($medida['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-actions">
            <a href="<?= base_url("admin/productos/categoria/$categoria_id") ?>" class="btn-cancel">Cancelar</a>
            <button type="submit" class="btn btn-primary">💾 Guardar Producto</button>
        </div>
    
        <?php endif; ?>

    </form>

</div>

</div><!-- content-area -->
</div><!-- main-content -->
</body>
</html>