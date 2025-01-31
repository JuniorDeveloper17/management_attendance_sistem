<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IzinResource\Pages;
use App\Models\Izin;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class IzinResource extends Resource
{
    protected static ?string $model = Izin::class;
    protected static ?string $navigationGroup = 'Manage Kehadiran';
    protected static ?string $navigationLabel = 'Izin';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Split::make([
                    Fieldset::make()
                        ->columnSpanFull()
                        ->columns(1)
                        ->relationship('karyawan')
                        ->schema([
                            Forms\Components\TextInput::make('nama')
                                ->disabled(),
                        ]),
                    Section::make([Forms\Components\TextInput::make('keterangan')
                        ->required()
                        ->maxLength(255)
                        ->disabled(),])
                ])->columnSpanFull(),
                Forms\Components\Textarea::make('keterangan_lanjutan')
                    ->columnSpanFull()
                    ->disabled()
                    ->cols(5)
                    ->rows(10),
                Split::make([
                    Forms\Components\FileUpload::make('document')
                        ->image()
                        ->disabled()
                        ->openable()
                        ->panelAspectRatio('3:3')
                        ->downloadable()
                        ->default(null),
                    Forms\Components\Select::make('status')
                        ->label('Setujui Permintaan Izin')
                        ->columnSpanFull()
                        ->options([
                            '1' => 'SETUJUI',
                            '0' => 'TOLAK',
                        ])
                        ->required(),
                ])

            ]);
    }
    public static function table(Table $table): Table
    {

        return $table
            ->emptyStateHeading('Izin Kosong')
            ->columns([
                Tables\Columns\TextColumn::make('karyawan.nama')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('document')
                    ->url(fn($record) => $record->document_url)
                    ->circular(),
                Tables\Columns\IconColumn::make('status')
                    ->sortable()
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Diajukan')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('Office')
                    ->relationship('karyawan.office', 'nama')
                    ->preload(),
                SelectFilter::make('Shift')
                    ->relationship('karyawan.shift', 'nama')
                    ->preload(),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('Dari'),
                        DatePicker::make('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['Dari'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date)
                                    ->when(
                                        $data['Sampai'],
                                        fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date)
                                    )
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('ubah'),
                Tables\Actions\ViewAction::make()->color('success')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()->exporter(Izin::class)
                        ->label('Export')
                        ->color('success')
                        ->size(ActionSize::Large)
                        ->icon('heroicon-o-arrow-down-tray')
                        ->formats([ExportFormat::Xlsx])
                        ->chunkSize(1000)
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
            'index' => Pages\ListIzins::route('/'),
            'create' => Pages\CreateIzin::route('/create'),
            'edit' => Pages\EditIzin::route('/{record}/edit'),
        ];
    }
}
