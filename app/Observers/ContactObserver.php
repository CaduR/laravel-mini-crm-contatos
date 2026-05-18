<?php

namespace App\Observers;

use App\Models\Contact;

class ContactObserver
{
    public function saving(Contact $contact): void
    {
        if ($contact->phone) {
            // Remove tudo que não for número
            $contact->phone = preg_replace('/[^0-9]/', '', $contact->phone);
        }
    }
}
