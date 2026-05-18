<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pays = [
            ['nom' => 'Niger', 'code' => 'NE', 'statut' => true],
            ['nom' => 'Burkina Faso', 'code' => 'BF', 'statut' => true],
            ['nom' => 'Mali', 'code' => 'ML', 'statut' => true],
        ];

        foreach ($pays as $p) {
            \App\Models\Pays::create($p);
        }
    }
}
