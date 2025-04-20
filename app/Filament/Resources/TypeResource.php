<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Type;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\TypeResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TypeResource\Pages\EditType;
use App\Filament\Resources\TypeResource\Pages\ListTypes;
use App\Filament\Resources\TypeResource\Pages\CreateType;
use App\Filament\Resources\TypeResource\RelationManagers;

class TypeResource extends Resource
{
    protected static ?string $model = Type::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $navigationGroup = 'Providers';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Type')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->unique(Type::class, 'name', ignoreRecord: true)
                            ->reactive()
                    ->live(onBlur: true)
                            ->afterStateUpdated(function (callable $set, $state) {
                                $set('slug', Str::slug($state));
                            }),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->readOnly()
                            ->unique(ignoreRecord: true)
                            ->unique(Type::class, 'slug', ignoreRecord: true)
                            ->maxLength(255),
                        Select::make('type_id')
                            ->label('Parent Type')
                            ->searchable()
                            ->options(Type::all()->pluck('name', 'id'))
                            // ->relationship('type', 'name')
                            ->placeholder('Select a parent type')
                    ])->columnSpan(1)
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Types')
            ->description('Manage your types here.')
            ->defaultSort('created_at', 'desc')
            ->emptyStateDescription('Once you add your first type, it will appear here.')
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->label('Name'),
                TextColumn::make('slug')
                    ->sortable()
                    ->searchable()
                    ->label('Slug'),
                TextColumn::make('related_types.name')
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->separator(',')
                    ->searchable()
                    ->label('Parent Type'),
                TextColumn::make('created_at')
                    ->sortable()
                    ->searchable()
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip('l jS \of F Y h:i:s A')
                    ->label('Published At')
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])->tooltip('Actions')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListTypes::route('/'),
            'create' => Pages\CreateType::route('/create'),
            'edit' => Pages\EditType::route('/{record}/edit'),
        ];
    }
}
