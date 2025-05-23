<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProdutoRequest extends FormRequest
{
    /**
     * Nome do error-bag pra este request.
     * Assim não “pega” erros de outros forms (ex: cep/email).
     */
    protected $errorBag = 'produto';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome'        => 'required|string|max:255',
            'preco'       => 'required|numeric|min:0',
            'variacoes'   => 'required|array',
            'variacoes.*' => 'nullable|string|max:255',
            'estoque'     => 'required|array',
            'estoque.*'   => 'required|integer|min:0',
        ];
    }
}
