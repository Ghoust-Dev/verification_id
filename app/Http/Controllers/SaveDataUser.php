<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Validator;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg as FFMpeg;

class SaveDataUser extends Controller
{
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadPhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails())
        {
            return response(['message'=>$validator->errors()->all()], 422);
        }

        $path = storage_path('../storage/app/public/'.$request->idverification);

        
        if(!File::isDirectory($path)){
            File::makeDirectory($path, 0777, true, true);
        }

        $path = $path.'/' . $request->photo->getClientOriginalName();
        $img = Image::make($request->photo);
        $img->save($path);

        return response()->json('Has been taked', 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadVideo(Request $request)
    {
        $path = storage_path('../storage/app/public/'.$request->idverification);
        
        if(!File::isDirectory($path)){
            File::makeDirectory($path, 0777, true, true);
        }    

        $file = tap($request->file('video'))->store('videos');
        
        FFMpeg::fromDisk("local")->open("videos/". $file->hashName())
                    ->export()
                    ->toDisk('local')
                    ->inFormat(new \FFMpeg\Format\Video\X264('libmp3lame', 'libx264'))
                    ->save('public/'.$request->idverification.'/video-'.$request->idverification.'-'.$request->retake.'.mp4');
        

        return response()->json('Has been saved', 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkIdExist(Request $request)
    {
        $path = storage_path('../storage/app/public/'.$request->idverification);
        
        if(File::isDirectory($path)){
            return response()->json('Has existed', 422);
        }    

        return response()->json('Has been saved', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
