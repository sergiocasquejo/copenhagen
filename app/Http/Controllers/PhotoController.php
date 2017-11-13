<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PhotoController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        try {
            $photo = new \App\Photo;
            $validator = $photo->validate($request->file());
            if ($validator->fails()) {
                $validationStr = '';
                foreach ($validator->errors()->getMessages() as $k => $error) {
                    foreach ($error as $err) {
                        $validationStr .= $err .'<br/>';
                    }
                    
                }
                return response()->json($validationStr, 400, [], JSON_UNESCAPED_UNICODE);
            }
            $roomPhoto = new \App\Photo;
            $photo = $request->file('photo');
            $roomID  = $request->input('roomID');
            $filename = $photo->getClientOriginalName();
            $name = pathinfo($filename, PATHINFO_FILENAME); // file
            $ext = pathinfo($filename, PATHINFO_EXTENSION); // jpg
            
            $uploadPath = \Config::get('copenhagen.uploadsPath') .'/';
            $path = \Config::get('copenhagen.rooms.url');// . '/' . $roomID; 

            if (\File::exists(public_path(). $path .'/'. $filename))
            {
                $name = $name .'_'. time();
                $filename = $name .'.'.$ext;
            }

            if ($photo->move(public_path(). $path, $filename)) {
                $dir = public_path(). $path . '/';
                $sizes = \Config::get('copenhagen.rooms.image.sizes');
                $images = array(
                    'orig' => $path .'/'. $filename
                );
                foreach ($sizes as $key => $size) {
                    $img = \Image::make($dir. $filename)->fit($size['width'], $size['height']);
                    $_n = $name. '_'. $size['width'] .'x'. $size['height'] .'.'. $ext;
                    $images[$key]  = $path .'/'. $_n;
                    $img->save($dir.$_n);
                }

                
                $roomPhoto->file = $images;
                $roomPhoto->default = 0;
                
                $room = \App\Room::findOrFail($id);
            
                    $room->photos()->save($roomPhoto);
                

                $photos = $room->photos()->get();
                foreach($photos as $i => $photo) {
                    $photos[$i]['file'] = $photo->file;
                }
                return response()->json($photos, 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                return response()->json('failed', 400);
            }

        } catch(\Exception $e) {
            \Log::info('ERROR: '.$e->getMessage());
            return response()->json('Oops! Error please report to administrator.', 400, [], JSON_UNESCAPED_UNICODE);
        }
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($roomId, $id)
    {
        \App\Photo::find($id)->delete();
        $photos = \App\Room::find($roomId)->photos()->get();
        return response()->json($photos, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
