<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MultaVehicular extends Model
{
    use HasFactory;

    protected $table = 'simit_registros';

    protected $fillable = [
        'cliente_id',
        'placa',
        'valor',
        'infracciones',
        'departamento',
        'fecha',
        'comparendo',
        'estado_pago',
        'secretaria',
        'codigo_infraccion',
    ];

    protected $casts = [
        'fecha' => 'date',
        'valor' => 'decimal:2',
    ];

    /**
     * Get the cliente that owns the multa.
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}

