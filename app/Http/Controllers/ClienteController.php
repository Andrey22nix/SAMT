<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Cuota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    /**
     * Display the specified cliente with all multas and payment details.
     */
    public function show($id)
    {
        $cliente = Cliente::with(['multas', 'cuotas'])->findOrFail($id);
        
        // Calcular totales
        $totalMultas = $cliente->multas->sum('valor');
        $multasPagadas = $cliente->multas->where('estado_pago', 'pagado')->sum('valor');
        $multasPendientes = $cliente->multas->where('estado_pago', '!=', 'pagado')->sum('valor');
        
        // Obtener todas las cuotas ordenadas por número de cuota
        $cuotas = $cliente->cuotas()->orderBy('numero_cuota')->get();
        
        // Obtener cuotas pendientes para la tabla
        $cuotasPendientes = $cliente->cuotas()->where('estado', 'pendiente')->orderBy('numero_cuota')->get();
        
        return view('clientes.show', compact('cliente', 'totalMultas', 'multasPagadas', 'multasPendientes', 'cuotas', 'cuotasPendientes'));
    }

    /**
     * Show the form for creating a new cliente.
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Show the form for editing the specified cliente.
     */
    public function edit($id)
    {
        $cliente = Cliente::with('multas')->findOrFail($id);
        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Handle public consultation request.
     */
    public function consulta(Request $request)
    {
        $request->validate([
            'tipo_documento' => ['required', 'string', 'in:CC,CE,PA,NIT'],
            'numero_documento' => ['required', 'string', 'max:50'],
        ]);

        $cliente = Cliente::where('tipo_documento', $request->tipo_documento)
            ->where('numero_documento', $request->numero_documento)
            ->with(['multas', 'cuotas'])
            ->first();

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron registros para el documento ingresado.',
                'redirect' => null
            ], 404);
        }

        // Redirigir a la vista pública de resultados
        return response()->json([
            'success' => true,
            'redirect' => route('consulta.resultados', [
                'tipo_documento' => $request->tipo_documento,
                'numero_documento' => $request->numero_documento
            ])
        ]);
    }

    /**
     * Show public consultation results.
     */
    public function resultados(Request $request)
    {
        $request->validate([
            'tipo_documento' => ['required', 'string', 'in:CC,CE,PA,NIT'],
            'numero_documento' => ['required', 'string', 'max:50'],
        ]);

        $cliente = Cliente::where('tipo_documento', $request->tipo_documento)
            ->where('numero_documento', $request->numero_documento)
            ->with(['multas', 'cuotas'])
            ->first();

        if (!$cliente) {
            return redirect()->route('welcome')->with('error', 'No se encontraron registros para el documento ingresado.');
        }

        // Calcular totales
        $totalMultas = $cliente->multas->sum('valor');
        $multasPagadas = $cliente->multas->where('estado_pago', 'pagado')->sum('valor');
        $multasPendientes = $cliente->multas->where('estado_pago', '!=', 'pagado')->sum('valor');
        
        // Obtener todas las cuotas ordenadas por número de cuota
        $cuotas = $cliente->cuotas()->orderBy('numero_cuota')->get();
        
        // Obtener cuotas pendientes para la tabla
        $cuotasPendientes = $cliente->cuotas()->where('estado', 'pendiente')->orderBy('numero_cuota')->get();

        return view('consulta.resultados', compact('cliente', 'totalMultas', 'multasPagadas', 'multasPendientes', 'cuotas', 'cuotasPendientes'));
    }

    /**
     * Show payment confirmation page.
     */
    public function confirmarPago(Request $request)
    {
        // Si es GET, redirigir a resultados
        if ($request->isMethod('get')) {
            // Intentar obtener datos de la sesión o redirigir
            if ($request->has('tipo_documento') && $request->has('numero_documento')) {
                return redirect()->route('consulta.resultados', [
                    'tipo_documento' => $request->tipo_documento,
                    'numero_documento' => $request->numero_documento
                ]);
            }
            return redirect()->route('welcome')->with('error', 'Por favor, complete el proceso de pago desde la página de resultados.');
        }

        // Validar para POST
        $request->validate([
            'cuotas_ids' => ['required', 'array'],
            'cuotas_ids.*' => ['required', 'integer', 'exists:cuotas,id'],
            'nombre_pagador' => ['required', 'string', 'max:255'],
            'email_pagador' => ['required', 'email', 'max:255'],
            'telefono_pagador' => ['required', 'string', 'max:50'],
            'direccion_pagador' => ['required', 'string', 'max:500'],
        ]);

        $cuotasIds = $request->cuotas_ids;
        $cuotas = Cuota::whereIn('id', $cuotasIds)
            ->with('cliente')
            ->get();

        if ($cuotas->isEmpty()) {
            return redirect()->back()->with('error', 'No se encontraron las cuotas seleccionadas.');
        }

        $cliente = $cuotas->first()->cliente;
        $total = $cuotas->sum('valor_cuota');

        // Obtener URL de la imagen del QR desde configuración
        $qrImageUrl = $this->getQRImageUrl();

        return view('consulta.confirmar', [
            'cliente' => $cliente,
            'cuotasSeleccionadas' => $cuotas,
            'cuotasIds' => $cuotasIds,
            'total' => $total,
            'nombrePagador' => $request->nombre_pagador,
            'emailPagador' => $request->email_pagador,
            'telefonoPagador' => $request->telefono_pagador,
            'direccionPagador' => $request->direccion_pagador,
            'qrImageUrl' => $qrImageUrl,
        ]);
    }

    /**
     * Process payment.
     * NOTA: Este método ya no se usa, el pago se redirige a WhatsApp.
     * Se mantiene por compatibilidad pero no cambia el estado de las cuotas.
     */
    public function procesarPago(Request $request)
    {
        // NO procesar el pago aquí, solo retornar éxito
        // El pago se maneja a través de WhatsApp
        return response()->json([
            'success' => true,
            'message' => 'Redirigiendo a WhatsApp para confirmar el pago...'
        ]);
    }

    /**
     * Get QR image URL from configuration.
     */
    private function getQRImageUrl()
    {
        $qrPath = DB::table('configuraciones')
            ->where('clave', 'qr_image_path')
            ->value('valor');

        if ($qrPath && Storage::disk('public')->exists($qrPath)) {
            // Usar asset() directamente para generar la URL correcta
            return asset('storage/' . $qrPath);
        }

        // Si no existe en configuración, buscar en public como fallback
        if (file_exists(public_path('qr-code.png'))) {
            return asset('qr-code.png');
        }

        return null;
    }
}
