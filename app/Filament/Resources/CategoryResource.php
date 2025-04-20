<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\CategoryResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Filament\Resources\CategoryResource\Pages\EditCategory;
use App\Filament\Resources\CategoryResource\Pages\CreateCategory;
use App\Filament\Resources\CategoryResource\Pages\ListCategories;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Posts';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Category')
                    ->description('Create a new category')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->label('Category Name')
                            ->placeholder('Enter category name')
                            ->maxLength(120)
                            ->unique(Category::class, 'slug', ignoreRecord: true)
                            ->reactive()
                    ->live(onBlur: true)
                            ->afterStateUpdated(function (callable $set, $state) {
                                $set('slug', \Illuminate\Support\Str::slug($state));
                            }),
                        TextInput::make('slug')
                            ->required()
                            ->readOnly()
                            ->label('Slug')
                            ->placeholder('slug')
                            ->maxLength(255)
                            ->unique(Category::class, 'slug', ignoreRecord: true),
                        Select::make('category_id')
                            ->label('Parent Category')
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $search): array => Category::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id')->toArray())
                            ->getOptionLabelUsing(fn($value): ?string => Category::find($value)?->name),
                    ])->columns(2)->columnSpan(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Categories')
            ->description('Manage your categories here.')
            ->defaultSort('created_at', 'desc')
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
                ToggleColumn::make('is_featured')
                    ->label('Featured'),

                TextColumn::make('category.name')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->separator(',')
                    ->label('Parent Categories'),
                TextColumn::make('published_at')
                    ->sortable()
                    ->searchable()
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip('l jS \of F Y h:i:s A')
                    ->label('Published At'),
            ])
            ->filters([
            Filter::make('is_featured')
                ->toggle()
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
