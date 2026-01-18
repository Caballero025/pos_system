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

<input
    type="number"
    id="cantidad-producto"
    value="1"
    inputmode="decimal"
    oninput="actualizarCantidadDesdeInput(this)"
    min="0.1"
    step="0.1" <!-- 🔹 Esto permite decimales -->

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
    medida_id: <?= intval($producto['medida_id']) ?> // 🔹 importante
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
        !carrito.some(i => i.nombre.trim().toLowerCase() === 'tortillas') ||  producto.nombre.trim().toLowerCase() === 'servicio de arreachera' &&
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

    const bloqueado = item.bloqueado ? 'disabled' : '';
    const claseBloqueado = item.bloqueado ? 'item-bloqueado' : '';

    html += `
        <div class="carrito-item ${claseBloqueado}">
            <div class="item-info">
                <h4>${item.nombre}${item.bloqueado ? ' 🔒' : ''}</h4>
                <div class="item-precio">$${item.precio.toFixed(2)} c/u</div>
            </div>

            <div class="item-cantidad">
                <button 
                    class="cantidad-btn"
                    ${bloqueado}
                    onclick="modificarCantidad(${index}, -1)"
                >-</button>

                <input 
                    type="number"
                    class="cantidad-input"
                    value="${item.cantidad}"
                    ${bloqueado}
                    onchange="actualizarCantidad(${index}, this.value)"
                    min="0.1"
                    step="0.1"
                >

                <button 
                    class="cantidad-btn"
                    ${bloqueado}
                    onclick="modificarCantidad(${index}, 1)"
                >+</button>
            </div>

            <div class="item-total">$${item.subtotal.toFixed(2)}</div>

            ${
                item.bloqueado
                    ? ''
                    : `<button class="eliminar-item" onclick="eliminarDelCarrito(${index})">×</button>`
            }
        </div>
    `;
});

    
    carritoItems.innerHTML = html;
    subtotalElement.textContent = `$${subtotal.toFixed(2)}`;
    totalElement.textContent = `$${subtotal.toFixed(2)}`;
    
    calcularCambio();
}


function eliminarDelCarrito(index) {

    const item = carrito[index];
    if (!item) return;

    // 🥩 Si se elimina un servicio → eliminar tortillas
    if (item.esServicio) {
        carrito = carrito.filter(i => !i.esTortillas);
    }

    carrito.splice(index, 1);
    actualizarCarrito();
}

function modificarCantidad(index, cambio) {

    const item = carrito[index];
    if (!item) return;

    // 🔒 No permitir tocar tortillas
    if (item.bloqueado) {
        showMessage('⚠️ Las tortillas se ajustan automáticamente', 'info');
        return;
    }

    let incremento = item.esServicio ? 0.1 : 1;
    item.cantidad = Math.round((item.cantidad + cambio * incremento) * 10) / 10;

 if (item.cantidad <= 0) {

    if (item.esServicio) {
        carrito = carrito.filter(i => !i.esTortillas);
    }

    carrito.splice(index, 1);
    actualizarCarrito();
    return;
}


    item.subtotal = item.precio * item.cantidad;

    // 🔄 SI ES SERVICIO → ACTUALIZAR TORTILLAS
    if (item.esServicio) {
        agregarActualizarTortillas(item.cantidad);
    }

    actualizarCarrito();
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
    kilo: {
        tipoVenta: [
            { tipo: 'kilo', nombre: 'Por kilo', icono: '⚖️' },
            { tipo: 'monto', nombre: 'Por monto ($)', icono: '💵' }
        ]
    },

    taco: {
        extras: [{ nombre: 'Queso extra', precio: 2, icono: '🧀' }]
    },

    torta: {
        extras: [{ nombre: 'Queso extra', precio: 5, icono: '🧀' }]
    },

    gordita: {
        extras: [{ nombre: 'Queso extra', precio: 5, icono: '🧀' }]
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

function esServicio(producto) {
    if (!producto || !producto.nombre) return false;

    const nombre = producto.nombre
        .toLowerCase()
        .trim()
        .replace(/\s+/g, ' ');

    return nombre.startsWith('servicio');
}



function abrirModal(productoId) {
  const modal = document.getElementById('modal-agregados');

  // ❌ NO agregar clase aquí
  // modal.classList.add('modal-refrescos');

  modal.style.display = 'block';
  productoActual = productosMap[productoId];
  if (!productoActual) return;

  cantidadActual = 1;

  // Configurar input de cantidad
  const inputCantidad = document.getElementById('cantidad-producto');
  inputCantidad.value = 1;

  if (productoActual.medida_id == 1) { // kilos
      inputCantidad.step = "0.1";
      inputCantidad.min = "0.1";
  } else { // piezas
      inputCantidad.step = "1";
      inputCantidad.min = "1";
  }

  // Nombre y precio
  document.getElementById('modal-producto-nombre').textContent = productoActual.nombre;
  document.getElementById('modal-precio-base').textContent = productoActual.precio.toFixed(2);

  // Limpiar modal
  document.querySelector('.modal-opciones').innerHTML = '';
  document.getElementById('monto-control').style.display = 'none';
  document.querySelector('.cantidad-control').style.display = 'flex';
  document.getElementById('monto-input').value = '';
  document.getElementById('resultado-peso').style.display = 'none';
  tipoVentaSeleccionado = null;

  // 🔹 Servicio de carnitas
  if (esServicio(productoActual)) {
      document.querySelector('.modal-opciones').innerHTML += `
          <label class="extra-item obligatorio">
              <input type="checkbox" checked disabled>
              <span>🫓 Tortillas</span>
              <small>Incluido</small>
          </label>
      `;
  }

  let tipo = 'bebida';
  const nombre = productoActual.nombre.toLowerCase();

  // 🔹 1. COMIDA POR PIEZA (PRIORIDAD ALTA)
  const esPieza =
      nombre.includes('taco') ||
      nombre.includes('torta') ||
      nombre.includes('gordita') ||
      nombre.includes('quesadilla');

  // 🔹 2. SERVICIOS
  const esServicioProducto = nombre.startsWith('servicio de');

  // 🔹 3. CARNE DIRECTA (SOLO nombre exacto)
  const esCarneDirecta =
      !esPieza && (
          nombre === 'bistec' ||
          nombre === 'carnitas' ||
          nombre === 'chorizo' ||
          nombre === 'campechano' ||
          nombre === 'arrachera'
      );

  // 🔹 4. DECISIÓN FINAL
  if (esServicioProducto || esCarneDirecta) {
      tipo = 'kilo';
  }
  else if (esPieza) {
      if (nombre.includes('taco')) tipo = 'taco';
      else if (nombre.includes('torta')) tipo = 'torta';
      else if (nombre.includes('gordita')) tipo = 'gordita';
      else if (nombre.includes('quesadilla')) tipo = 'quesadilla';
  }
  else {
      tipo = 'bebida';
  }

  const config = opcionesPorProducto[tipo] || {};
  const opcionesDiv = document.querySelector('.modal-opciones');

  // 🔹 Tipo de venta (kilo / monto)
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

  // 🔹 Refresco 600 ml
  if (productoActual.nombre.toLowerCase().includes('refresco 600ml')) {
      cargarRefrescos600();
  }
  if (productoActual.nombre.toLowerCase().includes('refresco vidrio')) {
      cargarRefrescosvidrio();
  }
  if (productoActual.nombre.toLowerCase().includes('agua de sabor')) {
      cargarOpcionesAgua();
  }

  // 🔹 SOLO PARA REFRESCOS Y AGUA: hacer modal más ancho
  if (
    nombre.includes('refresco 600ml') ||
    nombre.includes('refresco vidrio') ||
    nombre.includes('agua de sabor')
  ) {
    modal.classList.add('modal-refrescos');
  } else {
    modal.classList.remove('modal-refrescos');
  }

  // Abrir modal
  document.getElementById('modal-agregados').style.display = 'flex';
}



let tipoVentaSeleccionado = null;

function seleccionarTipoVenta(tipo) {
    tipoVentaSeleccionado = tipo;

    const inputCantidad = document.getElementById('cantidad-producto');

    if (tipo === 'monto') {
        document.getElementById('monto-control').style.display = 'block';
        document.querySelector('.cantidad-control').style.display = 'none';

        inputCantidad.disabled = true;
    } 
    else if (tipo === 'kilo') {
        document.getElementById('monto-control').style.display = 'none';
        document.querySelector('.cantidad-control').style.display = 'flex';

        inputCantidad.disabled = false;
        inputCantidad.focus();
    }
}
function cargarRefrescos600() {
    const categoriaId = 5; // categoría 600 ml
    const contenedor = document.querySelector('.modal-opciones');
    contenedor.innerHTML = ''; // limpiar el contenedor

    fetch(`/materias-primas/obtener/${categoriaId}`)
        .then(res => res.json())
        .then(data => {
            if (!data.length) {
                contenedor.innerHTML = '<p>No hay materias primas disponibles</p>';
                return;
            }

            data.forEach(m => {
                const wrapper = document.createElement('div');
                wrapper.className = 'extra-item';

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.dataset.id = m.id; 
                checkbox.dataset.nombre = m.nombre;
                checkbox.value = productoActual.precio;
                checkbox.id = `materia-${m.id}`;

                // ✅ Evitar que se seleccionen varios
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        // Desmarcar todos los demás checkboxes
                        const todos = contenedor.querySelectorAll('input[type="checkbox"]');
                        todos.forEach(cb => {
                            if (cb !== this) cb.checked = false;
                        });
                    }
                });

                const label = document.createElement('label');
                label.setAttribute('for', `materia-${m.id}`);
                label.textContent = `${m.nombre}`;

                wrapper.appendChild(checkbox);
                wrapper.appendChild(label);

                contenedor.appendChild(wrapper);
            });
        })
        .catch(err => console.error('Error cargando materias primas:', err));
}


function cargarRefrescosvidrio() {
    const categoriaId = 6; // categoría 600 ml
    const contenedor = document.querySelector('.modal-opciones');
    contenedor.innerHTML = ''; // limpiar el contenedor

    fetch(`/materias-primas/obtener/${categoriaId}`)
        .then(res => res.json())
        .then(data => {
            if (!data.length) {
                contenedor.innerHTML = '<p>No hay materias primas disponibles</p>';
                return;
            }

            data.forEach(m => {
                const wrapper = document.createElement('div');
                wrapper.className = 'extra-item';

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.dataset.id = m.id; 
                checkbox.dataset.nombre = m.nombre;
                checkbox.value = productoActual.precio;
                checkbox.id = `materia-${m.id}`;

                // ✅ Evitar que se seleccionen varios
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        // Desmarcar todos los demás checkboxes
                        const todos = contenedor.querySelectorAll('input[type="checkbox"]');
                        todos.forEach(cb => {
                            if (cb !== this) cb.checked = false;
                        });
                    }
                });

                const label = document.createElement('label');
                label.setAttribute('for', `materia-${m.id}`);
                label.textContent = `${m.nombre} - $${productoActual.precio.toFixed(2)}`;

                wrapper.appendChild(checkbox);
                wrapper.appendChild(label);

                contenedor.appendChild(wrapper);
            });
        })
        .catch(err => console.error('Error cargando materias primas:', err));
}

function cambiarCantidad(valor) {
    if (!productoActual) return;

    const input = document.getElementById('cantidad-producto');
    if (!input) return;

    let cantidad = parseFloat(input.value) || 0;

    // 🔹 Incremento según medida_id
    // medida_id == 1 → kilo, otros → pieza
    const incremento = productoActual.medida_id == 1 ? 0.1 : 1;

    cantidad += valor * incremento;

    if (productoActual.medida_id == 1) {
        // kilos: mínimo 0.1
        cantidad = Math.round(cantidad * 10) / 10;
        if (cantidad < 0.1) cantidad = 0.1;
    } else {
        // piezas: mínimo 1 entero
        cantidad = Math.max(1, Math.round(cantidad));
    }

    input.value = cantidad;
    cantidadActual = cantidad;
}

function obtenerIncremento(item) {
    // Ejemplo: categoría 1 = pieza, categoría 2 = kilo
    if (item.medida_id === 1) return 1.2; // kilo
    return 1; // pieza
}


function cerrarModal() {
const modal = document.getElementById('modal-agregados');
    modal.classList.remove('modal-refrescos');
    modal.style.display = 'none';
    document.getElementById('modal-agregados').style.display = 'none';
    productoActual = null;
    tipoVentaSeleccionado = null;

    document.getElementById('monto-control').style.display = 'none';
    document.querySelector('.cantidad-control').style.display = 'flex';
}



function confirmarProducto() {

    const productoConfirmado = productoActual; // 👈 Guardamos referencia

    // 🥩 PRODUCTOS POR MONTO
    if (tipoVentaSeleccionado === 'monto') {

        const monto = parseFloat(document.getElementById('monto-input').value);
        if (!monto || monto <= 0) {
            showMessage('❌ Ingrese un monto válido', 'error');
            return;
        }

        const kilos = Math.round((monto / productoActual.precio) * 10) / 10;

carrito.push({
    id: productoActual.id,
    nombre: `${productoActual.nombre} (${kilos} kg)`,
    precio: monto,
    cantidad: kilos,
    subtotal: monto,
    tipoVenta: 'monto',
    esServicio: esServicio(productoActual)
});


if (typeof agregarActualizarTortillas === 'function' && esServicio(productoActual)) {
    agregarActualizarTortillas(kilos);
}

    }

    // 🥩 PRODUCTOS POR KILO
    else if (tipoVentaSeleccionado === 'kilo') {

        if (!cantidadActual || cantidadActual <= 0) {
            showMessage('❌ Ingrese una cantidad válida', 'error');
            return;
        }

        cantidadActual = Math.round(cantidadActual * 10) / 10;
        const subtotal = productoActual.precio * cantidadActual;

carrito.push({
    id: productoActual.id,
    nombre: `${productoActual.nombre} (${cantidadActual} kg)`,
    precio: productoActual.precio,
    cantidad: cantidadActual,
    subtotal: subtotal,
    tipoVenta: 'kilo',
    esServicio: esServicio(productoActual)
});



if (typeof agregarActualizarTortillas === 'function' && esServicio(productoActual)) {
    agregarActualizarTortillas(cantidadActual);
}

    }

    // 🌮 PRODUCTOS NORMALES (tacos, tortas, bebidas, etc.)
else {

    let extras = [];
    let extraTotal = 0;

    const esRefresco = productoActual.nombre.toLowerCase().includes('refresco');
    const esAgua = productoActual.nombre.toLowerCase().includes('agua');


    document
        .querySelectorAll('#modal-agregados input[type="checkbox"]:checked')
        .forEach(c => {
            extras.push(c.dataset.nombre);

            if (!esRefresco && !esAgua) {
                extraTotal += parseFloat(c.value);
            }
        });

    let nombreFinal = productoConfirmado.nombre;
    let precioFinal = parseFloat(productoConfirmado.precio);

    if (esAgua && productoActual.opcionAgua) {
        nombreFinal += ` / ${productoActual.opcionAgua.nombre}`;
        precioFinal = productoActual.opcionAgua.precio;
    }

const cantidad = cantidadActual || 1;
const precioUnitario = precioFinal + extraTotal;

carrito.push({
    id: productoConfirmado.id,
    nombre: nombreFinal + (extras.length ? ' / ' + extras.join(', ') : ''),
    precio: precioUnitario,
    cantidad: cantidad,
    subtotal: precioUnitario * cantidad,
    extras
});
const seleccionada = document.querySelector('#modal-agregados input[type="checkbox"]:checked');

if (seleccionada) {
    carrito[carrito.length - 1].materia_prima_id = seleccionada.dataset.id;
}

    if (esRefresco) {
        const refrescoSeleccionado = document.querySelector('input[name="refresco"]:checked');
        if (refrescoSeleccionado) {
            carrito[carrito.length - 1].nombre += ` (${refrescoSeleccionado.value})`;
        }
    }
}


    cerrarModal();
    actualizarCarrito();
    showMessage('✓ Producto agregado', 'success');
}
function cargarOpcionesAgua() {
    const contenedor = document.querySelector('.modal-opciones');
    contenedor.innerHTML = '';

    delete productoActual.opcionAgua;

    const opciones = [
        { label: '1 Litro', factor: 1 },
        { label: '½ Litro', factor: 0.5 }
    ];

    opciones.forEach((opcion, index) => {
        const wrapper = document.createElement('div');
        wrapper.className = 'extra-item';

        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.id = `agua-${index}`;
        checkbox.dataset.factor = opcion.factor;

        const precioCalculado = parseFloat(productoActual.precio) * opcion.factor;

        const label = document.createElement('label');
        label.setAttribute('for', checkbox.id);
        label.textContent = `${opcion.label} - $${precioCalculado.toFixed(2)}`;

        
        checkbox.addEventListener('change', () => {

            if (checkbox.checked) {
                contenedor.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                    if (cb !== checkbox) cb.checked = false;
                });

                productoActual.opcionAgua = {
                    nombre: opcion.label,
                    factor: opcion.factor,
                    precio: precioCalculado
                };
            } else {
                delete productoActual.opcionAgua;
            }
        });

        wrapper.appendChild(checkbox);
        wrapper.appendChild(label);
        contenedor.appendChild(wrapper);
    });
}

function actualizarCantidadDesdeInput(input) {
    
    input.value = input.value.replace(/[^0-9.]/g, '');

    const partes = input.value.split('.');
    if (partes.length > 2) {
        input.value = partes[0] + '.' + partes.slice(1).join('');
    }

    const valor = parseFloat(input.value);

    if (!isNaN(valor) && valor > 0) {
        cantidadActual = Math.round(valor * 10) / 10;
    } else {
        cantidadActual = null;
    }
}





function agregarActualizarTortillas(kilosServicio) {

    const productoTortillas = Object.values(productosMap)
        .find(p => p.nombre.toLowerCase().includes('tortilla'));

    if (!productoTortillas) {
        console.warn('⚠️ Producto tortillas no encontrado');
        return;
    }

    const precioPorKilo = 40;
    const kilos = Math.round(kilosServicio * 10) / 10;
    const subtotal = kilos * precioPorKilo;

    let tortillas = carrito.find(i => i.esTortillas);

    if (tortillas) {
        tortillas.cantidad = kilos;
        tortillas.subtotal = subtotal;
        tortillas.nombre = `Tortillas (${kilos} kg)`;
    } else {
        carrito.push({
            id: productoTortillas.id,
            nombre: `Tortillas (${kilos} kg)`,
            precio: precioPorKilo,
            cantidad: kilos,
            subtotal: subtotal,
            bloqueado: true,
            esTortillas: true
        });
    }
}



</script>

<?= $this->include('layouts/footer') ?>