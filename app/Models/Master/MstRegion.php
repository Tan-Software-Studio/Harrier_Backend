<?php

namespace App\Models\Master;

use App\Models\Unique\Country;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MstRegion extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "mst_regions";

    protected $fillable = [
        'state_name',
        'country_id',
        'status',
    ];

    // protected $appends = ['country_list'];

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

    protected $appends = ['country_name'];

    public function country_list()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    // /* desired employer types */
    public function getCountryNameAttribute()
    {
        if(!empty($this->country_id)){
            return Country::where('id', $this->country_id)->select('country_name')->value('country_name');
        }else{
            return null;
        }
    }
}
