<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CityResource\Pages;
use App\Models\City;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static ?string $navigationIcon  = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'Cities';
    protected static ?string $navigationGroup = 'Lookups';
    protected static ?int    $navigationSort  = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(100)
                ->label('City Name'),

            Forms\Components\Select::make('country')
                ->required()
                ->options([
                    'Saudi Arabia' => 'Saudi Arabia',
                    'UAE'          => 'UAE',
                    'Kuwait'       => 'Kuwait',
                    'Qatar'        => 'Qatar',
                    'Bahrain'      => 'Bahrain',
                    'Oman'         => 'Oman',
                ])
                ->label('Country'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('country')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Saudi Arabia' => 'success',
                        'UAE'          => 'info',
                        'Kuwait'       => 'warning',
                        'Qatar'        => 'danger',
                        'Bahrain'      => 'primary',
                        'Oman'         => 'gray',
                        default        => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('country')
                    ->options([
                        'Saudi Arabia' => 'Saudi Arabia',
                        'UAE'          => 'UAE',
                        'Kuwait'       => 'Kuwait',
                        'Qatar'        => 'Qatar',
                        'Bahrain'      => 'Bahrain',
                        'Oman'         => 'Oman',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('country');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit'   => Pages\EditCity::route('/{record}/edit'),
        ];
    }
}
