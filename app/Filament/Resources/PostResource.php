<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Post;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
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
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\PostResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PostResource\RelationManagers;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->label('Title'),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->label('Slug'),
                RichEditor::make('content')
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
                    ])
                    ->fileAttachmentsDirectory('attachments'),
                TagsInput::make('tags')
                    ->nestedRecursiveRules([
                        'min:3',
                        'max:255',
                    ]),
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'Archived' => 'Archived',
                    ])
                    ->default('published')
                    ->required()
                    ->label('Status'),
                FileUpload::make('featured_image')
                    ->label('Featured Image')
                    ->image()
                    ->mimeTypeMap([
                        'webp' => 'image/webp',
                        'avif' => 'image/avif',
                    ])
                    ->disk('public')
                    ->directory('posts')
                    ->preserveFilenames()
                    ->visibility('public')
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        null,
                        '16:9',
                        '4:3',
                        '1:1',
                    ]),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->label('Title')
                    ->copyable()
                    ->copyMessage('Title copied')
                    ->copyMessageDuration(1500),
                TextColumn::make('slug')
                    ->sortable()
                    ->searchable()
                    ->label('Slug'),
                TextColumn::make('user.name')
                    ->sortable()
                    ->searchable()
                    ->label('Author'),
                TextColumn::make('status')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        'archived' => 'danger',
                    })
                    ->label('Status'),
                TextColumn::make('tags')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->separator(',')
                    ->label('Tags'),
                TextColumn::make('views')
                    ->sortable()
                    ->numeric()
                    ->label('Views'),
                TextColumn::make('published_at')
                    ->sortable()
                    ->searchable()
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip('l jS \of F Y h:i:s A')
                    ->label('Published At'),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
