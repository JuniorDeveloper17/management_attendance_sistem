<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfficesResource\Pages;
use App\Http\Controllers\Controller;
use App\Http\Controllers\lokasiController;
use App\Models\Karyawan;
use App\Models\Offices;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class OfficesResource extends Resource
{
    protected static ?string $model = Offices::class;
    protected static ?string $navigationLabel = 'Office';
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Manage Office';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->label('Nama Office')
                    ->maxLength(255)
                    ->required(),
                Forms\Components\TextInput::make('latitude')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('radius')
                    ->maxLength(255)
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('longitude')
                    ->numeric()
                    ->required(),
                Forms\Components\Textarea::make('address')
                    ->label('Alamat Office')
                    ->maxLength(255),
                // Forms\Components\Actions::make([
                //     Action::make('star')->label('Genarate Lokasi')
                //         ->action(function (Component $component) {
                //             $controller = new lokasiController();
                //             $controller->generateLocation();
                //         })
                // ])
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
                Tables\Columns\TextColumn::make('address')
                    ->label('Alamat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('latitude')
                    ->searchable()
                    ->url(function ($record) {
                        return "https://www.google.com/maps?q={$record->latitude},{$record->longitude}";
                    })
                    ->badge()
                    ->color('warning')
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('longitude')
                    ->searchable()
                    ->url(function ($record) {
                        return "https://www.google.com/maps?q={$record->latitude},{$record->longitude}";
                    })
                    ->badge()
                    ->color('warning')
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('radius')
                    ->numeric()
                    ->badge()
                    ->color('success')
                    ->sortable(),
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
                            $hasKaryawan = Karyawan::where('id_office', $record->id)->exists();
                            if ($hasKaryawan) {
                                throw new \Exception("Office ini tidak bisa dihapus karena ada karyawan yang terdaftar di dalamnya.");
                            }
                            $record->delete();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal Menghapus Office')
                                ->body('Office ini tidak bisa dihapus karena ada karyawan yang terdaftar di dalamnya.')
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
                                $hasKaryawan = Karyawan::where('id_office', $record->id)->exists();
                                if ($hasKaryawan) {
                                    $failedDeletions[] = $record->nama;
                                } else {
                                    $record->delete();
                                }
                            }
                            if (!empty($failedDeletions)) {
                                Notification::make()
                                    ->title('Gagal Menghapus Office')
                                    ->body('Berikut adalah office yang tidak bisa dihapus karena ada karyawan yang terdaftar: ' . implode(', ', $failedDeletions))
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
            'index' => Pages\ListOffices::route('/'),
            'create' => Pages\CreateOffices::route('/create'),
            'edit' => Pages\EditOffices::route('/{record}/edit'),
        ];
    }
}
