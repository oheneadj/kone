<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\AffiliateLink;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
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
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AffiliateLinkResource\Pages;
use App\Filament\Resources\AffiliateLinkResource\RelationManagers;

class AffiliateLinkResource extends Resource
{
    protected static ?string $model = AffiliateLink::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationGroup = 'Providers';

    protected static ?int $navigationSort = 5;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Affiliate Link')
                    ->description('Create a new affiliate link')
                    ->schema([
                        Select::make('product_id')
                            ->label('Select a product')
                            ->searchable()
                            ->options(Product::all()->pluck('name', 'id'))
                            ->required()
                            ->label('Product'),
                        TextInput::make('link')
                            ->required()
                            ->label('Affiliate Link')
                            ->placeholder('eg. https://affiliate.com?ref=123')
                            ->maxLength(255)
                            ->unique(AffiliateLink::class, 'link', ignoreRecord: true),
                        TextInput::make('label')
                            ->required()
                            ->label('Add Link Label')
                            ->placeholder('eg. Main Link, Discount Link')
                            ->maxLength(255),
                    ])->columns(2)->columnSpan(2),
                Section::make('')
                    ->description('Affiliate Link Data')
                    ->schema([
                        TextInput::make('url_code')
                            ->required()
                            ->label('URL Code')
                            ->unique(AffiliateLink::class, 'url_code', ignoreRecord: true)
                            ->placeholder('eg. https://affiliate.com/go/{special_code}')
                            ->maxLength(255),
                        Toggle::make('is_primary')
                            ->label('Set as primary link')
                            ->helperText('This will be the default link for this product')
                            ->required()
                            ->default(false)
                            ->onIcon('heroicon-m-bolt')
                            ->offIcon('heroicon-m-user'),
                        Toggle::make('is_active')
                            ->onIcon('heroicon-m-bolt')
                            ->offIcon('heroicon-m-user')
                            ->label('Set as active link')
                            ->required()
                            ->default(true)
                            ->helperText('This will set the link as active or inactive'),
                    ])
                    ->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Affiliate Links')
            ->description('Manage your affiliate links here.')
            ->defaultSort('created_at', 'desc')
            ->emptyStateDescription('Once you add your first affilate link, it will appear here.')
            ->columns([
                TextColumn::make('product.name')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->label('Provider'),
                TextColumn::make('link')
                    ->sortable()
                    ->searchable()
                    ->label('Affiliate Link'),
                TextColumn::make('label')
                    ->sortable()
                    ->searchable()
                    ->label('Link Label'),
                ToggleColumn::make('is_primary')
                    ->sortable()
                    ->label('Primary Link'),
                ToggleColumn::make('is_active')
                    ->sortable()
                    ->label('Active Link'),
                TextColumn::make('clicks')
                    ->sortable()
                    ->searchable()
                    ->label('Clicks'),
                TextColumn::make('url_code')
                    ->sortable()
                    ->searchable()
                    ->label('URL Code'),
                // TextColumn::make('commission_rate')
                //     ->sortable()
                //     ->searchable()
                //     ->label('Commission Rate'),
                // TextColumn::make('commission_amount')
                //     ->sortable()
                //     ->searchable()
                //     ->label('Commission Amount'),
                // TextColumn::make('commission_type')
                //     ->sortable()
                //     ->searchable()
                //     ->label('Commission Type'),
                // TextColumn::make('currency')
                //     ->sortable()
                //     ->searchable()
                //     ->label('Currency'),

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
            'index' => Pages\ListAffiliateLinks::route('/'),
            'create' => Pages\CreateAffiliateLink::route('/create'),
            'edit' => Pages\EditAffiliateLink::route('/{record}/edit'),
        ];
    }
}
