<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            Log::channel('erp')->error($e->getMessage(), [
                'exception' => $e,
                'user_id'   => Auth::id(),
            ]);
        });
    }
}
