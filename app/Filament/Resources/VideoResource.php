<?php

namespace App\Filament\Resources;

use Embed\Embed;

use Filament\Forms;

use Filament\Tables;
use App\Models\Video;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use App\Contracts\VideoServiceInterface;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;


use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\VideoResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\VideoResource\RelationManagers;


class VideoResource extends Resource
{
    protected static ?string $model = Video::class;

    protected static ?string $navigationIcon = 'heroicon-o-play';

    protected static ?string $navigationGroup = 'Videos';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Post')
                    ->description('Add a new video')
                    ->schema([
                        TextInput::make('url')
                            ->required()
                            ->label('Video URL or Link')
                            ->placeholder('eg. https://www.youtube.com/watch?v=abcd')
                            ->maxLength(255)
                            ->unique(Video::class, 'url', ignoreRecord: true)
                            ->reactive()
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                    //
                    $video_service = app(VideoServiceInterface::class);
                                $embed = new Embed();
                                if (!$state) {
                                    return;
                    }
                                if ($video_service->isAValidVideoUrl($state)) {
                        $info = $embed->get($state);
                                    $set('title', $info->title);
                        $set('description', $info->description);
                                    try {
                                        $video_code = $video_service->extractVideoId($state);
                                        $set('video_id', $video_code);
                                    } catch (\Exception $e) {
                                        // Log the error or handle it as needed
                                        $set('video_id', null);
                        }
                                    $set('video_id', $video_code);
                    }
                                return;
                            }),
                        TextInput::make('video_id')
                            ->required()
                            ->label('Video ID')
                            ->readOnly()
                            ->placeholder(' video ID')
                            ->unique(Video::class, 'video_id', ignoreRecord: true)
                            ->columnSpan(1),
                        TextInput::make('title')
                            ->required()
                            ->label('Title')
                            ->placeholder('Enter video title')
                            ->maxLength(255)
                            ->unique(Video::class, 'title', ignoreRecord: true),
                        Select::make('video_type')
                            ->options([
                                'youtube' => 'YouTube',
                                'vimeo' => 'Vimeo',
                                'other' => 'Others',
                            ])
                            ->placeholder("Select video type")
                            ->required()
                            ->label('Video Type'),
                    ])->columns(2)->columnSpan(2),
                Section::make('')
                    ->schema([
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
                            ]),
                        TagsInput::make('tags')
                            ->nestedRecursiveRules([
                                'min:3',
                                'max:10',
                            ]),
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'archived' => 'Archived',
                            ])
                            ->default('published')
                            ->required()
                            ->label('Status')
                    ])
                    ->columnSpan(1),
                Section::make('Content')
                ->description('Write your video desctiption here')
                    ->schema([
                        Textarea::make('description')
                            ->required()
                            ->label('Description')
                            ->placeholder('Enter a video description')
                    ->maxLength(225)
                            ->rows(5)
                            ->columnSpan(2),
                    ])->columnSpan(2),
                Section::make('')
                    ->schema([
                        Toggle::make('is_featured')
                    ->label('Featured video')
                            ->helperText('Check to mark this video as featured'),
                        FileUpload::make('thumbnail')
                            ->label('Video Thumbnail')
                            ->image()
                            ->mimeTypeMap([
                                'webp' => 'image/webp',
                                'avif' => 'image/avif',
                            ])
                            ->disk('public')
                    ->directory('videos')
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
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Videos')
            ->description('Manage your videos here.')
            ->defaultSort('created_at', 'desc')

            ->columns([
                TextColumn::make('video_id')
                    ->sortable()
                    ->searchable()
                    ->label('Video ID'),
                ImageColumn::make('thumbnail')
                    ->label('Thumbnail')
                ->toggleable()
                    ->circular(),
                TextColumn::make('title')
                    ->sortable()
                    ->description(fn(Video $record): string => $record->url ?? '')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->words(5)
                    ->lineClamp(1)
                    ->label('Title')
                ->toggleable()
                ->url(fn(Video $record): string => $record->url ?? '')
                ->openUrlInNewTab()
                ->tooltip('Click to view video')
                ->copyable()
                    ->copyMessage('Title copied')
                    ->copyMessageDuration(1500),
                TextColumn::make('user.name')
                    ->sortable()
                ->toggleable()
                    ->searchable()
                    ->label('Author'),
                ToggleColumn::make('is_featured')
                    ->label('Featured'),
                TextColumn::make('categories.name')
                    ->sortable()
                    ->searchable()
                    ->badge()
                ->toggleable()
                    ->separator(',')
                    ->label('Categories'),
                TextColumn::make('video_type')
                    ->sortable()
                ->toggleable()
                    ->searchable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'others' => 'gray',
                        'vimeo' => 'success',
                        'youtube' => 'danger',
                    })
                    ->label('Video Type'),
                TextColumn::make('tags')
                    ->sortable()
                    ->searchable()
                    ->badge()
                ->toggleable()
                    ->separator(',')
                    ->label('Tags'),
                TextColumn::make('views')
                    ->sortable()
                    ->numeric()
                    ->label('Views'),
                TextColumn::make('created_at')
                    ->sortable()
                    ->searchable()
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip('l jS \of F Y h:i:s A')
                ->label('Published On')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListVideos::route('/'),
            'create' => Pages\CreateVideo::route('/create'),
            'edit' => Pages\EditVideo::route('/{record}/edit'),
        ];
    }
}
