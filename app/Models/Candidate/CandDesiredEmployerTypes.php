<?php

namespace App\Models\Candidate;

use App\Models\Master\MstEmployerType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandDesiredEmployerTypes extends Model
{
    use HasFactory;

    protected $table="cand_desired_employer_types";

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
        return $this->belongsTo(MstEmployerType::class, 'mst_id', 'id');
    }
}
