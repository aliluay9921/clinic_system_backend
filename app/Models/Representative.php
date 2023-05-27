<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Representative extends Model
{
    use HasFactory, Uuids, SoftDeletes;

    protected $guarded = [];
    protected $date = ["deleted_at"];
    protected $appends = ['paided', 'depted'];


    public function getpaidedAttribute()
    {
        return Log::where("target_id", $this->id)->where("log_type", 2)->where("status", 0)->sum("value");
    }
    public function getdeptedAttribute()
    {
        return Store::where("representative_id", $this->id)->sum("buy_price");
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }
}
