<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Datos del cliente
            'nombre'           => ['required', 'string', 'max:255'],
            'tipo_documento'   => ['required', 'string', Rule::in(['CC', 'CE', 'NIT', 'TI', 'PASAPORTE'])],
            'numero_documento' => ['required', 'string', 'max:50'],

            // Datos de las multas (array)
            'multas'                    => ['required', 'array', 'min:1'],
            'multas.*.placa'            => ['required', 'string', 'max:10'],
            'multas.*.valor'            => ['required', 'numeric', 'min:0'],
            'multas.*.infracciones'     => ['required', 'string'],
            'multas.*.departamento'     => ['required', 'string', 'max:100'],
            'multas.*.fecha'            => ['required', 'string', 'max:50'],
            'multas.*.comparendo'       => ['required', 'string', 'max:50'],
            'multas.*.estado_pago'      => ['required', 'string', Rule::in(['pagado', 'pendiente', 'vencido'])],
            'multas.*.secretaria'       => ['required', 'string', 'max:255'],
            'multas.*.codigo_infraccion'=> ['required', 'string', 'max:50'],

            // Forma de pago
            'forma_pago'               => ['nullable', 'string', Rule::in(['pago_unico', 'acuerdo_pago'])],
            'numero_cuotas'            => ['nullable', 'integer', 'min:1', 'required_if:forma_pago,acuerdo_pago'],
            'porcentaje_primera_cuota' => ['nullable', 'numeric', 'min:1', 'max:100', 'required_if:forma_pago,acuerdo_pago'],
        ];
    }
}


