<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Image;


use Validator;

class FileMangerController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth:sanctum')->only([
            'index', 'update',  'store'
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        $files = UploadedFile::where("user_id", $user->id)->get();

        $filegroup = array();

        foreach ($files as $file) {

          //  $smallthumbnailpath = public_path('storage/files/thumbnail/' . $file->File_name);

            $fileName =  Storage::get('public/files/thumbnail/' . $file->File_name);

            $dataUri = 'data:image/' . $file->ext . ';base64,' . base64_encode($fileName);

            array_push($filegroup, [
                "id" => $file->id,
                "File_name" => $file->File_name,
                "imge" => $dataUri,
            ]);
        }

        return response()->json($filegroup);
    }


    public $genralRule = [

        "file" => "required|mimes:doc,jpeg,jpg,png,docx,pdf|max:2048",

    ];

    public $viewRule = [

        "file" => "required|mimes:jpeg,jpg,png|max:2048",

    ];

    public function messages(){
        return [
            'file.required' => 'يجب كتابة اسم ',
            "file.mimes" => "doc , jpeg , jpg , png , docx , pdf صيغة ملف غير مقبولة الصيغ المسموحة ",
            "file.max" =>"حجم الملف اكبر من المسموح به"

        ];
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function store(Request  $request)
    {

        $user = Auth::user();

        if ($file = $request->file('file')) {


        $validation = $request->validate( $this->genralRule, $this->messages());

      //  if ($validation->fails()) {

         //  return response()->json($validation->errors(), 400);
      //  }

            $name = $file->getClientOriginalName();

            $newfilem = rand(111, 22022) . $name;

            $file->storeAs('public/files/', $newfilem);

            $this->createThumbnail($request, $newfilem, 500, 400);

            $save = new UploadedFile();

            $save->File_name = $newfilem;
            $save->ext = $file->getClientOriginalExtension();
            $save->user_id = $user->id;
            $save->Request_id = $user->id;

            $save->save();

            $thecoded  = 'data:image/' . $file->getClientOriginalExtension() . ';base64,' . base64_encode($file);

            return response()->json(
                [
                    "file" => [
                        "id" => $save->id,
                        "File_name" => $save->File_name,
                        "imge" => $thecoded
                    ]
                ]
                //'yes yes'
            );
        }
        return response()->json(['file_uploaded'], 200);
    }

    /**
     * Display the specified resource.
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request  $request, $id)
    {
        //   return $id;
        // $user = Auth::user();
        $file = UploadedFile::find($id);
        //  return response($file);
        if (is_null($file)) {
            return response()->json('not found', 404);
        }

        if($file->is_pro)
        {
            return response()->json('not found', 404);
 
        }

        //   if($file->user_id != $user->id)
        //  {
        //   return response()->json('the file is not youe');
        //   }
        // $smallthumbnailpath = public_path('storage/files/thumbnail/' . $file->File_name);

        $fileName =  Storage::get('public/files/thumbnail/' . $file->File_name);

        //   if (file_exists($file)) {

     
        $dataUri = 'data:image/' . $file->ext . ';base64,' . base64_encode($fileName);

        return $dataUri;

        //return response($fileName)
        //->header('Content-Type' ,'image/jpeg');
        //     } else {
        //  abort(404, 'File not found!');
        //    }

    }

    /**
     * Display the specified resource.
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function showImge($id)
    {

        $file = UploadedFile::find($id);

        if($file->is_pro)
        {
            return response()->json('not found', 404);
        }

        if (is_null($file)) {
            return response()->json('not found', 404);
        }
  
        $fileName =  Storage::get('public/files/thumbnail/' . $file->File_name);

        $dataUri = 'data:image/' . $file->ext . ';base64,' . base64_encode($fileName);

        return $dataUri;
  
        //return response($fileName)->header('Content-Type', 'image/jpeg');
    
    }

    public function showPublicImge($id)
    {

        $file = UploadedFile::find($id);

        if(!$file->is_pro)
        {
            return response()->json('not found', 404);
        }

        if (is_null($file)) {
            return response()->json('not found', 404);
        }
  
        $fileName =  Storage::get('public/files/public/' . $file->File_name);

       // $dataUri = 'data:image/' . $file->ext . ';base64,' . base64_encode($fileName);

       // return $dataUri;
  
        return response($fileName)->header('Content-Type', 'image/jpeg');
    
    }








    /**
     * Create a thumbnail of specified size
     *
     * @param string $path path of thumbnail
     * @param int $width
     * @param int $height
     */
    public function createThumbnail($request, $newfilem, $width, $height)
    {

        $img = Image::make($request->file('file')->getRealPath());
        $smallthumbnailpath = public_path('storage/files/thumbnail/' . $newfilem);

        $img->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });

        $img->save($smallthumbnailpath);
    }


    public function createPDF($request, $newfilem, $width, $height)
    {

        $img = Image::make($request->file('file')->getRealPath());
        $smallthumbnailpath = public_path('storage/files/thumbnail/' . $newfilem);

        $img->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });

        $img->save($smallthumbnailpath);
    }
}
