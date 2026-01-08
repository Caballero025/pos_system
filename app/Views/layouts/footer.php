<?php if (isset($scripts) && is_array($scripts)): ?>
    <?php foreach ($scripts as $script): ?>
        <script src="<?= base_url($script) ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Scripts globales -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Función global para mostrar alertas
function showAlert(type, message) {
    const alert = document.createElement('div');
    alert.className = `global-alert global-alert-${type}`;
    alert.innerHTML = `
        <div class="alert-content">
            <span class="alert-message">${message}</span>
            <button class="alert-close" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;
    
    document.body.appendChild(alert);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (alert.parentElement) {
            alert.remove();
        }
    }, 5000);
}

// Estilos para alertas globales
const alertStyles = document.createElement('style');
alertStyles.textContent = `
    .global-alert {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        min-width: 300px;
        max-width: 500px;
        animation: slideInRight 0.3s ease;
    }
    
    .global-alert-success .alert-content {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        border-radius: 8px;
        padding: 12px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .global-alert-error .alert-content {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        border-radius: 8px;
        padding: 12px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .global-alert-warning .alert-content {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
        border-radius: 8px;
        padding: 12px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .global-alert-info .alert-content {
        background: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
        border-radius: 8px;
        padding: 12px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .alert-close {
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        margin-left: 10px;
        color: inherit;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .fade-out {
        animation: fadeOut 0.3s ease forwards;
    }
    
    @keyframes fadeOut {
        to {
            opacity: 0;
            transform: translateX(100%);
        }
    }
`;
document.head.appendChild(alertStyles);
</script>

</body>
</html>