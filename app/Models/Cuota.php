<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cuota extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'numero_cuota',
        'valor_cuota',
        'fecha_pago',
        'estado',
        'fecha_resolucion',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'fecha_resolucion' => 'date',
        'valor_cuota' => 'decimal:2',
    ];

    /**
     * Get the cliente that owns the cuota.
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}

