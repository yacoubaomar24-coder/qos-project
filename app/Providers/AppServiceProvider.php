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
        Gate::before(function (Utilisateur $user, string $ability) {
            return $user->hasRole('Admin') ? true : null;
        });

    }
}
