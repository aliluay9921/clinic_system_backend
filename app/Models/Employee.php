<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, Uuids, SoftDeletes;

    protected $guarded = [];
    protected $date = ['deleted_at'];


    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }
}
