<?php

namespace App\Http\Controllers\Api;

use App\Application\Contacts\UseCases\CreateContactUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Http\Resources\ContactResource;
use App\Jobs\ProcessContactScoreJob;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    private CreateContactUseCase $createContactUseCase;

    // Injeção de dependência do Use Case de Criação
    public function __construct(CreateContactUseCase $createContactUseCase)
    {
        $this->createContactUseCase = $createContactUseCase;
    }

    /**
     * Lista todos os contatos com paginação.
     */
    public function index()
    {
        // O Resource::collection entende o paginate do Laravel
        return ContactResource::collection(Contact::paginate(10));
    }

    // Cria um novo contato usando a arquitetura DDD.
    public function store(StoreContactRequest $request)
    {
        $contact = $this->createContactUseCase->execute($request->validated());

        return new ContactResource($contact);
    }

    // Retorna os detalhes de um único contato.
    public function show(Contact $contact)
    {
        return new ContactResource($contact);
    }

    // Atualiza um contato existente.

    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:contacts,email,'.$contact->id,
            'phone' => 'sometimes|string|max:20',
        ]);

        $contact->update($validated);

        return new ContactResource($contact);
    }

    // Deleta (Soft Delete) um contato.
    public function destroy(Contact $contact)
    {
        $contact->delete();

        // 204 No Content indica que deletou com sucesso e não tem nada para retornar
        return response()->json(null, 204);
    }

    // Gatilho para iniciar o cálculo do Score
    public function processScore(Contact $contact)
    {
        ProcessContactScoreJob::dispatch($contact);

        return response()->json(['message' => 'Processamento de score iniciado em segundo plano.'], 202);
    }
}
