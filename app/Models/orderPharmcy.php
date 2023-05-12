<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orderPharmcy extends Model
{
    use HasFactory, Uuids;
    protected $guarded = [];
    protected $with = ["medicans"];


    public function medicans()
    {
        return $this->belongsToMany(PharmacyStore::class, 'medican_order', 'order_id', 'medican_id');
    }
}