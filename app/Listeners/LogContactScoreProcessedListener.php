<?php

namespace App\Listeners;

use App\Events\ContactScoreProcessed;
use Illuminate\Support\Facades\Log;

class LogContactScoreProcessedListener
{
    public function handle(ContactScoreProcessed $event): void
    {
        // O log::build permite criar em um arquivo customizado na hora
        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/contact.log'),
        ])->info('Score processado', [
            'id' => $event->contact->id,
            'email' => $event->contact->email,
            'novo_score' => $event->contact->score,
            'status' => $event->contact->status,
        ]);
    }
}
