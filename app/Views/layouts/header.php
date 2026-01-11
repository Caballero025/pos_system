<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'POS System' ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
</head>
<body>
    <!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-header">
        <img src="<?= base_url('assets/img/logo.png') ?>" 
             alt="Logo"
             class="sidebar-logo">
    </div>

    <?php if (session()->get('user_role') === 'admin'): ?>
        <ul class="nav-links">
            <li><a href="<?= base_url('dashboard') ?>">🏠 Inicio</a></li>
            <li><a href="<?= base_url('admin/productos') ?>">📦 Productos</a></li>
            <li><a href="<?= base_url('ventas') ?>">💰 Ventas</a></li>
            <li><a href="<?= base_url('admin/materias') ?>">🥩 Materias primas</a></li>
            <li><a href="<?= base_url('ventas/historial') ?>">📊 Detalles ventas</a></li>
            <li><a href="<?= base_url('admin/reportes') ?>">📈 Reportes</a></li>
            <li><a href="<?= base_url('admin/configuracion') ?>">⚙️ Configuración</a></li>
        </ul>
    <?php else: ?>
        <ul class="nav-links">
            <li><a href="<?= base_url('ventas') ?>">💰 Ventas</a></li>
        </ul>
    <?php endif; ?>
</div>


    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Header con Barra de Búsqueda -->
        <div class="top-header">
            <div class="search-container">
                <div class="search-box">
                    <input type="text" class="search-input" placeholder="Buscar productos, clientes, ventas...">
                    <button class="search-btn">🔍</button>
                </div>
            </div>
            <div class="user-info">
                <span class="user-welcome">Bienvenido, <?= session()->get('user_name') ?? 'Usuario' ?></span>
                <a href="<?= base_url('logout') ?>" class="btn-logout">Cerrar Sesión</a>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">