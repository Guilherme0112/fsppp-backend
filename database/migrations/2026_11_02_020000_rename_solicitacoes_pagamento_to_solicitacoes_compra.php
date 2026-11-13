<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('solicitacoes_pagamento') && !Schema::hasTable('solicitacoes_compra')) {
            Schema::rename('solicitacoes_pagamento', 'solicitacoes_compra');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('solicitacoes_compra') && !Schema::hasTable('solicitacoes_pagamento')) {
            Schema::rename('solicitacoes_compra', 'solicitacoes_pagamento');
        }
    }
};
