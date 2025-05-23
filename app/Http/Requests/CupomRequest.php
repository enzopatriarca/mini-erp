<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CupomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $cupomId = $this->route('cupom')?->id;
        $unique   = "unique:cupons,codigo" . ($cupomId ? ",{$cupomId}" : '');

        return [
            'codigo'          => ['required','string',$unique],
            'desconto'        => ['required','numeric','min:0'],
            'minimo_subtotal' => ['required','numeric','min:0'],
            'validade'        => ['required','date','after:today'],
        ];
    }
}
