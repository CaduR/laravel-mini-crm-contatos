<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id(); // coluna ID
            $table->string('name');
            $table->string('email')->unique(); // não pode repetir
            $table->string('phone');
            $table->integer('score')->default(0);
            $table->enum('status', ['pending', 'processing', 'active', 'failed'])->default('pending');
            $table->timestamp('processed_at')->nullable(); // data de quando o score foi calculado
            $table->timestamps(); // cria 'created_at' e 'updated_at'
            $table->softDeletes(); // deletar sem apagar de verdade (deleted_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
