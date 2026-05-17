<?php

namespace App\Application\Contacts\UseCases;

use App\Domain\Contacts\Repositories\ContactRepositoryInterface;
use App\Models\Contact;
use App\Jobs\ProcessContactScoreJob;

class CreateContactUseCase
{
    // Recebemos a Interface (Contrato), mas o Laravel vai nos entregar o Eloquent (Implementação)
    private ContactRepositoryInterface $repository;

    public function __construct(ContactRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(array $data): Contact
    {   //cria o contato no banco
        $contact = $this->repository->create($data);
        //enfileira o job para rodar em segundo plano
        ProcessContactScoreJob::dispatch($contact);

        return $contact;
    }
}