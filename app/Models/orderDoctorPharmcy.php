<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orderDoctorPharmcy extends Model
{
    use HasFactory, Uuids;
    protected $guarded = [];
    protected $with = ["booking", "doctor"];

    public function booking()
    {
        return $this->belongsTo(Booking::class, "booking_id");
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, "doctor_id");
    }
}
