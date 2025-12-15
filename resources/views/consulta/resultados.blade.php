<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Resultados de Consulta - SIMIT</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      font-family: 'Open Sans', sans-serif;
    }
    .payment-bar {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background-color: #00a651;
      color: white;
      padding: 1rem 2rem;
      box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
      z-index: 1000;
    }
    .next-cuota {
      background-color: #d1f7c4 !important;
      font-weight: bold;
    }
    .paid-cuota {
      background-color: #e0e0e0 !important;
      color: #666;
    }
    .paid-cuota td {
      color: #666;
    }
    .section-header {
      background-color: #e3f2fd;
      padding: 1rem;
      font-weight: 600;
      cursor: pointer;
      border-bottom: 1px solid #ddd;
    }
    .section-header:hover {
      background-color: #bbdefb;
    }
    .section-content {
      padding: 1.5rem;
    }
    .infractor-name {
      font-size: 1.1rem;
      font-weight: 600;
      color: #333;
    }
    #modalConfirmacion {
      backdrop-filter: blur(4px);
    }
    #modalConfirmacion .bg-white {
      animation: slideDown 0.3s ease-out;
    }
    @keyframes slideDown {
      from {
        transform: translateY(-20px);
        opacity: 0;
      }
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }
  </style>
</head>
<body class="bg-gray-50">
  <div class="min-h-screen pb-32">
    <!-- Header -->
    <header class="bg-blue-900 text-white py-4">
      <div class="container mx-auto px-4">
        <div class="flex justify-between items-center">
          <h1 class="text-xl font-bold">SIMIT - Consulta de Infracciones</h1>
          <a href="{{ route('welcome') }}" class="text-white hover:text-green-300">Nueva Consulta</a>
        </div>
      </div>
    </header>

    <div class="container mx-auto px-4 py-8 max-w-7xl">

      <!-- 1. Información del Infractor -->
      <div class="bg-white rounded-lg shadow-lg mb-6">
        <div class="section-header">
          Información del Infractor
        </div>
        <div class="section-content">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-500 mb-1">Tipo de Documento</label>
              <p class="text-lg font-semibold text-gray-900">{{ $cliente->tipo_documento }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-500 mb-1">Número</label>
              <p class="text-lg font-semibold text-gray-900">{{ $cliente->numero_documento }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-500 mb-1">Nombre</label>
              <p class="infractor-name">{{ $cliente->nombre }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- 2. Multas Registradas -->
      <div class="bg-white rounded-lg shadow-lg mb-6">
        <div class="section-header" onclick="toggleMultas()">
          <span id="multas-icon">▼</span> Ver Multas/Comparendos
        </div>
        <div class="section-content" id="multas-content">
          @if($cliente->multas->count() > 0)
            <div class="overflow-x-auto mb-4">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Placa</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Infracciones</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departamento</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"># Comparendo</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado Pago</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Secretaría</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código del Infracción</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  @foreach($cliente->multas as $multa)
                    <tr class="hover:bg-gray-50 multa-row" id="multa-row-{{ $multa->id }}">
                      <td class="px-4 py-4 whitespace-nowrap">
                        @php
                          $multasPendientesCount = $cliente->multas->where('estado_pago', '!=', 'pagado')->count();
                        @endphp
                        @if($multasPendientesCount === 1 && $multa->estado_pago !== 'pagado' && (!isset($cliente->forma_pago) || $cliente->forma_pago !== 'acuerdo_pago'))
                          <!-- Cuando solo hay una multa pendiente y no hay acuerdo de pago, permitir seleccionarla como pago único -->
                          <input type="checkbox"
                                 class="multa-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                 value="{{ $multa->id }}"
                                 data-valor="{{ number_format($multa->valor, 2, '.', '') }}"
                                 data-placa="{{ $multa->placa }}"
                                 data-comparendo="{{ $multa->comparendo }}"
                                 data-fecha="@if(is_string($multa->fecha)){{ $multa->fecha }}@else{{ $multa->fecha->format('d/m/Y') }}@endif"
                                 onchange="updateTotal()">
                        @endif
                      </td>
                      <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $multa->placa }}</td>
                      <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">${{ number_format($multa->valor, 0, ',', '.') }}</td>
                      <td class="px-4 py-4 whitespace-nowrap">
                        <button onclick="toggleInfracciones({{ $multa->id }})" class="text-blue-600 hover:text-blue-800 font-medium" id="btn-infracciones-{{ $multa->id }}">
                          ► Ver infracciones
                        </button>
                      </td>
                      <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ $multa->departamento }}</td>
                      <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if(is_string($multa->fecha))
                          {{ $multa->fecha }}
                        @else
                          {{ $multa->fecha->format('d/m/Y') }}
                        @endif
                      </td>
                      <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ $multa->comparendo }}</td>
                      <td class="px-4 py-4 whitespace-nowrap">
                        @if($multa->estado_pago == 'pagado')
                          <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Pagado</span>
                        @elseif($multa->estado_pago == 'vencido')
                          <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Vencido</span>
                        @else
                          <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>
                        @endif
                      </td>
                      <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ $multa->secretaria }}</td>
                      <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ $multa->codigo_infraccion }}</td>
                    </tr>
                    <!-- Fila desplegable con descripción de la infracción -->
                    <tr id="infracciones-row-{{ $multa->id }}" class="hidden bg-blue-50">
                      <td colspan="10" class="px-4 py-4">
                        <div class="bg-white rounded-lg p-4 border border-blue-200">
                          <p class="text-sm text-gray-700">{{ $multa->infracciones }}</p>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <div class="mt-4 p-4 bg-gray-50 rounded-lg">
              @if($cliente->forma_pago === 'pago_unico' && isset($descuentoAplicado) && $descuentoAplicado > 0)
                <p class="text-sm text-gray-700">
                  <strong>Total multas a pagar (sin descuento):</strong>
                  <span class="text-lg font-bold text-gray-600 line-through">${{ number_format($multasPendientes, 0, ',', '.') }}</span>
                </p>
                <p class="text-sm text-gray-700 mt-2">
                  <strong>Descuento Pago Único ({{ number_format($cliente->descuento_pago_unico, 2, ',', '.') }}%):</strong>
                  <span class="text-lg font-bold text-green-600">-${{ number_format($descuentoAplicado, 0, ',', '.') }}</span>
                </p>
                <p class="text-sm text-gray-700 mt-2">
                  <strong>Total multas a pagar (con descuento):</strong>
                  <span class="text-lg font-bold text-gray-900">${{ number_format($multasPendientesConDescuento, 0, ',', '.') }}</span>
                </p>
              @else
                <p class="text-sm text-gray-700">
                  <strong>Total multas a pagar:</strong>
                  <span class="text-lg font-bold text-gray-900">${{ number_format($multasPendientes, 0, ',', '.') }}</span>
                </p>
              @endif
              <p class="text-sm text-gray-700 mt-2">
                <strong>Cantidad de multas sin pagar:</strong>
                <span class="text-lg font-bold text-gray-900">{{ $cliente->multas->where('estado_pago', '!=', 'pagado')->count() }}</span>
              </p>
            </div>
          @else
            <div class="text-center py-12">
              <p class="text-gray-500">No se encontraron multas registradas para este documento.</p>
            </div>
          @endif
        </div>
      </div>

      <!-- Acuerdos de Pago (solo si existe acuerdo) -->
      @if($cliente->forma_pago == 'acuerdo_pago' && $cliente->numero_acuerdo && $cuotas->count() > 0)
      <div class="bg-white rounded-lg shadow-lg mb-6">
        <div class="section-header">
          Acuerdos de Pago
        </div>
        <div class="section-content">
          <div class="mb-4 p-4 bg-blue-50 rounded-lg">
            <p class="text-sm text-gray-700">
              <strong>Número de Acuerdo:</strong> {{ $cliente->numero_acuerdo }}
            </p>
            <p class="text-sm text-gray-700 mt-2">
              <strong>Total de Cuotas:</strong> {{ $cliente->numero_cuotas }}
            </p>
            @if($cliente->porcentaje_primera_cuota)
              <p class="text-sm text-gray-700 mt-2">
                <strong>Porcentaje Primera Cuota:</strong> {{ number_format($cliente->porcentaje_primera_cuota, 2, ',', '.') }}%
              </p>
            @endif
          </div>

          <div class="overflow-x-auto mb-4">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <input type="checkbox" id="selectAllCuotas" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" onchange="toggleAllCuotas(this)">
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número de Acuerdo</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número de Cuota</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor Cuota</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Pago</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departamento</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Resolución</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                @php
                  $primeraCuotaPendiente = $cuotas->where('estado', 'pendiente')->first();
                @endphp
                @foreach($cuotas as $cuota)
                  @php
                    $isNext = $cuota->estado == 'pendiente' && $cuota->fecha_pago <= now()->addDays(30);
                    $isPaid = $cuota->estado == 'pagado';
                    $isFirstPending = $primeraCuotaPendiente && $cuota->id == $primeraCuotaPendiente->id;
                  @endphp
                  <tr class="hover:bg-gray-50 cuota-row {{ $isNext ? 'next-cuota' : '' }} {{ $isPaid ? 'paid-cuota' : '' }}"
                      data-estado="{{ $cuota->estado }}">
                    <td class="px-4 py-4 whitespace-nowrap">
                      <input type="checkbox" class="cuota-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                             value="{{ $cuota->id }}"
                             data-valor="{{ number_format($cuota->valor_cuota, 2, '.', '') }}"
                             data-acuerdo="{{ $cliente->numero_acuerdo }}"
                             data-numero="{{ $cuota->numero_cuota }}"
                             data-fecha="{{ $cuota->fecha_pago->format('d/m/Y') }}"
                             onchange="updateTotal()"
                             @if($isFirstPending) checked @endif
                             @if($isPaid) disabled @endif>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $cliente->numero_acuerdo }}</td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $cuota->numero_cuota }}</td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">${{ number_format($cuota->valor_cuota, 0, ',', '.') }}</td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ $cuota->fecha_pago->format('d/m/Y') }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                      @if($cuota->estado == 'pagado')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Pagado</span>
                      @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>
                      @endif
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ $cliente->multas->first()->departamento ?? 'N/A' }}</td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                      @if($cuota->fecha_resolucion)
                        {{ $cuota->fecha_resolucion->format('d/m/Y') }}
                      @else
                        @php
                          // Si no hay fecha_resolucion, usar el created_at de la primera multa
                          $primeraMulta = $cliente->multas()->orderBy('created_at')->first();
                          $fechaResolucion = $primeraMulta ? $primeraMulta->created_at : now();
                        @endphp
                        {{ $fechaResolucion->format('d/m/Y') }}
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
      @endif

      <!-- 3. Datos del Pagador -->
      <div class="bg-white rounded-lg shadow-lg mb-6">
        <div class="section-header">
          Datos del Pagador
        </div>
        <div class="section-content">
            <form id="pagadorForm" class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Nombre -->
                <div>
                    <label for="nombre_pagador" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre completo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nombre_pagador" name="nombre_pagador"
                           minlength="3"
                           maxlength="60"
                           required
                           autocomplete="name"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                           placeholder="Ingrese el nombre completo"
                           oninput="this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, ''); validarCampoPagador(this);"
                           onblur="validarCampoPagador(this);">
                    <p id="error_nombre_pagador" class="text-red-500 text-xs mt-1 hidden">Solo letras y espacios, mínimo 3 caracteres</p>
                </div>

                <!-- Email -->
                <div>
                    <label for="email_pagador" class="block text-sm font-medium text-gray-700 mb-2">
                        Correo electrónico <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email_pagador" name="email_pagador"
                           required
                           maxlength="80"
                           autocomplete="email"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                           placeholder="correo@ejemplo.com"
                           oninput="validarCampoPagador(this);"
                           onblur="validarCampoPagador(this);">
                    <p id="error_email_pagador" class="text-red-500 text-xs mt-1 hidden">Ingrese un correo electrónico válido</p>
                </div>

                <!-- Teléfono -->
                <div>
                    <label for="telefono_pagador" class="block text-sm font-medium text-gray-700 mb-2">
                        Teléfono <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" id="telefono_pagador" name="telefono_pagador"
                           minlength="7"
                           maxlength="10"
                           required
                           autocomplete="tel"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                           placeholder="3001234567"
                           oninput="this.value = this.value.replace(/[^0-9]/g, ''); validarCampoPagador(this);"
                           onblur="validarCampoPagador(this);">
                    <p id="error_telefono_pagador" class="text-red-500 text-xs mt-1 hidden">Solo números, entre 7 y 10 dígitos</p>
                </div>

                <!-- Dirección -->
                <div>
                    <label for="direccion_pagador" class="block text-sm font-medium text-gray-700 mb-2">
                        Dirección <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="direccion_pagador" name="direccion_pagador"
                           minlength="5"
                           maxlength="120"
                           required
                           autocomplete="street-address"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                           placeholder="Calle, número, ciudad"
                           oninput="validarCampoPagador(this);"
                           onblur="validarCampoPagador(this);">
                    <p id="error_direccion_pagador" class="text-red-500 text-xs mt-1 hidden">Mínimo 5 caracteres</p>
                </div>

            </form>
        </div>
      </div>

    </div>

    <!-- Barra de Pago Fija -->
    <div class="payment-bar">
      <div class="container mx-auto flex justify-between items-center">
        <div>
          <span class="text-lg font-bold">Total seleccionado para pagar: </span>
          <span class="text-2xl font-bold" id="total-seleccionado">$0</span>
        </div>
        <div class="flex gap-4">
          <button onclick="pagarAhora()" class="bg-blue-900 hover:bg-blue-800 text-white font-semibold px-6 py-2 rounded-lg transition-colors">
            Pagar Ahora
          </button>
          <a href="{{ route('welcome') }}" class="bg-blue-900 hover:bg-blue-800 text-white font-semibold px-6 py-2 rounded-lg transition-colors inline-block text-center">
            Volver
          </a>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="bg-blue-900 text-white py-6 mt-12">
      <div class="container mx-auto px-4 text-center">
        <p class="text-sm">© 2025 SIMIT - Sistema Integrado de Información sobre Multas y Sanciones por Infracciones de Tránsito.</p>
      </div>
    </footer>
  </div>

  <!-- Modal de Confirmación -->
  <div id="modalConfirmacion" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" onclick="if(event.target === this) cerrarModal()">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
      <div class="p-6 border-b border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800">Confirmar Selección</h2>
      </div>
      <div class="p-6">
        <p class="text-gray-700 mb-4">Has seleccionado los siguientes ítems para pagar:</p>
        <ul id="listaItems" class="space-y-2 mb-6 list-disc list-inside">
          <!-- Los items se agregarán aquí dinámicamente -->
        </ul>
        <div class="border-t border-gray-200 pt-4">
          <div class="flex justify-between items-center">
            <span class="text-lg font-semibold text-gray-700">Total a pagar:</span>
            <span id="totalModal" class="text-2xl font-bold text-gray-900">$0</span>
          </div>
        </div>
      </div>
      <div class="p-6 border-t border-gray-200 flex justify-end gap-4">
        <button onclick="cerrarModal()" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-6 py-2 rounded-lg transition-colors">
          Cancelar
        </button>
        <button onclick="confirmarPago()" class="bg-blue-900 hover:bg-blue-800 text-white font-semibold px-6 py-2 rounded-lg transition-colors">
          Confirmar
        </button>
      </div>
    </div>
  </div>


  <script>
    // Variables globales con totales
    const totalMultas = {{ $totalMultas }};
    const totalCuotasPendientes = {{ $cuotas->where('estado', 'pendiente')->count() }};
    // Variables para descuento
    const formaPago = @json($cliente->forma_pago ?? null);
    const descuentoPagoUnico = {{ $cliente->descuento_pago_unico ?? 0 }};
    const tieneDescuento = formaPago === 'pago_unico' && descuentoPagoUnico > 0;

    // ========== VALIDACIÓN DE CAMPOS DEL PAGADOR ==========
    
    /**
     * Valida un campo del formulario del pagador y muestra/oculta mensajes de error
     */
    function validarCampoPagador(input) {
      const id = input.id;
      const valor = input.value.trim();
      const errorElement = document.getElementById('error_' + id);
      let esValido = true;
      
      // Validaciones específicas por campo
      switch(id) {
        case 'nombre_pagador':
          // Solo letras y espacios, mínimo 3 caracteres
          const regexNombre = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{3,60}$/;
          esValido = regexNombre.test(valor) && valor.length >= 3;
          break;
          
        case 'email_pagador':
          // Email válido
          const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          esValido = regexEmail.test(valor);
          break;
          
        case 'telefono_pagador':
          // Solo números, entre 7 y 10 dígitos
          const regexTelefono = /^[0-9]{7,10}$/;
          esValido = regexTelefono.test(valor);
          break;
          
        case 'direccion_pagador':
          // Mínimo 5 caracteres
          esValido = valor.length >= 5;
          break;
      }
      
      // Aplicar estilos visuales
      if (valor.length > 0) {
        if (esValido) {
          input.classList.remove('border-red-500', 'ring-red-500');
          input.classList.add('border-green-500', 'ring-green-500');
          if (errorElement) errorElement.classList.add('hidden');
        } else {
          input.classList.remove('border-green-500', 'ring-green-500');
          input.classList.add('border-red-500', 'ring-red-500');
          if (errorElement) errorElement.classList.remove('hidden');
        }
      } else {
        // Sin valor, quitar estilos de validación
        input.classList.remove('border-red-500', 'ring-red-500', 'border-green-500', 'ring-green-500');
        if (errorElement) errorElement.classList.add('hidden');
      }
      
      return esValido;
    }

    /**
     * Valida todo el formulario del pagador
     */
    function validarFormularioPagador() {
      const campos = ['nombre_pagador', 'email_pagador', 'telefono_pagador', 'direccion_pagador'];
      let formularioValido = true;
      let primerCampoInvalido = null;
      
      campos.forEach(campoId => {
        const input = document.getElementById(campoId);
        if (input) {
          const esValido = validarCampoPagador(input);
          if (!esValido && !primerCampoInvalido) {
            primerCampoInvalido = input;
          }
          if (!esValido) {
            formularioValido = false;
          }
        }
      });
      
      // Hacer scroll y focus al primer campo inválido
      if (primerCampoInvalido) {
        primerCampoInvalido.scrollIntoView({ behavior: 'smooth', block: 'center' });
        primerCampoInvalido.focus();
      }
      
      return formularioValido;
    }

    let multasAbiertas = true;

    function toggleMultas() {
      const content = document.getElementById('multas-content');
      const icon = document.getElementById('multas-icon');
      multasAbiertas = !multasAbiertas;

      if (multasAbiertas) {
        content.style.display = 'block';
        icon.textContent = '▼';
      } else {
        content.style.display = 'none';
        icon.textContent = '►';
      }
    }


    function updateTotal() {
      // Sumar cuotas seleccionadas
      const cuotasSeleccionadas = document.querySelectorAll('.cuota-checkbox:checked');
      let total = 0;
      let esPagoMultas = false;

      cuotasSeleccionadas.forEach(checkbox => {
        if (checkbox.checked) {
          const valorStr = checkbox.getAttribute('data-valor');
          const valor = parseFloat(valorStr);
          if (!isNaN(valor) && isFinite(valor)) {
            total += valor;
          }
        }
      });

      // Si no hay cuotas seleccionadas, intentar sumar multa única seleccionada (pago directo de multa)
      if (cuotasSeleccionadas.length === 0) {
        const multasSeleccionadas = document.querySelectorAll('.multa-checkbox:checked');
        multasSeleccionadas.forEach(checkbox => {
          if (checkbox.checked) {
            const valorStr = checkbox.getAttribute('data-valor');
            const valor = parseFloat(valorStr);
            if (!isNaN(valor) && isFinite(valor)) {
              total += valor;
            }
          }
        });
        esPagoMultas = multasSeleccionadas.length > 0;
      }

      // Aplicar descuento si es pago único de multas y hay descuento configurado
      let totalConDescuento = total;
      if (esPagoMultas && tieneDescuento) {
        const descuento = total * (descuentoPagoUnico / 100);
        totalConDescuento = total - descuento;
      }

      // Formatear el total con descuento aplicado (si corresponde)
      const totalRedondeado = Math.round(totalConDescuento);
      const totalFormateado = totalRedondeado.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      });

      document.getElementById('total-seleccionado').textContent = '$' + totalFormateado;
    }

    function toggleAllCuotas(checkbox) {
      const checkboxes = document.querySelectorAll('.cuota-checkbox:not(:disabled)');
      checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
      });
      updateTotal();
    }

    function toggleInfracciones(multaId) {
      const row = document.getElementById(`infracciones-row-${multaId}`);
      const button = document.getElementById(`btn-infracciones-${multaId}`);

      if (row.classList.contains('hidden')) {
        // Desplegar
        row.classList.remove('hidden');
        button.innerHTML = '▼ Ocultar infracciones';
      } else {
        // Ocultar
        row.classList.add('hidden');
        button.innerHTML = '► Ver infracciones';
      }
    }

    function pagarAhora() {
      const cuotasCheckboxes = document.querySelectorAll('.cuota-checkbox:checked');
      const multasCheckboxes = document.querySelectorAll('.multa-checkbox:checked');

      if (cuotasCheckboxes.length === 0 && multasCheckboxes.length === 0) {
        alert('Por favor, seleccione al menos una cuota o multa para pagar.');
        return;
      }

      // Validar formulario del pagador con la nueva función
      if (!validarFormularioPagador()) {
        alert('Por favor, complete correctamente todos los datos del pagador.');
        return;
      }

      const nombrePagador = document.getElementById('nombre_pagador').value.trim();
      const emailPagador = document.getElementById('email_pagador').value.trim();
      const telefonoPagador = document.getElementById('telefono_pagador').value.trim();
      const direccionPagador = document.getElementById('direccion_pagador').value.trim();

      // Preparar datos para el modal
      const items = [];
      let total = 0;
      let esPagoMultasModal = false;

      if (cuotasCheckboxes.length > 0) {
        cuotasCheckboxes.forEach(checkbox => {
          const acuerdo = checkbox.getAttribute('data-acuerdo');
          const numero = checkbox.getAttribute('data-numero');
          const valor = parseFloat(checkbox.getAttribute('data-valor'));
          const fecha = checkbox.getAttribute('data-fecha');

          total += valor;

          items.push({
            id: checkbox.value,
            tipo: 'cuota',
            acuerdo: acuerdo,
            numero: numero,
            valor: valor,
            fecha: fecha
          });
        });

        // Ordenar items por número de cuota
        items.sort((a, b) => parseInt(a.numero) - parseInt(b.numero));
      } else {
        // Pago directo de multas (solo se permite cuando hay una)
        esPagoMultasModal = true;
        multasCheckboxes.forEach(checkbox => {
          const placa = checkbox.getAttribute('data-placa');
          const comparendo = checkbox.getAttribute('data-comparendo');
          const valor = parseFloat(checkbox.getAttribute('data-valor'));
          const fecha = checkbox.getAttribute('data-fecha');

          total += valor;

          items.push({
            id: checkbox.value,
            tipo: 'multa',
            placa: placa,
            comparendo: comparendo,
            valor: valor,
            fecha: fecha
          });
        });
      }

      // Aplicar descuento si es pago único de multas y hay descuento configurado
      let totalConDescuento = total;
      if (esPagoMultasModal && tieneDescuento) {
        const descuento = total * (descuentoPagoUnico / 100);
        totalConDescuento = total - descuento;
      }

      // Mostrar modal
      mostrarModal(items, total, totalConDescuento, esPagoMultasModal);
    }

    function mostrarModal(items, total, totalConDescuento, esPagoMultas) {
      const listaItems = document.getElementById('listaItems');
      const totalModal = document.getElementById('totalModal');
      const modal = document.getElementById('modalConfirmacion');

      // Limpiar lista anterior
      listaItems.innerHTML = '';

      // Agregar items a la lista
      items.forEach(item => {
        const li = document.createElement('li');
        li.className = 'text-gray-700 py-1';
        const valorFormateado = new Intl.NumberFormat('es-CO', {
          minimumFractionDigits: 0,
          maximumFractionDigits: 0
        }).format(item.valor);

        if (item.tipo === 'multa') {
          li.innerHTML = `<strong>Multa placa ${item.placa}</strong> (Comparendo ${item.comparendo}): $${valorFormateado} (Fecha: ${item.fecha})`;
        } else {
          li.innerHTML = `<strong>Cuota #${item.acuerdo}-Cuota${item.numero}</strong>: $${valorFormateado} (Fecha: ${item.fecha})`;
        }
        listaItems.appendChild(li);
      });

      // Mostrar información de descuento si aplica
      if (esPagoMultas && tieneDescuento && totalConDescuento < total) {
        const totalOriginalFormateado = new Intl.NumberFormat('es-CO', {
          minimumFractionDigits: 0,
          maximumFractionDigits: 0
        }).format(total);
        const descuentoFormateado = new Intl.NumberFormat('es-CO', {
          minimumFractionDigits: 0,
          maximumFractionDigits: 0
        }).format(total - totalConDescuento);
        
        const descuentoLi = document.createElement('li');
        descuentoLi.className = 'text-gray-700 py-1 mt-2 border-t pt-2';
        descuentoLi.innerHTML = `<strong>Total original:</strong> $${totalOriginalFormateado}`;
        listaItems.appendChild(descuentoLi);
        
        const descuentoLi2 = document.createElement('li');
        descuentoLi2.className = 'text-green-600 py-1';
        descuentoLi2.innerHTML = `<strong>Descuento Pago Único (${descuentoPagoUnico.toFixed(2)}%):</strong> -$${descuentoFormateado}`;
        listaItems.appendChild(descuentoLi2);
      }

      // Mostrar total con descuento aplicado (si corresponde)
      const totalFinal = esPagoMultas && tieneDescuento ? totalConDescuento : total;
      const totalFormateado = new Intl.NumberFormat('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(totalFinal);
      totalModal.textContent = '$' + totalFormateado;

      // Mostrar modal
      modal.classList.remove('hidden');
    }

    function cerrarModal() {
      const modal = document.getElementById('modalConfirmacion');
      modal.classList.add('hidden');
    }

    function confirmarPago() {
      const cuotasCheckboxes = document.querySelectorAll('.cuota-checkbox:checked');
      const multasCheckboxes = document.querySelectorAll('.multa-checkbox:checked');
      const cuotasIds = Array.from(cuotasCheckboxes).map(cb => cb.value);
      const multasIds = Array.from(multasCheckboxes).map(cb => cb.value);

      if (cuotasIds.length === 0 && multasIds.length === 0) {
        alert('Por favor, seleccione al menos una cuota o multa para pagar.');
        return;
      }

      // Validar formulario del pagador
      if (!validarFormularioPagador()) {
        cerrarModal();
        alert('Por favor, complete correctamente todos los datos del pagador.');
        return;
      }

      const nombrePagador = document.getElementById('nombre_pagador').value.trim();
      const emailPagador = document.getElementById('email_pagador').value.trim();
      const telefonoPagador = document.getElementById('telefono_pagador').value.trim();
      const direccionPagador = document.getElementById('direccion_pagador').value.trim();

      // Crear formulario para enviar datos
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = '{{ route("pago.confirmar") }}';

      // Agregar token CSRF
      const csrfInput = document.createElement('input');
      csrfInput.type = 'hidden';
      csrfInput.name = '_token';
      csrfInput.value = '{{ csrf_token() }}';
      form.appendChild(csrfInput);

      // Agregar IDs de cuotas o multas según corresponda
      cuotasIds.forEach((id, index) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `cuotas_ids[${index}]`;
        input.value = id;
        form.appendChild(input);
      });

      multasIds.forEach((id, index) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `multas_ids[${index}]`;
        input.value = id;
        form.appendChild(input);
      });

      // Agregar datos del pagador
      const nombreInput = document.createElement('input');
      nombreInput.type = 'hidden';
      nombreInput.name = 'nombre_pagador';
      nombreInput.value = nombrePagador;
      form.appendChild(nombreInput);

      const emailInput = document.createElement('input');
      emailInput.type = 'hidden';
      emailInput.name = 'email_pagador';
      emailInput.value = emailPagador;
      form.appendChild(emailInput);

      const telefonoInput = document.createElement('input');
      telefonoInput.type = 'hidden';
      telefonoInput.name = 'telefono_pagador';
      telefonoInput.value = telefonoPagador;
      form.appendChild(telefonoInput);

      const direccionInput = document.createElement('input');
      direccionInput.type = 'hidden';
      direccionInput.name = 'direccion_pagador';
      direccionInput.value = direccionPagador;
      form.appendChild(direccionInput);

      // Agregar formulario al body y enviarlo
      document.body.appendChild(form);
      form.submit();
    }

    // Inicializar total al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
      // Desmarcar todos los checkboxes de cuotas al cargar
      document.querySelectorAll('.cuota-checkbox').forEach(cb => {
        cb.checked = false;
      });
      // Solo marcar la primera cuota pendiente si existe
      const primeraCuotaCheckbox = document.querySelector('.cuota-checkbox:not(:disabled)');
      if (primeraCuotaCheckbox) {
        primeraCuotaCheckbox.checked = true;
      }
      updateTotal();
    });
  </script>
</body>
</html>
