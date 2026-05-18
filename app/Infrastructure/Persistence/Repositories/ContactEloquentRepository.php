<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Contacts\Repositories\ContactRepositoryInterface;
use App\Models\Contact;

class ContactEloquentRepository implements ContactRepositoryInterface
{
    public function create(array $data): Contact
    {
        return Contact::create($data);
    }
}
