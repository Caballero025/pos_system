
<?= $this->include('layouts/header') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/productos.css') ?>">

<div class="page-header">
    <h1>Gestión de Productos</h1>
</div>



<!-- Categorías visuales -->
<div class="categoria-cards">

    <a href="<?= base_url('admin/productos/categoria/1') ?>" class="categoria-card">
        <img src="<?= base_url('assets/img/comida.jpg') ?>" alt="Comida">
        <div class="categoria-overlay">
            <h2>🍽️ COMIDA</h2>
            <p>Ver productos</p>
        </div>
    </a>

    <a href="<?= base_url('admin/productos/categoria/2') ?>" class="categoria-card">
        <img src="<?= base_url('assets/img/bebida.jpg') ?>" alt="Bebida">
        <div class="categoria-overlay">
            <h2>🥤 BEBIDA</h2>
            <p>Ver productos</p>
        </div>
    </a>

</div>

</div><!-- content-area -->
</div><!-- main-content -->
</body>
</html>