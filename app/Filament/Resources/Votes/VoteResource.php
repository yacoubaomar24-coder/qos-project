<?php

namespace App\Filament\Resources\Votes;

use App\Filament\Resources\Votes\Pages\CreateVote;
use App\Filament\Resources\Votes\Pages\EditVote;
use App\Filament\Resources\Votes\Pages\ListVotes;
use App\Filament\Resources\Votes\Schemas\VoteForm;
use App\Filament\Resources\Votes\Tables\VotesTable;
use App\Models\Vote;
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
use App\Models\Utilisateur;
use App\Filament\Resources\Traits\HasResourcePermissions;

class VoteResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $model = Vote::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $recordTitleAttribute = 'Votes';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestion des ressources';

    public static function form(Schema $schema): Schema
    {
        //return VoteForm::configure($schema);
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
                //Select::make('dispositif_id')->relationship('dispositif', 'nom')->required(),
                Select::make('dispositif_id')
                    ->options(function () {
                        /** @var Utilisateur $user */
                        $user = filament()->auth()->user();

                        return \App\Models\Dispositif::query()
                            ->when($user->hasRole('Super admin'), fn($q) =>
                                $q->where('created_by', $user->id)
                            )
                            ->pluck('nom', 'id');
                    })
                    ->label('Nom du site')
                    ->required(),
                Select::make('niveau')
                    ->options([
                        'satisfait' => 'Satisfait',
                        'moyen' => 'Moyen',
                        'insatisfait' => 'Insatisfait',
                    ])->native(false)->label('Niveau')->required(),
                Toggle::make('statut')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        //return VotesTable::configure($table);
        return $table->columns([
                TextColumn::make('site.nom')->label('Site'),
                TextColumn::make('dispositif.nom')->label('Dispositif'),
                TextColumn::make('niveau')->label('Niveau'),
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
            'index' => ListVotes::route('/'),
            'create' => CreateVote::route('/create'),
            'edit' => EditVote::route('/{record}/edit'),
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
            $villeIds = \App\Models\Ville::whereHas('region', fn($q) =>
                $q->where('pays_id', $user->pays_id)
            )->pluck('id');

            $siteIds = \App\Models\Site::whereIn('ville_id', $villeIds)->pluck('id');

            return $query->whereIn('site_id', $siteIds);
        }
        
        if ($user->hasRole('Admin régional')) {
            // Remonter directement via site → ville → region
            $siteIds = \App\Models\Site::whereHas('ville', function($q) use ($user) {
                $q->where('region_id', $user->region_id);
            })->pluck('id');

            return $query->whereIn('site_id', $siteIds);
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
        return $user->can('view_any_VoteResource');
    }
}