<?= $this->include('layouts/header') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/productos.css') ?>">

<div class="page-header">
    <h1>Editar Materia</h1>
    <a href="<?= base_url('admin/materias') ?>" class="btn btn-cancel">← Volver</a>
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
    <form method="post" action="<?= base_url('admin/materias/actualizar/' . $materia['id']) ?>" enctype="multipart/form-data">
        <div class="form-group">
            <label class="form-label" for="nombre">Nombre del Producto *</label>
            <input type="text" id="nombre" name="nombre" class="form-input" 
                   value="<?= old('nombre', $materia['nombre']) ?>" required>
        </div>

 

        <div class="form-row" style="display: flex; gap: 15px;">
            <div class="form-group" style="flex: 1;">
                <label class="form-label" for="precio">Precio de Venta *</label>
                <input type="number" id="precio" name="precio" class="form-input" 
                       value="<?= old('precio', $materia['precio']) ?>" step="0.01" min="0" required>
            </div>

        </div>

        <div class="form-group">
            <label class="form-label" for="categoria_id">Categoría</label>
            <select id="categoria_id" name="categoria_id" class="form-input">
                <option value="">Seleccionar categoría</option>
                <?php foreach($categorias as $categoria): ?>
                    <option value="<?= $categoria['id'] ?>" 
                        <?= (old('categoria_id', $materia['categoria_id']) == $categoria['id']) ? 'selected' : '' ?>>
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
                        <?= (old('categoria_id', $materia['categoria_id']) == $medida['id']) ? 'selected' : '' ?>>
                        <?= esc($medida['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-row" style="display: flex; gap: 15px;">
    <div class="form-group" style="flex: 1;">
        <label class="form-label" for="cantidad">Cantidad *</label>
        <input type="number"
               id="cantidad"
               name="cantidad"
               class="form-input"
              value="<?= old('cantidad', $materia['cantidad']) ?>"
               step="1"
               min="1"
               required
               placeholder="0">
    </div>
</div>


        <div class="form-group">
            <label class="form-label">Estado</label>
            <div>
                <label style="display: inline-flex; align-items: center; margin-right: 20px;">
                    <input type="radio" name="activo" value="1" 
                        <?= (old('activo', $materia['activo']) == 1) ? 'checked' : '' ?>> 
                    <span style="margin-left: 5px;">Activo</span>
                </label>
                <label style="display: inline-flex; align-items: center;">
                    <input type="radio" name="activo" value="0" 
                        <?= (old('activo', $materia['activo']) == 0) ? 'checked' : '' ?>> 
                    <span style="margin-left: 5px;">Inactivo</span>
                </label>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= base_url('admin/materias') ?>" class="btn-cancel">Cancelar</a>
            <button type="submit" class="btn btn-primary">💾 Actualizar Producto</button>
        </div>

    </form>
</div>

</div><!-- content-area -->
</div><!-- main-content -->
</body>
</html>