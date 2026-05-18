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
    {
        // se for inválido estoura um InvalidArgumentException aqui
        $email = new \App\Domain\Contacts\ValueObjects\Email($data['email']);
        $phone = new \App\Domain\Contacts\ValueObjects\Phone($data['phone']);

        //passando o valor já validado 
        $data['email'] = $email->getValue();
        $data['phone'] = $phone->getValue();
        // salva no banco
        return $this->repository->create($data);
    }
}