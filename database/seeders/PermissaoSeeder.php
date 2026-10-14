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

            'pagamentos.listar',
            'pagamentos.criar',
            'pagamentos.editar',
            'pagamentos.deletar',

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

            "Pagamentos" => [
                'pagamentos.listar',
                'pagamentos.criar',
                'pagamentos.editar',
                'pagamentos.deletar',
                'solicitacoes.criar',
            ],

            'Aprovador Pagamentos' => [
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

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
