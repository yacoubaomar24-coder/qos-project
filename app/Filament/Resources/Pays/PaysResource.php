<?php

namespace App\Filament\Resources\Pays;

use App\Models\Pays;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\Pays\Pages\CreatePays;
use App\Filament\Resources\Pays\Pages\EditPays;
use App\Filament\Resources\Pays\Pages\ListPays;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Utilisateur;
use App\Filament\Resources\Traits\HasResourcePermissions;

class PaysResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $model = Pays::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeEuropeAfrica;

    protected static ?string $recordTitleAttribute = 'Pays';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestion des ressources';

    protected static ?string $slug = 'pays';

    // ---------------- FORMULAIRE ----------------
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('nom')->label('Nom')->required(),
                TextInput::make('code')->label('Code')->required(),
                Toggle::make('statut')->label('Actif')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        //return PaysTable::configure($table);
        return $table->columns([
                TextColumn::make('nom')->searchable()->label('Nom')->sortable(),
                TextColumn::make('code')->searchable()->label('Code'),
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
            'index' => ListPays::route('/'),
            'create' => CreatePays::route('/create'),
            'edit' => EditPays::route('/{record}/edit'),
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

        // Admin voit tout
        if ($user->hasRole('Admin')) {
            return $query;
        }

        // Super admin voit uniquement ses propres pays
        if ($user->hasRole('Super admin')) {
            return $query->where('created_by', $user->id);
        }

        // Admin national voit uniquement les utilisateurs qu'il a créés pour son pays
        if ($user->hasRole('Admin national')) {
            return $query->where('id', $user->pays_id);
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
