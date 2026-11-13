<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitacoes_pagamento', function (Blueprint $table) {
            $table->string('prioridade')->default('media')->after('status');
            $table->longText('motivo_rejeicao')->nullable()->after('prioridade');
            $table->boolean('recursos_existem')->nullable()->after('motivo_rejeicao');
            $table->string('status_compra')->nullable()->after('recursos_existem');
            $table->foreignId('diretoria_por')->nullable()->after('status_compra')->constrained('usuarios');
            $table->foreignId('projetos_por')->nullable()->after('diretoria_por')->constrained('usuarios');
            $table->foreignId('compras_por')->nullable()->after('projetos_por')->constrained('usuarios');
        });
    }

    public function down(): void
    {
        Schema::table('solicitacoes_pagamento', function (Blueprint $table) {
            $table->dropForeign(['diretoria_por']);
            $table->dropForeign(['projetos_por']);
            $table->dropForeign(['compras_por']);
            $table->dropColumn([
                'prioridade',
                'motivo_rejeicao',
                'recursos_existem',
                'status_compra',
                'diretoria_por',
                'projetos_por',
                'compras_por',
            ]);
        });
    }
};
