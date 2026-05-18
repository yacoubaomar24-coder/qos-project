<?php

namespace App\Filament\Resources\Votes\Pages;

use App\Filament\Resources\Votes\VoteResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Utilisateur;

class CreateVote extends CreateRecord
{
    protected static string $resource = VoteResource::class;

    // Ajouter automatiquement l'ID de l'utilisateur connecté lors de la création d'un vote
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /** @var Utilisateur $user */
        $user = filament()->auth()->user();
        $data['created_by'] = $user->id;

        return $data;
    }
}
