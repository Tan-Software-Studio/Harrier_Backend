<?php

namespace App\Models\Candidate;

use App\Models\Candidate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandWorkingArrangements extends Model
{
    use HasFactory;

    protected $table="cand_working_arrangements";

    protected $fillable = [
        'c_uuid',
        'title',
        'mst_id'
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
