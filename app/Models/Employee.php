<?php

namespace App\Models;

use App\Traits\Uuids;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory, Uuids, SoftDeletes;

    protected $guarded = [];
    protected $date = ['deleted_at'];
    protected $appends = ['amount_withdraw_salary'];


    public function getAmountWithdrawSalaryAttribute()
    {
        return Log::where("target_id", $this->id)->where("log_type", 0)->whereBetween("created_at", [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum("value");
    }
    public function clinic()
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }
}
