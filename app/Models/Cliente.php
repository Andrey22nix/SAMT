<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'tipo_documento',
        'numero_documento',
        'numero_acuerdo',
        'forma_pago',
        'numero_cuotas',
        'porcentaje_primera_cuota',
    ];

    protected $casts = [
        'porcentaje_primera_cuota' => 'decimal:2',
    ];

    /**
     * Get the multas for the cliente.
     */
    public function multas(): HasMany
    {
        return $this->hasMany(MultaVehicular::class);
    }

    /**
     * Get the cuotas for the cliente.
     */
    public function cuotas(): HasMany
    {
        return $this->hasMany(Cuota::class);
    }
}

