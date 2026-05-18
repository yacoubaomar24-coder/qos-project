<?php

namespace App\Filament\Resources\Regions\Pages;

use App\Filament\Resources\Regions\RegionResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Utilisateur;

class CreateRegion extends CreateRecord
{
    protected static string $resource = RegionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /** @var Utilisateur $user */
        $user = filament()->auth()->user();
        $data['created_by'] = $user->id;

        return $data;
    }
}
