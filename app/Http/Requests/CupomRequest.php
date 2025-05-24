<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CupomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $cupomId = $this->route('cupom');

        return [
            'codigo' => [
                'required',
                'string',
                Rule::unique('cupons', 'codigo')
                    ->ignore($cupomId),
            ],
            'desconto'        => ['required', 'numeric', 'min:0.01'],
            'minimo_subtotal' => ['required', 'numeric', 'min:0.01'],
            'validade'        => ['required', 'date', 'after_or_equal:today'],
        ];
    }

}

