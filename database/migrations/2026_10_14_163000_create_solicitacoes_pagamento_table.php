<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitacoes_pagamento', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->integer('valor');
            $table->longText('descricao')->nullable();
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->string('status')->default('pendente');
            $table->foreignId('aprovado_cancelado_por')->nullable()->constrained('usuarios');
            $table->foreignId('concluido_por')->nullable()->constrained('usuarios');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitacoes_pagamento');
    }
};
