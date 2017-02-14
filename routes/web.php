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

            $aminities = $room->aminities()->get()->toArray();
            $r['aminities'] = array();
            foreach($aminities as $i => $a) {
                $r['aminities'][] = $a['id'];
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
        $room->location = Request::input('location', '');
        $room->bed = Request::input('bed', '');
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
        $room->location = Request::input('location', '');
        $room->bed = Request::input('bed', '');
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

    Route::delete('/room/{id}', function() {
        $roomID  = Request::input('roomID');
        if ($room = \App\Room::find($roomID)) {
            // Delete photos
            $photos = $room->photos()->get();
            foreach ($photos as $i => $photo) {
                if ($photo->file) {
                    foreach(json_decode($photo->file) as $key => $file) {
                        File::delete(public_path() . $file);
                    }
                }
            }
            $room->delete();
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
         $path = Config::get('copenhagen.rooms.url');// . '/' . $roomID; 

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
                $img = Image::make($dir. $filename)->fit($size['width'], $size['height']);
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

    Route::post('/room/{roomID}/aminities', function() {
        $room = \App\Room::find(Request::input('roomID'));
        $aminites = json_decode(Request::input('aminities'));
        if ($room && count($aminites) != 0) {
            if ($room->aminities()->sync($aminites)) {

                $aminities = $room->aminities()->get()->toArray();
                $r = array();
                foreach($aminities as $i => $a) {
                    $r[] = $a['id'];
                }

                return response()->json($r, 200, [], JSON_UNESCAPED_UNICODE);
            }
        }

        return response()->json('failed', 400);
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

    Route::get('/rooms/aminities', function() {
        $aminites = \App\Aminities::all();
        return response()->json($aminites->toArray(), 200, [], JSON_UNESCAPED_UNICODE);
    });

    Route::post('/rooms/facility', function() {
        $facility = new \App\Aminities;
        $facility->name = strtolower(Request::input('name'));
        try {
            if ($facility->save()) {
                $aminites = \App\Aminities::all();
                return response()->json($aminites->toArray(), 200, [], JSON_UNESCAPED_UNICODE);
            }
        } catch(\Exception $e) {
            return response()->json($e->getMessage(), 200, [], JSON_UNESCAPED_UNICODE);
        }
    });

    Route::get('rooms/types', function() {
        $types = \App\Room::select(['*', DB::raw('CONCAT(name, " ", building, " - ID ", id) AS name')])->get()->toArray();
        return response()->json($types, 200, [], JSON_UNESCAPED_UNICODE);
    });

    Route::post('calendar', function() {
        try {
            $from = Request::input('from');
            $to = Request::input('to');

            while(strtotime($from) <= strtotime($to)) {
                $from = date ("Y-m-d", strtotime("+1 day", strtotime($from)));
                $calendar = new \App\Calendar;
                $calendar->roomID = Request::input('roomType.id');
                $calendar->selectedDate = $from;
                $calendar->roomOnly = Request::input('roomOnly', 0);
                $calendar->single = Request::input('single', 0);
                $calendar->double = Request::input('double', 0);
                $calendar->minStay = Request::input('minStay', 0);
                $calendar->maxStay = Request::input('maxStay', 0);
                $calendar->availability = Request::input('availability', 0);
                $calendar->isActive = Request::input('isActive', 0);
                $calendar->save();
            }
        } catch(\Exception $e) {
            return response()->json($e->getMessage(), 400, [], JSON_UNESCAPED_UNICODE);
        }
        
    });

    Route::get('room/{roomID}/calendar', function($roomID) {
        $calendar = \App\Calendar::where('roomID', $roomID)->select(['*', DB::raw('selectedDate AS title'), DB::raw('selectedDate AS start')])->get()->toArray();
        return response()->json($calendar, 200, [], JSON_UNESCAPED_UNICODE);
    });
});

Route::any('{all}', function(){
    return view('welcome');
})->where('all', '.*');
// Auth::routes();

// Route::get('/home', 'HomeController@index');
