<?php

namespace App\Filament\Resources\Sites;

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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
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
                        $user = filament()->auth()->user();

                        return \App\Models\Ville::query()
                            ->when($user->hasRole('Super admin'), fn($q) =>
                                $q->where('created_by', $user->id)
                            )
                            ->when($user->hasRole('Admin régional'), fn($q) =>
                                $q->where('region_id', $user->region_id)
                            )
                            ->pluck('nom', 'id');
                    })
                    ->required(),
                TextInput::make('nom')->label('Nom')->required(),
                Toggle::make('statut')->label('Actif')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        //return SitesTable::configure($table);
        return $table->columns([
                TextColumn::make('ville.nom')->label('Ville')->sortable(),
                TextColumn::make('nom')->searchable()->label('Nom')->sortable(),
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
            'index' => ListSites::route('/'),
            'create' => CreateSite::route('/create'),
            'edit' => EditSite::route('/{record}/edit'),
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
