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

class RegionResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $model = Region::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;

    protected static ?string $recordTitleAttribute = 'Régions';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestion des ressources';

    public static function form(Schema $schema): Schema
    {
        //return RegionForm::configure($schema);
        return $schema
            ->schema([
                //Select::make('pays_id')->relationship('pays', 'nom')->required(),
                Select::make('pays_id')
                    ->label('Pays')
                    ->options(function () {
                        /** @var Utilisateur $user */
                        $user = filament()->auth()->user();

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
                TextInput::make('nom')->required(),
                Toggle::make('statut')->default(true),
                //TextInput::make('pays_id'),
        ]);
    }

    public static function table(Table $table): Table
    {
        //return RegionsTable::configure($table);
        return $table->columns([
                TextColumn::make('pays.nom')->label('Pays')->sortable(),
                TextColumn::make('nom')->searchable()->label('Région')->sortable(),
                IconColumn::make('statut')->label('Statut')->boolean(),
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

    public static function getEloquentQuery(): Builder
    {
        /** @var Utilisateur|null $user */
        $user  = filament()->auth()->user();
        $query = parent::getEloquentQuery();

        if (!$user instanceof Utilisateur) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasRole('Admin')) {
            return $query;
        }

        if ($user->hasRole('Super admin')) {
            return $query->where('created_by', $user->id);
        }

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
        $user = filament()->auth()->user();
        return $user->can('view_any_RegionResource');
    }
}
