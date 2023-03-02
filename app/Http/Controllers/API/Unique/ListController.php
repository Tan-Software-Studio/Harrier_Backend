<?php

namespace App\Http\Controllers\API\Unique;

use App\Http\Controllers\Controller;
use App\Http\Traits\Candidate\CandMultipleSelectTrait;
use App\Http\Traits\CityTrait;
use App\Http\Traits\CountryTrait;
use App\Http\Traits\StateTrait;
use App\Models\Candidate;
use App\Models\Master\MstRegion;
use Illuminate\Http\Request;

class ListController extends Controller
{
    use CountryTrait, StateTrait, CityTrait, CandMultipleSelectTrait;

    public function countriesGet(Request $request)
    {
        if(auth()->check())
        {
            return self::countriesList($request, auth()->user()->role);
        }
        return self::countriesList($request, null);
        
    }

    public function statesGet(Request $request)
    {
        if(auth()->check())
        {
            return self::statesList($request, auth()->user()->role);
        }
        return self::statesList($request, null);
        
    }

    public function citiesGet(Request $request)
    {
        if(auth()->check())
        {
            return self::citiesList($request, auth()->user()->role);
        }
        return self::citiesList($request, null);
        
    }

    public function filesPath(Request $request)
    {
        $data = [
            'api_base_url' => env('APP_URL'),
            'cv_path' => asset('/').'storage/uploads/cv/',
            'profile_path' => asset('/').'storage/uploads/profile/',
            'logo_path' => asset('/').'storage/uploads/logo/',
            'job_attach_file_path' => asset('/').'storage/uploads/attach_file/',
            'privacy_policy' => asset('/').'assets/terms_privacy_doc/pdf/privacy_policy.pdf',
            'terms_of_business' => asset('/').'assets/terms_privacy_doc/pdf/terms_of_business.pdf',
            'terms_of_use' => asset('/').'assets/terms_privacy_doc/pdf/terms_of_use.pdf',
        ];
        return sendDataHelper("List", $data, ok());
    }

    public function legalTechTools()
    { 
        $c_uuid = '1cb81e91-9380-44fe-b7fd-ff4585a3b2fb';
        $legal_tech_tools = [
            "Betty Blocks",
            "HighQ - Competent",
            "Betty Blocks - ",
            "BRYTER - Beginner"
            
        ];
        $table = 'cand_legal_tech_tools';
        // $legal_tech_tools = [];
        return $this->multipleSelectUpsert($table, $c_uuid, $legal_tech_tools);
    }
}