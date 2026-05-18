<?php

use App\Http\Controllers\Api\ContactController;
use Illuminate\Support\Facades\Route;

// Rota de gatilho score
Route::post('contacts/{contact}/process-score', [ContactController::class, 'processScore']);

// Rotas do crud
Route::apiResource('contacts', ContactController::class);
