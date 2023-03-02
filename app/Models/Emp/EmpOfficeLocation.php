<?php

namespace App\Models\Emp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmpOfficeLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table="emp_office_locations";

    protected $fillable = [
        'emp_uuid',
        'location',
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
