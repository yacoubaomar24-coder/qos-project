<?php

namespace App\Filament\Resources\Regions;

use App\Filament\Resources\Regions\Pages\CreateRegion;
use App\Filament\Resources\Regions\Pages\EditRegion;
use App\Filament\Resources\Regions\Pages\ListRegions;

use App\Models\Region;
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
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Traits\HasResourcePermissions;
use Filament\Tables\Columns\ToggleColumn;

class RegionResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $model = Region::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;

    protected static ?string $recordTitleAttribute = 'Régions';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestion du contenu';

    public static function form(Schema $schema): Schema
    {
        //return RegionForm::configure($schema);
        return $schema
            ->schema([
                //Select::make('pays_id')->relationship('pays', 'nom')->required(),
                Select::make('pays_id')
                    ->label('Pays de la région')
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

                        return [];
                        /*
                        return \App\Models\Pays::query()
                            ->when(
                                $user->hasRole('Super admin'),
                                fn($q) => $q->where('created_by', $user->id) // ← filtre par super admin
                            )
                            ->pluck('nom', 'id'); // ← distinct automatique car basé sur id
                        */
                    })
                    ->searchable()
                    ->required(),
                TextInput::make('nom')->label('Nom de la région')->required(),
                Toggle::make('statut')->default(true),
                //TextInput::make('pays_id'),
        ]);
    }

    public static function table(Table $table): Table
    {
        //return RegionsTable::configure($table);
        return $table->columns([
                TextColumn::make('pays.nom')->label('Pays'),
                TextColumn::make('nom')->searchable()->label('Région'),
                //IconColumn::make('statut')->label('Statut')->boolean(),
                ToggleColumn::make('statut')->label('Statut')
                    ->visible(function () {

                            /** @var Utilisateur $user */
                            $user = \Illuminate\Support\Facades\Auth::guard('web')->user();

                            // Seuls ces 3 peuvent voir le statut
                            return $user->hasAnyRole([
                                'Admin',
                                'Super admin',
                                'Admin national',
                            ]);
                        }),
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
            'index' => ListRegions::route('/'),
            'create' => CreateRegion::route('/create'),
            'edit' => EditRegion::route('/{record}/edit'),
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
            return $query->where('pays_id', $user->pays_id);
        }

        if ($user->hasRole('Admin régional')) {
            return $query->where('id', $user->region_id);
        }

        if ($user->hasRole('Admin de site')) {
            // Utiliser directement site_id sur la table votes
            return $query->where('site_id', $user->site_id);
        }

        return $query->whereRaw('1 = 0');
    }

    public static function canViewAny(): bool
    {
        /** @var \App\Models\Utilisateur $user */
        //$user = filament()->auth()->user();
        $user  = \Illuminate\Support\Facades\Auth::guard('web')->user();
        return $user->can('view_any_RegionResource');
    }
}
