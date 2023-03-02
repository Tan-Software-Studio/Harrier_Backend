<?php

namespace App\Http\Traits;

use App\Models\Candidate;
use App\Models\Master\MstCandidateStatus;
use App\Models\Master\MstChannel;
use App\Models\Master\MstCulturalBackground;
use App\Models\Master\MstCustomerType;
use App\Models\Master\MstEmployerType;
use App\Models\Master\MstFaith;
use App\Models\Master\MstGender;
use App\Models\Master\MstLanguage;
use App\Models\Master\MstLegalTechTool;
use App\Models\Master\MstQualification;
use App\Models\Master\MstRegion;
use App\Models\Master\MstSchoolType;
use App\Models\Master\MstSex;
use App\Models\Master\MstSexualOrientation;
use App\Models\Master\MstTechTools;
use App\Models\Master\MstWorkingArrangements;
use App\Models\Unique\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

trait FilterTrait {
    public function candidateQuickSearchOptions(Request $request)
    {
        $res1 = [
            [   'select' => 'job_title', 'name' => 'Job Title', 'list' => $this->jobTitleList()],
            [   'select' => 'mst_employer_types', 'name' => 'Employer Type', 'list' => MstEmployerType::get(['id', 'title'])],
            [   'select' => 'desired_employer_type', 'name' => 'Desired Employer Type', 'list' => MstEmployerType::get(['id', 'title'])],
            [   'select' => 'time_in_industry', 'name' => 'Time in industry', 'list' =>  time_in_ind() ],
            [   'select' => 'time_in_current_role', 'name' => 'Time in current role', 'list' => time_in_role_opt() ],            
            [   'select' => 'line_management', 'name' => 'Line Management', 'list' => line_manageemnt() ],
            [   'select' => 'mst_countries', 'name' => 'Current Country', 'list' => Country::get(['id', 'country_name as title']) ],
            [   'select' => 'desired_country', 'name' => 'Desired Country', 'list' => Country::get(['id', 'country_name as title']) ],
            [   'select' => 'mst_regions', 'name' => 'Current Region', 'list' => MstRegion::get(['id', 'state_name as title']) ],
            [   'select' => 'desired_region', 'name' => 'Desired Region', 'list' => MstRegion::get(['id', 'state_name as title']) ],
            [   'select' => 'mst_working_arrangements', 'name' => 'Working Arrangements', 'list' => MstWorkingArrangements::get(['id', 'title']) ],
            [   'select' => 'desired_working_arrangements', 'name' => 'Desired Working Arrangements', 'list' => MstWorkingArrangements::get(['id', 'title']) ],
            [   'select' => 'notice_period', 'name' => 'Notice Period', 'list' => notice_period_weeks() ],
            
            [   'select' => 'mst_customer_types', 'name' => 'Customer Type', 'list' => MstCustomerType::get(['id', 'title']) ],
            [   'select' => 'mst_qualifications', 'name' => 'Qualifications', 'list' => MstQualification::get(['id', 'title']) ],
            [   'select' => 'mst_legal_tech_tools', 'name' => 'LegalTech Tools', 'list' => MstLegalTechTool::get(['id', 'title'])],
            [   'select' => 'mst_tech_tools', 'name' => 'Tech Tools', 'list' => MstTechTools::get(['id', 'title']) ],
            [   'select' => 'desired_salary', 'name' => 'Desired Salary', 'list' => [] ],
            [   'select' => 'desired_bonus_or_commission', 'name' => 'Desired Bonus/Commission', 'list' => [] ],
            
            [   'select' => 'freelance_current', 'name' => 'Current Freelance', 'list' => yesNo() ],
            [   'select' => 'freelance_future', 'name' => 'Open to Freelance', 'list' => yesNo() ],
            [   'select' => 'freelance_daily_rate', 'name' => 'Day Rate', 'list' => [] ],
            //Legal
            [   'select' => 'law_degree', 'name' => 'Law Degree', 'list' => yesNo() ],
            [   'select' => 'qualified_lawyer', 'name' => 'Qualified Lawyer', 'list' => yesNo() ],
            [   'select' => 'jurisdiction', 'name' => 'Jurisdiction', 'list' => $this->jurisdictionList() ],
            [   'select' => 'pqe', 'name' => 'Post-Qualified Experience', 'list' => pqe_opt() ],
            [   'select' => 'legal_experience', 'name' => 'Legal Specialism', 'list' => [] ],
            [   'select' => 'area_of_law', 'name' => 'Area of Law', 'list' => [] ], 
            
            // [   'select' => 'mst_channels', 'name' => 'Channel', 'list' => MstChannel::get(['id', 'title']) ], 
            // [   'select' => 'mst_cultural_backgrounds', 'name' => 'Cultural Background', 'list' => MstCulturalBackground::get(['id', 'title']) ],
            // [   'select' => 'mst_faiths', 'name' => 'Faith', 'list' => MstFaith::get(['id', 'title']) ],
            // [   'select' => 'mst_genders', 'name' => 'Gender', 'list' => MstGender::get(['id', 'title']) ],
            // [   'select' => 'mst_school_types', 'name' => 'School Type', 'list' => MstSchoolType::get(['id', 'title']) ],
            // [   'select' => 'mst_sexes', 'name' => 'Sex', 'list' => MstSex::get(['id', 'title']) ],
            // [   'select' => 'mst_sexual_orientations', 'name' => 'Sexual Orientation', 'list' => MstSexualOrientation::get(['id', 'title']) ],
        ];
        $res2 = [];
        if(auth()->check() && auth()->user()->role == roleEmp())
        {
            $res2 = [
                [   'select' => 'status', 'name' => 'Status', 'list' => MstCandidateStatus::get(['id', 'title']) ],
                [   'select' => 'legaltech_vendor_or_consultancy', 'name' => 'Legaltech Vendor / Consultancy', 'list' => yesNo()],
                [   'select' => 'deal_size', 'name' => 'Average Deal Size', 'list' => []],
                [   'select' => 'sales_quota', 'name' => 'Sales Quota', 'list' => []],
                [   'select' => 'languages', 'name' => 'Languages', 'list' => MstLanguage::get(['id', 'title'])],
            ];
            $response = Arr::collapse([$res1, $res2]);
        }
        $response = Arr::collapse([$res1, $res2]);
        
        return sendDataHelper("List", $response, ok());
    }

    public function moreCandidateQuickSearchOptions(Request $request)
    {
        $data = [ 
            [   'select' => 'time_in_current_role_start', 'name' => 'Time in current role', 'list' => [] ],
            [   'select' => 'time_in_current_role_end', 'name' => 'Time in current role', 'list' => [] ],
            
            [   'select' => 'time_in_industry_start', 'name' => 'Time in industry', 'list' => [] ],
            [   'select' => 'time_in_industry_end', 'name' => 'Time in industry', 'list' => [] ],
            
            [   'select' => 'pqe', 'name' => 'Post-Qualified Experience', 'list' => [] ],
            [   'select' => 'notice_period', 'name' => 'Notice Period', 'list' => [] ],
            
            [   'select' => 'desired_salary', 'name' => 'Desired Salary', 'list' => [] ],
            
            [   'select' => 'line_management', 'name' => 'Line Management', 'list' => [] ],
            
        ];

        if(canGUEST() || canADMIN())
        {
            $array = [   'select' => 'current_salary', 'name' => 'Current Salary', 'list' => [] ] ;
            array_push($data, $array);
        }
        return $response = $data; 

        return sendDataHelper("List", $response, ok());
    }

    public function jobTitleList()
    {
        return Candidate::distinct('job_title')->get('job_title as title')->makeHidden(['cv_path','profile_path', 'legal_tech_tools_list', 'time_in_current_role_diff', 'time_in_industry_diff', 'pqe_diff']);
    }

    public function jurisdictionList()
    {
        return Candidate::distinct('jurisdiction')->get('jurisdiction as title')->makeHidden(['cv_path','profile_path', 'legal_tech_tools_list', 'time_in_current_role_diff', 'time_in_industry_diff', 'pqe_diff']);
    }

    public function fields($id, $title, $k_name, $list)
    {
        $f['id'] = $id;
        $f['title'] = $title;
        $loop_dataArr = array();
        // for ($i=0; $i < count($list); $i++) {
        foreach ($list as $key => $value) {
            $arr[$k_name] = $value->$k_name;
            array_push($loop_dataArr, $arr);
        }
        $f['list'] = $loop_dataArr;
        // }
        return $f; 
    }
}