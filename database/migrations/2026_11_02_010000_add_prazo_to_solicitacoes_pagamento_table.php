<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitacoes_pagamento', function (Blueprint $table) {
            $table->date('prazo')->nullable()->after('valor');
        });
    }

    public function down(): void
    {
        Schema::table('solicitacoes_pagamento', function (Blueprint $table) {
            $table->dropColumn('prazo');
        });
    }
};
