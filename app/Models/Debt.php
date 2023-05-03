<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    use HasFactory, Uuids;

    protected $guarded = [];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
