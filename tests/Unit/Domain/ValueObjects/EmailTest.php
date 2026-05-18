<?php

namespace Tests\Unit\Domain\ValueObjects;

use App\Domain\Contacts\ValueObjects\Email;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function test_create_valid_email()
    {
        $email = new Email('fulano@teste.com');
        $this->assertEquals('fulano@teste.com', $email->getValue());
    }

    public function test_cannot_create_invalid_email()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('E-mail informado é invalido');

        new Email('fulano.invalido');
    }
}