<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Application\Contacts\UseCases\CreateContactUseCase;

class ContactController extends Controller
{
    private CreateContactUseCase $createContactUseCase;

    //injeção de dependecia
    public function __construct(CreateContactUseCase $createContacteUseCase)
    {
        $this->createContactUseCase = $createContacteUseCase;
    }

    public function store(StoreContactRequest $request)
    {
        $contact = $this->createContactUseCase->execute($request->validated());
        //retorna o json formatado pelo resource com status 201 (created)
        return new ContactResource($contact);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        //
    }
}
