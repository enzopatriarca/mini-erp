<?php

namespace Tests;

trait CreatesApplication
{
    /**
     * Cria a aplicação Laravel para os testes.
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        return $app;
    }
}
