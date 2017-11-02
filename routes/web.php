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

Route::get('/test', function ($any = null) {
    $booking = \App\Booking::find(2);
    return view('emails.booking')
        ->with('booking', $booking)
        ->with('name', $booking->customer->firstName)
        ->with('pageHeading', 'You have successfully booked!')
        ->with('message', 'You have received this email because you have successfully booked with us.. please see below the information of your booking.');
});

Route::get('/contact', function ($any = null) {
    return view('emails.contact')->with([
            'firstname' => Request::input('firstname'),
            'lastname' => Request::input('lastname'),
            'email' => Request::input('email'),
            'phone' => Request::input('phone'),
            'message' => Request::input('message'),
        ]);
});



Route::put('profile/{id}', 'UserController@profile');

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



Route::get('logout', function() {
    Auth::logout();
    return response()->json('logout', 200);
});

Route::post('login', function() {
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

Route::post('contact', 'PageController@contact');

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

Route::group(['prefix' => 'booking'], function() {
    Route::post('step/{step}', 'BookingController@step')->where('id', '[0-9]+');
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

Route::group(['prefix' => 'rooms'], function() {
    Route::get('{roomId}/calendar/unavailable/{start}/{end}', 'CalendarController@notAvailableDateByRoomId');
    Route::post('availability', 'CalendarController@availability');
});

    
Route::group(['middleware' => 'auth'], function() {
    Route::get('seo/{seoableType}/{seoableId}', 'SeoController@meta');
    Route::put('seo/{id}', 'SeoController@update');

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

    Route::get('rooms/lists', 'RoomController@lists');
    Route::resource('rooms', 'RoomController', ['only' => [
            'index', 'store', 'update', 'destroy'
        ]]);
    
    Route::resource('calendar', 'CalendarController', ['only' => [
            'index', 'store', 'update', 'destroy'
        ]]);

    Route::resource('disable-date', 'DisableDateController', ['only' => [
            'index', 'store', 'update', 'destroy'
        ]]);


    
});

Route::get('rooms/available', 'RoomController@showAvailable');
Route::get('rooms/{slug}', 'RoomController@showBySlug');


Route::any('{all}', function(){
return view('app');
})->where('all', '.*');
// Auth::routes();

// Route::get('/home', 'HomeController@index');