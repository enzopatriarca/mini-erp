<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // outros listeners...
    ];

    public function boot()
    {
        parent::boot();

        Event::listen(MessageSent::class, function (MessageSent $event) {
            $msg = $event->message;
            Log::channel('mail')->debug('⮕ e-mail enviado', [
                'to'      => array_keys($msg->getTo() ?? []),
                'subject' => $msg->getSubject(),
                'body'    => $msg->getBody(),
            ]);
        });
    }
}
