<?php

namespace Database\Seeders;

use App\Models\User;
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
        // User::factory(10)->create();
        /*
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);*/
        $this->call([
            RoleAndPermissionSeeder::class,   // Créer les rôles et permissions
            UtilisateurSeeder::class,         // Créer les utilisateurs de base
            MigrateExistingUserSeeder::class, // Migrer les utilisateurs existants vers les rôles Spatie
        ]);
    }
}
