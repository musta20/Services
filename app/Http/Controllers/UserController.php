<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OtpVerify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\UploadedFile;


use Validator;


class UserController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth:sanctum')->only(['index', 'update', 'destroy']);
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */

    public $rule = [
        "name" => "required|string|max:25|min:5",
        "username" => "unique:users|required|string|max:9|min:9",
        //"email"=>"unique:users|required|email|max:255|min:3",

    ];

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */

    public $adminRule = [
        "name" => "required|string|max:25|min:5",
        "des" => "required|string|max:200|min:5",
        "phone" => "required|numeric|digits:9",
        // "username" => "unique:users|required|string|max:9|min:9",
        //"email"=>"unique:users|required|email|max:255|min:3",
    ];


    public $registerRule =  [
        "name" => "required|string|max:25|min:5",
        "username" =>  "unique:users|required|numeric|digits:9",
        // "username" => "unique:users|required|string|max:9|min:9",
        //"email"=>"unique:users|required|email|max:255|min:3",
    ];

    public function adminmessages()
    {
        return [
            'name.required' => ' يجب كتابة اسم ',
            'name.string' => ' يجب ان يكون العنوان نص فقط ',
            "name.max" => " يجب ان لا يزيد الاسم النص عن 25 حرف ",
            "name.min" => " يجب ان لا يقل عنوان النص عن 3 حرف ",

            'des.required' => ' يجب كتابة اسم ',
            'des.string' => ' يجب ان يكون العنوان نص فقط ',
            "des.max" => " يجب ان لا يزيد الاسم النص عن 25 حرف ",
            "des.min" => " يجب ان لا يقل عنوان النص عن 3 حرف ",

            'phone.required' => ' يجب كتابة اسم ',
            'phone.numeric' => ' يجب ان يكون ارقام فقط ',
            "phone.digits" => " يجب ان لا يزيد الاسم النص عن 9 حرف ",

            'password.required' => 'يجب كتابة اسم ',
            'password.string' => 'يجب ان يكون العنوان نص فقط',
            "password.max" => "يجب ان لا يزيد عنوان النص عن 25 حرف",
            "password.min" => "يجب ان لا يقل عنوان النص عن 3 حرف",


        ];
    }

    public function registrayionMessage()
    {
        return [
            'name.required' => ' يجب كتابة اسم ',
            'name.string' => ' يجب ان يكون العنوان نص فقط ',
            "name.max" => " يجب ان لا يزيد الاسم النص عن 25 حرف ",
            "name.min" => " يجب ان لا يقل عنوان النص عن 3 حرف ",


            'username.required' => ' يجب كتابة اسم ',
            'username.unique' => ' رقم الجوال مستخدم ',
            'username.numeric' => ' يجب ان يكون ارقام فقط ',
            "username.digits" => " يجب ان لا يزيد الاسم النص عن 9 حرف ",

            'password.required' => 'يجب كتابة اسم ',
            'password.string' => 'يجب ان يكون العنوان نص فقط',
            "password.max" => "يجب ان لا يزيد عنوان النص عن 25 حرف",
            "password.min" => "يجب ان لا يقل عنوان النص عن 3 حرف",


        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */

    public function messages()
    {
        return [
            'name.required' => ' يجب كتابة اسم ',
            'name.alpha' => ' يجب ان يكون العنوان نص فقط ',
            "name.max" => " يجب ان لا يزيد الاسم النص عن 25 حرف ",
            "name.min" => " يجب ان لا يقل عنوان النص عن 3 حرف ",

            'username.required' => 'يجب كتابة اسم ',
            'username.string' => 'يجب ان يكون العنوان نص فقط',
            "username.max" => "يجب ان لا يزيد عنوان النص عن 25 حرف",
            "username.min" => "يجب ان لا يقل عنوان النص عن 3 حرف",
            "username.unique" => " رقم الجوال مستخدم  ",

            'email.required' => 'يجب كتابة اسم ',
            'email.email' => 'يجب ان يكون العنوان نص فقط',
            "email.max" => "يجب ان لا يزيد عنوان النص عن 25 حرف",
            "email.min" => "يجب ان لا يقل عنوان النص عن 3 حرف",
            "email.unique" => " رقم الجوال مستخدم  ",

            'password.required' => 'يجب كتابة اسم ',
            'password.string' => 'يجب ان يكون العنوان نص فقط',
            "password.max" => "يجب ان لا يزيد عنوان النص عن 25 حرف",
            "password.min" => "يجب ان لا يقل عنوان النص عن 3 حرف",


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
        return response()->json($user);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function Register(Request $request)
    {
        $validation = Validator::make($request->all(), $this->registerRule, $this->registrayionMessage());

        if ($validation->fails()) {

            return response()->json($validation->errors(), 401);
        }

        /*
        $otp  = OtpVerify::where(["phone"=>$request->phone])->get();
        
        if(is_null($otp))
        {
            return response()->json('resend the code',201);
        }

        if($otp->key != $request->key)
        {
            return response()->json('worng code',401);
        }
        */

        $user = $request->all();
        $user['password'] = Hash::make($request->password);

        $user['img_id']=0;
        $user = User::create($user);
        //return response()->json($user->all());

        // return response()->json('user registred');

        $token = $user->createToken("UserToken", ['server:user'])->plainTextToken;

        $userJwt = Hash::make($user->id . $user->name . $user->username);
        $minutes = 300;



        return response()->json(
            [
                "data" => [
                    "Jwt" => $token,
                    "UserData" => [
                        "id" => $user->id,
                        "name" => $user->name,
                        "username" => $user->username,
                    ]
                ]
            ]
        )->cookie(
            'UserToken',
            $userJwt,
            $minutes
        )->cookie(
            'id',
            $user->id,
            $minutes
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ReSetSession(Request $request)
    {
        $UserToken = $request->cookie('UserToken');
        $id = $request->cookie('id');
        $user = User::find($id);

        if (is_null($UserToken) || is_null($id)) {
            return response()->json('no data', 401);
        }

        $userJwt = $user->id . $user->name . $user->username;
        if (!Hash::check($userJwt, $UserToken)) {

            return response()->json('kalemit ial ser khatah', 401);
        }



        if ($user->user_type == 3) {
            $token = $user->createToken("UserToken", ['server:user'])->plainTextToken;
        }

        if ($user->user_type == 2) {
            $token = $user->createToken("UserToken", ['server:company'])->plainTextToken;
        }


        $minutes = 111;


        return response()->json(
            [
                "data" => [
                    "Jwt" => $token,
                    "UserData" => [
                        "id" => $user->id,
                        "name" => $user->name,
                        "username" => $user->username,
                        "user_type" => $user->user_type,
                    ]
                ]
            ]
        )->cookie(
            'UserToken',
            $userJwt,
            $minutes
        )->cookie(
            'id',
            $user->id,
            $minutes
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {

        //   Cookie::queue(Cookie::forget('id')->forget('UserToken'));
        //->forget('id');
        $cookie = \Cookie::forget('UserToken');
        $cookie2 = \Cookie::forget('id');

        return response('view')->withCookie($cookie)->withCookie($cookie2);

        //  return response('logout');
    }
    public function SendOtpVerify($Phone)
    {

        $validation = Validator::make($Phone, $this->rule, $this->messages());

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        $key  = rand(5000, 9000);
        OtpVerify::create(["phone" => $Phone, "otp" => $key]);

        /**
         * $messagebird = new MessageBird\Client('Your-API-Key]');
         * $message = new MessageBird\Objects\Message;
         * $message->originator = '+31XXXXXXXXX';
         * $message->recipients = [ $Phone ];
         * $message->body = the virfiction code is $key;
         * $response = $messagebird->messages->create($message);
         * var_dump($response 
         *  */
    }

    /**
     * Display the specified resource.
     *
     * @param string $username
     * @return \Illuminate\Http\Response
     */
    public function show($username)
    {
        $user = User::where('username', $username)->get();

        if (is_null($user)) {
            return response()->json('not found', 404);
        }

        return response()->json($user);
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

        $currentUser  = Auth::user();

        $user = User::find($currentUser->id);

        if ($currentUser->tokenCan('server:company')) {

            $validation = Validator::make($request->all(), $this->adminRule, $this->adminmessages());

        } else {

            $validation = Validator::make($request->all(), $this->rule, $this->messages());
            $request->email=$request->username.'@serv.com';
        }


        if ($validation->fails()) {

            return response()->json($validation->errors(), 400);
        }


        if ($currentUser->tokenCan('server:company')) {

            $currentUser->name = $request->name;

            $currentUser->des = $request->des;

            $currentUser->phone = $request->phone;


            if ($currentUser->img_id !== $request->img_id) {
                $newbackimg = UploadedFile::find($request->img_id);
                $backimg = UploadedFile::find($currentUser->img_id);

                $backimg->is_pro = 0;
                $newbackimg->is_pro = 1;

                $newbackimg->save();
                $backimg->save();
            }

            $currentUser->img_id = $request->img_id;





            $currentUser->save();

            return response()->json('data updated');
        }



        if (!is_null($request->password)) {

            $currentUser->password = $request->password;
        }

        $currentUser->email = $request->email;

        $currentUser->name = $request->name;

        $currentUser->save();


        return response()->json('data updated');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function Login(Request $request)
    {

        $flitred = collect($this->rule);

        $validation =  Validator::make($request->all(), [$flitred->only('username')], $this->messages());

        if ($validation->fails()) {
            return response()->json($validation->errors(), 401);
        }

        $user = User::where('username', $request->username)->first();

        if (is_null($user)) {
            return response()->json('اسم المستخدم او كلمة المرور غير صحيحة', 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json('اسم المستخدم او كلمة المرور غير صحيحة', 401);
        }


        $minutes = 111;

        if ($user->user_type == 3) {

            $token = $user->createToken("UserToken", ['server:user'])->plainTextToken;
        }

        if ($user->user_type == 2) {

            $token = $user->createToken("UserToken", ['server:company'])->plainTextToken;
        }

        $userJwt = Hash::make($user->id . $user->name . $user->username);

        return response()->json(
            [
                "data" => [
                    "Jwt" => $token,
                    "UserData" => [
                        "id" => $user->id,
                        "name" => $user->name,
                        "username" => $user->username,
                        "user_type" => $user->user_type,
                    ]
                ]
            ]
        )->cookie(
            'UserToken',
            $userJwt,
            $minutes
        )->cookie(
            'id',
            $user->id,
            $minutes
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $currentUser = Auth::user();
        if ($user->id != $currentUser->id) {
            return response()->json();
        }

        $user->delete();

        return response()->json('delete log out plz');
    }
}
