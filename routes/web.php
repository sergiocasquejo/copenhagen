<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function ($any = null) {
    return view('welcome');
});

// Route::get('/', function () {
//     return view('welcome');
// });


Route::group(['prefix' => 'api/v1'], function() {
    Route::get('/', function() {
        return 'test';
    });

    Route::post('/login', function() {
        $email = Request::input('username');
        $password = Request::input('password');
        $remember = Request::input('remember');

        if (Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            // Authentication passed...
            return response()->json('success', 200);
        } else {
            return response()->json('failed', 400);
        }
    });

    function getAllRooms() {
        $list = array();
        $rooms = \App\Room::all();
        // dd(\App\Room::find(1)->photos()->get()->toArray());
        foreach ($rooms as $room) {
            $r = $room->toArray();
            $r['photos'] = $room->photos()->get()->toArray();
            foreach($r['photos'] as $i => $photo) {
                $r['photos'][$i]['file'] = json_decode($photo['file']);
            }
            $list[] = $r;
        };

        return $list;
    }

    Route::get('/rooms', function() {
        $list = getAllRooms();
        return response()->json($list, 200, [], JSON_UNESCAPED_UNICODE);   
    });

    Route::post('/room', function() {
        $defaultName = 'Studio Standard-' .time();
        $room = new \App\Room;
        $room->name = Request::input('name', $defaultName);
        $room->slug = str_slug(Request::input('name', $defaultName));
        $room->roomSize = Request::input('roomSize', 0);
        $room->totalRooms = Request::input('totalRooms', 0);
        $room->standardRate = Request::input('standardRate', 0);
        $room->minimumRate = Request::input('minimumRate', 0);
        $room->totalPerson = Request::input('totalPerson', 1);
        $room->extraBed = Request::input('extraBed', 0);
        $room->bathrooms = Request::input('bathrooms', 0);
        $room->building = Request::input('building', 0);
        $room->isActive = Request::input('isActive', 0);
        $room->sort = Request::input('sort', 0);

        if ($room->save()) {
            $list = getAllRooms();
            return response()->json($list, 200, [], JSON_UNESCAPED_UNICODE);  
        }

        return response()->json('failed', 400);
    });

    Route::put('/room/{id}', function($id) {
        $room = \App\Room::find(Request::input('id'));
        $room->name = Request::input('name', '');
        $room->slug = str_slug(Request::input('name', ''));
        $room->roomSize = Request::input('roomSize', 0);
        $room->totalRooms = Request::input('totalRooms', 0);
        $room->standardRate = Request::input('standardRate', 0);
        $room->minimumRate = Request::input('minimumRate', 0);
        $room->totalPerson = Request::input('totalPerson', 1);
        $room->extraBed = Request::input('extraBed', 0);
        $room->bathrooms = Request::input('bathrooms', 0);
        $room->building = Request::input('building', 0);
        $room->isActive = Request::input('isActive', 0);
        $room->sort = Request::input('sort', 0);
        $room->updated_at = date('Y-m-d H:i:s');

        if ($room->update()) {
            $list = getAllRooms();
            return response()->json($list, 200, [], JSON_UNESCAPED_UNICODE); 
        }

        return response()->json('failed', 400);
    });

     Route::post('/room/{id}/photo', function($id) {
         $photo = Request::file('photo');
         $roomID  = Request::input('roomID');
         $filename = $photo->getClientOriginalName();
         $name = pathinfo($filename, PATHINFO_FILENAME); // file
         $ext = pathinfo($filename, PATHINFO_EXTENSION); // jpg
         
         $uploadPath = Config::get('copenhagen.uploadsPath') .'/';
         $path = Config::get('copenhagen.rooms.url') . '/' . $roomID; 

         if (File::exists(public_path(). $path .'/'. $filename))
         {
             $name = $name .'_'. time();
             $filename = $name .'.'.$ext;
         }

         if ($photo->move(public_path(). $path, $filename)) {
             $dir = public_path(). $path . '/';
             $sizes = Config::get('copenhagen.rooms.image.sizes');
             $images = array(
                 'orig' => $path .'/'. $filename
             );
             foreach ($sizes as $key => $size) {
                $img = Image::make($dir. $filename)->resize($size['width'], $size['height']);
                $_n = $name. '_'. $size['width'] .'x'. $size['height'] .'.'. $ext;
                $images[$key]  = $path .'/'. $_n;
                $img->save($dir.$_n);

                

             }
            $photo = new App\Photo;
            $photo->roomID = $roomID;
            $photo->file = json_encode($images);
            $photo->default = 0;
            $photo->save();

            $photos= \App\Photo::where('roomID', $roomID)->get();
 
            
            foreach($photos as $i => $photo) {
                $photos[$i]['file'] = json_decode($photo->file);
            }
            


            return response()->json($photos, 200, [], JSON_UNESCAPED_UNICODE);
         } else {
             return response()->json('failed', 400);
         }
    });

    Route::delete('/rooms/{roomID}/photo/{photoID}', function($roomID, $photoID) {
        $photo = \App\Photo::find($photoID);
        if ($photo) {
            $photo = $photo->first();
            foreach (json_decode($photo->file) as $name => $file) {
                File::delete(public_path() . $file);
            }
            
            if ($photo->delete()) {
                $photos= \App\Photo::where('roomID', $roomID)->get();
                foreach($photos as $i => $photo) {
                    $photos[$i]['file'] = json_decode($photo->file);
                }
                return response()->json($photos, 200, [], JSON_UNESCAPED_UNICODE);
            }
        }
        return response()->json('failed', 400);
    });
});

Route::any('{all}', function(){
    return view('welcome');
})->where('all', '.*');
// Auth::routes();

// Route::get('/home', 'HomeController@index');
