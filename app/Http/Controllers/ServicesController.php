<?php

namespace App\Http\Controllers;

use App\Models\Services;
use App\Models\UploadedFile;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\RequirementUploader;
use Illuminate\Support\Facades\Storage;


class ServicesController extends Controller
{

    /** status 
     *code
     *200 ok
     *400 bad entry
     *401 Unauthorized
     *403 Forbidden
     *404 Not Found
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['update', 'store', 'destroy', 'getMycompany']);
        //   $this->middleware('auth:sanctum')->only('store');

    }

    public $rule = [
        "Title" => "required|string|max:255|min:3",
        "Description" => "required|string|max:255|min:3",
        "Requirement" => "required|string|max:255|min:3",
      //  "Delivery_Time" => "required",
     //   "IsOnTime_Service" => "required|boolean",
    ];

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */

    public function messages()
    {
        return [
            'Title.string' => 'يجب كتابة اسم ',
            'Title.string' => 'يجب ان يكون العنوان نص فقط',
            "Title.max" => "يجب ان لا يزيد عنوان النص عن 25 حرف",
            "Title.min" => "يجب ان لا يقل عنوان النص عن 3 حرف",

            'Description.required' => 'يجب كتابة اسم ',
            'Description.string' => 'يجب ان يكون العنوان نص فقط',
            "Description.max" => "يجب ان لا يزيد عنوان النص عن 25 حرف",
            "Description.min" => "يجب ان لا يقل عنوان النص عن 3 حرف",

            'Delivery_Time.required' => 'يجب ان يكون العنوان نص فقط',

            'IsOnTime_Service.required' => 'يجب ان يكون العنوان نص فقط',
            'IsOnTime_Service.boolean' => 'يجب ان يكون العنوان نص فقط',

        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services  = Services::paginate(15);
        foreach ($services as $key) {
            $key->User;
            # code...
        }
        // $theServicestable = $services->User;

        return response()->json($services);
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
        if (!$user->tokenCan('server:company')) {
            return response()->json('not company', 403);
        }

        $validation = Validator::make($request->all(), $this->rule, $this->messages());

        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        $serviceItem = $request->all();

        $serviceItem["user_id"] = $user->id;

        $serviceItem["Delivery_Time"] = "2022-01-25 05:22:28";
        $serviceItem["IsOnTime_Service"] = 0;
        $serviceItem["NumberOf_Request_Done"] = 0;

        $requplArry = $serviceItem["requpl"];

        unset($serviceItem["requpl"]);

        if ($serviceItem["img_id"]) {
            $TheImge  = UploadedFile::find($serviceItem["img_id"]);

            if ($TheImge->user_id !== $user->id) 
            {
                return response()->json('not your imge', 401);
            }
         
            try {
                Storage::copy('public/files/thumbnail/' . $TheImge->File_name, 'public/files/public/' . $TheImge->File_name);
            } catch (\Throwable $th) {
                //throw $th;
            }




          //  if (!$TheImge->is_pro) {

                $TheImge->is_pro = true;

                $TheImge->save();
          //  }
        }

        $service = Services::create($serviceItem);


        if (!is_null($requplArry)) {
            
            foreach ($requplArry as $requpl) {

                RequirementUploader::create([
                    "Title_upload" => $requpl[0],
                    "Service_id" => $service->id,
                    "is_required" => $requpl[1],
                ]);
            }
        }



        return response()->json(['service add', $service]);
    }

    public function Search(Request $request)
    {
        $keyword  = $request->Search;
        //return response()->json($keyword );
        $reresultoult = Services::where('Title', 'LIKE', "%{$keyword}%")
            ->orWhere('Description', 'LIKE', "%{$keyword}%")
            ->get();

        foreach ($reresultoult as $key) {
            $key->User;
        }

        return response()->json($reresultoult);
    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $service = Services::find($id);
        if (is_null($service)) {
            return response()->json('not found', 404);
        }

        $RequirementUploader = RequirementUploader::where('Service_id', $service->id)->get();

        $service['requpl'] = $RequirementUploader;

        return response()->json($service);
    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function showServicesBycompany($id)
    {
        $service = Services::where('user_id', $id)->get();
        if (is_null($service)) {
            return response()->json('not found', 404);
        }
        foreach ($service as $key) {
            $key->User;
            # code...
        }

        return response()->json($service->all());
    }

    /**
     * Display the specified resource.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showServicesByCat(Request $request)
    {
        //  return response( $request->Cats);
        $allservicebycat = [];

        foreach ($request->Cats as $key => $value) {

            $servicebycat = Services::where('cat_id', $value)->get();

            foreach ($servicebycat as $key) {

                $key->User;

                array_push($allservicebycat, $key);
            }
        }

        if (is_null($allservicebycat)) {
            return response()->json('not found', 404);
        }



        return response()->json($allservicebycat);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function getMycompany()
    {
        $user = Auth::user();
        $service = Services::where('user_id', $user->id)->get();
        if (is_null($service)) {
            return response()->json('not found', 404);
        }

        foreach ($service as $key) {
            $key->User;
        }

        return response()->json($service->all());
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
        $services = Services::find($id);
        $user     = Auth::user();

        // return response()->json('not company',403);

        if (!$user->tokenCan('server:company')) {
            return response()->json('not company', 403);
        }

        //RequirementUploader

        if ($user->id != $services->user_id) {
            return response()->json(['not authraized to modfiy this service'], 403);
        }

        $RequirementUploader = $request['requpl'];

        unset($request['requpl']);

        $validation = Validator::make($request->all(), $this->rule, $this->messages());

        if ($validation->fails()) {
            return response()->json($validation->errors());
        }

        $services->Title = $request->Title;
        $services->Description = $request->Description;
        $services->Requirement = $request->Requirement;


        $services->IsOnTime_Service = 0;
        $services->NumberOf_Request_Done = 0;

        $services->Delivery_Time = "2022-01-25 05:22:28";
        $services->cat_id = $request->cat_id;


        if ($services->img_id !== $request->img_id && $services->img_id) {

            $newbackimg = UploadedFile::find($request->img_id);

            if ($newbackimg->user_id !== $user->id) {
                return response()->json('not your imge', 401);
            }

            $backimg = UploadedFile::find($services->img_id);

        //  dd('this code is wotking herhrh yahayyyy');

        try {
            Storage::copy('public/files/thumbnail/' . $newbackimg->File_name, 'public/files/public/' . $newbackimg->File_name);
        } catch (\Throwable $th) {
            //throw $th;
        }

         //   return $backimg;
         $services->img_id = $request->img_id;

         $services->save();
         
         $isbackserv = Services::where('img_id',$backimg->id)->first();

         if(is_null($isbackserv) && $user->img_id !== $backimg->id)
         {
            $backimg->is_pro = 0;
         }

            $newbackimg->is_pro = true;

            $newbackimg->save();

            $backimg->save();
        }




        if (!is_null($RequirementUploader)) {
            foreach ($RequirementUploader as $requpl) {

                RequirementUploader::where(

                    "Service_id",
                    $services->id
                )->delete();
            }
            foreach ($RequirementUploader as $requpl) {

                RequirementUploader::create([
                    "Title_upload" => $requpl[0],
                    "Service_id" => $services->id,
                    "is_required" => $requpl[1]
                ]);
            }
        }

        return response()->json('تم تعديل البيانات');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $services  = Services::find($id);
        //  return response()->json($services);

        $user = Auth::user();
        if (!$user->tokenCan('server:company')) {
            return response()->json('not found', 403);
        }
        // return response()->json($services);

        if ($user->id != $services->user_id) {
            return response()->json('un uthraized', 403);
        }
        if (is_null($services)) {
            return response('the item dos not exsit', 404);
        }

        $services->delete();

        return response()->json('deleted');
    }
}
