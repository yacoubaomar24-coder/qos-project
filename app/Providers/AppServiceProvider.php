<?php

namespace App\Providers;

use App\Models\Site;
use App\Models\Region;
use App\Models\Ville;
use App\Models\Dispositif;
use App\Models\Vote;
use App\Models\Utilisateur;
use App\Models\Pays;

use App\Policies\PaysPolicy;
use App\Policies\SitePolicy;
use App\Policies\RegionPolicy;
use App\Policies\VillePolicy;
use App\Policies\DispositifPolicy;
use App\Policies\VotePolicy;
use App\Policies\UtilisateurPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ✅ Cacher Shield de la navigation
        // RoleResource::navigationSort(-999);
        // ✅ Empêcher l'accès à Shield pour tout le monde
        \Illuminate\Support\Facades\Gate::policy(
            \Spatie\Permission\Models\Role::class,
            \App\Policies\RolePolicy::class
        );

        // ça force la langue fr
        \Illuminate\Support\Facades\App::setLocale('fr');

        // Enregistrer les Policies
        Gate::policy(Utilisateur::class,  UtilisateurPolicy::class);
        Gate::policy(Pays::class,  PaysPolicy::class);
        Gate::policy(Site::class,       SitePolicy::class);
        Gate::policy(Region::class,     RegionPolicy::class);
        Gate::policy(Ville::class,      VillePolicy::class);
        Gate::policy(Dispositif::class, DispositifPolicy::class);
        Gate::policy(Vote::class,       VotePolicy::class);

        // Admin passe tous les gates sans vérification
        Gate::before(function (Utilisateur $user, string $ability, $arguments = null) {
            
            // ✅ Bloquer Shield pour TOUT le monde y compris Super admin
            // Vérifier si l'ability concerne le modèle Role de Spatie
            $model = is_array($arguments) ? ($arguments[0] ?? null) : $arguments;
            
            if (
                $model === \Spatie\Permission\Models\Role::class ||
                $model instanceof \Spatie\Permission\Models\Role ||
                str_contains($ability, 'RoleResource')
            ) {
                return false; // ← bloquer avant tout autre check
            }
            
            // Super admin bypass total
            if ($user->hasRole('Super admin')) {
                // ✅ Bloquer explicitement la suppression
                if (str_starts_with($ability, 'delete') || str_starts_with($ability, 'force_delete')) {
                    return false;
                }
                return true;
            }

            // Admin — PAS de bypass total
            // Il passe par les permissions normales
            return null;
        });

    }
}
