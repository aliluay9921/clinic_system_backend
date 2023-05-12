<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyStore extends Model
{
    use HasFactory, Uuids;

    protected $guarded = [];


    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }
    public function represntatives()
    {
        return $this->belongsTo(Representative::class, 'representative_id');
    }
}