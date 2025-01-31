<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShiftsResource\Pages;
use App\Models\Karyawan;
use App\Models\Shifts;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class ShiftsResource extends Resource
{
    protected static ?string $model = Shifts::class;
    protected static ?string $navigationLabel = 'Shift';
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Manage Office';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TimePicker::make('waktu_masuk')
                    ->required(),
                Forms\Components\TimePicker::make('waktu_keluar')
                    ->required(),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->emptyStateHeading('')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                Tables\Columns\TextColumn::make('waktu_masuk')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('waktu_keluar')
                ->badge()
                ->color('danger'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('ubah'),
                Tables\Actions\DeleteAction::make()->label('hapus')
                ->action(function ($record) {
                    try {
                        $hasKaryawan =Karyawan::where('id_shift', $record->id)->exists();
                        if ($hasKaryawan) {
                            throw new \Exception("Shift ini tidak bisa dihapus karena ada karyawan yang terdaftar di dalamnya.");
                        }
                        $record->delete();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal Menghapus Shift')
                            ->body('Shift ini tidak bisa dihapus karena ada karyawan yang terdaftar di dalamnya.')
                            ->danger()
                            ->color('danger')
                            ->send();
                    }
                }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    ->action(function ($records) {
                        $failedDeletions = [];
                        foreach ($records as $record) {
                            $hasKaryawan = Karyawan::where('id_shift', $record->id)->exists();

                            if ($hasKaryawan) {
                                $failedDeletions[] = $record->nama; 
                            } else {
                                $record->delete();
                            }
                        }
                        if (!empty($failedDeletions)) {
                            Notification::make()
                                ->title('Gagal Menghapus Shift')
                                ->body('Berikut adalah Shift yang tidak bisa dihapus karena ada karyawan yang terdaftar: ' . implode(', ', $failedDeletions))
                                ->danger()
                                ->color('danger')
                                ->send();
                        }
                    }),
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
            'index' => Pages\ListShifts::route('/'),
            'create' => Pages\CreateShifts::route('/create'),
            'edit' => Pages\EditShifts::route('/{record}/edit'),
        ];
    }
}
