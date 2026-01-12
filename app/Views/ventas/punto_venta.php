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

            <div id="scanner-message" style="margin-top: 5px; font-size: 12px; color: #666;"></div>
        </div>

        <!-- Lista de Productos -->
<h3>Productos Disponibles</h3>
<div class="productos-grid" id="productos-grid">
<?php foreach($productos as $producto): ?>

    <?php
    // Suponiendo que categoría 1 = comida, 2 = bebida
    $mostrar = false;

    if ($producto['categoria_id'] == 1) {
        // Comida → mostrar siempre
        $mostrar = true;
    } elseif ($producto['categoria_id'] == 2 && $producto['stock'] > 0) {
        // Bebida → mostrar solo si stock > 0
        $mostrar = true;
    }
    ?>

    <?php if ($mostrar): ?>
        <div class="producto-card" onclick="abrirModal(<?= $producto['id'] ?>)">

            <!-- IMAGEN -->
            <img 
                src="<?= base_url('uploads/productos/' . $producto['imagen']) ?>" 
                alt="<?= esc($producto['nombre']) ?>" 
                style="width:100%; height:120px; object-fit:cover; border-radius:8px;"
            >

            <h4><?= esc($producto['nombre']) ?></h4>
            <div class="producto-precio">$<?= number_format($producto['precio'], 2) ?></div>
            <div class="producto-stock">Stock: <?= $producto['stock'] ?></div>
        </div>
    <?php endif; ?>

<?php endforeach; ?>
</div>

    </div>
<!-- MODAL DE AGREGADOS -->
<div id="modal-agregados" class="modal-overlay" style="display:none;">
  <div class="modal-box minimal">

    <div class="modal-header">
      <span class="modal-icon"></span>
      <h3 id="modal-producto-nombre"></h3>
    </div>

    <p class="modal-precio">
      $<span id="modal-precio-base"></span>
    </p>

    <p id="modal-mensaje" class="modal-mensaje"></p>
<div class="modal-opciones"></div>
<div id="monto-control" class="monto-control" style="display:none;">
    <label>Monto a pagar</label>

    <div class="monto-input-wrapper">
        <span class="currency">$</span>
        <input 
            type="text"
            id="monto-input"
            placeholder="0.00"
            inputmode="numeric"
            oninput="calcularPesoDesdeMonto()"
        />
    </div>
</div>

<div id="resultado-peso" class="peso-info" style="display:none;">
    <span id="peso-texto"></span>
</div>

<div class="cantidad-control">
  <button type="button" onclick="cambiarCantidad(-1)">−</button>
  <span id="cantidad-producto">1</span>
  <button type="button" onclick="cambiarCantidad(1)">+</button>
</div>
    <div class="modal-actions">
      <button class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
      <button class="btn-confirm" onclick="confirmarProducto()">Agregar</button>
    </div>

  </div>
</div>


    <!-- Panel del Carrito -->
    <div class="carrito-panel">
        <div class="carrito-header">
            <h2>🛒 Venta Actual</h2>
        </div>

<div class="cliente-section">
    <label class="form-label">Clientes / Mesas</label>
    <select id="cliente-select" class="form-input">
        <option value="" id="opcion-default">Seleccionar una opción</option>
        <?php foreach ($clientes as $cliente): ?>
            <option value="<?= $cliente['id'] ?>">
                <?= esc($cliente['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
<script>
const selectCliente = document.getElementById('cliente-select');

selectCliente.addEventListener('focus', () => {
    const opcion = document.getElementById('opcion-default');
    if (opcion) {
        opcion.remove();
    }
});
</script>

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

const productosMap = {};
<?php foreach($productos as $producto): ?>
productosMap[<?= $producto['id'] ?>] = {
    id: <?= $producto['id'] ?>,
    nombre: '<?= addslashes($producto['nombre']) ?>',
    precio: <?= floatval($producto['precio']) ?>,
    stock: <?= intval($producto['stock']) ?>,
    categoria_id: <?= intval($producto['categoria_id']) ?>, // 🔹 agregado
};
<?php endforeach; ?>


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

function agregarAlCarrito(productoId) {
    const producto = productosMap[productoId];
    
    if (!producto) {
        showMessage('❌ Producto no encontrado', 'error');
        return;
    }
    if (producto.categoria_id == 2 && producto.stock <= 0) {
        showMessage('❌ Producto sin stock disponible', 'error');
        return;
    }

    const itemExistente = carrito.find(item => item.id == productoId);

    if (itemExistente) {
        if (producto.categoria_id == 2 && itemExistente.cantidad >= producto.stock) {
            showMessage('❌ No hay suficiente stock disponible', 'error');
            return;
        }
        itemExistente.cantidad++;
        itemExistente.subtotal = itemExistente.cantidad * itemExistente.precio;
    } else {
 carrito.push({
    id: productoActual.id,
    nombre: productoActual.nombre + (extras.length ? ' + ' + extras.join(', ') : ''),
    precio: productoActual.precio + extraTotal,
    cantidad: cantidadActual,
    subtotal: (productoActual.precio + extraTotal) * cantidadActual,
    extras: extras
});

    }

    if (
        producto.nombre.trim().toLowerCase() === 'servicio de carnitas' &&
        !carrito.some(i => i.nombre.trim().toLowerCase() === 'tortillas')
    ) {
        const productoExtra = Object.values(productosMap)
            .find(p => p.nombre.trim().toLowerCase() === 'tortillas');

        if (productoExtra) {
            agregarProductoExtra(productoExtra.id);
        }
    }

    actualizarCarrito();
    showMessage('✓ Producto agregado al carrito', 'success');

    const scannerInput = document.getElementById('scanner-input');
    if (scannerInput) scannerInput.focus();
}
function agregarProductoExtra(productoId) {
    const producto = productosMap[productoId];
    if (!producto) return;

    const item = carrito.find(i => i.id == productoId);

 
    const precioFinal = producto.precio + 10;

    if (item) {
        return;
    } else {
        carrito.push({
            id: producto.id,
            nombre: producto.nombre,
            precio: precioFinal,   
            cantidad: 1,
            subtotal: precioFinal,
            soloUno: true     
        });
    }

    actualizarCarrito();
}




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

function modificarCantidad(index, cambio) {

    if (carrito[index].soloUno) {
        return;
    }

    const producto = productosMap[carrito[index].id];
    const nuevaCantidad = carrito[index].cantidad + cambio;

    if (producto.categoria_id == 2 && nuevaCantidad > producto.stock) {
        showMessage('❌ No hay suficiente stock disponible', 'error');
        return;
    }

    if (nuevaCantidad >= 1) {
        carrito[index].cantidad = nuevaCantidad;
        carrito[index].subtotal = nuevaCantidad * carrito[index].precio;
        actualizarCarrito();
    }
}


function actualizarCantidad(index, nuevaCantidad) {

    if (carrito[index].soloUno) {

        carrito[index].cantidad = 1; 
        carrito[index].subtotal = carrito[index].precio;
        actualizarCarrito();
        return;
    }

    const producto = productosMap[carrito[index].id];
    nuevaCantidad = parseInt(nuevaCantidad);

    if (producto.categoria_id == 2 && nuevaCantidad > producto.stock) {
        showMessage('❌ No hay suficiente stock disponible', 'error');
        carrito[index].cantidad = producto.stock;
        carrito[index].subtotal = producto.stock * carrito[index].precio;
        actualizarCarrito();
        return;
    }

    if (nuevaCantidad >= 1) {
        carrito[index].cantidad = nuevaCantidad;
        carrito[index].subtotal = nuevaCantidad * carrito[index].precio;
        actualizarCarrito();
    }
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

        const ticketUrl = `<?= base_url('ventas/imprimir/') ?>${data.venta_id}`;
        window.open(ticketUrl, '_blank', 'width=400,height=600');

        setTimeout(() => {
            location.reload();
        }, 1500);

    } else {
        showMessage(`❌ ${data.message}`, 'error');
    }
})

    .catch(error => {
        console.error('Error:', error);
        showMessage('❌ Error de conexión al procesar la venta', 'error');
    })
    .finally(() => {

        btnProcesar.innerHTML = originalText;
        btnProcesar.disabled = false;
    });
}

document.getElementById('cliente-select').addEventListener('input', function(e) {
    const searchTerm = this.value;
    
    if (searchTerm.length >= 2) {
        fetch(`<?= base_url('clientes/buscar') ?>?term=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(clientes => {
                console.log('Clientes encontrados:', clientes);
                
            });
    }
});


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


let productoActual = null;
let cantidadActual = 1;


const opcionesPorProducto = {
    taco: {
        extras: [{ nombre: 'Queso extra', precio: 5, icono: '🧀' }]
    },

    torta: {
        extras: [{ nombre: 'Queso extra', precio: 5, icono: '🧀' }]
    },

    carnitas: {
        tipoVenta: [
            { tipo: 'kilo', nombre: 'Por kilo', icono: '⚖️' },
            { tipo: 'monto', nombre: 'Por monto ($)', icono: '💵' }
        ]
    },

    bistec: {
        tipoVenta: [
            { tipo: 'kilo', nombre: 'Por kilo', icono: '⚖️' },
            { tipo: 'monto', nombre: 'Por monto ($)', icono: '💵' }
        ]
    },

    chorizo: {
        tipoVenta: [
            { tipo: 'kilo', nombre: 'Por kilo', icono: '⚖️' },
            { tipo: 'monto', nombre: 'Por monto ($)', icono: '💵' }
        ]
    },

    arrachera: {
        tipoVenta: [
            { tipo: 'kilo', nombre: 'Por kilo', icono: '⚖️' },
            { tipo: 'monto', nombre: 'Por monto ($)', icono: '💵' }
        ]
    },

    bebida: {}
};


function calcularPesoDesdeMonto() {
    const monto = parseFloat(document.getElementById('monto-input').value);
    const precioKilo = productoActual.precio;

    const resultado = document.getElementById('resultado-peso');
    const texto = document.getElementById('peso-texto');

    if (!monto || monto <= 0) {
        resultado.style.display = 'none';
        return;
    }

    const kilos = monto / precioKilo;

    if (kilos >= 1) {
        texto.textContent = `${kilos.toFixed(2)} kg`;
    } else {
        texto.textContent = `${Math.round(kilos * 1000)} g`;
    }

    resultado.style.display = 'block';
}

function abrirModal(productoId) {

    productoActual = productosMap[productoId];
    if (!productoActual) return;

    cantidadActual = 1;
    document.getElementById('cantidad-producto').textContent = cantidadActual;

    document.getElementById('modal-producto-nombre').textContent = productoActual.nombre;
    document.getElementById('modal-precio-base').textContent = productoActual.precio.toFixed(2);

    document.querySelector('.modal-opciones').innerHTML = '';
    document.getElementById('monto-control').style.display = 'none';
    document.querySelector('.cantidad-control').style.display = 'flex';
    document.getElementById('monto-input').value = '';
    document.getElementById('resultado-peso').style.display = 'none';
    tipoVentaSeleccionado = null;

    let tipo = 'bebida';
    const nombre = productoActual.nombre.toLowerCase();

    if (nombre.includes('taco')) tipo = 'taco';
    else if (nombre.includes('torta')) tipo = 'torta';
    else if (nombre.includes('carnita')) tipo = 'carnitas';
    else if (nombre.includes('bistec')) tipo = 'bistec';
    else if (nombre.includes('chorizo')) tipo = 'chorizo';
    else if (nombre.includes('arrachera')) tipo = 'arrachera';

    const config = opcionesPorProducto[tipo] || {};
    const opcionesDiv = document.querySelector('.modal-opciones');

    // 🔹 Tipo de venta
    if (config.tipoVenta) {
        config.tipoVenta.forEach(op => {
            const btn = document.createElement('button');
            btn.className = 'opcion-btn';
            btn.innerHTML = `${op.icono} ${op.nombre}`;
            btn.onclick = () => seleccionarTipoVenta(op.tipo);
            opcionesDiv.appendChild(btn);
        });

        document.querySelector('.cantidad-control').style.display = 'none';
    }

    // 🔹 Extras
    if (config.extras) {
        config.extras.forEach(extra => {
            opcionesDiv.innerHTML += `
                <label class="extra-item">
                    <input type="checkbox" value="${extra.precio}" data-nombre="${extra.nombre}">
                    <span>${extra.icono} ${extra.nombre}</span>
                    <small>+$${extra.precio}</small>
                </label>
            `;
        });
    }

    document.getElementById('modal-agregados').style.display = 'flex';
}

let tipoVentaSeleccionado = null;

function seleccionarTipoVenta(tipo) {
    tipoVentaSeleccionado = tipo;

    if (tipo === 'monto') {
        document.getElementById('monto-control').style.display = 'block';
        document.querySelector('.cantidad-control').style.display = 'none';
    } 
    else if (tipo === 'kilo') {
        document.getElementById('monto-control').style.display = 'none';
        document.querySelector('.cantidad-control').style.display = 'flex';
    }
}


function cambiarCantidad(valor) {
    cantidadActual += valor;
    if (cantidadActual < 1) cantidadActual = 1;
    document.getElementById('cantidad-producto').textContent = cantidadActual;
}
function cerrarModal() {
    document.getElementById('modal-agregados').style.display = 'none';
    productoActual = null;
    tipoVentaSeleccionado = null;

    document.getElementById('monto-control').style.display = 'none';
    document.querySelector('.cantidad-control').style.display = 'flex';
}


function confirmarProducto() {

    // 🥩 PRODUCTOS POR MONTO (carnitas, arrachera, bistec, etc.)
    if (tipoVentaSeleccionado === 'monto') {

        const monto = parseFloat(document.getElementById('monto-input').value);

        if (!monto || monto <= 0) {
            showMessage('❌ Ingrese un monto válido', 'error');
            return;
        }

        const kilos = monto / productoActual.precio;

        const descripcionPeso = kilos >= 1
            ? `${kilos.toFixed(2)} kg`
            : `${Math.round(kilos * 1000)} g`;

        carrito.push({
            id: productoActual.id,
            nombre: `${productoActual.nombre} (${descripcionPeso})`,
            precio: monto,
            cantidad: 1,
            subtotal: monto,
            tipoVenta: 'monto',
            peso: descripcionPeso
        });
    }

    // 🌮 PRODUCTOS NORMALES (tacos, tortas, etc.)
    else {

        let extras = [];
        let extraTotal = 0;

        document
            .querySelectorAll('#modal-agregados input[type="checkbox"]:checked')
            .forEach(c => {
                extras.push(c.dataset.nombre);
                extraTotal += parseFloat(c.value);
            });

        const precioFinal = productoActual.precio + extraTotal;

        carrito.push({
            id: productoActual.id,
            nombre: productoActual.nombre + (extras.length ? ' + ' + extras.join(', ') : ''),
            precio: precioFinal,
            cantidad: cantidadActual,
            subtotal: precioFinal * cantidadActual,
            extras
        });
    }

    cerrarModal();
    actualizarCarrito();
    showMessage('✓ Producto agregado', 'success');
}



</script>

<?= $this->include('layouts/footer') ?>