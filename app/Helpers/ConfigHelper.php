<?php

namespace App\Helpers;

use App\Models\Configuration;
use App\Models\Utilisateur;

class ConfigHelper
{
    // -----------------------------------------------
    // Récupérer la config de l'utilisateur connecté
    // -----------------------------------------------
    public static function get(): ?Configuration
    {
        // ✅ Auth::guard() fonctionne partout — vues, controllers, jobs
        $user  = \Illuminate\Support\Facades\Auth::guard('web')->user();
        
        if (!$user) return null;

        return Configuration::where('created_by', $user->id)->first();
    }

    // -----------------------------------------------
    // Libellés des boutons
    // -----------------------------------------------
    public static function libelleSatisfait(): string
    {
        return static::get()?->libelle_satisfait ?? 'Satisfait';
    }

    public static function libelleMoyen(): string
    {
        return static::get()?->libelle_moyen ?? 'Moyennement satisfait';
    }

    public static function libelleInsatisfait(): string
    {
        return static::get()?->libelle_insatisfait ?? 'Insatisfait';
    }

     public static function couleurSatisfait(): string
    {
        return static::get()?->couleur_satisfait ?? '#22c55e';
    }

    public static function couleurMoyen(): string
    {
        return static::get()?->couleur_moyen ?? '#f59e0b';
    }

    public static function couleurInsatisfait(): string
    {
        return static::get()?->couleur_insatisfait ?? '#ef4444';
    }
}