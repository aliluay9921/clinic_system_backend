<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Clinic extends Model
{
    use HasFactory, Uuids;
    protected $guarded = [];


    public function users()
    {
        return $this->hasMany(User::class, 'clinic_id');
    }


    public function getLogoAttrebute()
    {
        $path = public_path() . $this->logo;
        $base64Data = base64_encode(file_get_contents($path));
        return $base64Data;
    }
}
