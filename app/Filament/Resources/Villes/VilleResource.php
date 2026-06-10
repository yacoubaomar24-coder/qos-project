<?php

namespace App\Filament\Resources\Villes;

use App\Filament\Resources\Villes\Pages\CreateVille;
use App\Filament\Resources\Villes\Pages\EditVille;
use App\Filament\Resources\Villes\Pages\ListVilles;
use App\Models\Ville;
use App\Models\Site;
use App\Models\Utilisateur;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Traits\HasResourcePermissions;
use Filament\Tables\Columns\ToggleColumn;

class VilleResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $model = Ville::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static ?string $recordTitleAttribute = 'Villes';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestion du contenu';

    public static function form(Schema $schema): Schema
    {
        //return VilleForm::configure($schema);
        return $schema
            ->schema([
                //Select::make('region_id')->relationship('region', 'nom')->required(),
                Select::make('region_id')
                    ->label('Région de la ville')
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
                            // Super admin verra les régions créées par lui et ses admins nationaux
                            
                            /** @var Utilisateur $user */
                            $user = \Illuminate\Support\Facades\Auth::guard('web')->user();

                            $query = \App\Models\Region::query();
                            
                            // SUPER ADMIN
                            if ($user->hasRole('Super admin')) {

                                // Utilisateurs créés par ce super admin
                                $userIds = \App\Models\Utilisateur::where(
                                    'created_by',
                                    $user->id
                                )->pluck('id');

                                // Ajouter le super admin lui-même
                                $userIds->push($user->id);

                                $query->whereIn('created_by', $userIds);
                            }
                            return $query->pluck('nom', 'id');
                        }

                        if ($user->hasRole('Admin national')) {
                            // Admin national voit toutes les régions de son pays
                            return $query->where('pays_id', $user->pays_id)->pluck('nom', 'id');
                        }

                        if ($user->hasRole('Admin régional')) {
                            // Admin régional voit uniquement sa région
                            return $query->where('id', $user->region_id)->pluck('nom', 'id');
                        }

                        return [];
                    })
                    ->required(),
                TextInput::make('nom')->label('Nom de la ville')->required(),
                Toggle::make('statut')->label('Actif')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        //return VillesTable::configure($table);
        return $table->columns([
                TextColumn::make('region.nom')->label('Région'),
                TextColumn::make('nom')->searchable()->label('Nom'),
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
            'index' => ListVilles::route('/'),
            'create' => CreateVille::route('/create'),
            'edit' => EditVille::route('/{record}/edit'),
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
            // Villes dont la région appartient à son pays
            $regionIds = \App\Models\Region::where('pays_id', $user->pays_id)
                ->pluck('id');

            return $query->whereIn('region_id', $regionIds);
        }

        if ($user->hasRole('Admin régional')) {
            return $query->where('region_id', $user->region_id);
        }

        if ($user->hasRole('Admin de site')) {
            $site = \App\Models\Site::find($user->site_id);
            return $site
                ? $query->where('id', $site->ville_id)
                : $query->whereRaw('1 = 0');
        }
        return $query->whereRaw('1 = 0');
    }
}
