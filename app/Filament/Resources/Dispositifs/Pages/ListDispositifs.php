<?php

namespace App\Filament\Resources\Dispositifs\Pages;

use App\Filament\Resources\Dispositifs\DispositifResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDispositifs extends ListRecords
{
    protected static string $resource = DispositifResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return []; // ← retourne un tableau vide = pas de breadcrumb
    }
}
