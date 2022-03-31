<?php

namespace App\Http\Controllers;

use App\Models\imgetorequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class filetoreqController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['store', 'update','show', 'index']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $imgtoreq  = $request->FormFiles;
        
        foreach ($imgtoreq as $img) {
            $newimge = [];
            $newimge['user_id'] = $user->id;
            $newimge['img_id'] = $img['value'];
            $newimge['input'] = $img['input'];
            $newimge['req_id'] = $request->req_id;
            $newimge['combany_id'] = $request->combany_id;

            imgetorequest::create($newimge);
        }


        return response()->json([$imgtoreq]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user  = Auth::user();

        if ($user->tokenCan('server:company')) {
            $imgreq =  imgetorequest::where('combany_id',$user->id)->where('req_id', $id)->get();;
        }  else
        {
            $imgreq =  imgetorequest::where('user_id',$user->id)->where('req_id', $id)->get();;
    
        }      


        return response()->json($imgreq);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $imgtogile = imgetorequest::where('req_id', $id)->get();

        $requestform =  $request->FormFiles;

        foreach ($imgtogile as $key) {

            $imgform = imgetorequest::find($key->id);

            $id = $this->searchForId($key->input, $requestform);

            $imgform->img_id = $id['value'];

            $imgform->save();
        }

        return response('updated');
    }


    function searchForId($id, $array)
    {
        foreach ($array as $key) {
            if ($key['input'] === $id) {
                return $key;
            }
        }
        return null;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\imgetorequest  $imgetorequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(imgetorequest $imgetorequest)
    {
        $imgreq =  imgetorequest::where('req_id', $id)->get();;

        return response()->json($imgreq);
    }
}
