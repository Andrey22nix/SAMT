<?php

namespace App\Repositories;

use App\Models\MultaVehicular;

class MultaVehicularRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'placa',
        'comparendo',
        'estado_pago',
        'departamento',
        'created_at',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return MultaVehicular::class;
    }

    public function getMultasWithCliente(int $perPage = 15)
    {
        return $this->with('cliente')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getMultaWithClienteAndMultas($id)
    {
        return $this->with(['cliente.multas'])->find($id);
    }

    public function getEstadisticasPago(): array
    {
        $model = $this->makeModel();

        $totalMultasPagar = $model
            ->where('estado_pago', '!=', 'pagado')
            ->sum('valor');

        $cantidadMultasSinPagar = $model
            ->where('estado_pago', '!=', 'pagado')
            ->count();

        return [
            'total_multas_pagar' => $totalMultasPagar ?? 0,
            'cantidad_sin_pagar' => $cantidadMultasSinPagar ?? 0,
        ];
    }

    public function createMultiple(array $multasData): array
    {
        $multas = [];

        foreach ($multasData as $multaData) {
            $multas[] = $this->create($multaData);
        }

        return $multas;
    }
}


