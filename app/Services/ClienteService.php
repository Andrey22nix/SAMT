<?php

namespace App\Services;

use App\Models\Cliente;
use App\Repositories\ClienteRepository;
use App\Repositories\CuotaRepository;
use App\Repositories\MultaVehicularRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ClienteService
{
    protected ClienteRepository $clienteRepository;
    protected CuotaRepository $cuotaRepository;
    protected MultaVehicularRepository $multaRepository;

    public function __construct(
        ClienteRepository $clienteRepository,
        CuotaRepository $cuotaRepository,
        MultaVehicularRepository $multaRepository
    ) {
        $this->clienteRepository = $clienteRepository;
        $this->cuotaRepository = $cuotaRepository;
        $this->multaRepository = $multaRepository;
    }

    public function generarNumeroAcuerdo(): string
    {
        do {
            $numero = str_pad((string) rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);
        } while ($this->clienteRepository->getDataByField('numero_acuerdo', $numero));

        return $numero;
    }

    /**
     * Convertir fecha de formato dd/mm/yyyy a Y-m-d, con tolerancia a otros formatos.
     */
    public function convertirFecha(string $fecha): string
    {
        try {
            $carbon = Carbon::createFromFormat('d/m/Y', $fecha);
            return $carbon->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                $carbon = Carbon::parse($fecha);
                return $carbon->format('Y-m-d');
            } catch (\Exception $e2) {
                throw new \InvalidArgumentException("Formato de fecha inválido: {$fecha}. Use el formato dd/mm/yyyy");
            }
        }
    }

    /**
     * Generar cuotas para un cliente (lógica tomada del controlador original).
     */
    public function generarCuotas(Cliente $cliente): void
    {
        // Eliminar cuotas existentes
        $this->cuotaRepository->deleteByCliente($cliente->id);

        // Recargar el cliente para asegurar que tiene todas las multas actualizadas
        $cliente->refresh();
        $cliente->load('multas');

        // Calcular total de multas
        $totalMultas = $cliente->multas->sum('valor');

        if ($totalMultas <= 0 || ! $cliente->numero_cuotas) {
            return;
        }

        // Fecha de resolución (created_at de la primera multa)
        $primeraMulta = $cliente->multas()->orderBy('created_at')->first();
        $fechaResolucionString = $primeraMulta ? $primeraMulta->created_at->format('Y-m-d') : now()->format('Y-m-d');
        // Convertir a Carbon para poder hacer cálculos de meses
        $fechaResolucion = Carbon::parse($fechaResolucionString);

        // Primera cuota
        $porcentajePrimera = $cliente->porcentaje_primera_cuota ?? 30;
        $primeraCuota = ($totalMultas * $porcentajePrimera) / 100;

        $resto = $totalMultas - $primeraCuota;
        $numeroCuotasRestantes = $cliente->numero_cuotas - 1;

        if ($numeroCuotasRestantes <= 0) {
            return;
        }

        $valorBaseCuotaRestante = $resto / $numeroCuotasRestantes;

        $cuotasData = [];

        // Primera cuota (redondeada) - usa la fecha de resolución directamente
        $primeraCuotaRedondeada = round($primeraCuota, 2);
        $cuotasData[] = [
            'cliente_id'       => $cliente->id,
            'numero_cuota'     => 1,
            'valor_cuota'      => $primeraCuotaRedondeada,
            'fecha_pago'       => $fechaResolucion->format('Y-m-d'),
            'fecha_resolucion' => $fechaResolucionString,
            'estado'           => 'pendiente',
        ];

        $sumaAcumulada = $primeraCuotaRedondeada;

        // Cuotas intermedias - cada una suma un mes desde la fecha de resolución
        for ($i = 2; $i < $cliente->numero_cuotas; $i++) {
            // Sumar (i-1) meses desde la fecha de resolución para mantener el mismo día del mes
            $fechaPago = $fechaResolucion->copy()->addMonths($i - 1);
            $valorCuotaRedondeada = round($valorBaseCuotaRestante, 2);

            $cuotasData[] = [
                'cliente_id'       => $cliente->id,
                'numero_cuota'     => $i,
                'valor_cuota'      => $valorCuotaRedondeada,
                'fecha_pago'       => $fechaPago->format('Y-m-d'),
                'fecha_resolucion' => $fechaResolucionString,
                'estado'           => 'pendiente',
            ];

            $sumaAcumulada += $valorCuotaRedondeada;
        }

        // Última cuota ajustada para cuadrar total
        $ultimaCuota = $totalMultas - $sumaAcumulada;
        // Sumar (numero_cuotas - 1) meses desde la fecha de resolución
        $fechaPagoUltima = $fechaResolucion->copy()->addMonths($cliente->numero_cuotas - 1);

        $cuotasData[] = [
            'cliente_id'       => $cliente->id,
            'numero_cuota'     => $cliente->numero_cuotas,
            'valor_cuota'      => round($ultimaCuota, 2),
            'fecha_pago'       => $fechaPagoUltima->format('Y-m-d'),
            'fecha_resolucion' => $fechaResolucionString,
            'estado'           => 'pendiente',
        ];

        $this->cuotaRepository->createMultiple($cuotasData);
    }

    public function actualizarFormaPago(Cliente $cliente, array $data): Cliente
    {
        $updateData = [
            'forma_pago'               => $data['forma_pago'] ?? null,
            'numero_cuotas'           => $data['forma_pago'] === 'acuerdo_pago' ? ($data['numero_cuotas'] ?? null) : null,
            'porcentaje_primera_cuota' => $data['forma_pago'] === 'acuerdo_pago' ? ($data['porcentaje_primera_cuota'] ?? 30.00) : null,
            'descuento_pago_unico'     => $data['forma_pago'] === 'pago_unico' ? ($data['descuento_pago_unico'] ?? null) : null,
        ];

        if ($data['forma_pago'] === 'acuerdo_pago' && empty($cliente->numero_acuerdo)) {
            $updateData['numero_acuerdo'] = $this->generarNumeroAcuerdo();
        }

        $cliente = $this->clienteRepository->update($updateData, $cliente->id);

        if ($data['forma_pago'] === 'acuerdo_pago' && $cliente->numero_cuotas) {
            $this->generarCuotas($cliente);
        }

        return $cliente;
    }

    public function limpiarFormaPago(Cliente $cliente): Cliente
    {
        $this->cuotaRepository->deleteByCliente($cliente->id);

        return $this->clienteRepository->limpiarFormaPago($cliente);
    }

    /**
     * Registrar cliente con multas y gestionar forma de pago.
     *
     * @return array{cliente: Cliente, wasRecentlyCreated: bool, cantidadMultas: int}
     */
    public function registrarClienteConMultas(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // Buscar o crear cliente
            $result = $this->clienteRepository->findOrCreateByDocumento(
                $data['tipo_documento'],
                $data['numero_documento'],
                ['nombre' => $data['nombre']]
            );

            /** @var Cliente $cliente */
            $cliente = $result['cliente'];
            $wasRecentlyCreated = $result['wasRecentlyCreated'];

            // Crear multas (con conversión de fecha)
            $multasData = $this->prepararDatosMultas($data['multas'], $cliente->id);
            $this->multaRepository->createMultiple($multasData);

            // Recargar cliente con multas
            $cliente->refresh();
            $cliente->load('multas');

            // Gestionar forma de pago
            if (count($data['multas']) >= 1 && ! empty($data['forma_pago'])) {
                $this->actualizarFormaPago($cliente, $data);
            } else {
                $this->limpiarFormaPago($cliente);
            }

            return [
                'cliente'          => $cliente,
                'wasRecentlyCreated' => $wasRecentlyCreated,
                'cantidadMultas'   => count($data['multas']),
            ];
        });
    }

    /**
     * Actualizar cliente y multas existentes.
     *
     * @return array{cliente: Cliente, cantidadNuevas: int}
     */
    public function actualizarClienteConMultas(int $multaId, array $data): array
    {
        return DB::transaction(function () use ($multaId, $data) {
            $multaOriginal = $this->multaRepository->find($multaId);

            if (! $multaOriginal) {
                throw new \Exception('Multa no encontrada');
            }

            // Buscar o crear cliente
            $result = $this->clienteRepository->findOrCreateByDocumento(
                $data['tipo_documento'],
                $data['numero_documento'],
                ['nombre' => $data['nombre']]
            );

            /** @var Cliente $cliente */
            $cliente = $result['cliente'];

            // Actualizar primera multa con conversión de fecha
            $primeraMulta = $data['multas'][0];

            $this->multaRepository->update(
                array_merge(
                    $primeraMulta,
                    [
                        'cliente_id' => $cliente->id,
                        'fecha'      => $this->convertirFecha($primeraMulta['fecha']),
                    ]
                ),
                $multaOriginal->id
            );

            // Crear nuevas multas si hay más de una
            $cantidadNuevas = 0;

            if (count($data['multas']) > 1) {
                $nuevasMultas = array_slice($data['multas'], 1);
                $multasData   = $this->prepararDatosMultas($nuevasMultas, $cliente->id);

                $this->multaRepository->createMultiple($multasData);
                $cantidadNuevas = count($nuevasMultas);
            }

            // Recargar cliente con multas
            $cliente->refresh();
            $cliente->load('multas');

            // Gestionar forma de pago
            if (count($data['multas']) >= 1 && ! empty($data['forma_pago'])) {
                $this->actualizarFormaPago($cliente, $data);
            } else {
                $this->limpiarFormaPago($cliente);
            }

            return [
                'cliente'       => $cliente,
                'cantidadNuevas' => $cantidadNuevas,
            ];
        });
    }

    /**
     * Preparar datos de multas agregando cliente_id y convirtiendo fecha.
     */
    protected function prepararDatosMultas(array $multas, int $clienteId): array
    {
        return array_map(function ($multaData) use ($clienteId) {
            return array_merge($multaData, [
                'cliente_id' => $clienteId,
                'fecha'      => $this->convertirFecha($multaData['fecha']),
            ]);
        }, $multas);
    }
}


