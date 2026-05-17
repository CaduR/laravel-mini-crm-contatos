<?php

namespace Tests\Unit\Application\Contacts\UseCases;

use Tests\TestCase;
use Mockery;
use App\Models\Contact;
use App\Domain\Contacts\Repositories\ContactRepositoryInterface;
use App\Application\Contacts\UseCases\CreateContactUseCase;

class CreateContactUseCaseTest extends TestCase
{
    public function test_create_contact_sucessfully()
    {
        $fakeData = [
            'name' => 'Fulano T',
            'email' => 'fulano@empresa.com.br',
            'phone' => '11999999999',
        ];

        $fakeContact = new Contact($fakeData);
        $fakeContact->id = 1;

        $repositoryMock = Mockery::mock(ContactRepositoryInterface::class);
        $repositoryMock->shouldReceive('create')
            ->once() // garante chamar a func create 1 vez
            ->with($fakeData)
            ->andReturn($fakeContact);

        // Agir
        $useCase = new CreateContactUseCase($repositoryMock);
        $result = $useCase->execute($fakeData);

        // Verifico
        $this->assertEquals(1, $result->id);
        $this->assertEquals('Fulano T', $result->name);
    }

    // limpa a memory apos o teste
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
