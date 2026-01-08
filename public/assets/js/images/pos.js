// POS System JavaScript - Versión Simplificada
class POSSystem {
    constructor() {
        this.carrito = [];
        this.initEventListeners();
        this.updateCartDisplay();
        this.updateCurrentTime();
        setInterval(() => this.updateCurrentTime(), 1000);
        
        console.log('POS System inicializado');
        console.log('Productos cargados:', productosData.length);
    }

    initEventListeners() {
        // Búsqueda de productos
        const searchInput = document.getElementById('search-input');
        const btnSearch = document.getElementById('btn-search');
        
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.buscarProductos(e.target.value);
            });
            
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.buscarProductos(e.target.value);
                }
            });
        }
        
        if (btnSearch) {
            btnSearch.addEventListener('click', () => {
                this.buscarProductos(searchInput.value);
            });
        }

        // Event delegation para productos
        const productsGrid = document.getElementById('products-grid');
        if (productsGrid) {
            productsGrid.addEventListener('click', (e) => {
                const productCard = e.target.closest('.product-card');
                if (productCard) {
                    const productId = productCard.dataset.productId;
                    this.agregarAlCarrito(productId);
                }
            });
        }

        // Eventos del carrito
        const cartItems = document.getElementById('cart-items');
        if (cartItems) {
            cartItems.addEventListener('click', (e) => {
                if (e.target.classList.contains('item-remove')) {
                    const index = parseInt(e.target.dataset.index);
                    this.eliminarDelCarrito(index);
                } else if (e.target.classList.contains('quantity-btn')) {
                    const index = parseInt(e.target.dataset.index);
                    const isIncrement = e.target.classList.contains('increment');
                    this.actualizarCantidad(index, isIncrement);
                }
            });
        }

        // Descuento
        const descuentoInput = document.getElementById('descuento');
        if (descuentoInput) {
            descuentoInput.addEventListener('input', () => {
                this.calcularTotales();
            });
        }

        // Botones de acción
        const btnProcesar = document.getElementById('btn-procesar-venta');
        const btnLimpiar = document.getElementById('btn-limpiar-carrito');
        const btnNuevaVenta = document.getElementById('btn-nueva-venta');
        
        if (btnProcesar) {
            btnProcesar.addEventListener('click', () => {
                this.mostrarConfirmacion();
            });
        }
        
        if (btnLimpiar) {
            btnLimpiar.addEventListener('click', () => {
                this.limpiarCarrito();
            });
        }
        
        if (btnNuevaVenta) {
            btnNuevaVenta.addEventListener('click', () => {
                this.limpiarCarrito();
            });
        }

        // Modal
        const btnConfirm = document.getElementById('btn-confirm-venta');
        const btnCancel = document.getElementById('btn-cancel-venta');
        
        if (btnConfirm) {
            btnConfirm.addEventListener('click', () => {
                this.procesarVenta();
            });
        }
        
        if (btnCancel) {
            btnCancel.addEventListener('click', () => {
                this.cerrarModal();
            });
        }

        // Cerrar modal al hacer clic fuera
        window.addEventListener('click', (e) => {
            const modal = document.getElementById('confirm-modal');
            if (e.target === modal) {
                this.cerrarModal();
            }
        });
    }

    buscarProductos(query) {
        if (!query || query.length < 2) {
            document.getElementById('search-results').style.display = 'none';
            return;
        }

        const resultados = productosData.filter(producto => 
            producto.nombre.toLowerCase().includes(query.toLowerCase()) ||
            producto.codigo.toLowerCase().includes(query.toLowerCase())
        );

        this.mostrarResultadosBusqueda(resultados);
    }

    mostrarResultadosBusqueda(productos) {
        const resultsContainer = document.getElementById('search-results');
        if (!resultsContainer) return;

        resultsContainer.innerHTML = '';

        if (productos.length === 0) {
            resultsContainer.innerHTML = '<div class="search-result-item">No se encontraron productos</div>';
        } else {
            productos.forEach(producto => {
                const item = document.createElement('div');
                item.className = 'search-result-item';
                item.innerHTML = `
                    <div style="flex: 1;">
                        <strong>${this.escapeHtml(producto.nombre)}</strong>
                        <div style="font-size: 0.8rem; color: #666;">${this.escapeHtml(producto.codigo)}</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-weight: bold; color: #28a745;">$${parseFloat(producto.precio).toFixed(2)}</div>
                        <div style="font-size: 0.8rem;">Stock: ${producto.stock}</div>
                    </div>
                `;
                item.addEventListener('click', () => {
                    this.agregarAlCarrito(producto.id);
                    resultsContainer.style.display = 'none';
                    document.getElementById('search-input').value = '';
                });
                resultsContainer.appendChild(item);
            });
        }

        resultsContainer.style.display = 'block';
    }

    agregarAlCarrito(productId) {
        const producto = productosData.find(p => p.id == productId);
        if (!producto) {
            this.mostrarAlerta('error', 'Producto no encontrado');
            return;
        }

        // Verificar stock
        if (producto.stock <= 0) {
            this.mostrarAlerta('error', 'Producto sin stock disponible');
            return;
        }

        // Buscar si ya está en el carrito
        const existingIndex = this.carrito.findIndex(item => item.id == productId);

        if (existingIndex !== -1) {
            // Incrementar cantidad si hay stock
            const item = this.carrito[existingIndex];
            if (item.cantidad < producto.stock) {
                item.cantidad += 1;
            } else {
                this.mostrarAlerta('warning', 'No hay suficiente stock disponible');
                return;
            }
        } else {
            // Agregar nuevo item
            this.carrito.push({
                id: producto.id,
                nombre: producto.nombre,
                codigo: producto.codigo,
                precio: parseFloat(producto.precio),
                cantidad: 1,
                stock: producto.stock
            });
        }

        this.updateCartDisplay();
        this.mostrarAlerta('success', 'Producto agregado al carrito');
    }

    eliminarDelCarrito(index) {
        if (index >= 0 && index < this.carrito.length) {
            this.carrito.splice(index, 1);
            this.updateCartDisplay();
            this.mostrarAlerta('info', 'Producto removido del carrito');
        }
    }

    actualizarCantidad(index, isIncrement) {
        if (index < 0 || index >= this.carrito.length) return;

        const item = this.carrito[index];
        const producto = productosData.find(p => p.id == item.id);
        
        if (!producto) return;

        if (isIncrement) {
            if (item.cantidad < producto.stock) {
                item.cantidad += 1;
            } else {
                this.mostrarAlerta('warning', 'No hay suficiente stock disponible');
                return;
            }
        } else {
            item.cantidad -= 1;
            if (item.cantidad <= 0) {
                this.eliminarDelCarrito(index);
                return;
            }
        }

        this.updateCartDisplay();
    }

    updateCartDisplay() {
        const cartItemsContainer = document.getElementById('cart-items');
        const cartCount = document.getElementById('cart-count');
        const btnProcesar = document.getElementById('btn-procesar-venta');

        if (!cartItemsContainer || !cartCount || !btnProcesar) return;

        // Actualizar contador
        const totalItems = this.carrito.reduce((sum, item) => sum + item.cantidad, 0);
        cartCount.textContent = `${totalItems} producto${totalItems !== 1 ? 's' : ''}`;

        // Habilitar/deshabilitar botón
        btnProcesar.disabled = this.carrito.length === 0;

        // Mostrar items
        if (this.carrito.length === 0) {
            cartItemsContainer.innerHTML = `
                <div class="empty-cart">
                    <p>El carrito está vacío</p>
                    <small>Agrega productos para comenzar la venta</small>
                </div>
            `;
        } else {
            cartItemsContainer.innerHTML = this.carrito.map((item, index) => `
                <div class="cart-item">
                    <div class="item-info">
                        <div class="item-name">${this.escapeHtml(item.nombre)}</div>
                        <div class="item-price">$${item.precio.toFixed(2)} c/u</div>
                        <div class="item-code">${this.escapeHtml(item.codigo)}</div>
                    </div>
                    <div class="item-quantity">
                        <button class="quantity-btn decrement" data-index="${index}">-</button>
                        <span class="quantity-display">${item.cantidad}</span>
                        <button class="quantity-btn increment" data-index="${index}">+</button>
                    </div>
                    <div class="item-total">
                        $${(item.precio * item.cantidad).toFixed(2)}
                    </div>
                    <div class="item-remove" data-index="${index}" title="Eliminar">🗑️</div>
                </div>
            `).join('');
        }

        this.calcularTotales();
    }

    calcularTotales() {
        const subtotal = this.carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
        const descuento = parseFloat(document.getElementById('descuento').value) || 0;
        const impuesto = 0;
        const totalFinal = Math.max(0, subtotal - descuento + impuesto);

        const subtotalElement = document.getElementById('subtotal');
        const impuestoElement = document.getElementById('impuesto');
        const totalFinalElement = document.getElementById('total-final');

        if (subtotalElement) subtotalElement.textContent = `$${subtotal.toFixed(2)}`;
        if (impuestoElement) impuestoElement.textContent = `$${impuesto.toFixed(2)}`;
        if (totalFinalElement) totalFinalElement.innerHTML = `<strong>$${totalFinal.toFixed(2)}</strong>`;
    }

    limpiarCarrito() {
        this.carrito = [];
        this.updateCartDisplay();
        document.getElementById('descuento').value = 0;
        document.getElementById('cliente_id').value = '';
        document.getElementById('metodo_pago').value = 'efectivo';
        document.getElementById('observaciones').value = '';
        this.mostrarAlerta('info', 'Carrito limpiado');
    }

    mostrarConfirmacion() {
        const modal = document.getElementById('confirm-modal');
        const confirmDetails = document.getElementById('confirm-details');

        if (!modal || !confirmDetails) return;

        const subtotal = this.carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
        const descuento = parseFloat(document.getElementById('descuento').value) || 0;
        const totalFinal = subtotal - descuento;

        const clienteSelect = document.getElementById('cliente_id');
        const metodoPagoSelect = document.getElementById('metodo_pago');
        
        const clienteNombre = clienteSelect.options[clienteSelect.selectedIndex].text;
        const metodoPagoNombre = metodoPagoSelect.options[metodoPagoSelect.selectedIndex].text;

        confirmDetails.innerHTML = `
            <div class="confirm-section">
                <h4>Resumen de Venta</h4>
                <div class="confirm-items">
                    ${this.carrito.map(item => `
                        <div class="confirm-item">
                            <span>${this.escapeHtml(item.nombre)} (x${item.cantidad})</span>
                            <span>$${(item.precio * item.cantidad).toFixed(2)}</span>
                        </div>
                    `).join('')}
                </div>
            </div>
            <div class="confirm-totals">
                <div class="confirm-total-line">
                    <span>Subtotal:</span>
                    <span>$${subtotal.toFixed(2)}</span>
                </div>
                <div class="confirm-total-line">
                    <span>Descuento:</span>
                    <span>$${descuento.toFixed(2)}</span>
                </div>
                <div class="confirm-total-line grand-total">
                    <span><strong>Total:</strong></span>
                    <span><strong>$${totalFinal.toFixed(2)}</strong></span>
                </div>
            </div>
            <div class="confirm-info">
                <p><strong>Método de pago:</strong> ${this.escapeHtml(metodoPagoNombre)}</p>
                <p><strong>Cliente:</strong> ${this.escapeHtml(clienteNombre)}</p>
            </div>
        `;

        modal.style.display = 'block';
    }

    cerrarModal() {
        const modal = document.getElementById('confirm-modal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    async procesarVenta() {
        const btnConfirm = document.getElementById('btn-confirm-venta');
        if (!btnConfirm) return;

        btnConfirm.disabled = true;
        btnConfirm.textContent = 'Procesando...';

        const ventaData = {
            cliente_id: document.getElementById('cliente_id').value || '',
            metodo_pago: document.getElementById('metodo_pago').value,
            descuento: parseFloat(document.getElementById('descuento').value) || 0,
            impuesto: 0,
            observaciones: document.getElementById('observaciones').value,
            items: JSON.stringify(this.carrito)
        };

        try {
            const response = await fetch(`${base_url}ventas/procesar_venta`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams(ventaData)
            });

            const data = await response.json();

            if (data.success) {
                this.mostrarAlerta('success', `Venta procesada correctamente - Código: ${data.codigo_venta}`);
                this.limpiarCarrito();
                this.cerrarModal();
            } else {
                this.mostrarAlerta('error', data.message || 'Error al procesar la venta');
            }
        } catch (error) {
            console.error('Error:', error);
            this.mostrarAlerta('error', 'Error de conexión al procesar la venta');
        } finally {
            btnConfirm.disabled = false;
            btnConfirm.textContent = '✅ Confirmar Venta';
        }
    }

    mostrarAlerta(tipo, mensaje) {
        // Crear alerta simple
        const alerta = document.createElement('div');
        alerta.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            padding: 12px 20px;
            border-radius: 6px;
            color: white;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideInRight 0.3s ease;
        `;

        if (tipo === 'success') {
            alerta.style.background = '#28a745';
        } else if (tipo === 'error') {
            alerta.style.background = '#dc3545';
        } else if (tipo === 'warning') {
            alerta.style.background = '#ffc107';
            alerta.style.color = '#212529';
        } else {
            alerta.style.background = '#17a2b8';
        }

        alerta.textContent = mensaje;
        document.body.appendChild(alerta);

        setTimeout(() => {
            if (alerta.parentElement) {
                alerta.remove();
            }
        }, 3000);
    }

    updateCurrentTime() {
        const timeElement = document.getElementById('current-time');
        if (timeElement) {
            const now = new Date();
            timeElement.textContent = now.toLocaleDateString('es-ES', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    new POSSystem();
});

// Agregar estilos para animaciones
const style = document.createElement('style');
style.textContent = `
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
`;
document.head.appendChild(style);