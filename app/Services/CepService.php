<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CepService
{
    public function buscar(string $cep): array
    {
        return Http::get("https://viacep.com.br/ws/{$cep}/json/")->json();
    }
}
