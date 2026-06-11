<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vider le cache des permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Définir toutes les permissions par ressource
        $resources = ['UtilisateurResource', 'PaysResource', 'RegionResource', 'VilleResource',
                     'SiteResource', 'DispositifResource', 'VoteResource'];

        $actions = [
            'view_any',   // voir la liste
            'view',       // voir un enregistrement
            'create',     // créer
            'update',     // modifier
            'delete',     // supprimer
            'restore',    // restaurer (soft delete)
            'force_delete', // suppression définitive
        ];

        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$action}_{$resource}",
                    'guard_name' => 'web'
                ]);
            }
        }

        $this->command->info('✅ Permissions créées.');

        // Rôle Admin (accès total — géré par Shield)
        $admin = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web'
        ]);
        //$admin->syncPermissions(Permission::all());
        // Toutes les permissions sauf les 3 liées à la gestion des votes 
        // (car les admins ne doivent pas pouvoir gérer les votes)
        $permissions = Permission::where(function ($q) {
            $q->whereNotIn('name', [
                'create_VoteResource',
                'update_VoteResource',
                'delete_VoteResource',
            ]);
        })->get();
        $admin->syncPermissions($permissions);

        // Rôle Super admin (accès total sauf gestion des utilisateurs)
        $superAdmin = Role::firstOrCreate([
            'name' => 'Super admin',
            'guard_name' => 'web'
        ]);
        $superAdmin->syncPermissions([

            // Utilisateurs
            'view_UtilisateurResource', 'view_any_UtilisateurResource',
            'create_UtilisateurResource', 'update_UtilisateurResource',
            
            // Pays
            'view_PaysResource', 'view_any_PaysResource',
            'create_PaysResource', 'update_PaysResource',

            // Régions
            'view_RegionResource', 'view_any_RegionResource',
            'create_RegionResource', 'update_RegionResource',

            // Villes
            'view_VilleResource', 'view_any_VilleResource',
            'create_VilleResource', 'update_VilleResource',

            // Sites
            'view_SiteResource', 'view_any_SiteResource',
            'create_SiteResource', 'update_SiteResource',

            // Dispositifs
            'view_DispositifResource', 'view_any_DispositifResource',
            'create_DispositifResource', 'update_DispositifResource',
            
            // Votes
            'view_VoteResource', 'view_any_VoteResource',
        ]);

        // Rôle Admin national (accès total pour un pays donné)
        $superAdmin = Role::firstOrCreate([
            'name' => 'Admin national',
            'guard_name' => 'web'
        ]);
        $superAdmin->syncPermissions([
            
            // Utilisateurs
            'view_UtilisateurResource', 'view_any_UtilisateurResource',
            'create_UtilisateurResource', 'update_UtilisateurResource',
            
            // Pays
            'view_PaysResource', 'view_any_PaysResource',

            // Régions
            'view_RegionResource', 'view_any_RegionResource',
            'create_RegionResource', 'update_RegionResource',

            // Villes
            'view_VilleResource', 'view_any_VilleResource',
            'create_VilleResource', 'update_VilleResource',

            // Sites
            'view_SiteResource', 'view_any_SiteResource',
            'create_SiteResource', 'update_SiteResource',

            // Dispositifs
            'view_DispositifResource', 'view_any_DispositifResource',
            'create_DispositifResource', 'update_DispositifResource',
            
            // Votes
            'view_VoteResource', 'view_any_VoteResource',
        ]);

        // Rôle Admin régional (accès à tous les sites d'une région)
        $adminRegional = Role::firstOrCreate([
            'name' => 'Admin régional',
            'guard_name' => 'web'
        ]);
        $adminRegional->syncPermissions([

            // Utilisateurs
            'view_UtilisateurResource', 'view_any_UtilisateurResource',

            // Régions (lecture seule)
            'view_RegionResource', 'view_any_RegionResource',

            // Villes (lecture seule)
            'view_VilleResource', 'view_any_VilleResource',

            // Sites (accès complet)
            'view_SiteResource', 'view_any_SiteResource',

            // Dispositifs (accès complet)
            'view_DispositifResource', 'view_any_DispositifResource',

            // Votes (accès complet)
            'view_VoteResource', 'view_any_VoteResource',
        ]);

        // Rôle Admin de site (accès à un site spécifique)
        $adminSite = Role::firstOrCreate([
            'name' => 'Admin de site',
            'guard_name' => 'web'
        ]);
        $adminSite->syncPermissions([
            // Sites (lecture + modification uniquement)
            'view_SiteResource', 'view_any_SiteResource',

            // Dispositifs (accès complet)
            'view_DispositifResource', 'view_any_DispositifResource',

            // Votes (accès complet)
            'view_VoteResource', 'view_any_VoteResource',
        ]);
        $this->command->info('✅ Rôles et permissions synchronisés avec succès.');
    }
}
