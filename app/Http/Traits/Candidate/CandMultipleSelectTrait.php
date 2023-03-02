<?php

namespace App\Http\Traits\Candidate;


use App\Models\Candidate\CandLegalTechTools;
use App\Models\Master\MstCulturalBackground;
use App\Models\Master\MstCustomerType;
use App\Models\Master\MstLanguage;
use App\Models\Master\MstWorkingArrangements;
use App\Models\Unique\Country;
use Illuminate\Support\Facades\DB;

trait CandMultipleSelectTrait {
    public function multipleSelectUpsertTitle($table, $c_uuid, $id_title)
    {
        if(@$id_title && count($id_title))
        {            
            DB::table($table)->where('c_uuid', $c_uuid)->whereNotIn('title', $id_title)->delete();
            foreach($id_title as $title)
            {
                if(!DB::table($table)->where(['title' =>  $title, 'c_uuid' => $c_uuid])->first())
                {
                    $input['title'] = $title;
                    $input['c_uuid'] = $c_uuid;
                    DB::table($table)->insert($input);
                }
            }
        }else{
            DB::table($table)->where('c_uuid', $c_uuid)->delete();
        }
        return DB::table($table)->where('c_uuid',$c_uuid)->get();
    }

    public function multipleSelectUpsertId($table, $c_uuid, $ids)
    {
        if(@$ids && count($ids))
        {            
            switch ($table) {
                case 'cand_working_arrangements':
                    $data_list = MstWorkingArrangements::whereIn('id', $ids)->get();
                    break;
                case 'cand_mst_cultural_backgrounds':
                    $data_list = MstCulturalBackground::whereIn('id', $ids)->get();
                    break;
                case 'cand_desired_countries':
                    $data_list = Country::whereIn('id', $ids)->get();
                    break;
                case 'cand_mst_customer_types':
                    $data_list = MstCustomerType::whereIn('id', $ids)->get();
                    break;
                case 'cand_mst_languages':
                    $data_list = MstLanguage::whereIn('id', $ids)->get();
                    break;                    
                    
                default:
                    break;
            }
            $this->storeRemove($table, $c_uuid, $data_list); 
        }else{
            DB::table($table)->where('c_uuid', $c_uuid)->delete();
        }
        return DB::table($table)->where('c_uuid',$c_uuid)->get();
    }

    public function storeRemove($table, $c_uuid, $data_list)
    {
        if(@$data_list && count($data_list)){
            DB::table($table)->where('c_uuid', $c_uuid)->whereNotIn('mst_id', $data_list->pluck('id'))->delete();
            foreach($data_list as $list)
            {
                if(!DB::table($table)->where(['mst_id' =>  $list->id, 'c_uuid' => $c_uuid])->first())
                {
                    $input['title'] = $list->title;
                    $input['mst_id'] = $list->id;
                    $input['c_uuid'] = $c_uuid;
                    DB::table($table)->insert($input);
                }
            }
        }else{
            DB::table($table)->where('c_uuid', $c_uuid)->delete();
        }
    }
}
