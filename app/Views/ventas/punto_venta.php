<?= $this->include('layouts/header') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/punto_venta.css') ?>">
<?php if (session()->get('user_role') === 'admin'): ?>
<div class="page-header">
    <h1>Punto de Venta</h1>
    <div style="display: flex; gap: 10px;">
                <a href="<?= base_url('ventas/historial') ?>" class="btn-historial" style="display: inline-flex; align-items: center; gap: 5px; padding: 10px 15px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background 0.3s;">
            📊 Historial
        </a>
        
        <!-- Botón Dashboard -->
        <a href="<?= base_url('dashboard') ?>" class="btn-dashboard" style="display: inline-flex; align-items: center; gap: 5px; padding: 10px 15px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background 0.3s;">
            🏠 Dashboard
        </a>
    </div>
</div>
 <?php endif; ?>

<div class="punto-venta-container">
    <!-- Panel de Productos -->
    <div class="productos-panel">
        <!-- Scanner -->
        <div class="scanner-section">
            <input type="text" 
                   id="scanner-input" 
                   class="scanner-input" 
                   placeholder="🔍 Escanear código de barras o buscar producto..."
                   autofocus>
            <div id="scanner-message" style="margin-top: 5px; font-size: 12px; color: #666;"></div>
        </div>

        <!-- Lista de Productos -->
        <h3>Productos Disponibles</h3>
        <div class="productos-grid" id="productos-grid">
    <?php foreach($productos as $producto): ?>
        <?php if($producto['stock'] > 0): ?>
            <div class="producto-card" onclick="agregarAlCarrito(<?= $producto['id'] ?>)">

                <!-- IMAGEN -->
                <img 
                    src="<?= base_url('uploads/productos/' . $producto['imagen']) ?>" 
                    alt="<?= esc($producto['nombre']) ?>" 
                    style="width:100%; height:120px; object-fit:cover; border-radius:8px;"
                >

                <h4><?= esc($producto['nombre']) ?></h4>
                <div class="producto-precio">$<?= number_format($producto['precio'], 2) ?></div>
                <div class="producto-stock">Stock: <?= $producto['stock'] ?></div>
                <div style="font-size: 10px; color: #999;"><?= esc($producto['codigo']) ?></div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

    </div>

    <!-- Panel del Carrito -->
    <div class="carrito-panel">
        <div class="carrito-header">
            <h2>🛒 Venta Actual</h2>
        </div>

        <!-- Cliente -->
        <div class="cliente-section">
        <label class="form-label">Cliente / Mesa</label>
        <input list="mesas" id="cliente-input" class="form-input" placeholder="Ejemplo: Mesa 1">
    
        <datalist id="mesas">
            <option value="Mesa 1">
            <option value="Mesa 2">
            <option value="Mesa 3">
            <option value="Mesa 4">
            <option value="Mesa 5">
            <option value="Cliente general">
            <option value="Para llevar">
        </datalist>
    </div>

        <!-- Items del Carrito -->
        <div class="carrito-items" id="carrito-items">
            <div class="empty-carrito">
                <p>El carrito está vacío</p>
                <p>Escanea productos o haz clic en ellos</p>
            </div>
        </div>

        <!-- Totales -->
        <div class="carrito-totales">
            <div class="total-line">
                <span>Subtotal:</span>
                <span id="subtotal">$0.00</span>
            </div>
            <div class="total-line">
                <span>Total:</span>
                <span id="total" class="total-final">$0.00</span>
            </div>
        </div>

        <!-- Formas de Pago -->
        <div class="pago-section">
            <div class="form-group">
                <label class="form-label">Efectivo Recibido</label>
                <input type="number" 
                       id="efectivo-input" 
                       class="form-input" 
                       placeholder="0.00" 
                       step="0.01"
                       onchange="calcularCambio()">
            </div>

            <div id="cambio-display" class="cambio-display" style="display: none;">
                Cambio: $<span id="cambio-monto">0.00</span>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="acciones-venta">
            <button class="btn-venta btn-cancelar" onclick="cancelarVenta()">❌ Cancelar</button>
            <button class="btn-venta btn-procesar" onclick="procesarVenta()">✅ Cobrar</button>
        </div>
    </div>
</div>

<script>
let carrito = [];

// Crear mapa de productos de forma robusta
const productosMap = {};
<?php foreach($productos as $producto): ?>
productosMap[<?= $producto['id'] ?>] = {
    id: <?= $producto['id'] ?>,
    nombre: '<?= addslashes($producto['nombre']) ?>',
    precio: <?= floatval($producto['precio']) ?>,
    stock: <?= intval($producto['stock']) ?>,
    codigo: '<?= addslashes($producto['codigo']) ?>'
};
<?php endforeach; ?>

// Scanner de código de barras
document.getElementById('scanner-input').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        const codigo = this.value.trim();
        if (codigo) {
            buscarProductoPorCodigo(codigo);
        }
        this.value = '';
    }
});

// Buscar producto por código
function buscarProductoPorCodigo(codigo) {
    fetch(`<?= base_url('ventas/buscar-producto/') ?>${encodeURIComponent(codigo)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                agregarAlCarrito(data.producto.id);
                showMessage('✓ Producto agregado', 'success');
            } else {
                showMessage('✗ Producto no encontrado', 'error');
            }
        })
        .catch(error => {
            showMessage('✗ Error en la búsqueda', 'error');
        });
}

// Agregar producto al carrito
function agregarAlCarrito(productoId) {
    const producto = productosMap[productoId];
    
    if (!producto) {
        console.error('Producto no encontrado:', productoId);
        showMessage('❌ Producto no encontrado', 'error');
        return;
    }
    
    // Verificar stock
    if (producto.stock <= 0) {
        showMessage('❌ Producto sin stock disponible', 'error');
        return;
    }
    
    const itemExistente = carrito.find(item => item.id == productoId);
    
    if (itemExistente) {
        // Verificar que no exceda el stock disponible
        if (itemExistente.cantidad < producto.stock) {
            itemExistente.cantidad++;
            itemExistente.subtotal = itemExistente.cantidad * itemExistente.precio;
        } else {
            showMessage('❌ No hay suficiente stock disponible', 'error');
            return;
        }
    } else {
        carrito.push({
            id: producto.id,
            nombre: producto.nombre,
            precio: producto.precio,
            cantidad: 1,
            subtotal: producto.precio
        });
    }
    
    actualizarCarrito();
    showMessage('✓ Producto agregado al carrito', 'success');
    document.getElementById('scanner-input').focus();
}

// Actualizar interfaz del carrito
function actualizarCarrito() {
    const carritoItems = document.getElementById('carrito-items');
    const subtotalElement = document.getElementById('subtotal');
    const totalElement = document.getElementById('total');
    
    if (carrito.length === 0) {
        carritoItems.innerHTML = `
            <div class="empty-carrito">
                <p>El carrito está vacío</p>
                <p>Escanea productos o haz clic en ellos</p>
            </div>
        `;
        subtotalElement.textContent = '$0.00';
        totalElement.textContent = '$0.00';
        document.getElementById('cambio-display').style.display = 'none';
        return;
    }
    
    let subtotal = 0;
    let html = '';
    
    carrito.forEach((item, index) => {
        subtotal += item.subtotal;
        
        html += `
            <div class="carrito-item">
                <div class="item-info">
                    <h4>${item.nombre}</h4>
                    <div class="item-precio">$${item.precio.toFixed(2)} c/u</div>
                </div>
                <div class="item-cantidad">
                    <button class="cantidad-btn" onclick="modificarCantidad(${index}, -1)">-</button>
                    <input type="number" class="cantidad-input" value="${item.cantidad}" 
                           onchange="actualizarCantidad(${index}, this.value)" min="1">
                    <button class="cantidad-btn" onclick="modificarCantidad(${index}, 1)">+</button>
                </div>
                <div class="item-total">$${item.subtotal.toFixed(2)}</div>
                <button class="eliminar-item" onclick="eliminarDelCarrito(${index})">×</button>
            </div>
        `;
    });
    
    carritoItems.innerHTML = html;
    subtotalElement.textContent = `$${subtotal.toFixed(2)}`;
    totalElement.textContent = `$${subtotal.toFixed(2)}`;
    
    calcularCambio();
}

// Funciones del carrito
function modificarCantidad(index, cambio) {
    const producto = productosMap[carrito[index].id];
    const nuevaCantidad = carrito[index].cantidad + cambio;
    
    if (nuevaCantidad >= 1 && nuevaCantidad <= producto.stock) {
        carrito[index].cantidad = nuevaCantidad;
        carrito[index].subtotal = nuevaCantidad * carrito[index].precio;
        actualizarCarrito();
    } else if (nuevaCantidad > producto.stock) {
        showMessage('❌ No hay suficiente stock disponible', 'error');
    }
}

function actualizarCantidad(index, nuevaCantidad) {
    const producto = productosMap[carrito[index].id];
    nuevaCantidad = parseInt(nuevaCantidad);
    
    if (nuevaCantidad >= 1 && nuevaCantidad <= producto.stock) {
        carrito[index].cantidad = nuevaCantidad;
        carrito[index].subtotal = nuevaCantidad * carrito[index].precio;
        actualizarCarrito();
    } else if (nuevaCantidad > producto.stock) {
        showMessage('❌ No hay suficiente stock disponible', 'error');
        carrito[index].cantidad = producto.stock;
        carrito[index].subtotal = producto.stock * carrito[index].precio;
        actualizarCarrito();
    }
}

function eliminarDelCarrito(index) {
    carrito.splice(index, 1);
    actualizarCarrito();
}

// Cálculo de cambio
function calcularCambio() {
    const efectivo = parseFloat(document.getElementById('efectivo-input').value) || 0;
    const total = carrito.reduce((sum, item) => sum + item.subtotal, 0);
    const cambio = efectivo - total;
    
    const cambioDisplay = document.getElementById('cambio-display');
    const cambioMonto = document.getElementById('cambio-monto');
    
    if (efectivo > 0 && cambio >= 0) {
        cambioDisplay.style.display = 'block';
        cambioMonto.textContent = cambio.toFixed(2);
    } else {
        cambioDisplay.style.display = 'none';
    }
}

// Procesar venta
function procesarVenta() {
    if (carrito.length === 0) {
        showMessage('❌ El carrito está vacío', 'error');
        return;
    }
    
    const efectivo = parseFloat(document.getElementById('efectivo-input').value) || 0;
    const total = carrito.reduce((sum, item) => sum + item.subtotal, 0);
    const clienteId = document.getElementById('cliente-select').value;
    
    if (efectivo <= 0) {
        showMessage('❌ Debe ingresar el efectivo recibido', 'error');
        document.getElementById('efectivo-input').focus();
        return;
    }
    
    if (efectivo < total) {
        showMessage('❌ El efectivo recibido es menor al total', 'error');
        document.getElementById('efectivo-input').focus();
        return;
    }
    
    const cambio = efectivo - total;
    
    if (!confirm(`¿Procesar venta por $${total.toFixed(2)}?\n\nEfectivo: $${efectivo.toFixed(2)}\nCambio: $${cambio.toFixed(2)}`)) {
        return;
    }
    
    const ventaData = {
        carrito: carrito,
        cliente_id: clienteId || null,
        efectivo: efectivo,
        total: total
    };
    
    // Mostrar loading
    const btnProcesar = document.querySelector('.btn-procesar');
    const originalText = btnProcesar.innerHTML;
    btnProcesar.innerHTML = '⏳ Procesando...';
    btnProcesar.disabled = true;
    
    fetch('<?= base_url('ventas/procesar') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(ventaData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(`✅ ${data.message}`, 'success');
            
            // Abrir ticket en nueva ventana
            const ticketUrl = `<?= base_url('ventas/imprimir/') ?>${data.venta_id}`;
            window.open(ticketUrl, '_blank', 'width=400,height=600');
            
            // Limpiar carrito después de éxito
            setTimeout(() => {
                cancelarVenta();
            }, 2000);
            
        } else {
            showMessage(`❌ ${data.message}`, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('❌ Error de conexión al procesar la venta', 'error');
    })
    .finally(() => {
        // Restaurar botón
        btnProcesar.innerHTML = originalText;
        btnProcesar.disabled = false;
    });
}
// Búsqueda dinámica de clientes
document.getElementById('cliente-select').addEventListener('input', function(e) {
    const searchTerm = this.value;
    
    if (searchTerm.length >= 2) {
        fetch(`<?= base_url('clientes/buscar') ?>?term=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(clientes => {
                console.log('Clientes encontrados:', clientes);
                // Aquí puedes implementar autocompletado
            });
    }
});

// Botón para agregar cliente rápido desde punto de venta
function agregarClienteRapido() {
    const nombre = prompt('Nombre del cliente:');
    if (nombre) {
        const telefono = prompt('Teléfono (opcional):');
        const clienteData = {
            nombre: nombre,
            telefono: telefono || ''
        };
        
        fetch('<?= base_url('clientes/guardar') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(clienteData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Agregar el nuevo cliente al select
                const select = document.getElementById('cliente-select');
                const option = document.createElement('option');
                option.value = data.cliente_id;
                option.text = nombre + (telefono ? ` (${telefono})` : '');
                select.appendChild(option);
                select.value = data.cliente_id;
                alert('Cliente agregado correctamente');
            } else {
                alert('Error al agregar cliente');
            }
        });
    }
}

// Cancelar venta
function cancelarVenta() {
    if (carrito.length > 0 && !confirm('¿Cancelar la venta actual?')) {
        return;
    }
    
    carrito = [];
    document.getElementById('efectivo-input').value = '';
    document.getElementById('cliente-select').value = '';
    document.getElementById('cambio-display').style.display = 'none';
    actualizarCarrito();
    document.getElementById('scanner-input').focus();
}

// Función para mostrar mensajes
function showMessage(message, type) {
    const messageDiv = document.getElementById('scanner-message');
    messageDiv.textContent = message;
    messageDiv.style.color = type === 'success' ? '#27ae60' : '#e74c3c';
    
    setTimeout(() => {
        messageDiv.textContent = '';
    }, 3000);
}

// Inicializar
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('scanner-input').focus();
});
</script>

<?= $this->include('layouts/footer') ?>