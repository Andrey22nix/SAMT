@extends('layouts.app')

@section('content')
<div>
    <div class="p-8">
        <!-- Botón Volver -->
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al Dashboard
            </a>
        </div>

        <!-- Información del Cliente -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Detalle del Cliente</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Nombre</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $cliente->nombre }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Tipo de Documento</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $cliente->tipo_documento }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Número de Documento</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $cliente->numero_documento }}</p>
                </div>
            </div>
        </div>

        <!-- Resumen Financiero -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-blue-500">
                <h3 class="text-gray-600 text-sm font-medium mb-2">Total Multas</h3>
                <p class="text-3xl font-bold text-blue-600">${{ number_format($totalMultas, 2, ',', '.') }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-green-500">
                <h3 class="text-gray-600 text-sm font-medium mb-2">Multas Pagadas</h3>
                <p class="text-3xl font-bold text-green-600">${{ number_format($multasPagadas, 2, ',', '.') }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-red-500">
                <h3 class="text-gray-600 text-sm font-medium mb-2">Multas Pendientes</h3>
                <p class="text-3xl font-bold text-red-600">${{ number_format($multasPendientes, 2, ',', '.') }}</p>
            </div>
        </div>

        <!-- Forma de Pago y Cuotas -->
        @if($cliente->forma_pago)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Forma de pago</h2>
            
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de pago</label>
                    @if($cliente->forma_pago == 'acuerdo_pago')
                        <span class="inline-block px-4 py-2 bg-blue-50 text-blue-700 rounded-full text-sm font-medium border border-blue-200">Acuerdo de Pago</span>
                    @else
                        <span class="inline-block px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-medium">Pago Único</span>
                    @endif
                </div>
                @if($cliente->forma_pago == 'acuerdo_pago')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Número de Cuotas</label>
                        <p class="text-lg font-bold text-gray-900">{{ $cliente->numero_cuotas }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Porcentaje Primera Cuota</label>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($cliente->porcentaje_primera_cuota, 2, ',', '.') }}%</p>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Tabla de Cuotas Pendientes -->
        @if($cliente->forma_pago == 'acuerdo_pago' && $cliente->numero_acuerdo && $cliente->numero_cuotas)
        @php
            // Calcular valores para mostrar cuotas
            $totalPagar = $cliente->multas->sum('valor');
            $porcentajePrimera = $cliente->porcentaje_primera_cuota ?? 30;
            $primeraCuota = ($totalPagar * $porcentajePrimera) / 100;
            $resto = $totalPagar - $primeraCuota;
            $cuotaRestante = $cliente->numero_cuotas > 1 ? $resto / ($cliente->numero_cuotas - 1) : 0;
            $fechaBase = now()->addMonth()->day(6);
            $fechaGeneracion = $cliente->created_at ?? now();
            
            // Obtener cuotas pendientes de BD
            $cuotasPendientesBD = $cuotas->where('estado', 'pendiente')->keyBy('numero_cuota');
        @endphp
        <div class="bg-white rounded-lg shadow-md mb-6 overflow-hidden">
            <div class="bg-green-50 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h2 class="text-xl font-bold text-gray-800">Cuotas Pendientes (Acuerdo #{{ $cliente->numero_acuerdo }})</h2>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" id="selectAll">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acuerdo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número de Cuota</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Vencimiento</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Generación</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @for($i = 1; $i <= $cliente->numero_cuotas; $i++)
                            @php
                                // Si existe la cuota en BD, usar sus datos. Si no, calcular
                                $cuotaBD = $cuotasPendientesBD->get($i);
                                
                                if ($cuotaBD) {
                                    $valorCuota = $cuotaBD->valor_cuota;
                                    $fechaVencimiento = $cuotaBD->fecha_pago;
                                    $fechaGen = $cuotaBD->created_at ?? $cliente->created_at ?? now();
                                } else {
                                    $valorCuota = ($i == 1) ? $primeraCuota : $cuotaRestante;
                                    $fechaVencimiento = $fechaBase->copy()->addMonths($i - 1);
                                    $fechaGen = $fechaGeneracion;
                                }
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 cuota-checkbox">
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $cliente->numero_acuerdo }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $i }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-bold text-gray-900">$ {{ number_format($valorCuota, 0, ',', '.') }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ $fechaVencimiento->format('m/d/Y') }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">Pendiente</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">FCM</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ $fechaGen->format('m/d/Y') }}</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Lista de Multas -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Historial de Multas</h2>
            
            @if($cliente->multas->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Placa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departamento</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comparendo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado de Pago</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Secretaría</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código Infracción</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Infracciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($cliente->multas as $multa)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $multa->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $multa->placa }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($multa->valor, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $multa->departamento }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if(is_string($multa->fecha))
                                            {{ $multa->fecha }}
                                        @else
                                            {{ $multa->fecha->format('d/m/Y') }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $multa->comparendo }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($multa->estado_pago == 'pagado')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Pagado</span>
                                        @elseif($multa->estado_pago == 'vencido')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Vencido</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $multa->secretaria }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $multa->codigo_infraccion }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $multa->infracciones }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500">Este cliente no tiene multas registradas.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

