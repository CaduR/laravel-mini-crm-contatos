<?php

namespace App\Domain\Contacts\Repositories;

use App\Models\Contact;

interface ContactRepositoryInterface
{
    public function create(array $data): Contact;
}

