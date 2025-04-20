<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Post;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
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
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\PostResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PostResource\RelationManagers;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationGroup = 'Posts';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Post')
                    ->description('Create a new post')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->label('Title')
                            ->placeholder('Enter post title')
                            ->maxLength(255)
                            ->unique(Post::class, 'slug', ignoreRecord: true)
                            ->reactive()
                    ->live(onBlur: true)
                            ->afterStateUpdated(function (callable $set, $state) {
                                $set('slug', \Illuminate\Support\Str::slug($state));
                    })->columnSpan(2),
                        TextInput::make('slug')
                            ->required()
                            ->readOnly()
                            ->label('Slug')
                            ->placeholder('Enter post slug')
                            ->maxLength(255)
                            ->unique(Post::class, 'slug', ignoreRecord: true),
                        Select::make('category_id')
                            ->label('Parent Category')
                            ->searchable()
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->required()
                            ->label('Categories')
                            ->createOptionForm([
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
                                    ->relationship('category', 'name')
                            ])
                    ])->columns(2)->columnSpan(2),
                Section::make('Content')
                    ->schema([
                        TagsInput::make('tags')
                            ->nestedRecursiveRules([
                                'min:3',
                                'max:255',
                            ]),
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'Archived' => 'Archived',
                            ])
                            ->default('published')
                            ->required()
                            ->label('Status')
                    ])
                    ->columnSpan(1),
                Section::make('Content')
                    ->description('Write your post content here')
                    ->schema([
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
                            ])->fileAttachmentsDirectory('attachments')
                    ])->columnSpan(2),
                Section::make('')
                    ->schema([
                        Toggle::make('is_featured')
                            ->label('Featured Post')
                            ->helperText('Check to mark this post as featured'),
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
                        Textarea::make('excerpt')
                            ->label('Excerpt')
                            ->placeholder('Enter post excerpt')
                    ->maxLength(225)
                    ->rows(6)

                    ->helperText('A short summary of the post content.'),

                    ])->columnSpan(1),
            ])->columns(3);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->heading('Posts')
            ->description('Manage your posts here.')
            ->defaultSort('created_at', 'desc')
            ->emptyStateDescription('Once you write your first post, it will appear here.')
            ->emptyStateActions([
                // Action::make('create')
                //     ->label('Create post')
                //     ->url(route('filament.resources.posts.create'))
                //     ->icon('heroicon-m-plus')
                //     ->button(),
            ])
            ->columns([
                //image
                TextColumn::make('title')
                ->sortable()
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->words(5)
                    ->lineClamp(1)
                    ->label('Title')
                    ->copyable()
                    ->copyMessage('Title copied')
                    ->copyMessageDuration(1500),
                TextColumn::make('user.name')
                    ->sortable()
                    ->searchable()
                    ->label('Author'),
                TextColumn::make('categories.name')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->separator(',')
                    ->label('Categories'),
                ToggleColumn::make('is_featured')
                    ->label('Featured'),
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
