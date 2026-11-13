<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->renomearPapelLegado('Pagamentos', 'Compras');
        $this->renomearPapelLegado('Aprovador Pagamentos', 'Aprovador Compras');

        $permissions = [
            'quadros.listar',
            'quadros.criar',
            'quadros.editar',
            'quadros.deletar',

            'tarefas.listar',
            'tarefas.criar',
            'tarefas.editar',
            'tarefas.deletar',

            'usuario.listar',
            'usuario.criar',
            'usuario.editar',
            'usuario.deletar',

            'solicitacoes.criar',
            'solicitacoes.ver-todas',
            'solicitacoes.aprovar',
            'solicitacoes.concluir',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $roles = [
            'Admin' => Permission::all(),

            'Quadros e Tarefas' => [
                'quadros.listar',
                'quadros.criar',
                'quadros.editar',
                'quadros.deletar',
                'tarefas.listar',
                'tarefas.criar',
                'tarefas.editar',
                'tarefas.deletar',
            ],

            'Compras' => [
                'solicitacoes.criar',
            ],

            'Aprovador Compras' => [
                'solicitacoes.ver-todas',
                'solicitacoes.aprovar',
                'solicitacoes.concluir',
            ],

            "Usuários" => [
                'usuario.listar',
                'usuario.criar',
                'usuario.editar',
                'usuario.deletar',
            ]
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }

        // The purchase flow used to be called "Pagamentos". Its permissions are
        // no longer used, so remove them instead of leaving dead entries behind.
        Permission::query()
            ->whereIn('name', [
                'pagamentos.listar',
                'pagamentos.criar',
                'pagamentos.editar',
                'pagamentos.deletar',
            ])
            ->get()
            ->each(fn (Permission $permission) => $permission->delete());

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function renomearPapelLegado(string $nomeAntigo, string $nomeNovo): void
    {
        $papelAntigo = Role::query()->where('name', $nomeAntigo)->first();
        $papelNovo = Role::query()->where('name', $nomeNovo)->first();

        if ($papelAntigo === null || $papelNovo !== null) {
            return;
        }

        $papelAntigo->update(['name' => $nomeNovo]);
    }
}
