<?php

namespace App\Filament\Resources\Dispositifs\Pages;

use App\Filament\Resources\Dispositifs\DispositifResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDispositif extends EditRecord
{
    protected static string $resource = DispositifResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
