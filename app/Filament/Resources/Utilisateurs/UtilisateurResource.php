<?php

namespace App\Filament\Resources\Utilisateurs;

use App\Filament\Resources\Utilisateurs\Pages\CreateUtilisateur;
use App\Filament\Resources\Utilisateurs\Pages\EditUtilisateur;
use App\Filament\Resources\Utilisateurs\Pages\ListUtilisateurs;
use App\Models\Utilisateur;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Traits\HasResourcePermissions;

class UtilisateurResource extends Resource
{
    //use HasResourcePermissions;

    protected static ?string $model = Utilisateur::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $recordTitleAttribute = 'Utilisateur Admin';

    public static function form(Schema $schema): Schema
    {
        //return UtilisateurForm::configure($schema);
        return $schema->schema([
            Select::make('role')

                // Pour restreindre les rôlesen fonction de l'utilisateur connecté
                ->options( function () {
                    /** @var Utilisateur $user */
                    $user = filament()->auth()->user();

                    // Admin voit tous les rôles
                    if ($user->hasRole('Admin')) {
                        return [
                            'Admin' => 'Admin',
                            'Super admin' => 'Super admin',
                            'Admin régional' => 'Admin régional',
                            'Admin de site' => 'Admin de site',
                        ];
                    }

                    // Super admin ne peut créer que des admins régionaux et de site
                    if ($user->hasRole('Super admin')) {
                        return [
                            'Admin régional' => 'Admin régional',
                            'Admin de site'  => 'Admin de site',
                        ];
                    }
                    return [];
                })->native(false)->required(),
            Select::make('region_id')->relationship('region', 'nom')->nullable(),
            Select::make('site_id')->relationship('site', 'nom')->nullable(),
            TextInput::make('nom')->label('Nom')->required(),
            TextInput::make('prenom')->required(),
            TextInput::make('numero')->required(),
            TextInput::make('email')->required()->email(),
            TextInput::make('password')->required()->password(),
            Toggle::make('statut')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        //return UtilisateursTable::configure($table);
        return $table->columns([
            TextColumn::make('nom')->label('Nom')->searchable(),
            TextColumn::make('prenom')->label('Prénom')->searchable(),
            TextColumn::make('numero')->label('Numéro'),
            TextColumn::make('email')->label('Email'),
            TextColumn::make('role')->label('Rôle')->searchable(),
            TextColumn::make('createdBy.nom')->label('Créé par')->searchable(),
            TextColumn::make('password')->label('Mot de passe'),
            IconColumn::make('statut')->label('Statut')->boolean()->default(true),
        ])->filters([
            //
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ])
        ->toolbarActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUtilisateurs::route('/'),
            'create' => CreateUtilisateur::route('/create'),
            'edit' => EditUtilisateur::route('/{record}/edit'),
        ];
    }
    
    //  
    public static function getEloquentQuery(): Builder
    {

        /** @var Utilisateur|null $user */
        $user  = filament()->auth()->user();
        $query = parent::getEloquentQuery();

        if (!$user instanceof Utilisateur) {
            return $query->whereRaw('1 = 0');
        }

        // Admin voit tout
        if ($user->hasRole('Admin')) {
            return $query;
        }

        // Super admin voit uniquement les utilisateurs qu'il a créés
        if ($user->hasRole('Super admin')) {
            return $query
                ->where('created_by', $user->id)
                ->whereIn('role', ['Admin régional', 'Admin de site']);
        }

        // Admin régional voit les utilisateurs de sa région
        if ($user->hasRole('Admin régional')) {
            return $query->where('region_id', $user->region_id);
        }

        // Admin de site voit les utilisateurs de son site
        if ($user->hasRole('Admin de site')) {
            return $query->where('site_id', $user->site_id);
        }

        return $query->whereRaw('1 = 0');
    }

    public static function canViewAny(): bool
    {
        $user = filament()->auth()->user() ?? \Illuminate\Support\Facades\Auth::guard('web')->user();
        if (!$user instanceof Utilisateur) return false;
        return $user->can('view_any_UtilisateurResource');
    }

    public static function canCreate(): bool
    {
        $user = filament()->auth()->user() ?? \Illuminate\Support\Facades\Auth::guard('web')->user();
        if (!$user instanceof Utilisateur) return false;
        return $user->can('create_UtilisateurResource');
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = filament()->auth()->user() ?? \Illuminate\Support\Facades\Auth::guard('web')->user();
        if (!$user instanceof Utilisateur) return false;
        return $user->can('update_UtilisateurResource');
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = filament()->auth()->user() ?? \Illuminate\Support\Facades\Auth::guard('web')->user();
        if (!$user instanceof Utilisateur) return false;
        return $user->can('delete_UtilisateurResource');
    }
}
