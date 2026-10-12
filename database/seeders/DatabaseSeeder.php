<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Usuario::query()->updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'nome' => 'Test User',
                'senha' => bcrypt('senha'),
            ],
        );
    }
}
