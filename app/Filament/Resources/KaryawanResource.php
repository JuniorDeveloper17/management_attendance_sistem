<?php

namespace App\Filament\Resources;

use App\Filament\Exports\KaryawanExporter;
use App\Filament\Resources\KaryawanResource\Pages;
use App\Models\Karyawan;
use Filament\Actions\Action;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Actions\Modal\Actions\ButtonAction;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;

class KaryawanResource extends Resource
{
    protected static ?string $model = Karyawan::class;
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Manage Karyawan';
    protected static ?string $navigationLabel = 'Karyawan';
    public $record;


    public static function form(Form $form): Form
    {
        $formState = $form->getState();
        \Log::debug('Data Karyawan:', $formState);
        return $form
            ->schema([
                //  Split::make([
                //      Section::make()->columns(1)->schema([]),
                //      Section::make()->columns(1)->schema([]),
                //  ])->columnSpanFull(),
                Forms\Components\TextInput::make('nip')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->numeric(),
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('alamat')
                    ->default('belum di tambahkan')
                    ->rows(5)
                    ->cols(10)
                    ->columnSpanFull(),
                Forms\Components\Select::make('jenis_kelamin')
                    ->options([
                        'Laki Laki' => 'Laki Laki',
                        'Perempuan' => 'Perempuan'
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('tanggal_lahir')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->default('Univbi12345')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('no_telp')
                    ->unique(ignoreRecord: true)
                    ->tel()
                    ->numeric()
                    ->required()
                    ->maxLength(13),
                Forms\Components\FileUpload::make('foto')
                    ->image()
                    ->openable()
                    ->imageResizeMode('cover')
                    ->imagePreviewHeight('250')
                    ->panelAspectRatio('2:1')
                    ->panelLayout('integrated')
                    ->imageEditor()
                    ->disk('public')
                    ->directory('karyawan')
                    ->default(null),
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
                Forms\Components\TextInput::make('face_id')
                    ->label('Face Id')
                    ->default(null)
                    ->nullable()

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
                Tables\Columns\TextColumn::make('nip')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('foto')
                    ->circular(),
                Tables\Columns\TextColumn::make('nama')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->searchable()
                    ->color(function ($state) {
                        return  $state == 'Laki Laki' ? 'info' : 'warning';
                    })
                    ->badge(),
                Tables\Columns\TextColumn::make('no_telp')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('shift.nama')
                    ->searchable()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('office.nama')
                    ->searchable()
                    ->numeric()
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
                Tables\Actions\EditAction::make()->color('info')->label('ubah'),
                Tables\Actions\DeleteAction::make()->label('hapus'),
                Tables\Actions\ViewAction::make()->color('success')->label('lihat')

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()->exporter(KaryawanExporter::class)
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
            'index' => Pages\ListKaryawans::route('/'),
            'create' => Pages\CreateKaryawan::route('/create'),
            'edit' => Pages\EditKaryawan::route('/{record}/edit'),
        ];
    }
}
