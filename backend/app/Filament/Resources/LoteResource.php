<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoteResource\Pages;
use App\Models\Lote;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LoteResource extends Resource
{
    protected static ?string $model = Lote::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationGroup = 'Inventario';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('producto_id')
                ->relationship('producto', 'nombre')
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\TextInput::make('numero_lote')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            Forms\Components\TextInput::make('cantidad')
                ->numeric()
                ->required()
                ->minValue(0),
            Forms\Components\DatePicker::make('fecha_fabricacion'),
            Forms\Components\DatePicker::make('fecha_vencimiento'),
            Forms\Components\Select::make('estado')
                ->options([
                    'activo' => 'Activo',
                    'vencido' => 'Vencido',
                    'agotado' => 'Agotado',
                ])
                ->default('activo')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_lote')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('producto.nombre')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('cantidad')->sortable(),
                Tables\Columns\TextColumn::make('fecha_fabricacion')->date()->sortable(),
                Tables\Columns\TextColumn::make('fecha_vencimiento')->date()->sortable(),
                Tables\Columns\BadgeColumn::make('estado')
                    ->colors([
                        'success' => 'activo',
                        'danger' => 'vencido',
                        'warning' => 'agotado',
                    ]),
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
            'index' => Pages\ListLotes::route('/'),
            'create' => Pages\CreateLote::route('/create'),
            'edit' => Pages\EditLote::route('/{record}/edit'),
        ];
    }
}
