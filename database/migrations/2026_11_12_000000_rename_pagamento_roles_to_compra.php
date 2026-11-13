<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $permissoesLegadas = [
        'pagamentos.listar',
        'pagamentos.criar',
        'pagamentos.editar',
        'pagamentos.deletar',
    ];

    public function up(): void
    {
        $this->renomearPapel('Pagamentos', 'Compras');
        $this->renomearPapel('Aprovador Pagamentos', 'Aprovador Compras');

        $ids = DB::table('permissions')
            ->whereIn('name', $this->permissoesLegadas)
            ->pluck('id');

        if ($ids->isEmpty()) {
            return;
        }

        DB::table('role_has_permissions')->whereIn('permission_id', $ids)->delete();
        DB::table('model_has_permissions')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->whereIn('id', $ids)->delete();
    }

    public function down(): void
    {
        $this->renomearPapel('Compras', 'Pagamentos');
        $this->renomearPapel('Aprovador Compras', 'Aprovador Pagamentos');
    }

    private function renomearPapel(string $nomeAntigo, string $nomeNovo): void
    {
        $existeAntigo = DB::table('roles')->where('name', $nomeAntigo)->exists();
        $existeNovo = DB::table('roles')->where('name', $nomeNovo)->exists();

        if (! $existeAntigo || $existeNovo) {
            return;
        }

        DB::table('roles')->where('name', $nomeAntigo)->update(['name' => $nomeNovo]);
    }
};
