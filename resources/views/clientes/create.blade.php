@extends('layouts.app')

@section('content')
<div class="p-8">
    <div class="max-w-5xl mx-auto">
        <!-- Header del Formulario -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Agregar Cliente</h2>
            <a href="{{ route('dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <span>Cancelar</span>
            </a>
        </div>

        <!-- Mensajes de error en el formulario -->
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
                <strong class="font-bold">¡Error al guardar!</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <form id="multaForm" method="POST" action="{{ route('clientes.store') }}" class="bg-white rounded-lg shadow-lg p-8" data-store-url="{{ route('clientes.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="multa_id" id="multa_id">

            <!-- Sección Cliente -->
            <div class="mb-8">
                <h4 class="text-xl font-semibold text-gray-700 mb-6 border-b-2 border-blue-500 pb-3">Datos del Cliente</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre *</label>
                        <input type="text" name="nombre" id="nombre" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('nombre') }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Documento *</label>
                        <select name="tipo_documento" id="tipo_documento" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Seleccione...</option>
                            <option value="CC" {{ old('tipo_documento') == 'CC' ? 'selected' : '' }}>Cédula de Ciudadanía</option>
                            <option value="CE" {{ old('tipo_documento') == 'CE' ? 'selected' : '' }}>Cédula de Extranjería</option>
                            <option value="NIT" {{ old('tipo_documento') == 'NIT' ? 'selected' : '' }}>NIT</option>
                            <option value="TI" {{ old('tipo_documento') == 'TI' ? 'selected' : '' }}>Tarjeta de Identidad</option>
                            <option value="PASAPORTE" {{ old('tipo_documento') == 'PASAPORTE' ? 'selected' : '' }}>Pasaporte</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Número de Documento *</label>
                        <input type="text" name="numero_documento" id="numero_documento" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ old('numero_documento') }}">
                    </div>
                </div>
            </div>

            <!-- Sección Multas -->
            <div class="mb-8">
                <h4 class="text-xl font-semibold text-gray-700 mb-6 border-b-2 border-blue-500 pb-3">Datos de la Multa</h4>

                <div id="multasContainer">
                    <div class="multa-item mb-6 p-6 border-2 border-gray-200 rounded-lg bg-gray-50">
                        <div class="flex justify-between items-center mb-4">
                            <h5 class="text-lg font-semibold text-gray-700">Multa #1</h5>
                            <button type="button" onclick="eliminarMulta(this)" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm hidden eliminar-multa-btn">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Eliminar
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Placa *</label>
                                <input type="text" name="multas[0][placa]" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Valor *</label>
                                <input
                                    type="number"
                                    step="1"
                                    min="0"
                                    name="multas[0][valor]"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    inputmode="numeric"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, ''); calcularCuotas();">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Departamento *</label>
                                <input type="text" name="multas[0][departamento]" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha *</label>
                                <input type="text" name="multas[0][fecha]" required placeholder="dd/mm/aaaa" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Comparendo *</label>
                                <input type="text" name="multas[0][comparendo]" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Estado de Pago *</label>
                                <select name="multas[0][estado_pago]" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="pendiente">Pendiente</option>
                                    <option value="pagado">Pagado</option>
                                    <option value="vencido">Vencido</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Secretaría *</label>
                                <input type="text" name="multas[0][secretaria]" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Código de Infracción *</label>
                                <input type="text" name="multas[0][codigo_infraccion]" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Infracciones *</label>
                                <input type="text" name="multas[0][infracciones]" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Descripción de la infracción">
                            </div>
                        </div>

                        <!-- Pegar texto del comparendo para autocompletar ESTA multa -->
                        <div class="mt-6 border-t border-dashed border-gray-300 pt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Pegar información completa del comparendo para esta multa
                            </label>
                            <textarea
                                class="texto-comparendo w-full px-4 py-3 border border-blue-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
                                rows="6"
                                placeholder="Pega aquí el texto del comparendo correspondiente a esta multa">
                            </textarea>
                            <div class="mt-3 flex justify-end">
                                <button
                                    type="button"
                                    onclick="procesarTextoComparendo(this)"
                                    class="inline-flex items-center px-5 py-2 rounded-lg text-sm font-semibold shadow-md transition
                                           bg-blue-600 hover:bg-blue-700 text-white border border-blue-700"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582M20 4v5h-.581M4 20h16M4 9a8 8 0 0116 0"></path>
                                    </svg>
                                    Procesar texto de esta multa
                                </button>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">
                                Este botón solo autocompleta los campos de <span class="font-semibold">esta</span> multa. No se guarda nada hasta que presiones “Guardar”.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <button type="button" onclick="agregarMulta()" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg text-sm flex items-center space-x-2 shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Agregar Multa</span>
                    </button>
                </div>
            </div>

            <!-- Sección Forma de Pago (visible desde una multa) -->
            <div id="formaPagoSection" class="mb-8">
                <h4 class="text-xl font-semibold text-gray-700 mb-6 border-b-2 border-blue-500 pb-3">Forma de Pago</h4>

                <div class="bg-blue-50 p-6 rounded-lg mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Forma de Pago *</label>
                            <select name="forma_pago" id="forma_pago" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" onchange="actualizarFormaPago()">
                                <option value="">Seleccione...</option>
                                <option value="pago_unico">Pago Único</option>
                                <option value="acuerdo_pago">Acuerdo de Pago</option>
                            </select>
                        </div>

                        <div id="numeroCuotasDiv" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Número de Cuotas *</label>
                            <input type="number" name="numero_cuotas" id="numero_cuotas" min="1" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" onchange="calcularCuotas()">
                        </div>

                        <div id="porcentajePrimeraDiv" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Porcentaje Primera Cuota (%) *</label>
                            <input type="number" name="porcentaje_primera_cuota" id="porcentaje_primera_cuota" min="1" max="100" step="0.01" value="30" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" onchange="calcularCuotas()">
                        </div>
                    </div>
                </div>

                <!-- Resumen de Cuotas -->
                <div id="resumenCuotas" class="hidden bg-white p-6 rounded-lg border-2 border-blue-200">
                    <h5 class="text-lg font-semibold text-gray-700 mb-4">Resumen de Cuotas</h5>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center p-3 bg-blue-50 rounded">
                            <span class="font-medium">Total a Pagar:</span>
                            <span class="font-bold text-blue-600" id="totalPagar">$0.00</span>
                        </div>
                        <div id="detalleCuotas" class="space-y-2"></div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('dashboard') }}" class="px-8 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-medium shadow-md">
                    Cancelar
                </a>
                <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium shadow-md">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Inicializar forma de pago al cargar la página
    // La sección debe estar SIEMPRE visible desde una multa
    document.addEventListener('DOMContentLoaded', function() {
        const formaPagoSection = document.getElementById('formaPagoSection');
        if (formaPagoSection) {
            // Forzar que esté visible siempre (siempre hay al menos 1 multa en create)
            formaPagoSection.classList.remove('hidden');
            formaPagoSection.style.display = '';
        }

        // Llamar a verificarFormaPago si está disponible (con retraso para asegurar que el DOM esté listo)
        setTimeout(function() {
            if (typeof verificarFormaPago === 'function') {
                verificarFormaPago();
            }
            // Asegurar nuevamente que esté visible después de verificar
            if (formaPagoSection) {
                formaPagoSection.classList.remove('hidden');
                formaPagoSection.style.display = '';
            }
        }, 50);
    });
</script>
@endsection

