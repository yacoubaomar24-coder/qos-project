<?php

namespace App\Filament\Resources\Pays\Pages;

use App\Filament\Resources\Pays\PaysResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Utilisateur;

class CreatePays extends CreateRecord
{
    protected static string $resource = PaysResource::class;

    // Cette fonction nous permet d'éviter la rédirection vers la page edit 404:not found
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /** @var Utilisateur $user */
        $user = filament()->auth()->user();
        $data['created_by'] = $user->id;

        return $data;
    }
}
