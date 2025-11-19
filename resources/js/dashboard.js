let multaCounter = 1;

function mostrarFormulario() {
    // Redirigir a la página de crear cliente
    window.location.href = '/clientes/create';
}

function mostrarLista() {
    document.getElementById('formularioView').classList.add('hidden');
    document.getElementById('listaView').classList.remove('hidden');
}

function verificarFormaPago() {
    const multas = document.querySelectorAll('.multa-item');
    const formaPagoSection = document.getElementById('formaPagoSection');
    
    if (multas.length > 1) {
        formaPagoSection.classList.remove('hidden');
    } else {
        formaPagoSection.classList.add('hidden');
        document.getElementById('forma_pago').value = '';
        document.getElementById('numero_cuotas').value = '';
        document.getElementById('porcentaje_primera_cuota').value = '30';
        document.getElementById('resumenCuotas').classList.add('hidden');
    }
}

function calcularTotal() {
    let total = 0;
    const valores = document.querySelectorAll('input[name*="[valor]"]');
    valores.forEach(input => {
        const valor = parseFloat(input.value) || 0;
        total += valor;
    });
    return total;
}

function calcularCuotas() {
    const formaPago = document.getElementById('forma_pago').value;
    const total = calcularTotal();
    
    document.getElementById('totalPagar').textContent = '$' + total.toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    
    if (formaPago === 'acuerdo_pago') {
        const numeroCuotas = parseInt(document.getElementById('numero_cuotas').value) || 0;
        const porcentajePrimera = parseFloat(document.getElementById('porcentaje_primera_cuota').value) || 30;
        
        if (numeroCuotas > 0 && total > 0) {
            const primeraCuota = (total * porcentajePrimera) / 100;
            const resto = total - primeraCuota;
            const cuotaRestante = resto / (numeroCuotas - 1);
            
            const detalleCuotas = document.getElementById('detalleCuotas');
            detalleCuotas.innerHTML = '';
            
            // Primera cuota
            const primeraDiv = document.createElement('div');
            primeraDiv.className = 'flex justify-between items-center p-3 bg-yellow-50 rounded border border-yellow-200';
            primeraDiv.innerHTML = `
                <span class="font-medium">Cuota 1 (${porcentajePrimera}%):</span>
                <span class="font-bold text-yellow-700">$${primeraCuota.toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
            `;
            detalleCuotas.appendChild(primeraDiv);
            
            // Resto de cuotas
            for (let i = 2; i <= numeroCuotas; i++) {
                const cuotaDiv = document.createElement('div');
                cuotaDiv.className = 'flex justify-between items-center p-3 bg-gray-50 rounded';
                cuotaDiv.innerHTML = `
                    <span class="font-medium">Cuota ${i}:</span>
                    <span class="font-bold text-gray-700">$${cuotaRestante.toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                `;
                detalleCuotas.appendChild(cuotaDiv);
            }
            
            document.getElementById('resumenCuotas').classList.remove('hidden');
        } else {
            document.getElementById('resumenCuotas').classList.add('hidden');
        }
    } else if (formaPago === 'pago_unico') {
        const detalleCuotas = document.getElementById('detalleCuotas');
        detalleCuotas.innerHTML = `
            <div class="flex justify-between items-center p-3 bg-green-50 rounded border border-green-200">
                <span class="font-medium">Pago Único:</span>
                <span class="font-bold text-green-700">$${total.toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
            </div>
        `;
        document.getElementById('resumenCuotas').classList.remove('hidden');
    } else {
        document.getElementById('resumenCuotas').classList.add('hidden');
    }
}

function actualizarFormaPago() {
    const formaPago = document.getElementById('forma_pago').value;
    const numeroCuotasDiv = document.getElementById('numeroCuotasDiv');
    const porcentajePrimeraDiv = document.getElementById('porcentajePrimeraDiv');
    
    if (formaPago === 'acuerdo_pago') {
        numeroCuotasDiv.classList.remove('hidden');
        porcentajePrimeraDiv.classList.remove('hidden');
    } else {
        numeroCuotasDiv.classList.add('hidden');
        porcentajePrimeraDiv.classList.add('hidden');
    }
    
    calcularCuotas();
}

function agregarMulta() {
    const container = document.getElementById('multasContainer');
    const nuevaMulta = document.createElement('div');
    nuevaMulta.className = 'multa-item mb-6 p-6 border-2 border-gray-200 rounded-lg bg-gray-50';
    nuevaMulta.innerHTML = `
        <div class="flex justify-between items-center mb-4">
            <h5 class="text-lg font-semibold text-gray-700">Multa #${multaCounter + 1}</h5>
            <button type="button" onclick="eliminarMulta(this)" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm eliminar-multa-btn">
                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Eliminar
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Placa *</label>
                <input type="text" name="multas[${multaCounter}][placa]" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Valor *</label>
                <input type="number" step="0.01" name="multas[${multaCounter}][valor]" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" oninput="calcularCuotas()">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Departamento *</label>
                <input type="text" name="multas[${multaCounter}][departamento]" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha *</label>
                <input type="text" name="multas[${multaCounter}][fecha]" required placeholder="dd/mm/aaaa" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Comparendo *</label>
                <input type="text" name="multas[${multaCounter}][comparendo]" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado de Pago *</label>
                <select name="multas[${multaCounter}][estado_pago]" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="pendiente">Pendiente</option>
                    <option value="pagado">Pagado</option>
                    <option value="vencido">Vencido</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Secretaría *</label>
                <input type="text" name="multas[${multaCounter}][secretaria]" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Código de Infracción *</label>
                <input type="text" name="multas[${multaCounter}][codigo_infraccion]" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Infracciones *</label>
                <input type="text" name="multas[${multaCounter}][infracciones]" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Descripción de la infracción">
            </div>
        </div>
    `;
    container.appendChild(nuevaMulta);
    multaCounter++;
    verificarFormaPago();
    
    // Mostrar botón eliminar en la primera multa si hay más de una
    const primeraMulta = container.querySelector('.multa-item');
    if (container.querySelectorAll('.multa-item').length > 1) {
        const primeraMultaBtn = primeraMulta.querySelector('.eliminar-multa-btn');
        if (primeraMultaBtn) {
            primeraMultaBtn.classList.remove('hidden');
        }
    }
}

function eliminarMulta(button) {
    const multaItem = button.closest('.multa-item');
    const container = document.getElementById('multasContainer');
    const multas = container.querySelectorAll('.multa-item');
    
    if (multas.length > 1) {
        multaItem.remove();
        verificarFormaPago();
        calcularCuotas();
        
        // Renumerar las multas
        const multasRestantes = container.querySelectorAll('.multa-item');
        multasRestantes.forEach((multa, index) => {
            const titulo = multa.querySelector('h5');
            if (titulo) {
                titulo.textContent = `Multa #${index + 1}`;
            }
            
            // Ocultar botón eliminar si solo queda una
            if (multasRestantes.length === 1) {
                const btn = multa.querySelector('.eliminar-multa-btn');
                if (btn) {
                    btn.classList.add('hidden');
                }
            }
        });
    }
}

function resetearMultas() {
    // Eliminar todas las multas excepto la primera
    const multasContainer = document.getElementById('multasContainer');
    const multas = multasContainer.querySelectorAll('.multa-item');
    for (let i = multas.length - 1; i > 0; i--) {
        multas[i].remove();
    }
    
    // Resetear contador
    multaCounter = 1;
    
    // Limpiar campos de la primera multa
    const primeraMulta = multasContainer.querySelector('.multa-item');
    if (primeraMulta) {
        primeraMulta.querySelectorAll('input, select').forEach(input => {
            if (input.name !== 'multas[0][estado_pago]') {
                input.value = '';
            } else {
                input.value = 'pendiente';
            }
        });
    }
    
    // Limpiar forma de pago
    document.getElementById('forma_pago').value = '';
    document.getElementById('numero_cuotas').value = '';
    document.getElementById('porcentaje_primera_cuota').value = '30';
    document.getElementById('numeroCuotasDiv').classList.add('hidden');
    document.getElementById('porcentajePrimeraDiv').classList.add('hidden');
    document.getElementById('resumenCuotas').classList.add('hidden');
    
    verificarFormaPago();
}

async function editarMulta(id) {
    try {
        const response = await fetch(`/simit-registros/${id}/edit-data`);
        const multa = await response.json();
        
        // Redirigir a la página de editar cliente
        if (multa.cliente && multa.cliente.id) {
            window.location.href = `/clientes/${multa.cliente.id}/edit`;
        } else {
            alert('Error: No se encontró el cliente asociado a esta multa');
        }
    } catch (error) {
        console.error('Error al cargar multa:', error);
        alert('Error al cargar los datos de la multa');
    }
}

// Exportar funciones al scope global para que estén disponibles en los onclick
window.mostrarFormulario = mostrarFormulario;
window.mostrarLista = mostrarLista;
window.verificarFormaPago = verificarFormaPago;
window.calcularTotal = calcularTotal;
window.calcularCuotas = calcularCuotas;
window.actualizarFormaPago = actualizarFormaPago;
window.agregarMulta = agregarMulta;
window.eliminarMulta = eliminarMulta;
window.resetearMultas = resetearMultas;
window.editarMulta = editarMulta;

