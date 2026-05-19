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

class DispositifResource extends Resource
{
    use HasResourcePermissions;
    
    protected static ?string $model = Dispositif::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCpuChip;

    protected static ?string $recordTitleAttribute = 'Dispositifs';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestion des ressources';

    public static function form(Schema $schema): Schema
    {
        //return DispositifForm::configure($schema);
        return $schema
            ->schema([
                //Select::make('site_id')->relationship('site', 'nom')->required(),
                Select::make('site_id')
                    ->options(function () {
                        /** @var Utilisateur $user */
                        $user = filament()->auth()->user();

                        return \App\Models\Site::query()
                            ->when($user->hasRole('Super admin'), fn($q) =>
                                $q->where('created_by', $user->id)
                            )
                            ->pluck('nom', 'id');
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
                    ]),
                Toggle::make('statut')->label('Actif')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        //return DispositifsTable::configure($table);
        return $table->columns([
                TextColumn::make('site.nom')->label('Nom du site')->sortable(),
                TextColumn::make('nom')->searchable()->label('Nom')->sortable(),
                TextColumn::make('adresse_mac')->searchable()->label('Adresse MAC'),
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
            'index' => ListDispositifs::route('/'),
            'create' => CreateDispositif::route('/create'),
            'edit' => EditDispositif::route('/{record}/edit'),
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
        $user = filament()->auth()->user();
        return $user->can('view_any_DispositifResource');
    }
}
