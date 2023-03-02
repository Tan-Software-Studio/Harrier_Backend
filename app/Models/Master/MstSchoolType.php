<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MstSchoolType extends Model
{
    use HasFactory, SoftDeletes;
    protected $table="mst_school_types";

    protected $fillable = [
        'title',
        'deleted_at'
    ];

    protected $hidden = [
        
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
