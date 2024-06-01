<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Job;
use App\Notifications\EmpolyerStatusChangeEmail;
use App\Notifications\RequestAccept;

trait EmployerTrait {

    public function validatorJob($request, $data = null)
    {
        $data = Validator::make($request, [
            'job_title' => 'required|unique:jobs,job_title,'.$data->emp->id,
            'office_location' => 'required',
            'role_overview' => 'required',
            'salary_range_start' => 'required',
            'salary_range_end' => 'required',
            'candidate_requirements' => 'required',
            'additional_benefits' => 'required',
        ]);

        if ($data->fails()) {
            return sendError($data->errors()->first(), [], errorValid());
        }
    }

    public function storeJob($request, $data = null)
    {
        $in = new Job();
        $in->emp_uid = $data->emp->uuid;
        $in->job_title = $request->job_title ?? null;
        $in->office_location = $request->office_location ?? null;
        $in->role_overview = $request->role_overview ?? null;
        $in->salary_range_start = $request->salary_range_start ?? null;
        $in->salary_range_end = $request->salary_range_end ?? null;
        $in->candidate_requirements = $request->candidate_requirements ?? null;
        $in->additional_benefits = $request->additional_benefits ?? null;
        $in->save();
    }

    public function mailsendLoginAccept($in)
    {
        $in->notify(new EmpolyerStatusChangeEmail($in));
    }

}