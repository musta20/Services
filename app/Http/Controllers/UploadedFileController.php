<?php

namespace App\Http\Controllers;

use App\Models\UploadedFile;
use App\Models\imgetorequest;
use App\Models\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use Validator;

class UploadedFileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['store','getRequestImge','update','destroy','getMyPost','GetFilsForReq','downloadImge']);
    
    }


        public $rule = [
            "file"=>"required|mimes:doc,docx,pdf,txt,csv|max:2048",
         
        ];

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
       


  
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function getImgeRequest($id)
    {
     
            $allimge=[];
            $files = imgetorequest::where('req_id',$id)->get();

            foreach ($files as $img) {
                      $theimge = UploadedFile::find($img->img_id);

                     $fileName =  Storage::get('public/files/'.$theimge->File_name);

                     $dataUri = 'data:image/' . $theimge->ext . ';base64,' . base64_encode($fileName);

                    array_push($allimge,["img"=>$dataUri]);
                     
                  }
               //   array_push($key['req'],$allimge);
        return response()->json($allimge);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */    
    public function GetFilsForReq($id)
    {
        $user = Auth::user();

        if ($user->tokenCan('server:company'))
         {
            $allfiles = imgetorequest::where("req_id",$id)->where('combany_id',$user->id)->get();


         }else{

            $allfiles = imgetorequest::where("req_id",$id)->where('User_id',$user->id)->get();

         }


        $filegroup = array();
        foreach ($allfiles as $img) {

            if(!$img->img_id) break;
            

            

            $file = UploadedFile::find( $img->img_id);

            $fileName =  Storage::get('public/files/'.$file->File_name);

            $dataUri = 'data:image/' . $file->ext . ';base64,' . base64_encode($fileName);

            array_push($filegroup,[
                "id"=>$file->id,
                "File_name"=>$file->File_name,
                "imge"=>$dataUri,
            ]);
        }

        return response()->json($filegroup);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UploadedFile  $uploadedFile
     * @return \Illuminate\Http\Response
     */
    public function show(UploadedFile $uploadedFile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UploadedFile  $uploadedFile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UploadedFile $uploadedFile)
    {
        //
    }


        /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function downloadImge($id)
    {
        $user= Auth::user();

        $file = UploadedFile::find($id);

        if($user->id !== $file->user_id)
        {
            return response()->json('aunthrazition required',401);
        } 

        return Storage::download('public/files/'.$file->File_name);

     }
     public function getRequestImge($id)
     {
         $user= Auth::user();
 
         if (!$user->tokenCan('server:company'))
         {
            return response()->json('aunthrazition required',401);
  
         }
         
         $imge = imgetorequest::where('combany_id',$user->id)->where('img_id',$id)->first();


         if(is_null($imge))
         {
            return response()->json('aimge not belog to oyi',401);
   
         }

         $file = UploadedFile::find($id);
 
         return Storage::download('public/files/'.$file->File_name);
 
      }
 


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $user  = Auth::user();

        if($user->img_id == $id)
        {
            return response()->json('you are using the file in your back ground',401);
        }

        $ismyfile = UploadedFile::where("id",$id)->where('user_id',$id);
        if(is_null($ismyfile))
        {
            return response()->json('لاتملك صلاحية لتعديل هذا الملف',401);
        }


        $isinservices = Services::where("img_id",$id)->first();

        if(!is_null($isinservices))
        {

            return response()->json('الملف مستخدم في احد الخدمات',401);
        }
     

        $file = UploadedFile::find($id);
        if(is_null( $file ))
        {
            return response()->json('تم حذف الملف',200);
   
        }

        $isInUse = imgetorequest::where('img_id',$id)->first();

        if(!is_null($isInUse))
        {
          // if($isInUse)
            return response()->json('الملف مستخدم في الطلبات',401);
        }

        $file->delete();

        return response()->json('تم حذف الملف',200);

    }
}
