<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleResource\Pages;
use App\Models\Karyawan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class ScheduleResource extends Resource
{
    protected static ?string $model = Karyawan::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Schedule';
    protected static ?string $navigationGroup = 'Manage Karyawan';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->nullable()
                    ->readOnly()
                    ->maxLength(255),
                Forms\Components\Select::make('id_shift')
                    ->label('shift')
                    ->relationship('shift', 'nama')
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('id_office')
                    ->label('office')
                    ->relationship('office', 'nama')
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('wfa')->label('Tipe Karyawan')
                ->default(fn($state) => $state)
                ->options([
                    '0'=> 'Work from office',
                    '1'=> 'work from anywhere'
                ]),
                Forms\Components\Select::make('status')
                    ->options([
                        'Aktif' => 'Aktif',
                        'Tidak Aktif' => 'Tidak Aktif'
                    ])
                    ->required(),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table

            ->emptyStateHeading('no data schedule')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('shift.nama')
                    ->label('Shift')
                    ->description(function ($record) {
                        return $record->shift
                            ? $record->shift->waktu_masuk . ' - ' . $record->shift->waktu_keluar
                            : '-';
                    })  ->badge()
                    ->color('info')
                    ->searchable(),
                Tables\Columns\TextColumn::make('office.nama')
                    ->searchable(),
                Tables\Columns\IconColumn::make('wfa')
                    ->color(function ($state) {
                        return  $state == True ? 'success' : 'danger';
                    })
                    ->boolean(),
                Tables\Columns\TextColumn::make('status')
                    ->color(function ($state) {
                        return  $state == 'Aktif' ? 'success' : 'danger';
                    })
                    ->badge()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('ubah'),
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
            'index' => Pages\ListSchedules::route('/'),
            'edit' => Pages\EditSchedule::route('/{record}/edit'),
        ];
    }
}
