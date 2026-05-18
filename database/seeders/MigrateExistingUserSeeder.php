<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Utilisateur;
use Spatie\Permission\PermissionRegistrar;

class MigrateExistingUserSeeder extends Seeder
{
    /**
     * Correspondance entre les anciennes valeurs du champ 'role'
     * et les nouveaux noms de rôles Spatie.
     */
    /*  
        # Note : Avant d'exécuter ce seeder, assurez-vous que les rôles Spatie sont créés 
                 et correspondent aux valeurs utilisées dans le champ 'role' des utilisateurs 
                 existants. Vous pouvez vérifier les rôles disponibles avec :
            \Spatie\Permission\Models\Role::all()->pluck('name'); 
            
        # 1. S'assurer que les rôles Spatie existent
        php artisan db:seed --class=RoleAndPermissionSeeder

        # 2. Migrer les utilisateurs existants
        php artisan db:seed --class=MigrateExistingUsersSeeder

        # 3. Vider le cache
        php artisan permission:cache-reset
    */
    private array $roleMapping = [
        'Admin'            => 'Admin',
        'Super admin'      => 'Super admin',
        'Admin régional'   => 'Admin régional',
        'Admin de site'    => 'Admin de site',
    ];

    public function run(): void
    {
        // Vider le cache des permissions pour s'assurer que les rôles sont correctement appliqués
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Compteurs pour le rapport final
        $total   = 0;
        $skipped = 0;

        // Parcourir tous les utilisateurs qui ont une valeur dans le champ 'role'
        Utilisateur::whereNotNull('role')->each(function (Utilisateur $user) use (&$total, &$skipped) {

            // Trouver le nouveau rôle correspondant à l'ancienne valeur
            $newRole = $this->roleMapping[$user->role] ?? null;

            if (!$newRole) {
                $this->command->warn(
                    "⚠️  Rôle inconnu « {$user->role} » pour l'utilisateur #{$user->id} ({$user->email}) — ignoré"
                );
                $skipped++;
                return;
            }

            // Vérifie si le rôle Spatie est déjà assigné
            if ($user->hasRole($newRole)) {
                $this->command->line(
                    "⏭️  Déjà migré : {$user->email} → {$newRole}"
                );
                return;
            }

            $user->syncRoles([$newRole]);

            $this->command->info(
                "✅ {$user->email} | « {$user->role} » → {$newRole}"
            );
            $total++;
        });

        $this->command->info("✅ {$total} utilisateurs migrés, {$skipped} ignorés.");
    }
}
