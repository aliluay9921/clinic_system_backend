<?php

namespace App\Models;

use App\Models\User;
use App\Models\Employee;
use App\Models\Representative;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Log extends Model
{
    use HasFactory;
    protected $table = "logs";
    protected $fillable = [
        "clinic_id",
        "target_id",
        "log_type",
        "note",
        "status",
        "value",
    ];




    public function employees()
    {
        return $this->belongsTo(Employee::class, "target_id");
    }
    public function users()
    {
        return $this->belongsTo(User::class, "target_id");
    }
    public function representative()
    {
        return $this->belongsTo(Representative::class, "target_id");
    }
}