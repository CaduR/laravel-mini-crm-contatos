<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Contact;

class ProcessContactScoreJob implements ShouldQueue
{
    use Queueable;

    public Contact $contact; // job guarda o contato

    public function __construct(Contact $contact)
    {
        $this->contact = $contact; //ao criar job ele recebe contato
    }

    public function handle(): void
    {
        //
    }
}
