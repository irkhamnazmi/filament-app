<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FakturResource\Pages;
use App\Filament\Resources\FakturResource\RelationManagers;
use App\Models\Barang;
use App\Models\CustomerModel;
use App\Models\Faktur;
use App\Models\FakturModel;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Attributes\Reactive;

class FakturResource extends Resource
{
    protected static ?string $model = FakturModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Faktur';
    protected static ?string $navigationGroup = 'Faktur';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('kode_faktur')->columnSpan(2),
                DatePicker::make('tanggal_faktur')->columnSpan([
                    'default' => 2,
                    'md' => 1,
                    'lg' => 1,
                    'xl' => 1,
                ])->required(),
                Select::make('customer_id')->relationship('customer', 'nama_customer')->columnSpan([
                    'default' => 2,
                    'md' => 1,
                    'lg' => 1,
                    'xl' => 1,
                ])->reactive()->afterStateUpdated(function ($state, callable $set) {
                    $customer = CustomerModel::find($state);

                    if ($customer) {
                        $set('kode_customer', $customer->kode_customer);
                    }
                })->afterStateHydrated(function ($state, callable $set) {
                    $customer = CustomerModel::find($state);

                    if ($customer) {
                        $set('kode_customer', $customer->kode_customer);
                    }
                }),
                TextInput::make('kode_customer')->columnSpan(2)->disabled()->dehydrated()->reactive(),
                Repeater::make('detail')
                    ->relationship()
                    ->schema([
                        Select::make('barang_id')->relationship('barang', 'nama_barang')->columnSpan([
                            'default' => 2,
                            'md' => 1,
                            'lg' => 1,
                            'xl' => 1,
                        ])->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $barang = Barang::find($state);

                                if ($barang) {
                                    $set('harga', $barang->harga_barang);
                                    $set('nama_barang', $barang->nama_barang);
                                }
                            }),
                        TextInput::make('nama_barang')->columnSpan([
                            'default' => 2,
                            'md' => 1,
                            'lg' => 1,
                            'xl' => 1,
                        ])->disabled()->dehydrated(),
                        TextInput::make('harga')->numeric()->columnSpan([
                            'default' => 2,
                            'md' => 1,
                            'lg' => 1,
                            'xl' => 1,
                        ])->prefix('Rp'),
                        TextInput::make('qty')->numeric()->columnSpan([
                            'default' => 2,
                            'md' => 1,
                            'lg' => 1,
                            'xl' => 1,
                        ])->reactive()->afterStateUpdated(function (Set $set, $state, Get $get) {
                            $tampungHarga = $get('harga');
                            $set('hasil_qty', intval($state * $tampungHarga));
                        }),
                        TextInput::make('hasil_qty')->numeric()->columnSpan(2),
                        TextInput::make('diskon')->numeric()->columnSpan([
                            'default' => 2,
                            'md' => 1,
                            'lg' => 1,
                            'xl' => 1,
                        ])->reactive()
                            ->afterStateUpdated(function (Set $set, $state, Get $get) {
                                $hasilQty = $get('hasil_qty');
                                $diskon = $hasilQty * ($state / 100);
                                $hasil = $hasilQty - $diskon;
                                $set('subtotal', intval($hasil));
                            }),
                        TextInput::make('subtotal')->numeric()->columnSpan([
                            'default' => 2,
                            'md' => 1,
                            'lg' => 1,
                            'xl' => 1,
                        ]),



                    ])->columnSpan(2),
                Textarea::make('ket_faktur')->columnSpan(2),
                TextInput::make('total')->columnSpan([
                    'default' => 2,
                    'md' => 1,
                    'lg' => 1,
                    'xl' => 1,
                ])->placeholder(function (Set $set, Get $get) {
                    $detail = collect($get('detail'))->pluck('subtotal')->sum();
                    if ($detail == null) {
                        $set('total', 0);
                    }
                    $set('total', $detail);
                }),
                TextInput::make('nominal_charge')->columnSpan([
                    'default' => 2,
                    'md' => 1,
                    'lg' => 1,
                    'xl' => 1,
                ])->reactive()->afterStateUpdated(function (Set $set, $state, Get $get) {
                    $total = $get('total');
                    $charge = $state / 100;
                    $totalFinal = $total + ($total * $charge);
                    $set('total_final', $totalFinal);
                    $set('charge', $charge);
                }),
                TextInput::make('charge')->columnSpan(2)->disabled()->dehydrated(),
                TextInput::make('total_final')->columnSpan(2),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_faktur'),
                TextColumn::make('tanggal_faktur'),
                TextColumn::make('kode_customer'),
                TextColumn::make('customer.nama_customer'),
                TextColumn::make('ket_faktur'),
                TextColumn::make('total')->formatStateUsing(fn(FakturModel $record): string => 'Rp' . number_format($record->total, 0, '.', '.'),),

                TextColumn::make('nominal_charge'),
                TextColumn::make('charge'),
                TextColumn::make('total_final'),

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
            'index' => Pages\ListFakturs::route('/'),
            'create' => Pages\CreateFaktur::route('/create'),
            'edit' => Pages\EditFaktur::route('/{record}/edit'),
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
