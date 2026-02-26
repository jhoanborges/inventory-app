<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RutaResource\Pages;
use App\Models\Ruta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RutaResource extends Resource
{
    protected static ?string $model = Ruta::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Logística';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nombre')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('origen')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('destino')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('operador_id')
                ->relationship('operador', 'name')
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('vehiculo')
                ->maxLength(255),
            Forms\Components\Select::make('estado')
                ->options([
                    'pendiente' => 'Pendiente',
                    'en_progreso' => 'En Progreso',
                    'completada' => 'Completada',
                ])
                ->default('pendiente')
                ->required(),
            Forms\Components\DateTimePicker::make('fecha_inicio'),
            Forms\Components\DateTimePicker::make('fecha_fin'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('origen')->sortable(),
                Tables\Columns\TextColumn::make('destino')->sortable(),
                Tables\Columns\TextColumn::make('operador.name')->sortable(),
                Tables\Columns\TextColumn::make('vehiculo'),
                Tables\Columns\BadgeColumn::make('estado')
                    ->colors([
                        'warning' => 'pendiente',
                        'primary' => 'en_progreso',
                        'success' => 'completada',
                    ]),
                Tables\Columns\TextColumn::make('fecha_inicio')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRutas::route('/'),
            'create' => Pages\CreateRuta::route('/create'),
            'edit' => Pages\EditRuta::route('/{record}/edit'),
        ];
    }
}
