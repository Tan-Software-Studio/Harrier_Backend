<?php

namespace App\Models\Emp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmpWorkingSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $table="emp_working_schedules";

    protected $fillable = [
        'emp_uuid',
        'schedule',
        'status',
        'deleted_at'
    ];

    protected $hidden = [
        
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
