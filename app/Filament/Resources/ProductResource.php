<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Type;
use Filament\Tables;
use App\Models\Product;
use App\Models\Provider;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductResource\RelationManagers;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?string $navigationGroup = 'Providers';

    protected static ?int $navigationSort = 4;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Product')
                    ->description('Create a new product')
                    ->schema([
                        Select::make('provider_id')
                            ->label('Select provider')
                            ->searchable()
                            ->options(Provider::all()->pluck('name', 'id'))
                            ->required()
                            ->label('Providers'),
                        TextInput::make('name')
                            ->required()
                            ->label('Product Name')
                            ->placeholder('Enter post name')
                            ->maxLength(255)
                            ->unique(Product::class, 'name', ignoreRecord: true)
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                $set('slug', \Illuminate\Support\Str::slug($state));
                            }),
                        TextInput::make('slug')
                            ->required()
                            ->readOnly()
                            ->label('Slug')
                            ->placeholder('provider slug')
                            ->maxLength(255)
                            ->unique(Provider::class, 'slug', ignoreRecord: true),
                        Select::make('type_id')
                            ->label('Select Type')
                            ->searchable()
                            ->options(Type::all()->pluck('name', 'id'))
                            ->required()
                            ->label('Product Type')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->unique(Type::class, 'name', ignoreRecord: true)
                                    ->reactive()
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
                            ])
                    ])->columns(2)->columnSpan(2),
                Section::make('Product Price and URL')
                    ->schema([
                        TextInput::make('base_price')
                            ->required()
                            ->prefix('GHS')
                            ->label('Base Price')
                            ->numeric()
                            ->inputMode('decimal')
                            ->minValue(0)
                            ->placeholder('Enter price')
                            ->maxLength(255),
                        TextInput::make('product_url')
                            ->required()
                            ->label('Product URL')
                            ->placeholder('https://example.com')
                            ->maxLength(255),
                    ])
                    ->columnSpan(1),
                Section::make('')
                    ->description('Write your product description here')
                    ->schema([
                        RichEditor::make('description')
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ])->fileAttachmentsDirectory('attachments')
                    ])->columnSpan(2),
                Section::make('Product Status, Logo and Features')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'discontinued' => 'Discontinued',
                            ])
                            ->default('active')
                            ->required()
                            ->label('Status'),
                        FileUpload::make('logo')
                            ->label('Logo')
                            ->placeholder('Upload a logo')
                            ->image()
                            ->mimeTypeMap([
                                'webp' => 'image/webp',
                                'avif' => 'image/avif',
                            ])
                            ->disk('public')
                            ->directory('products')
                            ->preserveFilenames()
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ]),
                        TagsInput::make('features')
                            ->label('Product Features')
                            ->required()
                            ->placeholder('Enter product features')
                            ->nestedRecursiveRules([
                                'min:3',
                                'max:255',
                            ]),
                    ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('provider.name')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->label('Provider'),
                TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->label('Product Name'),
                TextColumn::make('slug')
                    ->sortable()
                    ->searchable()
                    ->label('Slug'),
                TextColumn::make('type.name')
                    ->sortable()
                    ->searchable()
                    ->label('Type'),
                TextColumn::make('features')
                    ->sortable()
                    ->toggleable()
                    ->searchable()
                    ->badge()
                    ->limit(50)
                    ->label('Features'),
                TextColumn::make('base_price')
                    ->sortable()
                    ->searchable()
                    ->money('GHS')
                    ->label('Base Price'),
                TextColumn::make('product_url')
                    ->sortable()
                    ->searchable()
                    ->label('Product URL'),
                TextColumn::make('status')
                    ->sortable()
                    ->searchable()
                    ->badge()

                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'discontinued' => 'warning',
                    })
                    ->label('Status'),
                TextColumn::make('created_at')
                    ->sortable()
                    ->searchable()
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip('l jS \of F Y h:i:s A')
                    ->label('Created At'),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
