<?php

namespace App\Http\Controllers;

use App\Models\Requests;
use App\Models\User;
use App\Models\UploadedFile;
use App\Models\imgetorequest;
use App\Models\Messages;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Validator;

class RequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['store','isViewedRequests', 'update','show', 'index']);
    }

    public $rule = [
        "Service_id" => "required|integer|max:255|min:1",
        "Request_des" => "required|string|max:255|min:1",
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
            "Service_id.max" => "يجب ان لا يزيد عنوان النص عن 25 حرف",
            "Service_id.min" => "يجب ان لا يقل عنوان النص عن 3 حرف",


            'Request_des.required' => 'يجب كتابة اسم ',
            'Request_des.string' => 'يجب ان يكون العنوان نص فقط',
            "Request_des.max" => "يجب ان لا يزيد عنوان النص عن 25 حرف",
            "Request_des.min" => "يجب ان لا يقل عنوان النص عن 3 حرف",


        ];
    }




    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->tokenCan('server:company')) {
            $requests  = Requests::where('combany_id', $user->id)->get();
            foreach ($requests as $key) {
                $key->Service;
                $key->req;
            }
        } else {

            $requests  = Requests::where('user_id', $user->id)->get();
            foreach ($requests as $key) {

               // $message = Messages::where('User_id', $user->id)->where('req_id', $key->id)->where('is_viewed', 0)->get();
                $message = Messages::where('req_id', $key->id)->

                where('is_viewed', 0)->get();
               
                $key->Service;
                $key->combany;
                if($key->done_img)
                {
                    $key->DoneImge;

                }
                
                $key->msg = count($message);
            }
        }

        if (is_null($requests)) {
            return response()->json('not found', 404);
        }

        return response()->json($requests->all());
    }

    public function getDataImge()
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
        if (!$user->tokenCan('server:user')) {
            return response()->json('not premeted for this op', 403);
        }

        $validation = Validator::make($request->all(), $this->rule, $this->messages());

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        $requests  = $request->all();

        $useradmin =  User::where('username', $request['combany_id'])->first();
        $requests['combany_id'] = $useradmin->id;
        // return $requests;

        $requests['user_id'] = $user->id;
        $requests['isDone'] = 0;
        $requests['is_viewed'] = true;
        unset($requests['Jwt']);
        $imgtoreq  = $requests['FormFiles'];

        unset($requests['FormFiles']);

        $thesavedreq =   Requests::create($requests);



        foreach ($imgtoreq as $img) {

            if($img['value']){ 

            $newbackimg = UploadedFile::find($img['value']);

            if ($newbackimg->user_id !== $user->id) {
                return response()->json('not your imge', 401);
            }}

            $newimge = [];
            $newimge['user_id'] = $user->id;
            $newimge['img_id'] = $img['value'];
            $newimge['input'] = $img['input'];
            $newimge['req_id'] = $thesavedreq->id;
            $newimge['combany_id'] = $thesavedreq->combany_id;

            //   return $newimge;
            imgetorequest::create($newimge);
            //  return $bewitr;

        }


        return response()->json($thesavedreq);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();

        if ($user->tokenCan('server:company')) {

            $requests = Requests::find($id);
        } else {

            $requests = Requests::find($id);
        }

        if (is_null($requests)) {
            return response('not found', 404);
        }

        return response()->json($requests);
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
        $user = Auth::user();
        $requests = Requests::find($id);

        if ($user->id != $requests->User_id && $user->id != $requests->combany_id) {
            return response()->json('un authraized', 403);
        }

        if ($user->tokenCan('server:company')) {

            $requests->isDone = $request->isDone;
            
            $requests->Request_des = $request->Request_des;

            $requests->save();

            return response()->json('request updated');
        }


        $validation = Validator::make($request->all(), $this->rule, $this->messages());

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        $requests->Request_des = $request->Request_des;

        $requestform = $request['FormFiles'];

        unset($requests['FormFiles']);

        //    $thesavedreq =   Requests::create($requests);


        imgetorequest::where('req_id', $requests->id)->delete();


        foreach ($requestform as $img) {

            if($img['value']){ 

            $newbackimg = UploadedFile::find($img['value']);

            if ($newbackimg->user_id !== $user->id) {
                return response()->json('not your imge', 403);
            }}

            $newimge = [];
            $newimge['user_id'] = $user->id;
            $newimge['img_id'] = $img['value'];
            $newimge['input'] = $img['input'];
            $newimge['req_id'] = $requests->id;
            $newimge['combany_id'] = $requests->combany_id;

            //   return $newimge;
            imgetorequest::create($newimge);
            //  return $bewitr;

        }


        $requests->save();

        return response()->json($requests);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Requests::find($id);

        if ($order->isDone !== 0) {
            return response()->json('no');
        }

        $order->isDone = 2;
        $order->save();
        return response()->json(['delete'], 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function completeTask($id, Request $request)
    {
        $order = Requests::find($id);

        if ($order->isDone !== 0) {
            return response()->json('no');
        }

        $order->isDone = 1;

        $order->done_msg = $request->done_msg;
        $order->done_img = $request->done_img;
        $order->is_viewed = 0;

        imgetorequest::where('req_id',  $order->id)

        ->update(['img_id' => 0]);

        Messages::where('req_id', $order->id)

        ->update(['isDone' => 1]);

        $order->save();

        return response()->json(['delete'], 200);
    }

    public function searchForId($id, $array)
    {
        foreach ($array as $key) {
            if ($key['input'] === $id) {
                return $key;
            }
        }
        return null;
    }

    public function isViewedRequests()
    {

        $user = Auth::user();

     $isunviewsrequest =    Requests::where('User_id', $user->id)
        ->where('is_viewed', 0)
        ->where('isDone',1)
        ->first();
        
        if(!is_null($isunviewsrequest))
        {
            Requests::where('User_id', $user->id)
            ->where('isDone',1)


            ->update(['is_viewed' => 1]);
        }




        return response()->json('ok');
    }
}
