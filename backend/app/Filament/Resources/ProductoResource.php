<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoResource\Pages;
use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Inventario';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('sku')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            Forms\Components\TextInput::make('nombre')
                ->required()
                ->maxLength(255),
            Forms\Components\Textarea::make('descripcion')
                ->columnSpanFull(),
            Forms\Components\TextInput::make('categoria')
                ->maxLength(255),
            Forms\Components\Select::make('unidad_medida')
                ->options([
                    'kg' => 'Kilogramo',
                    'unidad' => 'Unidad',
                    'caja' => 'Caja',
                    'litro' => 'Litro',
                    'metro' => 'Metro',
                ])
                ->required(),
            Forms\Components\TextInput::make('precio')
                ->numeric()
                ->prefix('$'),
            Forms\Components\TextInput::make('stock_actual')
                ->numeric()
                ->default(0),
            Forms\Components\TextInput::make('stock_minimo')
                ->numeric()
                ->default(0),
            Forms\Components\TextInput::make('barcode')
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            Forms\Components\FileUpload::make('imagen')
                ->image()
                ->directory('productos'),
            Forms\Components\Toggle::make('activo')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('nombre')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('categoria')->sortable(),
                Tables\Columns\TextColumn::make('unidad_medida'),
                Tables\Columns\TextColumn::make('precio')->money('USD')->sortable(),
                Tables\Columns\TextColumn::make('stock_actual')->sortable(),
                Tables\Columns\TextColumn::make('stock_minimo'),
                Tables\Columns\IconColumn::make('activo')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('activo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductos::route('/'),
            'create' => Pages\CreateProducto::route('/create'),
            'edit' => Pages\EditProducto::route('/{record}/edit'),
        ];
    }
}
