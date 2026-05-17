<?php

namespace Tests\Unit\Application\Contacts\UseCases;

use Tests\TestCase;
use Mockery;
use App\Models\Contact;
use App\Domain\Contacts\Repositories\ContactRepositoryInterface;
use App\Application\Contacts\UseCases\CreateContactUseCase;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ProcessContactScoreJob;

class CreateContactUseCaseTest extends TestCase
{
    public function test_create_contact_sucessfully()
    {
        Queue::fake();
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
        // 2- Agir
        $useCase = new CreateContactUseCase($repositoryMock);
        $result = $useCase->execute($fakeData);
        //3 verifico
        $this->assertEquals(1, $result->id);
        $this->assertEquals('Fulano T', $result->name);
        //4 garantir que o job foi enviado a fila passando contato.
        Queue::assertPushed(ProcessContactScoreJob::class, function ($job) use ($fakeContact){
            return $job->contact->id === $fakeContact->id;
        });
    }

    //limpa a memory apos o teste
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}