<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenjualanResource\Pages;
use App\Models\Penjualan;
use App\Models\PenjualanModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PenjualanResource extends Resource
{
    protected static ?string $model = PenjualanModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Laporan Penjualan';
    protected static ?string $navigationGroup = 'Faktur';
    public static ?string $label = 'Laporan Penjualan';




    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')->label('Tanggal')->sortable()->searchable()->date('d F y'),
                TextColumn::make('kode')->label('Kode Faktur')->sortable()->searchable(),
                TextColumn::make('jumlah')->label('Jumlah')->sortable()->searchable(),
                TextColumn::make('customer.nama_customer')->label('Kode')->sortable()->searchable(),
                TextColumn::make('jenis')->label('Jenis')->sortable()->searchable()->badge(),
                TextColumn::make('status')->label('Status')->sortable()->searchable()->badge()->color(fn(string $state): string => match ($state) {
                    '0' => 'danger',
                    '1' => 'warning',
                })->formatStateUsing(fn(PenjualanModel $record): string => $record->status == 0 ? 'Belum Lunas' : 'Lunas',),



            ])->emptyStateHeading('Tidak ada Data Laporan')->emptyStateDescription('Silahkan Tambahkan Faktur Terlebih dahulu')->emptyStateIcon('heroicon-o-presentation-chart-bar')->emptyStateActions([
                Action::make('create')
                    ->label('Buat Faktur')
                    ->url(route('filament.admin.resources.fakturs.create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListPenjualans::route('/'),
            'create' => Pages\CreatePenjualan::route('/create'),
            'edit' => Pages\EditPenjualan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
