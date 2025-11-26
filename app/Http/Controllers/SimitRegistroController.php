<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Cuota;
use App\Models\MultaVehicular;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class SimitRegistroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $multas = MultaVehicular::with('cliente')
            ->latest()
            ->paginate(15);

        // Verificar si las columnas nuevas existen
        $hasNewColumns = Schema::hasColumn('simit_registros', 'valor');
        
        if ($hasNewColumns) {
            $totalMultasPagar = MultaVehicular::where('estado_pago', '!=', 'pagado')
                ->sum('valor');
            
            $cantidadMultasSinPagar = MultaVehicular::where('estado_pago', '!=', 'pagado')
                ->count();
        } else {
            $totalMultasPagar = 0;
            $cantidadMultasSinPagar = 0;
        }

        $stats = [
            'total_multas_pagar' => $totalMultasPagar ?? 0,
            'cantidad_sin_pagar' => $cantidadMultasSinPagar ?? 0,
        ];

        return view('dashboard', compact('multas', 'stats'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Datos del cliente
            'nombre' => ['required', 'string', 'max:255'],
            'tipo_documento' => ['required', 'string', Rule::in(['CC', 'CE', 'NIT', 'TI', 'PASAPORTE'])],
            'numero_documento' => ['required', 'string', 'max:50'],
            // Datos de las multas (array)
            'multas' => ['required', 'array', 'min:1'],
            'multas.*.placa' => ['required', 'string', 'max:10'],
            'multas.*.valor' => ['required', 'numeric', 'min:0'],
            'multas.*.infracciones' => ['required', 'string', 'max:255'],
            'multas.*.departamento' => ['required', 'string', 'max:100'],
            'multas.*.fecha' => ['required', 'string', 'max:50'],
            'multas.*.comparendo' => ['required', 'string', 'max:50'],
            'multas.*.estado_pago' => ['required', 'string', Rule::in(['pagado', 'pendiente', 'vencido'])],
            'multas.*.secretaria' => ['required', 'string', 'max:255'],
            'multas.*.codigo_infraccion' => ['required', 'string', 'max:50'],
            // Forma de pago (solo si hay m├ís de una multa)
            'forma_pago' => ['nullable', 'string', Rule::in(['pago_unico', 'acuerdo_pago'])],
            'numero_cuotas' => ['nullable', 'integer', 'min:2', 'required_if:forma_pago,acuerdo_pago'],
            'porcentaje_primera_cuota' => ['nullable', 'numeric', 'min:1', 'max:100', 'required_if:forma_pago,acuerdo_pago'],
        ]);

        DB::beginTransaction();
        try {
            // Buscar o crear cliente
            $cliente = Cliente::firstOrCreate(
                [
                    'tipo_documento' => $validated['tipo_documento'],
                    'numero_documento' => $validated['numero_documento'],
                ],
                [
                    'nombre' => $validated['nombre'],
                ]
            );

            // Si el cliente ya existe, actualizar el nombre si es diferente
            if ($cliente->wasRecentlyCreated === false) {
                if ($cliente->nombre !== $validated['nombre']) {
                    $cliente->update(['nombre' => $validated['nombre']]);
                }
            }

            // Crear todas las multas primero
            foreach ($validated['multas'] as $multaData) {
                // Convertir fecha de dd/mm/yyyy a Y-m-d
                $fecha = $this->convertirFecha($multaData['fecha']);
                
                MultaVehicular::create([
                    'cliente_id' => $cliente->id,
                    'placa' => $multaData['placa'],
                    'valor' => $multaData['valor'],
                    'infracciones' => $multaData['infracciones'],
                    'departamento' => $multaData['departamento'],
                    'fecha' => $fecha,
                    'comparendo' => $multaData['comparendo'],
                    'estado_pago' => $multaData['estado_pago'],
                    'secretaria' => $multaData['secretaria'],
                    'codigo_infraccion' => $multaData['codigo_infraccion'],
                ]);
            }
            
            // Recargar el cliente con las nuevas multas para calcular correctamente
            $cliente->refresh();
            
            // Actualizar forma de pago si hay m├ís de una multa
            if (count($validated['multas']) > 1) {
                if (isset($validated['forma_pago']) && !empty($validated['forma_pago'])) {
                    $updateData = [
                        'forma_pago' => $validated['forma_pago'],
                        'numero_cuotas' => $validated['forma_pago'] == 'acuerdo_pago' ? ($validated['numero_cuotas'] ?? null) : null,
                        'porcentaje_primera_cuota' => $validated['forma_pago'] == 'acuerdo_pago' ? ($validated['porcentaje_primera_cuota'] ?? 30.00) : null,
                    ];
                    
                    // Generar n├║mero de acuerdo si es acuerdo de pago y no existe
                    if ($validated['forma_pago'] == 'acuerdo_pago' && empty($cliente->numero_acuerdo)) {
                        $updateData['numero_acuerdo'] = $this->generarNumeroAcuerdo();
                    }
                    
                    $cliente->update($updateData);
                    
                    // Generar cuotas si es acuerdo de pago (despu├®s de crear las multas)
                    if ($validated['forma_pago'] == 'acuerdo_pago' && $cliente->numero_cuotas) {
                        $this->generarCuotas($cliente);
                    }
                }
            } else {
                // Si solo hay una multa, limpiar forma de pago y eliminar cuotas
                $cliente->update([
                    'forma_pago' => null,
                    'numero_cuotas' => null,
                    'porcentaje_primera_cuota' => null,
                    'numero_acuerdo' => null,
                ]);
                $cliente->cuotas()->delete();
            }

            DB::commit();

            $cantidadMultas = count($validated['multas']);
            $mensaje = $cliente->wasRecentlyCreated 
                ? "Cliente y {$cantidadMultas} multa(s) registradas exitosamente."
                : "Se agregaron {$cantidadMultas} multa(s) al cliente existente.";
            
            return redirect()->route('dashboard')->with('success', $mensaje);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar cliente y multas: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return redirect()->back()->withErrors(['error' => 'Error al registrar: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Get data for editing the specified resource (JSON response for AJAX).
     */
    public function getEditData($id)
    {
        $multa = MultaVehicular::with('cliente.multas')->findOrFail($id);
        
        return response()->json($multa);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            // Datos del cliente
            'nombre' => ['required', 'string', 'max:255'],
            'tipo_documento' => ['required', 'string', Rule::in(['CC', 'CE', 'NIT', 'TI', 'PASAPORTE'])],
            'numero_documento' => ['required', 'string', 'max:50'],
            // Datos de las multas (array)
            'multas' => ['required', 'array', 'min:1'],
            'multas.*.placa' => ['required', 'string', 'max:10'],
            'multas.*.valor' => ['required', 'numeric', 'min:0'],
            'multas.*.infracciones' => ['required', 'string', 'max:255'],
            'multas.*.departamento' => ['required', 'string', 'max:100'],
            'multas.*.fecha' => ['required', 'string', 'max:50'],
            'multas.*.comparendo' => ['required', 'string', 'max:50'],
            'multas.*.estado_pago' => ['required', 'string', Rule::in(['pagado', 'pendiente', 'vencido'])],
            'multas.*.secretaria' => ['required', 'string', 'max:255'],
            'multas.*.codigo_infraccion' => ['required', 'string', 'max:50'],
            // Forma de pago (solo si hay m├ís de una multa)
            'forma_pago' => ['nullable', 'string', Rule::in(['pago_unico', 'acuerdo_pago'])],
            'numero_cuotas' => ['nullable', 'integer', 'min:2', 'required_if:forma_pago,acuerdo_pago'],
            'porcentaje_primera_cuota' => ['nullable', 'numeric', 'min:1', 'max:100', 'required_if:forma_pago,acuerdo_pago'],
        ]);

        DB::beginTransaction();
        try {
            $multaOriginal = MultaVehicular::findOrFail($id);

            // Buscar o crear cliente
            $cliente = Cliente::firstOrCreate(
                [
                    'tipo_documento' => $validated['tipo_documento'],
                    'numero_documento' => $validated['numero_documento'],
                ],
                [
                    'nombre' => $validated['nombre'],
                ]
            );

            // Actualizar cliente si es necesario
            if ($cliente->nombre !== $validated['nombre']) {
                $cliente->update(['nombre' => $validated['nombre']]);
            }

            // Actualizar la primera multa (la que se est├í editando)
            $primeraMulta = $validated['multas'][0];
            // Convertir fecha de dd/mm/yyyy a Y-m-d
            $fecha = $this->convertirFecha($primeraMulta['fecha']);
            
            $multaOriginal->update([
                'cliente_id' => $cliente->id,
                'placa' => $primeraMulta['placa'],
                'valor' => $primeraMulta['valor'],
                'infracciones' => $primeraMulta['infracciones'],
                'departamento' => $primeraMulta['departamento'],
                'fecha' => $fecha,
                'comparendo' => $primeraMulta['comparendo'],
                'estado_pago' => $primeraMulta['estado_pago'],
                'secretaria' => $primeraMulta['secretaria'],
                'codigo_infraccion' => $primeraMulta['codigo_infraccion'],
            ]);

            // Crear nuevas multas si hay m├ís de una
            if (count($validated['multas']) > 1) {
                for ($i = 1; $i < count($validated['multas']); $i++) {
                    $multaData = $validated['multas'][$i];
                    // Convertir fecha de dd/mm/yyyy a Y-m-d
                    $fecha = $this->convertirFecha($multaData['fecha']);
                    
                    MultaVehicular::create([
                        'cliente_id' => $cliente->id,
                        'placa' => $multaData['placa'],
                        'valor' => $multaData['valor'],
                        'infracciones' => $multaData['infracciones'],
                        'departamento' => $multaData['departamento'],
                        'fecha' => $fecha,
                        'comparendo' => $multaData['comparendo'],
                        'estado_pago' => $multaData['estado_pago'],
                        'secretaria' => $multaData['secretaria'],
                        'codigo_infraccion' => $multaData['codigo_infraccion'],
                    ]);
                }
            }
            
            // Recargar el cliente con todas las multas actualizadas
            $cliente->refresh();
            
            // Actualizar forma de pago si hay m├ís de una multa
            if (count($validated['multas']) > 1) {
                if (isset($validated['forma_pago']) && !empty($validated['forma_pago'])) {
                    $updateData = [
                        'forma_pago' => $validated['forma_pago'],
                        'numero_cuotas' => $validated['forma_pago'] == 'acuerdo_pago' ? ($validated['numero_cuotas'] ?? null) : null,
                        'porcentaje_primera_cuota' => $validated['forma_pago'] == 'acuerdo_pago' ? ($validated['porcentaje_primera_cuota'] ?? 30.00) : null,
                    ];
                    
                    // Generar n├║mero de acuerdo si es acuerdo de pago y no existe
                    if ($validated['forma_pago'] == 'acuerdo_pago' && empty($cliente->numero_acuerdo)) {
                        $updateData['numero_acuerdo'] = $this->generarNumeroAcuerdo();
                    }
                    
                    $cliente->update($updateData);
                    
                    // Generar cuotas si es acuerdo de pago (despu├®s de crear todas las multas)
                    if ($validated['forma_pago'] == 'acuerdo_pago' && $cliente->numero_cuotas) {
                        $this->generarCuotas($cliente);
                    }
                }
            } else {
                // Si solo hay una multa, limpiar forma de pago y eliminar cuotas
                $cliente->update([
                    'forma_pago' => null,
                    'numero_cuotas' => null,
                    'porcentaje_primera_cuota' => null,
                    'numero_acuerdo' => null,
                ]);
                $cliente->cuotas()->delete();
            }

            DB::commit();

            $cantidadNuevas = count($validated['multas']) - 1;
            $mensaje = 'Cliente y multa actualizados exitosamente.';
            if ($cantidadNuevas > 0) {
                $mensaje = "Cliente y multa actualizados exitosamente. Se agregaron {$cantidadNuevas} multa(s) adicional(es).";
            }

            return redirect()->route('dashboard')->with('success', $mensaje);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $multa = MultaVehicular::findOrFail($id);
        $multa->delete();

        return redirect()->route('dashboard')->with('success', 'Multa eliminada exitosamente.');
    }

    /**
     * Generar n├║mero de acuerdo aleatorio
     */
    private function generarNumeroAcuerdo(): string
    {
        do {
            $numero = str_pad(rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);
        } while (Cliente::where('numero_acuerdo', $numero)->exists());
        
        return $numero;
    }

    /**
     * Convertir fecha de formato dd/mm/yyyy a Y-m-d
     */
    private function convertirFecha(string $fecha): string
    {
        try {
            // Intentar parsear el formato dd/mm/yyyy
            $carbon = Carbon::createFromFormat('d/m/Y', $fecha);
            return $carbon->format('Y-m-d');
        } catch (\Exception $e) {
            // Si falla, intentar con otros formatos comunes
            try {
                $carbon = Carbon::parse($fecha);
                return $carbon->format('Y-m-d');
            } catch (\Exception $e2) {
                // Si todo falla, lanzar excepci├│n con mensaje claro
                throw new \InvalidArgumentException("Formato de fecha inv├ílido: {$fecha}. Use el formato dd/mm/yyyy");
            }
        }
    }

    /**
     * Generar cuotas para un cliente
     */
    private function generarCuotas(Cliente $cliente): void
    {
        // Eliminar cuotas existentes
        $cliente->cuotas()->delete();
        
        // Recargar el cliente para asegurar que tiene todas las multas actualizadas
        $cliente->refresh();
        $cliente->load('multas');
        
        // Calcular total de multas (suma de TODAS las multas del cliente)
        $totalMultas = $cliente->multas->sum('valor');
        
        if ($totalMultas <= 0 || !$cliente->numero_cuotas) {
            return;
        }
        
        // Obtener la fecha de resoluci├│n (created_at de la primera multa)
        $primeraMulta = $cliente->multas()->orderBy('created_at')->first();
        $fechaResolucion = $primeraMulta ? $primeraMulta->created_at->format('Y-m-d') : now()->format('Y-m-d');
        
        // Calcular primera cuota basada en el porcentaje
        $porcentajePrimera = $cliente->porcentaje_primera_cuota ?? 30;
        $primeraCuota = ($totalMultas * $porcentajePrimera) / 100;
        
        // Calcular el resto que debe distribuirse en las cuotas restantes
        $resto = $totalMultas - $primeraCuota;
        
        // Calcular cu├íntas cuotas restantes hay (todas menos la primera)
        $numeroCuotasRestantes = $cliente->numero_cuotas - 1;
        
        if ($numeroCuotasRestantes <= 0) {
            return;
        }
        
        // Calcular el valor base de cada cuota restante (sin redondear a├║n)
        $valorBaseCuotaRestante = $resto / $numeroCuotasRestantes;
        
        // Fecha base (pr├│ximo mes, d├¡a 6)
        $fechaBase = now()->addMonth()->day(6);
        
        // Crear primera cuota (redondeada a 2 decimales)
        $primeraCuotaRedondeada = round($primeraCuota, 2);
        Cuota::create([
            'cliente_id' => $cliente->id,
            'numero_cuota' => 1,
            'valor_cuota' => $primeraCuotaRedondeada,
            'fecha_pago' => $fechaBase,
            'fecha_resolucion' => $fechaResolucion,
            'estado' => 'pendiente',
        ]);
        
        // Calcular la suma acumulada para ajustar la ├║ltima cuota
        $sumaAcumulada = $primeraCuotaRedondeada;
        
        // Crear resto de cuotas (todas menos la ├║ltima)
        for ($i = 2; $i < $cliente->numero_cuotas; $i++) {
            $fechaPago = $fechaBase->copy()->addMonths($i - 1);
            $valorCuotaRedondeada = round($valorBaseCuotaRestante, 2);
            
            Cuota::create([
                'cliente_id' => $cliente->id,
                'numero_cuota' => $i,
                'valor_cuota' => $valorCuotaRedondeada,
                'fecha_pago' => $fechaPago,
                'fecha_resolucion' => $fechaResolucion,
                'estado' => 'pendiente',
            ]);
            
            $sumaAcumulada += $valorCuotaRedondeada;
        }
        
        // Crear la ├║ltima cuota ajustando para que la suma total sea exacta
        $ultimaCuota = $totalMultas - $sumaAcumulada;
        $fechaPagoUltima = $fechaBase->copy()->addMonths($cliente->numero_cuotas - 1);
        
        Cuota::create([
            'cliente_id' => $cliente->id,
            'numero_cuota' => $cliente->numero_cuotas,
            'valor_cuota' => round($ultimaCuota, 2),
            'fecha_pago' => $fechaPagoUltima,
            'fecha_resolucion' => $fechaResolucion,
            'estado' => 'pendiente',
        ]);
    }
}

