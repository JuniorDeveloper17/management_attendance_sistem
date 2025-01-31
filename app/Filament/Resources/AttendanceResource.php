<?php

namespace App\Filament\Resources;

use App\Filament\Exports\AttendanceExporter;
use App\Filament\Resources\AttendanceResource\Pages;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use App\Models\Attendance;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Actions\HeaderActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;
    protected static ?string $navigationGroup = 'Manage Kehadiran';
    protected static ?string $navigationLabel = 'Kehadiran';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';


    public static function form(Form $form): Form
    {
        return $form

            ->schema([
                Split::make([
                    Fieldset::make()
                        ->columns(1)
                        ->relationship('karyawan')
                        ->schema([
                            Forms\Components\TextInput::make('nama')
                                ->label('Nama')
                                ->disabled(),
                        ]),
                    Section::make()
                        ->schema([
                            Forms\Components\TextInput::make('status')
                                ->required()
                                ->readOnly()
                                ->maxLength(255)
                        ])



                ])
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('checkin_image')
                    ->image()
                    ->disabled()
                    ->imageResizeMode('cover')
                    ->panelAspectRatio('2:1')
                    ->openable()
                    ->required(),
                Forms\Components\FileUpload::make('checkout_image')
                    ->image()
                    ->imageResizeMode('cover')
                    ->panelAspectRatio('2:1')
                    ->openable()
                    ->disabled()
                    ->required(),
                Forms\Components\TextInput::make('checkin_location')
                    ->required()
                    ->readOnly()
                    ->maxLength(255),
                Forms\Components\TextInput::make('checkout_location')
                    ->required()
                    ->readOnly()
                    ->maxLength(255),
                Forms\Components\TextInput::make('durasi_kerja')
                    ->required()
                    ->readOnly()
                    ->maxLength(255),
                Split::make([
                    Forms\Components\TimePicker::make('created_at')
                        ->label('Waktu masuk')
                        ->required()
                        ->readOnly(),
                    Forms\Components\TimePicker::make('updated_at')
                        ->label('Waktu keluar')
                        ->required()
                        ->readOnly()
                ]),
                Split::make([
                    Fieldset::make()
                        ->columns(1)
                        ->relationship('office')
                        ->schema([
                            Forms\Components\TextInput::make('nama')
                                ->label('Office')
                                ->disabled(),
                        ]),
                    Fieldset::make()
                        ->columns(1)
                        ->relationship('shift')
                        ->schema([
                            Forms\Components\TextInput::make('nama')
                                ->label('Shift')
                                ->disabled(),
                        ]),
                ])->columnSpanFull(),
                Forms\Components\TextInput::make('id_device')
                    ->required()
                    ->readOnly()
                    ->maxLength(255)->columnSpanFull(),

            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Attendance Kosong')
            ->poll('3s')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d-m-Y')
                    ->badge()
                    ->color('warning')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('karyawan.nama')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('checkin_location')
                    ->label('Lokasi Masuk')
                    ->badge()
                    ->color('success')
                    ->url(function ($record) {
                        return "https://www.google.com/maps?q={$record->checkin_location}";
                    })
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('checkout_location')
                    ->label('Lokasi Keluar')
                    ->badge()
                    ->color('success')
                    ->url(function ($record) {

                        return "https://www.google.com/maps?q={$record->checkout_location}";
                    })
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('durasi_kerja')
                    ->label('Durasi Kerja')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->color(function ($state) {
                        return  $state == 'tepat waktu' ? 'success' : 'danger';
                    })
                    ->badge()
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('Office')
                    ->relationship('office', 'nama')
                    ->preload(),
                SelectFilter::make('Shift')
                    ->relationship('shift', 'nama')
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
                Tables\Actions\DeleteAction::make()->label('hapus'),
                Tables\Actions\ViewAction::make()->color('success')->label('lihat')
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()->exporter(AttendanceExporter::class)
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            // 'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
