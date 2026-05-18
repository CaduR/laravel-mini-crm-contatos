<?php

namespace Tests\Feature\Api;

use App\Jobs\ProcessContactScoreJob;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ContactApiTest extends TestCase
{
    use RefreshDatabase; // Limpa o banco de teste apos cada execução

    public function test_list_contacts(): void
    {
        // Cria um contato fake
        Contact::create([
            'name' => 'Fulano Tal',
            'email' => 'fulano@teste.com',
            'phone' => '11999999999',
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/contacts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'email', 'phone', 'score', 'status'],
                ],
                'links',
                'meta',
            ]);
    }

    public function test_create_contact(): void
    {
        $payload = [
            'name' => 'Beltrano Silva',
            'email' => 'beltrano@teste.com',
            'phone' => '(11) 98888-8888', // com formatação
        ];

        $response = $this->postJson('/api/contacts', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Beltrano Silva');

        // Garante que o observer normalizou o telefone antes de salvar
        $this->assertDatabaseHas('contacts', [
            'email' => 'beltrano@teste.com',
            'phone' => '11988888888', // Sem formatação
        ]);
    }

    public function test_cannot_create_contact_with_invalid_email(): void
    {
        $payload = [
            'name' => 'Beltrano da Silva',
            'email' => 'email-invalido',
            'phone' => '11988888888',
        ];

        $response = $this->postJson('/api/contacts', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_show_contact(): void
    {
        $contact = Contact::create([
            'name' => 'Ciclano Souza',
            'email' => 'ciclano@teste.com',
            'phone' => '11977777777',
            'status' => 'active',
        ]);

        $response = $this->getJson("/api/contacts/{$contact->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $contact->id)
            ->assertJsonPath('data.name', 'Ciclano Souza');
    }

    public function test_update_contact(): void
    {
        $contact = Contact::create([
            'name' => 'Ciclano Original',
            'email' => 'original@teste.com',
            'phone' => '11977777777',
            'status' => 'active',
        ]);

        $payload = [
            'name' => 'Ciclano Editado',
            'email' => 'editado@teste.com',
        ];

        $response = $this->putJson("/api/contacts/{$contact->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Ciclano Editado')
            ->assertJsonPath('data.email', 'editado@teste.com');

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'name' => 'Ciclano Editado',
        ]);
    }

    public function test_delete_contact(): void
    {
        $contact = Contact::create([
            'name' => 'Contato Deletavel',
            'email' => 'deletavel@teste.com',
            'phone' => '11977777777',
            'status' => 'active',
        ]);

        $response = $this->deleteJson("/api/contacts/{$contact->id}");

        $response->assertStatus(204);

        // Garante o uso do Soft Delete (continua no banco, mas com deleted_at preenchido)
        $this->assertSoftDeleted('contacts', [
            'id' => $contact->id,
        ]);
    }

    public function test_can_trigger_score_processing(): void
    {
        Queue::fake(); // Impede que o job realmente execute para testar apenas se ele foi para a fila

        $contact = Contact::create([
            'name' => 'Contato Pontuavel',
            'email' => 'pontuavel@teste.com',
            'phone' => '11977777777',
            'status' => 'active',
        ]);

        $response = $this->postJson("/api/contacts/{$contact->id}/process-score");

        $response->assertStatus(202)
            ->assertJson(['message' => 'Processamento de score iniciado em segundo plano.']);

        // Verifica se o Job de calcular score foi de fato enfileirado com o contato correto
        Queue::assertPushed(ProcessContactScoreJob::class, function ($job) use ($contact) {
            return $job->contact->id === $contact->id;
        });
    }
}
