<?php

namespace App\Models\Unique;

use App\Models\Master\MstRegion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "cities";

    protected $fillable = [
        'city_name',
        'state_id',
        'status',
    ];

    protected $appends = ['state_list',
    //  'country_list'
    ];

    protected $hidden = [
        'status',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function state_list()
    {
        return $this->belongsTo(MstRegion::class, 'state_id', 'id');
    }
}
