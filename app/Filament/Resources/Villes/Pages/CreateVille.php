<?php

namespace App\Filament\Resources\Villes\Pages;

use App\Filament\Resources\Villes\VilleResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Utilisateur;

class CreateVille extends CreateRecord
{
    protected static string $resource = VilleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /** @var Utilisateur $user */
        $user = filament()->auth()->user();
        $data['created_by'] = $user->id;

        return $data;
    }
}
