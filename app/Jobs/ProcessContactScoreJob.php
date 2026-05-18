<?php

namespace App\Jobs;

use App\Domain\Contacts\Services\CalculateContactScoreService;
use App\Events\ContactScoreProcessed;
use App\Models\Contact;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use function Illuminate\Support\now;

class ProcessContactScoreJob implements ShouldQueue
{
    use Queueable;

    public Contact $contact; // job guarda o contato

    public function __construct(Contact $contact)
    {
        $this->contact = $contact; // ao criar job ele recebe contato
    }

    public function handle(CalculateContactScoreService $scoreService): void
    {
        // 1. Muda status para processando
        $this->contact->status = 'processing';
        $this->contact->save();

        sleep(1);

        try {
            // Calcula score usando regra de dominio
            $score = $scoreService->calculate($this->contact->name, $this->contact->email, $this->contact->phone);

            // Finaliza com sucesso
            $this->contact->score = $score;
            $this->contact->status = 'active';
            $this->contact->processed_at = now();
            $this->contact->save();

            ContactScoreProcessed::dispatch($this->contact);

        } catch (\Exception $e) {
            $this->contact->status = 'failed';
            $this->contact->save();
        }
    }
}
