<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;

class UtilisateurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un utilisateur Super Admin
        
        Utilisateur::create([
            'nom' => 'Saratech',
            'prenom' => 'ACADEMY',
            'numero' => '82208002',
            'email' => 'saratechniger@gmail.com',
            'role' => 'Admin',
            'password' => bcrypt('password'),
        ]);
    }
}
