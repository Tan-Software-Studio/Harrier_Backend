<?php

namespace App\Models\Emp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobWorkingSchedule extends Model
{
    use HasFactory;
    protected $table="job_working_schedules";

    protected $fillable = [
        'job_id',
        'working_schedule_id',
    ];

    protected $hidden = [
        
    ];

    protected $casts = [
    ];

    public function emp_working_schedule()
    {
        return $this->belongsTo(EmpWorkingSchedule::class, 'working_schedule_id', 'id');
    }
}
