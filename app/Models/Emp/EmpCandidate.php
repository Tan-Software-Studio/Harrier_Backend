<?php

namespace App\Models\Emp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpCandidate extends Model
{
    use HasFactory;

    protected $table="emp_candidates";

    protected $fillable = [
        'emp_uuid',
        'c_uuid'
    ];

    protected $hidden = [
        
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
