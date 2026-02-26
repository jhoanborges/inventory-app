<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MovimientoInventarioResource\Pages;
use App\Models\MovimientoInventario;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MovimientoInventarioResource extends Resource
{
    protected static ?string $model = MovimientoInventario::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationGroup = 'Inventario';

    protected static ?string $navigationLabel = 'Movimientos';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('producto_id')
                ->relationship('producto', 'nombre')
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\Select::make('lote_id')
                ->relationship('lote', 'numero_lote')
                ->searchable()
                ->preload(),
            Forms\Components\Select::make('ruta_id')
                ->relationship('ruta', 'nombre')
                ->searchable()
                ->preload(),
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\Select::make('tipo')
                ->options([
                    'entrada' => 'Entrada',
                    'salida' => 'Salida',
                ])
                ->required(),
            Forms\Components\TextInput::make('cantidad')
                ->numeric()
                ->required()
                ->minValue(1),
            Forms\Components\TextInput::make('motivo')
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('producto.nombre')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('lote.numero_lote'),
                Tables\Columns\TextColumn::make('user.name')->label('Usuario'),
                Tables\Columns\BadgeColumn::make('tipo')
                    ->colors([
                        'success' => 'entrada',
                        'danger' => 'salida',
                    ]),
                Tables\Columns\TextColumn::make('cantidad')->sortable(),
                Tables\Columns\TextColumn::make('motivo'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMovimientoInventarios::route('/'),
            'create' => Pages\CreateMovimientoInventario::route('/create'),
        ];
    }
}
