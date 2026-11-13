<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasTable('quadro_usuario')) {
            Schema::create('quadro_usuario', function (Blueprint $table) {
                $table->id();
                $table->foreignId('quadro_id')->constrained('quadros')->cascadeOnDelete();
                $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('tarefa_usuario')) {
            Schema::create('tarefa_usuario', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tarefa_id')->constrained('tarefas')->cascadeOnDelete();
                $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('tarefa_usuario');
        Schema::dropIfExists('quadro_usuario');
    }
};
