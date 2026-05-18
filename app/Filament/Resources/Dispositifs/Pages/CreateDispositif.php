<?php

namespace App\Filament\Resources\Dispositifs\Pages;

use App\Filament\Resources\Dispositifs\DispositifResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Utilisateur;

class CreateDispositif extends CreateRecord
{
    protected static string $resource = DispositifResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /** @var Utilisateur $user */
        $user = filament()->auth()->user();
        $data['created_by'] = $user->id;

        return $data;
    }
}
