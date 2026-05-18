<?php

namespace Tests\Unit\Domain\ValueObjects;

use App\Domain\Contacts\ValueObjects\Phone;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PhoneTest extends TestCase
{
    public function test_create_valid_phone()
    {
        $phone = new Phone('(11) 99999-9999');
        $this->assertEquals('(11) 99999-9999', $phone->getValue());
    }

    public function test_cannot_create_a_phone_with_invalid_length()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('O telefone informado é invalido.');

        new Phone('123'); // Curto para ser um telefone real
    }
}
