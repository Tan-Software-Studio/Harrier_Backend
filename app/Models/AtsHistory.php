<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtsHistory extends Model
{
    use HasFactory;

    protected $table="ats_histories";

    protected $fillable = [
        'job_candidate_id',
        'c_job_status',
        'date',
        'sequence',
        'role',
    ];

    protected $hidden = [
        
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
