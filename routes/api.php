<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ContactController;

// Rota de gatilho score
Route::post('contacts/{contact}/process-score', [ContactController::class, 'processScore']);

// Rotas do crud
Route::apiResource('contacts', ContactController::class);
