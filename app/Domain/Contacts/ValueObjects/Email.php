<?php

namespace App\Domain\Contacts\ValueObjects;

use InvalidArgumentException;

class Email
{
    private string $value;

    public function __construct(string $value)
    {
        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('O E-mail informado é invalido');
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
