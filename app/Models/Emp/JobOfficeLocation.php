<?php

namespace App\Models\Emp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOfficeLocation extends Model
{
    use HasFactory;

    protected $table="job_office_locations";

    protected $fillable = [
        'job_id',
        'office_location_id',
    ];

    protected $hidden = [
        
    ];

    protected $casts = [
    ];

    public function emp_office_locations()
    {
        return $this->belongsTo(EmpOfficeLocation::class, 'office_location_id', 'id');
    }
}
