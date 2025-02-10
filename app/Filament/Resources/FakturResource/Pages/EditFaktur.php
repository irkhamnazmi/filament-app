<?php

namespace App\Filament\Resources\FakturResource\Pages;

use App\Filament\Resources\FakturResource;
use App\Models\PenjualanModel;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFaktur extends EditRecord
{
    protected static string $resource = FakturResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        // Runs before the form fields are saved to the database.
    }

    protected function afterSave(): void
    {

        $penjualan = PenjualanModel::where('faktur_id', $this->record->id)->first();
        // if ($penjualan > 0) {
        //     $penjualan->update([
        //         'kode' => $this->record->kode_faktur,
        //         'tanggal' => $this->record->tanggal_faktur,
        //         'jumlah' => $this->record->total,
        //         'customer_id' => $this->record->customer_id,
        //         'keterangan' => $this->record->ket_faktur,
        //         'status' => 0,
        //     ]);
        // } else {

        //     PenjualanModel::create([
        //         'kode' => $this->record->kode_faktur,
        //         'tanggal' => $this->record->tanggal_faktur,
        //         'jumlah' => $this->record->total,
        //         'customer_id' => $this->record->customer_id,
        //         'faktur_id' => $this->record->id,
        //         'keterangan' => $this->record->ket_faktur,
        //         'status' => 0,
        //     ]);
        // }
    }
}
