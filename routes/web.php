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
    return view('app');
});


Route::group(['prefix' => 'api/v1'], function() {
    /*
    |--------------------------------------------------------------------------
    | Rates Routes
    |--------------------------------------------------------------------------
    |
    | Register your routes here that needs user authentication
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */

    

    Route::get('/logout', function() {
        Auth::logout();
        return response()->json('logout', 200);
    });

    Route::post('/login', function() {
        if (!Auth::check()) {
            $username = Request::input('username');
            $password = Request::input('password');
            $remember = Request::input('remember');

            if (Auth::attempt(['email' => $username, 'password' => $password], $remember)) {
                // Authentication passed...
                return response()->json(Auth::user()->with('customer')->get()->toArray(), 200);
            }elseif (Auth::attempt(['username' => $username, 'password' => $password], $remember)) {
                // Authentication passed...
                return response()->json(Auth::user()->with('customer')->get()->toArray(), 200);
            } else {
                return response()->json('Username or Password not exist.', 400);
            }
        } else {
            return response()->json(Auth::user()->toArray(), 200);
        }
    });
    /*
    function getAllRooms() {
        $list = array();
        $rooms = \App\Room::all();
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

    Route::get('/room/{id}', function($id) {
        $room = \App\Room::find($id)->first();
        if ($room) {
            $r = $room->toArray();
            $r['photos'] = $room->photos()->get()->toArray();
            foreach($r['photos'] as $i => $photo) {
                $r['photos'][$i]['file'] = json_decode($photo['file']);
            }

            $aminities = $room->aminities()->get()->toArray();
            $r['aminities'] = array();
            foreach($aminities as $i => $a) {
                $r['aminities'][] = array('id' => $a['id'], 'name' => $a['name']);
            }
            return response()->json($r, 200, [], JSON_UNESCAPED_UNICODE);  
        }
        
        return response()->json('failed', 400, [], JSON_UNESCAPED_UNICODE);  
         
    })->where('id', '[0-9]+');


    Route::get('/room/{slug}', function($slug) {
        $room = \App\Room::where('slug', $slug)->first();
        if ($room) {
            $r = $room->toArray();
            $r['photos'] = $room->photos()->get()->toArray();
            foreach($r['photos'] as $i => $photo) {
                $r['photos'][$i]['file'] = json_decode($photo['file']);
            }

            $aminities = $room->aminities()->get()->toArray();
            $r['aminities'] = array();
            foreach($aminities as $i => $a) {
                $r['aminities'][] = array('id' => $a['id'], 'name' => $a['name']);
            }
            return response()->json($r, 200, [], JSON_UNESCAPED_UNICODE);  
        }

        return response()->json('failed', 400, [], JSON_UNESCAPED_UNICODE);  
         
    })->where('name', '[A-Za-z]+');

    Route::get('/rooms/aminities', function() {
        $aminites = \App\Aminities::all();
        return response()->json($aminites->toArray(), 200, [], JSON_UNESCAPED_UNICODE);
    });

    
    Route::get('rooms/types', function() {
        $types = \App\Room::select(['*', DB::raw('CONCAT(name, " ", building, " - ID ", id) AS title')])->get()->toArray();
        return response()->json($types, 200, [], JSON_UNESCAPED_UNICODE);
    });

    
    

    Route::get('room/{roomID}/calendar', function($roomID) {
        $calendar = \App\Calendar::where('roomID', $roomID)->select(['*', DB::raw('CONCAT("TOTAL PHP", " ", FORMAT(doublePrice, 2)) AS title'), DB::raw('UNIX_TIMESTAMP(selectedDate) AS startsAt')])->get()->toArray();
        return response()->json($calendar, 200, [], JSON_UNESCAPED_UNICODE);
    });

    */

    /*
    |--------------------------------------------------------------------------
    | Booked API Routes
    |--------------------------------------------------------------------------
    |
    | Register your routes here that needs user authentication
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */

    

    Route::post('/book', function() {
        $customer = \App\Customer::firstOrNew(['email' => \Request::input('email')]);
        $booking = new \App\Booking;

        $rules = $customer->rules;
        if ($customer->id) {
            $rules['email'] = $rules['email'] .','. $customer->id . ',id';
        }

        $validator = $customer->validate(Request::all(), $rules);
        if ($validator->passes()) {
            $totalNights = $booking->countTotalNights(
                Request::input('checkIn'), 
                Request::input('checkOut')
            );
            $roomRate = Request::input('roomRate');
            $noOfAdults = Request::input('noOfAdults');
            $noOfChild = Request::input('noOfChild', 0);
            $noOfRooms = Request::input('noOfRooms');
            $totalAmount = $booking->calculateTotaPrice($roomRate, $noOfRooms, $totalNights);
            $booking->refId = time();
            $booking->customerID = Request::input('customerID');
            $booking->checkInTime = $booking::CHECK_IN_TIME;
            $booking->checkOutTime = $booking::CHECK_OUT_TIME;
            $booking->roomID = Request::input('roomID');
            $booking->checkIn = Request::input('checkIn');
            $booking->checkOut = Request::input('checkOut');
            $booking->rateCode = Request::input('rateCode');
            $booking->mealType = Request::input('mealType');
            $booking->roomTypeCode = Request::input('roomTypeCode');
            $booking->companyCode = Request::input('companyCode');
            $booking->noOfRooms = $noOfRooms;
            $booking->noOfNights = $totalNights;
            $booking->noOfAdults = $noOfAdults;
            $booking->noOfChild = $noOfChild;
            $booking->roomRate = $roomRate;
            $booking->totalAmount = $totalAmount;
            $booking->specialInstructions = Request::input('specialInstructions');
            $booking->billingInstructions = Request::input('billingInstructions');
            $booking->status = $booking::BOOKING_PENDING;

           $bookingData = Request::all();
           $bookingData['customerID'] = $customer->id;
           $bookingData['totalAmount'] = $booking->totalAmount;

            $validator = $booking->validate($bookingData);
            if ($validator->passes()) {

                try {
                    $customer->salutation = Request::input('salutation');
                    $customer->firstname = Request::input('firstname');
                    $customer->middleName = Request::input('middleName');
                    $customer->lastname = Request::input('lastname');
                    $customer->email = Request::input('email');
                    $customer->address1 = Request::input('address1');
                    $customer->address2 = Request::input('address2');
                    $customer->city = Request::input('city');
                    $customer->state = Request::input('state');
                    $customer->zipcode = Request::input('zipcode');
                    $customer->countryCode = Request::input('country');
                    $customer->contact = Request::input('contact');
                    if ($customer->save()) {
                        //$customer->bookings()->save($booking);
                        $booking->customerID = $customer->id;
                        if ($booking->save()) {
                            session(['orderRef' => $booking->refId]);
                            return response()->json('success', 200, [], JSON_UNESCAPED_UNICODE);
                        }
                    }
                }catch(\Exception $e) {
                    if (App::environment('local')) {
                        return response()->json($e->getMessage(), 400, [], JSON_UNESCAPED_UNICODE);
                    }
                    return response()->json('Oops!. Something went wrong with your booking. :(', 400, [], JSON_UNESCAPED_UNICODE);
                }

            } else {
                return response()->json($validator->errors()->getMessages(), 400, [], JSON_UNESCAPED_UNICODE);
            }
        } else {
            return response()->json($validator->errors()->getMessages(), 400, [], JSON_UNESCAPED_UNICODE);
        }


        

        
    });
    
    /*
    |--------------------------------------------------------------------------
    | Administrator Routes
    |--------------------------------------------------------------------------
    |
    | Register your routes here that needs user authentication
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */
    Route::get('rooms/available', 'RoomController@showAvailable');
    Route::group(['middleware' => 'auth'], function() {
        Route::resource('rates', 'RateController', ['only' => [
            'index', 'store', 'update', 'destroy'
        ]]);
        
        
        Route::group(['prefix' => 'rooms'], function() {
            Route::get('types', 'RoomController@types');
            

            Route::resource('aminities', 'AminitiesController', ['only' => [
                'index', 'store', 'update', 'destroy'
            ]]);
            Route::post('{roomId}/photos', 'PhotoController@store');
            Route::delete('{roomId}/photos/{id}', 'PhotoController@destroy');
            Route::post('{roomId}/aminities', 'RoomController@attachAminities');
        });
        Route::get('rooms/{slug}', 'RoomController@showBySlug');
        Route::resource('rooms', 'RoomController', ['only' => [
                'index', 'store', 'update', 'destroy'
            ]]);

        /*

        // Save Room
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
                // $from = date('Y-m-d', strtotime("-1 month", strtotime(date('Y-m-d'))));
                // $last = date('Y-m-d', strtotime("+12 months", strtotime($from)));

                // $data = array(
                //     'roomID' => $room->id,
                //     'from' => $from,
                //     'to' => $last,
                //     'roomOnly' => 0,
                //     'singlePrice' => $room->minimumRate,
                //     'doublePrice' => $room->minimumRate,
                //     'roomOnlyPrice' => $room->standardRate,
                //     'minStay' => 1,
                //     'maxStay' => 0,
                //     'availability' => $room->totalRooms,
                //     'isActive' => 1
                // );

                
                // saveCalendar($data);

                
                // $list = getAllRooms();
                return response()->json($list, 200, [], JSON_UNESCAPED_UNICODE);  
            }

            return response()->json('failed', 400);
        });
        // Update Room
        Route::put('/room/{id}', function($id) {
            try {
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

                    \App\Calendar::where('roomID', $room->id)->where('onCreateSetup', 0)->update([
                        'singlePrice' => $room->minimumRate, 
                        'doublePrice' => $room->minimumRate,
                        'roomOnlyPrice' => $room->standardRate,
                        'availability' => $room->totalRooms,
                        'onCreateSetup' => 1,
                    ]);
                    
                    $list = getAllRooms();
                    return response()->json($list, 200, [], JSON_UNESCAPED_UNICODE); 
                }

                
            } catch(\Exception $e) {
                return response()->json($e->getMessage(), 200, [], JSON_UNESCAPED_UNICODE);
            }

            return response()->json('failed', 200, [], JSON_UNESCAPED_UNICODE);
        });

        // Save room photo
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
        // Delete Photo
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

        // Save room aminities
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
        //Save Aminities
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
        


        // Saving Calendar Route
        function saveCalendar($data) {
            try {
                $from = $data['from'];
                $to = $data['to'];
                while(strtotime($from) <= strtotime($to)) {
                    $from = date ("Y-m-d", strtotime("+1 day", strtotime($from)));
                    $calendar = \App\Calendar::firstOrNew(array( 'selectedDate' => $from, 'roomID' => $data['roomID']));
                    $calendar->roomID = $data['roomID'];
                    $calendar->selectedDate = $from;
                    $calendar->roomOnly = $data['roomOnly'];
                    $calendar->singlePrice = $data['singlePrice'];
                    $calendar->doublePrice = $data['doublePrice'];
                    $calendar->minStay = $data['minStay'];
                    $calendar->maxStay = $data['maxStay'];
                    $calendar->availability = $data['availability'];
                    $calendar->isActive = $data['isActive'];
                    $calendar->save();
                }
            } catch (\Exception $e) {
                return response()->json($e->getMessage(), 400, [], JSON_UNESCAPED_UNICODE);
            }
        }
        Route::post('calendar', function() {
            try {
                $data = array(
                    'roomID' => Request::input('roomType.id'),
                    'from' => Request::input('from'),
                    'to' => Request::input('to'),
                    'roomOnly' => Request::input('roomOnly', 0),
                    'singlePrice' => Request::input('singlePrice', 0),
                    'doublePrice' => Request::input('doublePrice', 0),
                    'minStay' => Request::input('minStay', 0),
                    'maxStay' => Request::input('maxStay', 0),
                    'availability' => Request::input('availability', 0),
                    'isActive' => Request::input('isActive', 0)
                );
                saveCalendar($data);
            } catch(\Exception $e) {
                return response()->json($e->getMessage(), 400, [], JSON_UNESCAPED_UNICODE);
            }
            
        });*/
    });
});

Route::any('{all}', function(){
    return view('app');
})->where('all', '.*');
// Auth::routes();

// Route::get('/home', 'HomeController@index');


