<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = 'Shop';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->live(onBlur: true)
                                ->unique()
                                ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                    if ($operation !== 'create') {
                                        return;
                                    }

                                    $set('slug', Str::slug($state));
                                }),
                            Forms\Components\TextInput::make('slug')
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->unique(Product::class, 'slug', ignoreRecord: true),

                            MarkdownEditor::make('description')
                                ->columnSpanFull()
                        ])->columns(2)
                    ]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Status')
                            ->schema([
                                Toggle::make('is_visible')
                                    ->label('Visibility')
                                    ->helperText('Enable or disable category visibility')
                                    ->default(true),

                                Forms\Components\Select::make('parent_id')
                                ->relationship('parent', 'name')
                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('parent.name')
                    ->label('Parent')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_visible')
                    ->label('Visibility')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->date()
                    ->label('Updated Date')
                    ->sortable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
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
