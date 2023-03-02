<?php

namespace App\Models\Candidate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandMstCulturalBackground extends Model
{
    use HasFactory;

    protected $table="cand_mst_cultural_backgrounds";

    protected $fillable = [
        'c_uuid',
        'title',
    ];

    protected $hidden = [
        
    ];

    protected $casts = [
    ];

    public function candidate_list()
    {
        return $this->belongsTo(Candidate::class, 'c_uuid', 'uuid');
    }
}
