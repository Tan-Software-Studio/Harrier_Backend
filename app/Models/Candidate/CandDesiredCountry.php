<?php

namespace App\Models\Candidate;

use App\Models\Unique\Country;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandDesiredCountry extends Model
{
    use HasFactory;

    protected $table="cand_desired_countries";

    protected $fillable = [
        'c_uuid',
        'title',
        'mst_id',
    ];

    protected $hidden = [
        
    ];

    protected $casts = [
    ];

    public function desired_employer_types_view()
    {
        return $this->belongsTo(Country::class, 'mst_id', 'id');
    }
}
