<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Type;
use Filament\Tables;
use App\Models\Category;
use App\Models\Provider;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProviderResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProviderResource\RelationManagers;

class ProviderResource extends Resource
{
    protected static ?string $model = Provider::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube-transparent';

    protected static ?string $navigationGroup = 'Providers';

    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Post')
                    ->description('Create a new provider')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->label('Provider Name')
                            ->live()
                            ->debounce(1100)
                            ->placeholder('Enter provider name')
                            ->maxLength(120)
                            ->unique(Provider::class, 'name', ignoreRecord: true)
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
                        TextInput::make('website_url')
                            ->required()
                            ->url()
                            ->label('Website URL')
                            ->placeholder('eg. https://bluehost.com')
                            ->maxLength(255)
                            ->unique(Provider::class, 'website_url', ignoreRecord: true),
                        TextInput::make('api_key')
                            ->label('API Key')
                            ->placeholder('eg. API-1234567890')
                            ->maxLength(255)
                            ->unique(Provider::class, 'api_key', ignoreRecord: true),
                    ])->columns(2)->columnSpan(2),
                Section::make('')
                    ->schema([
                        Select::make('type_id')
                            ->label('Parent Type')
                            ->searchable()
                            ->options(Type::all()->pluck('name', 'id'))
                            ->relationship('type', 'name')
                            ->placeholder('Select a parent type'),
                        FileUpload::make('logo')
                            ->label('Provider Logo')
                            ->image()
                            ->mimeTypeMap([
                                'webp' => 'image/webp',
                                'avif' => 'image/avif',
                            ])
                            ->disk('public')
                            ->directory('provider_logo')
                            ->preserveFilenames()
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ]),

                    ])->columnSpan(1),
                Section::make('Description')
                    ->description('Write a short desctiption about the provider')
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

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Posts')
            ->description('Manage your providers here.')
            ->defaultSort('created_at', 'desc')
            ->emptyStateDescription('Once you add your first provider, it will appear here.')
            ->columns([
                ImageColumn::make('logo')
                    ->toggleable()
                    ->circular()
                    ->label('Logo'),
                TextColumn::make('name')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->words(5)
                    ->lineClamp(1)
                    ->label('Name'),
                TextColumn::make('website_url')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-o-globe-alt')
                    ->iconPosition('before')
                    ->label('Website URL'),
                TextColumn::make('api_key')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->size(TextColumn\TextColumnSize::Large)
                    ->icon('heroicon-s-key')
                    ->iconPosition('before')
                    ->weight(FontWeight::Bold)
                    ->color('success')
                    ->copyable()
                    ->copyMessage('API Key copied')
                    ->copyMessageDuration(1500)
                    ->label('API Key'),
                TextColumn::make('created_at')
                    ->sortable()
                    ->searchable()
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip('l jS \of F Y h:i:s A')
                    ->label('Date Created'),
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
            'index' => Pages\ListProviders::route('/'),
            'create' => Pages\CreateProvider::route('/create'),
            'edit' => Pages\EditProvider::route('/{record}/edit'),
        ];
    }
}
