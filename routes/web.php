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
        
    Route::group(['middleware' => 'auth'], function() {
        Route::resource('rates', 'RateController', ['only' => [
            'index', 'store', 'update', 'destroy'
        ]]);
        Route::resource('bookings', 'BookingController', ['only' => [
            'index', 'show', 'store', 'update', 'destroy'
        ]]);
        
        Route::group(['prefix' => 'rooms'], function() {
            Route::get('types', 'RoomController@types');
            Route::resource('aminities', 'AminitiesController', ['only' => [
                'index', 'store', 'update', 'destroy'
            ]]);
            Route::get('{roomId}/calendar/{start}/{end}', 'CalendarController@fetchCalendarByRoomIdAndDate');
            Route::post('{roomId}/photos', 'PhotoController@store');
            Route::delete('{roomId}/photos/{id}', 'PhotoController@destroy');
            Route::post('{roomId}/aminities', 'RoomController@attachAminities');
            Route::post('{roomId}/beds', 'RoomController@attachBeds');
            Route::delete('{roomId}/beds/{bedId}', 'RoomController@detachBed');
        });
        Route::resource('rooms', 'RoomController', ['only' => [
                'index', 'store', 'update', 'destroy'
            ]]);
        
        Route::resource('calendar', 'CalendarController', ['only' => [
                'index', 'store', 'update', 'destroy'
            ]]);
    });

    Route::get('rooms/available', 'RoomController@showAvailable');
    Route::get('rooms/{slug}', 'RoomController@showBySlug');
});

Route::any('{all}', function(){
    return view('app');
})->where('all', '.*');
// Auth::routes();

// Route::get('/home', 'HomeController@index');


