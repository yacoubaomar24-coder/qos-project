<?php

namespace App\Filament\Resources\Dispositifs\Pages;

use App\Filament\Resources\Dispositifs\DispositifResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Utilisateur;
use App\Models\Dispositif;
use Filament\Notifications\Notification;

class CreateDispositif extends CreateRecord
{
    protected static string $resource = DispositifResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /** @var Utilisateur $user */
        $user = filament()->auth()->user();

        $data['created_by'] = $user->id;

        // ✅ Générer le token automatiquement
        $data['token'] = Dispositif::genererToken($data['site_id']);
        $data['token_genere_le'] = now();

        return $data;
    }

    // ✅ Afficher le token après création — une seule fois
    protected function afterCreate(): void
    {
        $token = $this->record->token;

        Notification::make()
            ->title('Token du dispositif généré')
            ->body("Copiez ce token maintenant — il ne sera plus affiché en clair :\n\n{$token}")
            ->warning()
            ->persistent() // reste affiché jusqu'à fermeture manuelle
            ->send();
    }
}
