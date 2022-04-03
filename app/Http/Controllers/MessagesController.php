<?php

namespace App\Http\Controllers;

use App\Models\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Requests;

use Validator;


class MessagesController extends Controller
{

    /** status 
     * code
     *200 ok
     *400 bad entry
     *401 Unauthorized
     *403 Forbidden
     *404 Not Found
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['store', 'show', 'update', 'getNotfy', 'isViewedMessages', 'destroy']);
    }

    public $rule = [
        // "Sender_id" => "required|integer|max:255|min:1",
        "Messages" => "required|max:255|min:1",
        //    "User_id"=>"required|integer|max:255|min:25",
        //  "isDone"=>"tinyint",
    ];

    public function messages()
    {
        return [
            //   'Sender_id.required' => 'يجب كتابة اسم ',
            //  'Sender_id.integer' => 'يجب ان يكون العنوان نص فقط',
            //   "Sender_id.max" => "يجب ان لا يزيد عنوان النص عن 25 حرف",
            //    "Sender_id.min" => "يجب ان لا يقل عنوان النص عن 3 حرف",

            'Messages.required' => 'يجب كتابة اسم ',
            'Messages.string' => 'يجب ان يكون العنوان نص فقط',
            "Messages.max" => "يجب ان لا يزيد عنوان النص عن 25 حرف",
            "Messages.min" => "يجب ان لا يقل عنوان النص عن 3 حرف",

        ];
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

        $validation = Validator::make($request->all(), $this->rule, $this->messages());

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }
        //  return [$request->all(),"Sender_id"=>$user->id];
        // $request->Sender_id = $user->id
        $reqmessges = $request->all();
        $reqmessges["Sender_id"] = $user->id;
        $reqmessges["is_viewed"] = 0;

        if ($user->tokenCan('server:user')) {
            $reqmessges["is_viewed"] = 1;
        }

        $reqmessges["User_id"] = 0;
        $reqmessges["isDone"] = 0;
        $reqmessges["m_type"] = 0;



        $message = Messages::create($reqmessges);

        return response()->json($message);
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

        if ($user->tokenCan('server:user')) {
            $order = Requests::where('req_id', $id)->where('User_id',$user->id);
        }

        if ($user->tokenCan('server:company')) {
            $order = Requests::where('req_id', $id)->where('combany_id',$user->id);
        }

        if(is_null( $order))
        {
            return response()->json('not found', 404);
        }

        $message = Messages::where('req_id', $id)->get();



        if (is_null($message)) {
            return response()->json('not found', 404);
        }

        return response()->json($message);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Messages  $messages
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Messages $messages)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Messages  $messages
     * @return \Illuminate\Http\Response
     */
    public function destroy(Messages $messages)
    {
        //
    }

    //getNotfy

    public function getNotfy()
    {

        $user = Auth::user();

        $isunviewsrequest =    Requests::where('User_id', $user->id)
            ->where('is_viewed', 0)
            ->where('isDone', 1)
            ->get();

        $message = Messages::where('isDone', 0)->where('is_viewed', 0)->get();



        // $message->is_viewed = 1;

        if (is_null($message)) {
            return 0;
        }


        //  $message->save();

        return response()->json(count($message) + count($isunviewsrequest));
    }

    public function isViewedMessages($id)
    {

        //  if(msg.Sender_id!==cookies.UserData.id && !msg.is_viewed) fetcher();

        $user = Auth::user();
        //  $isRequest= request()::find($id)->where('user_id',$user->id);
        Messages::where('req_id', $id)
            ->where('Sender_id', '!=', $user->id)

            ->update(['is_viewed' => 1]);


        return response()->json('ok');
    }
}
