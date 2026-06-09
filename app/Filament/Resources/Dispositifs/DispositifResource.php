<?php

namespace App\Filament\Resources\Dispositifs;

use App\Filament\Resources\Dispositifs\Pages\CreateDispositif;
use App\Filament\Resources\Dispositifs\Pages\EditDispositif;
use App\Filament\Resources\Dispositifs\Pages\ListDispositifs;
use App\Models\Dispositif;
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
use App\Models\Utilisateur;
//use App\Filament\Traits\HasResourcePermissions;
use App\Filament\Resources\Traits\HasResourcePermissions;
use Filament\Tables\Columns\ToggleColumn;

class DispositifResource extends Resource
{
    use HasResourcePermissions;
    
    protected static ?string $model = Dispositif::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCpuChip;

    protected static ?string $recordTitleAttribute = 'Dispositifs';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestion du contenu';

    public static function form(Schema $schema): Schema
    {
        //return DispositifForm::configure($schema);
        return $schema
            ->schema([
                //Select::make('site_id')->relationship('site', 'nom')->required(),
                Select::make('site_id')
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
                            
                            /** @var Utilisateur $user */
                            $user = \Illuminate\Support\Facades\Auth::guard('web')->user();

                            $query = \App\Models\Site::query();
                            
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
                            $villeIds = \App\Models\Ville::where('region_id', $user->region_id)
                                ->pluck('id');
                            return $query->whereIn('ville_id', $villeIds)->pluck('nom', 'id');
                        }

                        if ($user->hasRole('Admin de site')) {
                            // Admin de site voit uniquement son site
                            return $query->where('id', $user->site_id)->pluck('nom', 'id');
                        }

                        return [];
                    })
                    ->label('Nom du site')
                    ->required(),
                TextInput::make('nom')->label('Nom du dispositif')->required(),
                TextInput::make('adresse_mac')
                    ->label('Adresse MAC')
                    ->placeholder('AA:BB:CC:DD:EE:FF')
                    ->unique(ignoreRecord: true)
                    ->rules(['regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/'])
                    ->validationMessages([
                        'regex' => 'Format invalide. Exemple : AA:BB:CC:DD:EE:FF',
                        'unique' => 'Cette adresse MAC est déjà utilisée.',
                    ]),
                Toggle::make('statut')->label('Actif')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        //return DispositifsTable::configure($table);
        return $table->columns([
                TextColumn::make('site.nom')->label('Site'),
                TextColumn::make('nom')->searchable()->label('Nom'),
                TextColumn::make('adresse_mac')->searchable()->label('Adresse MAC'),
                //IconColumn::make('statut')->label('Statut')->boolean(),
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
            'index' => ListDispositifs::route('/'),
            'create' => CreateDispositif::route('/create'),
            'edit' => EditDispositif::route('/{record}/edit'),
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
            $villeIds = \App\Models\Ville::whereHas('region', fn($q) =>
                $q->where('pays_id', $user->pays_id)
            )->pluck('id');

            $siteIds = \App\Models\Site::whereIn('ville_id', $villeIds)->pluck('id');

            return $query->whereIn('site_id', $siteIds);
        }

        if ($user->hasRole('Admin régional')) {
            $siteIds = \App\Models\Site::whereHas('ville', fn($q) =>
                $q->where('region_id', $user->region_id)
            )->pluck('id');

            return $query->whereIn('site_id', $siteIds);
        }
        if ($user->hasRole('Admin de site')) {
            return $query->where('site_id', $user->site_id);
        }

        return $query->whereRaw('1 = 0');
    }

    public static function canViewAny(): bool
    {
        /** @var \App\Models\Utilisateur $user */
        //$user = filament()->auth()->user();
        $user  = \Illuminate\Support\Facades\Auth::guard('web')->user();
        return $user->can('view_any_DispositifResource');
    }
}
