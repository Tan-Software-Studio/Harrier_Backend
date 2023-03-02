<?php

namespace App\Http\Traits;

use App\Models\AtsHistory;
use App\Models\Candidate;
use App\Models\Candidate\CandDesiredEmployerTypes;
use App\Models\Candidate\CandMstCulturalBackground;
use App\Models\Candidate\CandMstCustomerTypes;
use App\Models\Candidate\CandMstLanguage;
use App\Models\Candidate\CandQualifications;
use App\Models\Candidate\CandWorkingArrangements;
use App\Models\JobCondidate;
use Illuminate\Support\Facades\DB;

trait MstTrait {
    
    public function mstCheckTrue($tblname, $id) /* Cities list */
    {   
        $result = null;
        switch ($tblname) {
            case 'mst_regions':
                $tbl = DB::table($tblname)->select('id')->where('id', $id)->first();
                if($tbl)
                {
                    $result = Candidate::select('id')->where('current_region', $tbl->id)->first();
                }
                break;
            case 'countries':
                $tbl = DB::table($tblname)->whereNotIn('', [noContry()])->where('id', $id)->first();
                if($tbl)
                {
                    $result = Candidate::where('current_country', $tbl->id)->first();
                    if(!$result)
                    {
                        $result = DB::table('cand_desired_countries')->where('mst_id', $id)->first();
                    }
                }
                break;
            case 'mst_candidate_job_statuses':
                $tbl = DB::table($tblname)->where('id', $id)->first();
                if($tbl)
                {
                    $result = AtsHistory::where('c_job_status', $tbl->id)->first();
                    if(!$result)
                    {
                        $result = JobCondidate::where('c_job_status', $tbl->id)->first();
                    }
                }
                break;
            case 'mst_candidate_statuses':
                $tbl = DB::table($tblname)->where('id', $id)->first();
                if($tbl)
                {
                    $result = Candidate::where('status', $tbl->id)->first();
                }
                break;
            case 'mst_employer_types':
                $tbl = DB::table($tblname)->where('id', $id)->first();
                if($tbl)
                {
                    $result = CandDesiredEmployerTypes::where('mst_id', $tbl->id)->first();
                    if(!$result)
                    {
                       return $result = Candidate::where('employer_type', $tbl->id)->first();
                    }
                }
                
                break;
            case 'mst_customer_types':
                $tbl = DB::table($tblname)->where('id', $id)->first();
                if($tbl)
                {
                    $result = CandMstCustomerTypes::where('mst_id', $tbl->id)->first();
                }
                break;
            case 'mst_languages':
                $tbl = DB::table($tblname)->where('id', $id)->first();
                if($tbl)
                {
                    $result = CandMstLanguage::where('mst_id', $tbl->id)->first();
                }
                break;
            case 'mst_qualifications':
                break;
            case 'mst_legal_tech_tools':
                break;
            case 'mst_tech_tools':
                break;
            case 'mst_genders':
                $tbl = DB::table($tblname)->select('id')->where('id', $id)->first();
                if($tbl)
                {
                    $result = Candidate::select('id')->where('gender_identity', $tbl->id)->first();
                }
                break;
            case 'mst_sexes':
                $tbl = DB::table($tblname)->select('id')->where('id', $id)->first();
                if($tbl)
                {
                    $result = Candidate::select('id')->where('sex', $tbl->id)->first();
                }
                break;
            case 'mst_working_arrangements':
                $tbl = DB::table($tblname)->where('id', $id)->first();
                if($tbl)
                {
                    $result = CandWorkingArrangements::where('mst_id', $tbl->id)->first();
                    if(!$result)
                    {
                        $result = Candidate::select('id')->where('working_arrangements', $tbl->id)->first();
                    }
                }
                break;
            case 'mst_cultural_backgrounds':
                $tbl = DB::table($tblname)->where('id', $id)->first();
                if($tbl)
                {
                    $result = CandMstCulturalBackground::where('mst_id', $tbl->id)->first();
                    if(!$result)
                    {
                        $result = Candidate::select('id')->where('working_arrangements', $tbl->id)->first();
                    }
                }
                break;
            case 'mst_faiths':
                $tbl = DB::table($tblname)->where('id', $id)->first();
                if($tbl)
                {
                    $result = Candidate::select('id')->where('faith', $tbl->id)->first();
                }
                break;
            case 'mst_channels':
                $tbl = DB::table($tblname)->where('id', $id)->first();
                if($tbl)
                {
                    $result = Candidate::select('id')->where('channel', $tbl->id)->first();
                }
                break;
            case 'mst_school_types':
                $tbl = DB::table($tblname)->where('id', $id)->first();
                if($tbl)
                {
                    $result = Candidate::select('id')->where('school_type', $tbl->id)->first();
                }
                break;
            case 'mst_sexual_orientations':
                $tbl = DB::table($tblname)->where('id', $id)->first();
                if($tbl)
                {
                    $result = Candidate::select('id')->where('sexual_orientation', $tbl->id)->first();
                }
                break;
            case 'mst_main_earner_occupations':
                $tbl = DB::table($tblname)->where('id', $id)->first();
                if($tbl)
                {
                    $result = Candidate::select('id')->where('main_earner_occupation', $tbl->id)->first();
                }
                break;                
            default:
                // $result = 1; 
                break;
        }
        return $result;
    }
}
