<?php

namespace App\Domain\Contacts\ValueObjects;

use InvalidArgumentException;

class Phone
{
    private string $value;

    public function __construct(string $value)
    {
        // Pega apenas os números para ver se tem tamanho válido
        $numbersOnly = preg_replace('/[^0-9]/', '', $value);

        if (strlen($numbersOnly) < 10 || strlen($numbersOnly) > 11) {
            throw new InvalidArgumentException('O telefone informado é invalido.');
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __tostring(): string
    {
        return $this->value;
    }
}