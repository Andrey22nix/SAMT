<?php

namespace App\Repositories;

use App\Models\Cliente;

class ClienteRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'nombre',
        'tipo_documento',
        'numero_documento',
        'numero_acuerdo',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Cliente::class;
    }

    /**
     * Buscar un cliente por tipo y número de documento, o crearlo.
     *
     * @return array{cliente: Cliente, wasRecentlyCreated: bool}
     */
    public function findOrCreateByDocumento(string $tipoDocumento, string $numeroDocumento, array $attributes = []): array
    {
        $cliente = $this->findWhere([
            'tipo_documento'   => $tipoDocumento,
            'numero_documento' => $numeroDocumento,
        ])->first();

        $wasRecentlyCreated = false;

        if (! $cliente) {
            $cliente = $this->create(array_merge([
                'tipo_documento'   => $tipoDocumento,
                'numero_documento' => $numeroDocumento,
            ], $attributes));
            $wasRecentlyCreated = true;
        } else {
            // Actualizar nombre si es diferente
            if (isset($attributes['nombre']) && $cliente->nombre !== $attributes['nombre']) {
                $cliente = $this->update(['nombre' => $attributes['nombre']], $cliente->id);
            }
        }

        return ['cliente' => $cliente, 'wasRecentlyCreated' => $wasRecentlyCreated];
    }

    public function findWithRelations($id, array $relations = ['multas', 'cuotas'])
    {
        return $this->with($relations)->find($id);
    }

    public function limpiarFormaPago(Cliente $cliente): Cliente
    {
        return $this->update([
            'forma_pago'               => null,
            'numero_cuotas'            => null,
            'porcentaje_primera_cuota' => null,
            'numero_acuerdo'           => null,
        ], $cliente->id);
    }
}


