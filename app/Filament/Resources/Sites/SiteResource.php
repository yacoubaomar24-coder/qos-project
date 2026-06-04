<?php

namespace App\Filament\Resources\Sites;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;

use App\Filament\Resources\Sites\Pages\CreateSite;
use App\Filament\Resources\Sites\Pages\EditSite;
use App\Filament\Resources\Sites\Pages\ListSites;
use App\Models\Site;
use App\Models\Utilisateur;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Ville;
use App\Filament\Resources\Traits\HasResourcePermissions;
use Filament\Tables\Columns\ToggleColumn;

class SiteResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $model = Site::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $recordTitleAttribute = 'Sites';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestion des ressources';

    public static function form(Schema $schema): Schema
    {
        //return SiteForm::configure($schema);
        return $schema
            ->schema([
                //Select::make('ville_id')->relationship('ville', 'nom'),
                Select::make('ville_id')
                    ->options(function () {
                        /** @var Utilisateur $user */
                        //$user = filament()->auth()->user();
                        $user  = \Illuminate\Support\Facades\Auth::guard('web')->user();

                        $query = \App\Models\Ville::query();

                        if ($user->hasRole('Admin')) {
                            // Admin voit toutes les villes
                            return $query->pluck('nom', 'id');
                        }

                        if ($user->hasRole('Super admin')) {
                            // Super admin voit ses villes créées directement
                            return $query->where('created_by', $user->id)->pluck('nom', 'id');
                        }

                        if ($user->hasRole('Admin national')) {
                            // Admin national voit les villes de son pays
                            // Chemin direct : région → pays
                            $regionIds = \App\Models\Region::where('pays_id', $user->pays_id)
                                ->pluck('id');
                            return $query->whereIn('region_id', $regionIds)->pluck('nom', 'id');
                        }

                        if ($user->hasRole('Admin régional')) {
                            // Admin régional voit les villes de sa région
                            return $query->where('region_id', $user->region_id)->pluck('nom', 'id');
                        }

                        return [];
                    })
                    ->label('Nom de la ville')
                    ->required(),
                TextInput::make('nom')
                    ->label('Nom du site')
                    ->unique(
                        ignoreRecord: true // ← ignore l'enregistrement en cours lors de l'édition
                    )
                    ->validationMessages([
                        'unique' => 'Ce nom de site est déjà utilisé par un autre site.',
                    ])
                    ->required(),
                Section::make('Coordonnées GPS')
                    ->columns(2)
                    ->schema([
                        TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->placeholder('ex: 13.5137')
                            ->helperText('Latitude GPS du site'),

                        TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->placeholder('ex: 2.1098')
                            ->helperText('Longitude GPS du site'),
                ]),
                Toggle::make('statut')->label('Actif')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        //return SitesTable::configure($table);
        return $table->columns([
                TextColumn::make('ville.nom')->label('Ville'),
                TextColumn::make('nom')->searchable()->label('Nom'),
                // SiteResource.php → table()
                TextColumn::make('latitude')->label('Latitude')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('longitude')->label('Longitude')->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('statut')->label('Statut'),
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
            'index' => ListSites::route('/'),
            'create' => CreateSite::route('/create'),
            'edit' => EditSite::route('/{record}/edit'),
        ];
    }

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
        /** @var Utilisateur|null $user */
        //$user  = filament()->auth()->user();
        $user  = \Illuminate\Support\Facades\Auth::guard('web')->user();
        $query = parent::getEloquentQuery();

        if (!$user instanceof Utilisateur) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasRole('Admin')) {
            return $query;
        }

        if ($user->hasRole('Super admin')) {
            $creatorIds = static::getVisibleCreatorIds($user);
            return $query->whereIn('created_by', $creatorIds);
        }
        /*
        if ($user->hasRole('Super admin')) {
            return $query->where('created_by', $user->id);
        }*/

        if ($user->hasRole('Admin national')) {
            // Sites dont la ville → région → pays
            $villeIds = \App\Models\Ville::whereHas('region', fn($q) =>
                $q->where('pays_id', $user->pays_id)
            )->pluck('id');

            return $query->whereIn('ville_id', $villeIds);
        }

        if ($user->hasRole('Admin régional')) {
            $villeIds = \App\Models\Ville::where('region_id', $user->region_id)
                ->pluck('id');
            return $query->whereIn('ville_id', $villeIds);
        }

        if ($user->hasRole('Admin de site')) {
            return $query->where('id', $user->site_id);
        }

        return $query->whereRaw('1 = 0');
    }
}
