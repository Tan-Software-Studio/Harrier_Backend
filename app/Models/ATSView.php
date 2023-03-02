<?php

namespace App\Models;

use App\Models\Master\MstCurrency;
use App\Models\Unique\Country;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ATSView extends Model
{
    use HasFactory;
    protected $table="view_ats_data";

    protected $fillable = [
    ];

    protected $hidden = [
        
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function salary_range_start_symbol_list()    {   return $this->belongsTo(MstCurrency::class, 'salary_range_start_symbol', 'id'); }
    public function salary_range_end_symbol_list()    {   return $this->belongsTo(MstCurrency::class, 'salary_range_end_symbol', 'id'); }
    public function offer_salary_symbol_list()  {   return $this->belongsTo(MstCurrency::class, 'offer_salary_symbol', 'id');   }
    public function offer_bonus_commission_symbol_list()  {   return $this->belongsTo(MstCurrency::class, 'offer_bonus_commission_symbol', 'id');   }

    public function ats_history()
    {
        return $this->hasMany(AtsHistory::class, 'job_candidate_id', 'c_job_id');
    }
}
