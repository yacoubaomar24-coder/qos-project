<?php

namespace App\Filament\Pages;

use App\Models\Utilisateur;
use Filament\Pages\Page;

class Aide extends Page
{
    protected static ?string $navigationLabel = 'Aide';
    protected static ?string $title = 'Aide & Guide d\'utilisation';
    protected static ?int $navigationSort  = 99;
    protected string $view = 'filament.pages.aide';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-question-mark-circle';
    }

    // ✅ Visible par tous les rôles sauf Admin
    public static function shouldRegisterNavigation(): bool
    {
        /** @var Utilisateur|null $user */
        $user = filament()->auth()->user();
        if (!$user instanceof Utilisateur) return false;
        return !$user->hasRole('Admin');
    }
}