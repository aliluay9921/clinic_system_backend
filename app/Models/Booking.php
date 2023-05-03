<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory, Uuids;

    protected $guarded = [];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }



    public function debts()
    {
        return $this->hasMany(Debt::class, 'booking_id');
    }
}
