<?php

namespace App\Filament\Resources\Utilisateurs\Pages;

use App\Filament\Resources\Utilisateurs\UtilisateurResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Utilisateur;

class CreateUtilisateur extends CreateRecord
{
    protected static string $resource = UtilisateurResource::class;

    // Cette fonction nous permet d'éviter la rédirection vers la page edit 404:not found
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /** @var Utilisateur $user */
        $user = filament()->auth()->user();
        $data['created_by'] = $user->id;

        return $data;
    }

    protected function afterCreate(): void
    {
        // Assigner le rôle Spatie selon le champ role
        $this->record->syncRoles([$this->record->role]);

        \Spatie\Permission\PermissionRegistrar::class;
        app()[\Spatie\Permission\PermissionRegistrar::class]
            ->forgetCachedPermissions();
    }
}
