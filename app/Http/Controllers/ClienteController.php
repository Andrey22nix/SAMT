<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

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
}

