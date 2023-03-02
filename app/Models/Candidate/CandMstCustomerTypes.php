<?php

namespace App\Models\Candidate;

use App\Models\Master\MstCustomerType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandMstCustomerTypes extends Model
{
    use HasFactory;

    protected $table="cand_mst_customer_types";

    protected $fillable = [
        'c_uuid',
        'title',
        'mst_id',
    ];

    protected $hidden = [
        
    ];

    protected $casts = [
    ];

    public function mst_customer_types_list()
    {
        return $this->belongsTo(MstCustomerType::class, 'mst_id', 'id');
    }
}
