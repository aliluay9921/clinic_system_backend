<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory, Uuids;

    protected $guarded = [];
    protected $with = ["represntatives"];

    public function represntatives()
    {
        return $this->belongsTo(Representative::class, 'representative_id');
    }
    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }
}
