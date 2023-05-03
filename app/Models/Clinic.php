<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    use HasFactory, Uuids;
    protected $guarded = [];


    public function users()
    {
        return $this->hasMany(User::class, 'clinic_id');
    }
}
