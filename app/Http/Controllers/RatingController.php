<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Validator;

class RatingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['store','update','destroy']);
    }



    public $rule = [
        "Service_id"=>"required|integer|max:255|min:1",
        "Start_n"=>"required|integer|max:255|min:1",

    ];

/**
 * Get the error messages for the defined validation rules.
 *
 * @return array
 */

public function messages()
    {
        return [
            'Service_id.required' => 'يجب كتابة اسم ',
            'Service_id.integer' => 'يجب ان يكون العنوان نص فقط',
            "Service_id.max"=>"يجب ان لا يزيد عنوان النص عن 25 حرف",
            "Service_id.min"=>"يجب ان لا يقل عنوان النص عن 3 حرف",

            'Start_n.required' => 'يجب كتابة اسم ',
            'Start_n.integer' => 'يجب ان يكون العنوان نص فقط',
            "Start_n.max"=>"يجب ان لا يزيد عنوان النص عن 25 حرف",
            "Start_n.min"=>"يجب ان لا يقل عنوان النص عن 3 حرف",



        ];
    }






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
        $user =  Auth::user();
        
        if(!$user->tokenCan('server:user'))
        {
            return response()->json('not auth',403);
        }

        $validation = Validator::make($request->all(),$this->rule,$this->messages());

        if($validation->fails())
        {
            return response()->json($validation->errors(),401);
        }
        $rating = $request->all();
        $rating['User_id'] = $user->id;
        Rating::create($rating);

        return response()->json('rating done');

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $ratin =  Rating::where('Service_id',$id)->get();
        //return response()->json($ratin);
        $fivestar  = $this->getstar($ratin,5);
        $fourstar  = $this->getstar($ratin,4);
        $threestar = $this->getstar($ratin,3);
        $twostar  =  $this->getstar($ratin,2);
        $onestar  =  $this->getstar($ratin,1);
       // return response()->json($fourstar);
        $theRating = [
            "5"=>count($fivestar),
            "4"=>count($fourstar),
            "3"=>count($threestar),
            "2"=>count($twostar),
            "1"=>count($onestar),
        ];

        return response()->json($theRating);
    }

    public function getstar($arry,$key)
    {
        $result =  array();
        foreach ($arry as $value) {
            if($value->Start_n == $key)
            {
             array_push($result,$value)  ; 
            }
        }
        return $result;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Rating  $rating
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Rating $rating)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Rating  $rating
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rating $rating)
    {
        //
    }
}
