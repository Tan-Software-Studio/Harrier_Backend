<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CandidateComment extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $c_uuid)
    {
        try {
            $results = DB::table('candidate_comments')->where('c_uuid', $c_uuid)->get();
            return sendDataHelper('List.', $results, ok());
        } catch (\Throwable $th) {
            return sendErrorHelper('Error', $th->getMessage(), error());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            if(respValid($request)) { return respValid($request); }  /* response required validation */
            $request = decryptData($request['response']); /* Dectrypt  **/
            
            $data = Validator::make($request, [
                'uuid' => 'required|exists:candidates,uuid',
                'note' => 'required|nullable'
            ]);

            if ($data->fails()) {
                return sendError($data->errors()->first(), [], errorValid());
            }else{
                $request = (object) $request;
                $input['note'] = $request->note;
                $input['c_uuid'] = $request->uuid;
                $response = DB::table('candidate_comments')->insert($input);
                if ($response) {
                    DB::commit();
                    return sendDataHelper('Details updated.', $response, ok());
                } else {
                    DB::rollBack();
                    return sendError('Something went wrong', [], error());
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $bug = $th->getMessage();
            return sendErrorHelper('Error', $bug, error());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
