<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CepService
{
    public function buscar(string $cep): array
    {
        $response = Http::withOptions([
            'verify' => false,
        ])->get("https://viacep.com.br/ws/{$cep}/json/");

        $response->throw();

        $data = $response->json();

        if (isset($data['erro']) && $data['erro'] === true) {
            throw new \Exception("CEP não encontrado: {$cep}");
        }

        return $data;
    }
}
