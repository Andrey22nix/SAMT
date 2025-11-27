<?php

namespace App\Repositories;

use App\Models\Cuota;

class CuotaRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'cliente_id',
        'numero_cuota',
        'estado',
        'fecha_pago',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Cuota::class;
    }

    public function getCuotasByCliente(int $clienteId, ?string $estado = null)
    {
        $query = $this->makeModel()
            ->where('cliente_id', $clienteId)
            ->orderBy('numero_cuota');

        if ($estado) {
            $query->where('estado', $estado);
        }

        return $query->get();
    }

    public function deleteByCliente(int $clienteId): bool
    {
        return (bool) $this->makeModel()->where('cliente_id', $clienteId)->delete();
    }

    public function createMultiple(array $cuotasData): array
    {
        $cuotas = [];

        foreach ($cuotasData as $cuotaData) {
            $cuotas[] = $this->create($cuotaData);
        }

        return $cuotas;
    }
}


