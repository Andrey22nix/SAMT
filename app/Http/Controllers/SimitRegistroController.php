<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Repositories\MultaVehicularRepository;
use App\Services\ClienteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class SimitRegistroController extends Controller
{
    protected MultaVehicularRepository $multaRepository;
    protected ClienteService $clienteService;

    public function __construct(
        MultaVehicularRepository $multaRepository,
        ClienteService $clienteService
    ) {
        $this->multaRepository = $multaRepository;
        $this->clienteService = $clienteService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $multas = $this->multaRepository->getMultasWithCliente();
        $stats = $this->multaRepository->getEstadisticasPago();

        return view('dashboard', compact('multas', 'stats'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClienteRequest $request): RedirectResponse
    {
        try {
            $result = $this->clienteService->registrarClienteConMultas($request->validated());

            $mensaje = $result['wasRecentlyCreated']
                ? "Cliente y {$result['cantidadMultas']} multa(s) registradas exitosamente."
                : "Se agregaron {$result['cantidadMultas']} multa(s) al cliente existente.";

            return redirect()->route('dashboard')->with('success', $mensaje);
        } catch (\Exception $e) {
            Log::error('Error al registrar cliente y multas: ' . $e->getMessage(), [
                'trace'   => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Error al registrar: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Get data for editing the specified resource (JSON response for AJAX).
     */
    public function getEditData($id): JsonResponse
    {
        $multa = $this->multaRepository->getMultaWithClienteAndMultas($id);

        if (! $multa) {
            return response()->json(['error' => 'Multa no encontrada'], 404);
        }

        return response()->json($multa);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClienteRequest $request, $id): RedirectResponse
    {
        try {
            $result = $this->clienteService->actualizarClienteConMultas($id, $request->validated());

            $mensaje = 'Cliente y multa actualizados exitosamente.';
            if ($result['cantidadNuevas'] > 0) {
                $mensaje = "Cliente y multa actualizados exitosamente. Se agregaron {$result['cantidadNuevas']} multa(s) adicional(es).";
            }

            return redirect()->route('dashboard')->with('success', $mensaje);
        } catch (\Exception $e) {
            Log::error('Error al actualizar cliente y multas: ' . $e->getMessage(), [
                'trace'   => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            $errorMessage = $e->getMessage() === 'Multa no encontrada'
                ? 'Multa no encontrada'
                : 'Error al actualizar: ' . $e->getMessage();

            return redirect()->back()->withErrors(['error' => $errorMessage])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        try {
            $this->multaRepository->delete($id);

            return redirect()->route('dashboard')->with('success', 'Multa eliminada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar multa: ' . $e->getMessage());

            return redirect()->route('dashboard')->withErrors(['error' => 'Error al eliminar la multa.']);
        }
    }
}

