<?php

namespace App\Http\Traits;

use App\Models\Candidate\CandDesiredEmployerTypes;
use App\Models\Master\MstEmployerType;
use Illuminate\Support\Facades\DB;

trait CandDesEmpTypesTrait {
    public function updateAndCreateDesiredEmployerTypes($c_uuid, $ids)
    {
        if(@$ids && count($ids))
        {
            $get_ids =  MstEmployerType::whereIn('id', $ids)->pluck('id');
            if(count($get_ids))
            {
                $dataIds =  CandDesiredEmployerTypes::where('c_uuid', $c_uuid)
                ->whereIn('mst_id', $get_ids)
                ->pluck('mst_id');
    
                $delete = CandDesiredEmployerTypes::where('c_uuid', $c_uuid)
                    ->whereNotIn('mst_id', $get_ids)
                    ->delete();
            
                foreach($get_ids as $single_id)
                {
                    if(!CandDesiredEmployerTypes::where(['mst_id' =>  $single_id, 'c_uuid' => $c_uuid])->first())
                    {
                        $input['mst_id'] = $single_id;
                        $input['c_uuid'] = $c_uuid;
                        $response = DB::table('cand_desired_employer_types')->insert($input);
                    }
                }
            }
        }else{
            $delete = CandDesiredEmployerTypes::where('c_uuid', $c_uuid)
                ->whereNotIn('mst_id', [])
                ->delete();
        }
    }
}
