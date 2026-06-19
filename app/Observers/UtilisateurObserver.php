<?php

namespace App\Observers;

use App\Models\Utilisateur;
use Illuminate\Support\Facades\Log;

class UtilisateurObserver
{
    public function updated(Utilisateur $utilisateur): void
    {
        // Vérifier si le statut a changé
        if (!$utilisateur->wasChanged('statut')) return;

        Log::info('Observer déclenché pour : ' . $utilisateur->email . 
                  ' statut → ' . ($utilisateur->statut ? 'actif' : 'inactif'));

        if (!$utilisateur->statut) {
            // -----------------------------------------------
            // Désactivation en cascade
            // -----------------------------------------------
            match ($utilisateur->role) {
                'Super admin'    => $this->desactiverCascade($utilisateur),
                'Admin national' => $this->desactiverAdminsNational($utilisateur),
                'Admin régional' => $this->desactiverAdminsRegional($utilisateur),
                default          => null,
            };

        } else {
            // -----------------------------------------------
            // Réactivation en cascade
            // -----------------------------------------------
            match ($utilisateur->role) {
                'Super admin'    => $this->reactiverCascade($utilisateur),
                'Admin national' => $this->reactiverAdminsNational($utilisateur),
                'Admin régional' => $this->reactiverAdminsRegional($utilisateur),
                default          => null,
            };
        }
    }

    // -----------------------------------------------
    // Désactivation
    // -----------------------------------------------
    private function desactiverCascade(Utilisateur $superAdmin): void
    {
        Log::info('Désactivation cascade Super admin : ' . $superAdmin->email);

        Utilisateur::where('created_by', $superAdmin->id)
            ->where('statut', true)
            ->each(function (Utilisateur $u) use ($superAdmin) {
                // update() → déclenche l'observer récursivement pour la cascade
                $u->update(['statut' => false]);
                Log::info('Désactivé : ' . $u->email);
            });
    }

    private function desactiverAdminsNational(Utilisateur $adminNational): void
    {
        Utilisateur::where('created_by', $adminNational->id)
            ->whereIn('role', ['Admin régional', 'Admin de site'])
            ->where('statut', true)
            ->each(function (Utilisateur $u) {
                $u->update(['statut' => false]);
                Log::info('Désactivé : ' . $u->email);
            });
    }

    private function desactiverAdminsRegional(Utilisateur $adminRegional): void
    {
        Utilisateur::where('created_by', $adminRegional->id)
            ->where('role', 'Admin de site')
            ->where('statut', true)
            ->each(function (Utilisateur $u) {
                $u->update(['statut' => false]);
                Log::info('Désactivé : ' . $u->email);
            });
    }

    // -----------------------------------------------
    // Réactivation — updateQuietly() évite la récursion infinie
    // -----------------------------------------------
    private function reactiverCascade(Utilisateur $superAdmin): void
    {
        Log::info('Réactivation cascade Super admin : ' . $superAdmin->email);

        Utilisateur::where('created_by', $superAdmin->id)
            ->where('statut', false)
            ->each(function (Utilisateur $u) {
                if ($u->role === 'Admin national') {
                    // ✅ update() — déclenche l'observer pour réactiver
                    // les admins créés par cet Admin national
                    $u->update(['statut' => true]);
                    Log::info('Réactivé (avec cascade) : ' . $u->email);
                } else {
                    // ✅ updateQuietly() — pas besoin de cascade
                    $u->updateQuietly(['statut' => true]);
                    Log::info('Réactivé : ' . $u->email);
                }
            });
    }

    private function reactiverAdminsNational(Utilisateur $adminNational): void
    {
        Utilisateur::where('created_by', $adminNational->id)
            ->whereIn('role', ['Admin régional', 'Admin de site'])
            ->where('statut', false)
            ->each(function (Utilisateur $u) {
                // ✅ updateQuietly()
                $u->updateQuietly(['statut' => true]);
                Log::info('Réactivé : ' . $u->email);
            });
    }

    private function reactiverAdminsRegional(Utilisateur $adminRegional): void
    {
        Utilisateur::where('created_by', $adminRegional->id)
            ->where('role', 'Admin de site')
            ->where('statut', false)
            ->each(function (Utilisateur $u) {
                // ✅ updateQuietly()
                $u->updateQuietly(['statut' => true]);
                Log::info('Réactivé : ' . $u->email);
            });
    }
}