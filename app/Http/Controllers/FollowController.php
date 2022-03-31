<?php

namespace App\Http\Controllers;

use App\Models\User;

use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Validator;

class FollowController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['store','show','index']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user= Auth::user();

        $isflowwing = Follow::where('User_id',$user->id)->get();
        $compnays = [];
        foreach ($isflowwing as $office) {
            array_push( $compnays,User::find($office->Service_id));
        }
       

        return response()->json($compnays);
  
   
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $user= Auth::user();

        if(!$user->tokenCan('server:user'))
        {
            return response()->json('not auth',403);
        }

        $isflowwing = Follow::where('Service_id',$request->id)->where('User_id',$user->id)->first();
  
        if(!is_null($isflowwing))
        {
            $isflowwing->delete();

            return response()->json(false);
        }

        $floww = Follow::create([
            "User_id"=>$user->id,
            "Service_id"=> $request->id
        ]);

return response()->json(true);




    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user= Auth::user();

        $isflowwing = Follow::where('Service_id',$id)->where('User_id',$user->id)->first();
  
        if(is_null($isflowwing))
        {
            return response()->json(false);
        }

        return response()->json(true);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Follow  $follow
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Follow $follow)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Follow  $follow
     * @return \Illuminate\Http\Response
     */
    public function destroy(Follow $follow)
    {
        //
    }
}
