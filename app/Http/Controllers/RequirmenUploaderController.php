<?php

namespace App\Http\Controllers;

use App\Models\RequirementUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Validator;

class RequirmenUploaderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['store','update','destroy','getMyPost']);
    
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)                                                                                                                                                                                                                                                                                                   
    {
        
        $file = RequirementUploader::where('Service_id',$id)->get();

        if(is_null($file))
        {
            return response()->json('not found',404);
        }

        return response()->json($file);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RequirementUploader  $requirementUploader
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RequirementUploader $requirementUploader)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RequirementUploader  $requirementUploader
     * @return \Illuminate\Http\Response
     */
    public function destroy(RequirementUploader $requirementUploader)
    {
        //
    }
}
