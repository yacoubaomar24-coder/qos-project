<?php

namespace App\Filament\Resources\Utilisateurs\Pages;

use App\Filament\Resources\Utilisateurs\UtilisateurResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUtilisateurs extends ListRecords
{
    protected static string $resource = UtilisateurResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('+ Ajouter un utilisateur '),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return []; // ← retourne un tableau vide = pas de breadcrumb donc pas de utilisateurs/list
    }
}
