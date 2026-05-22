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
//use App\Filament\Resources\Traits\HasResourcePermissions;
//use Illuminate\Support\Facades\Auth;

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
                // Pour restreindre les rôles en fonction de l'utilisateur connecté
                ->options( function () {
                    /** @var Utilisateur $user */
                    //$user = filament()->auth()->user();
                    $user = \Illuminate\Support\Facades\Auth::guard('web')->user();

                    // Admin voit tous les rôles
                    if ($user->hasRole('Admin')) {
                        return [
                            'Admin' => 'Admin',
                            'Super admin' => 'Super admin',
                            'Admin national' => 'Admin national',
                            'Admin régional' => 'Admin régional',
                            'Admin de site' => 'Admin de site',
                        ];
                    }

                    // Super admin ne peut créer que des admins nationaux, régionaux et de site
                    if ($user->hasRole('Super admin')) {
                        return [
                            'Admin national' => 'Admin national',
                            'Admin régional' => 'Admin régional',
                            'Admin de site'  => 'Admin de site',
                        ];
                    }
                    
                    // Admin national ne peut créer que des admins régionaux et de site
                    if ($user->hasRole('Admin national')) {
                        return [
                            'Admin régional' => 'Admin régional',
                            'Admin de site'  => 'Admin de site',
                        ];
                    }

                    // Autres, on ne retourne rien
                    return [];
                })->native(false)->required(),
            //Select::make('pays_id')->relationship('pays', 'nom')->nullable(),  
            // Pays — filtré selon le rôle connecté
            Select::make('pays_id')
                ->label('Pays')
                ->options(function () {
                    /** @var Utilisateur $user */
                    //$user = filament()->auth()->user();
                    $user  = \Illuminate\Support\Facades\Auth::guard('web')->user();

                    $query = \App\Models\Pays::query();

                    if ($user->hasRole('Admin')) {
                        // Admin voit tous les pays sans restriction
                        return $query->pluck('nom', 'id');
                    }

                    if ($user->hasRole('Super admin')) {
                        // Super admin voit tous les pays qu'il a créés
                        return $query->where('created_by', $user->id)->pluck('nom', 'id');
                    }

                    if ($user->hasRole('Admin national')) {
                        // Admin national voit uniquement son propre pays
                        return $query->where('id', $user->pays_id)->pluck('nom', 'id');
                    }

                    if ($user->hasRole('Admin régional')) {
                        // Admin régional voit uniquement le pays de sa région
                        $paysId = \App\Models\Region::find($user->region_id)?->pays_id;
                        return $query->where('id', $paysId)->pluck('nom', 'id');
                    }

                    // Admin de site — pas de pays
                    return [];
                })
                ->nullable()
                ->requiredIf('role', ['Admin national', 'Admin régional', 'Admin de site'])
                ->helperText('Obligatoire pour les rôles Admin national, régional et de site'),
            //Select::make('region_id')->relationship('region', 'nom')->nullable(),
            // Région — filtrée selon le pays et le rôle
            Select::make('region_id')
                ->label('Région')
                ->options(function () {
                    /** @var Utilisateur $user */
                    //$user = filament()->auth()->user();
                    $user  = \Illuminate\Support\Facades\Auth::guard('web')->user();

                    $query = \App\Models\Region::query();

                    if ($user->hasRole('Admin')) {
                        // Admin voit toutes les régions
                        return $query->pluck('nom', 'id');
                    }

                    if ($user->hasRole('Super admin')) {
                        // Super admin voit uniquement ses régions créées
                        return $query->where('created_by', $user->id)->pluck('nom', 'id');
                    }

                    if ($user->hasRole('Admin national')) {
                        // Admin national voit toutes les régions de son pays
                        return $query->where('pays_id', $user->pays_id)->pluck('nom', 'id');
                    }

                    if ($user->hasRole('Admin régional')) {
                        // Admin régional voit uniquement sa propre région
                        return $query->where('id', $user->region_id)->pluck('nom', 'id');
                    }

                    // Admin de site — pas de région
                    return [];
                })
                ->nullable()
                ->requiredIf('role', ['Admin régional', 'Admin de site'])
                ->helperText('Obligatoire pour les rôles Admin régional et de site'),
            //Select::make('site_id')->relationship('site', 'nom')->nullable(),
            // Site — filtré selon la région et le rôle
            Select::make('site_id')
                ->label('Site')
                ->options(function () {
                    /** @var Utilisateur $user */
                    //$user = filament()->auth()->user();
                    $user  = \Illuminate\Support\Facades\Auth::guard('web')->user();

                    $query = \App\Models\Site::query();

                    if ($user->hasRole('Admin')) {
                        // Admin voit tous les sites
                        return $query->pluck('nom', 'id');
                    }

                    if ($user->hasRole('Super admin')) {
                        // Super admin voit les sites créés par lui et ses admins
                        $userIds = \App\Models\Utilisateur::where('created_by', $user->id)
                            ->pluck('id');

                        // Ajouter le super admin lui-même
                        $userIds->push($user->id);

                        // ça filtre uniquement les sites qui n'ont pas des admins
                        $usedSiteIds = Utilisateur::role('Admin de site')
                            ->whereNotNull('site_id')
                            ->pluck('site_id');

                        return $query
                            ->whereIn('created_by', $userIds)
                            ->whereNotIn('id', $usedSiteIds)  // Pour les sites sans admins
                            ->pluck('nom', 'id');
                    }

                    if ($user->hasRole('Admin national')) {
                        // Admin national voit les sites de son pays
                        // Chemin : pays → régions → villes → sites
                        $regionIds = \App\Models\Region::where('pays_id', $user->pays_id)
                            ->pluck('id');
                        $villeIds  = \App\Models\Ville::whereIn('region_id', $regionIds)
                            ->pluck('id');
                        return $query->whereIn('ville_id', $villeIds)->pluck('nom', 'id');
                    }

                    if ($user->hasRole('Admin régional')) {
                        // Admin régional voit les sites de sa région
                        // Chemin : région → villes → sites
                        $villeIds = \App\Models\Ville::where('region_id', $user->region_id)
                            ->pluck('id');
                        return $query->whereIn('ville_id', $villeIds)->pluck('nom', 'id');
                    }

                    if ($user->hasRole('Admin de site')) {
                        // Admin de site voit uniquement son propre site
                        return $query->where('id', $user->site_id)->pluck('nom', 'id');
                    }

                    return [];

                })
                ->nullable()
                ->requiredIf('role', ['Admin de site'])
                ->helperText('Obligatoire pour le rôle Admin de site'),
            TextInput::make('nom')->required(),
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
    
    // ça permet au Super admin de voir tout ce que admin national a créé
    private static function getVisibleCreatorIds(\App\Models\Utilisateur $user): array
    {
        // IDs des Admin nationaux créés par ce Super admin
        $adminNationalIds = \App\Models\Utilisateur::where('created_by', $user->id)
            ->where('role', 'Admin national')
            ->pluck('id')
            ->toArray();

        // Super admin lui-même + ses Admin nationaux
        return array_merge([$user->id], $adminNationalIds);
    }

    public static function getEloquentQuery(): Builder
    {

        /* Ce commentaire spécial sert à indiquer à l’éditeur que $user est soit un objet utilisateur 
           soit null si personne n'est connecté, ça aide aussi à éviter les lignes soulignés dans
           VS Code */
        /** @var Utilisateur|null $user */

        // Récupération de l’utilisateur actuellement connecté dans Filament
        //$user  = filament()->auth()->user();
        $user  = \Illuminate\Support\Facades\Auth::guard('web')->user();
        
        // C'est la requête normale du modèle, puis on pourra la modifier selon le rôle ou les permissions.
        $query = parent::getEloquentQuery();

        /* instanceof : Vérifie que $user est bien une instance du modèle Utilisateur 
           Si user n’existe pas alors, 1 = 0 => on ne retourne aucun résultat
        */
        if (!$user instanceof Utilisateur) {
            return $query->whereRaw('1 = 0');
        }

        // Admin voit tout
        if ($user->hasRole('Admin')) {
            return $query;
        }

        // Super admin voit uniquement les utilisateurs qu'il a créés
        /*if ($user->hasRole('Super admin')) {
            return $query
                ->where('created_by', $user->id)
                ->whereIn('role', ['Admin national', 'Admin régional', 'Admin de site']);
        }*/

        if ($user->hasRole('Super admin')) {
            $creatorIds = static::getVisibleCreatorIds($user);
            return $query->whereIn('created_by', $creatorIds);
        }

        if ($user->hasRole('Admin national')) {
            // Voit les Admin régionaux et de site de son pays
            $regionIds = \App\Models\Region::where('pays_id', $user->pays_id)->pluck('id');

            return $query
                ->whereIn('role', ['Admin régional', 'Admin de site'])
                ->where(function($q) use ($regionIds, $user) {
                    $q->whereIn('region_id', $regionIds)
                    ->orWhere('pays_id', $user->pays_id);
                });
        }

        // Admin régional voit les admins de sa région
        if ($user->hasRole('Admin régional')) {
            return $query
                ->where('region_id', $user->region_id)
                ->whereIn('role', ['Admin de site']); 
        }

        // Admin de site voit les utilisateurs de son site
        if ($user->hasRole('Admin de site')) {
            return $query->where('site_id', $user->site_id);
        }

        return $query->whereRaw('1 = 0');
    }

    // Cette fonction sert à contrôler si l’user a le droit d’afficher la liste des users
    public static function canViewAny(): bool
    {
        // Récupère l'utilisateur connecté via Filament
        // ou via le guard web classique (Laravel normal)
        // ?? signifie que “Si la valeur de gauche est null, utiliser celle de droite.”
        //$user = filament()->auth()->user() ?? \Illuminate\Support\Facades\Auth::guard('web')->user();
        $user  = \Illuminate\Support\Facades\Auth::guard('web')->user();

        if (!$user instanceof Utilisateur) return false;
        
        // Vérifie si l'utilisateur possède la permission
        // de voir la liste des utilisateurs
        return $user->can('view_any_UtilisateurResource');
    }

    public static function canCreate(): bool
    {
        //$user = filament()->auth()->user() ?? \Illuminate\Support\Facades\Auth::guard('web')->user();
        $user  = \Illuminate\Support\Facades\Auth::guard('web')->user();
        if (!$user instanceof Utilisateur) return false;
        return $user->can('create_UtilisateurResource');
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        //$user = filament()->auth()->user() ?? \Illuminate\Support\Facades\Auth::guard('web')->user();
        $user  = \Illuminate\Support\Facades\Auth::guard('web')->user();
        if (!$user instanceof Utilisateur) return false;
        return $user->can('update_UtilisateurResource');
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        //$user = filament()->auth()->user() ?? \Illuminate\Support\Facades\Auth::guard('web')->user();
        $user  = \Illuminate\Support\Facades\Auth::guard('web')->user();
        if (!$user instanceof Utilisateur) return false;
        return $user->can('delete_UtilisateurResource');
    }
}