<?php

namespace App\Filament\Resources\Sites\Pages;

use App\Filament\Resources\Sites\SiteResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Utilisateur;

class CreateSite extends CreateRecord
{
    protected static string $resource = SiteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /** @var Utilisateur $user */
        $user = filament()->auth()->user();
        $data['created_by'] = $user->id;

        return $data;
    }
}
