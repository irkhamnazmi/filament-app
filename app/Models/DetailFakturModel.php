<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailFakturModel extends Model
{
    protected $guarded = [];

    protected $table = 'detail';

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function faktur()
    {
        return $this->belongsTo(FakturModel::class, 'id');
    }
}
