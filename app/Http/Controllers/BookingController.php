<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(\App\Booking::lazyLoad()->get()->toArray(), 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        
        $booking = new \App\Booking;
        $validator = $booking->validate(Request::all());
        if ($validator->passes()) {
            $totalNights = $booking->countTotalNights(
                $request->input('checkIn'), 
                $request->input('checkOut')
            );
            $roomRate = $request->input('roomRate');
            $noOfAdults = $request->input('noOfAdults');
            $noOfChild = $request->input('noOfChild', 0);
            $noOfRooms = $request->input('noOfRooms');

            $totalAmount = $roomRate * $noOfRooms * $totalNights;

            $booking->customerID = $request->input('customerID');
            $booking->checkInTime = $booking::CHECK_IN_TIME;
            $booking->checkOutTime = $booking::CHECK_OUT_TIME;
            $booking->roomID = $request->input('roomID');
            $booking->checkIn = $request->input('checkIn');
            $booking->checkOut = $request->input('checkOut');
            $booking->noOfRooms = $noOfRooms;
            $booking->noOfNights = $totalNights;
            $booking->noOfAdults = $noOfAdults;
            $booking->noOfChild = $noOfChild;
            $booking->roomRate = $roomRate;
            $booking->totalAmount = $totalAmount;
            $booking->specialInstructions = $request->input('specialInstructions');
            $booking->billingInstructions = $request->input('billingInstructions');
            $booking->status = $booking::BOOKING_SUCCESS;

            if ($booking->save()) {
                $result = $booking->setupPrimeSoftData($booking);
                return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
            }
        } else {
            return response()->json($validator->errors()->getMessages(), 400, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function step(Request $request, $step) {
        switch($step) {
            case 1:
                return $this->step1($request);
                break;
            case 2: 
                return $this->step2($request);
                break;
            case 3: 
                return $this->step3($request);
                break;
        }

    }

    private function step1(Request $request) {
        $booking = new \App\Booking;
        // Calculate total nights
        $totalNights = $booking->countTotalNights($request->input('checkIn'), $request->input('checkOut'));

        $validator = $booking->validateStep1(
            array_add($request->input(), 'noOfNights', $totalNights), 
            $booking->step1Rules
        );

        
		if ($validator->passes()) {
            // Calculate total rates
            $roomRate = $booking->calculateTotalPrice(
                $request->input('checkIn'), 
                $request->input('checkOut'), 
                $request->input('roomId'), 
                $request->input('rateId')
            );
            $step1Data = [
                'roomId' => $request->input('roomId'),
                'rateId' => $request->input('rateId'),
                'checkIn' =>  $request->input('checkIn'),
                'checkOut' => $request->input('checkOut'),
                'noOfRooms' => $request->input('noOfRooms'),
                'noOfAdults' => $request->input('noOfAdults', 1),
                'noOfChild' => $request->input('noOfChild', 0),
                'noOfNights' => $totalNights,
                'roomRate' => $roomRate,
                'totalAmount' => $roomRate * $request->input('noOfRooms', 1)
            ];
            //Save data to session
            $request->session()->put('booking', $step1Data);
            return response()->json($step1Data, 200, [], JSON_UNESCAPED_UNICODE);
        }

        $validationStr = '';
        foreach ($validator->errors()->getMessages() as $k => $error) {
            foreach ($error as $err) {
                $validationStr .= $err .'<br/>';
            }
            
        }

        return response()->json($validationStr, 400, [], JSON_UNESCAPED_UNICODE);
    }

    private function step2(Request $request) {
        
        $customer = \App\Customer::firstOrNew(['email' => $request->input('email')]);
        
        $rules = $customer->rules;
        if ($customer->id) {
            $rules['email'] = $rules['email'] .','. $customer->id . ',id';
        }

        $validator = $customer->validate($request->input(), $rules);
        if ($validator->passes()) {
            $booking = array_merge($request->session()->get('booking'), [
                'salutation' => $request->input('salutation'),
                'firstname' => $request->input('firstname'),
                'middleName' => $request->input('middleName'),
                'lastname' => $request->input('lastname'),
                'email' => $request->input('email'),
                'address1' => $request->input('address1'),
                'address2' => $request->input('address2'),
                'city' => $request->input('city'),
                'state' => $request->input('state'),
                'zipcode' => $request->input('zipcode'),
                'country' => $request->input('country'),
                'contact' => $request->input('contact'),
                'specialInstructions' => $request->input('specialInstructions'),
                'billingInstructions' => $request->input('billingInstructions'),
            ]);
            $request->session()->put('booking', $booking);
            return response()->json('ok', 200, [], JSON_UNESCAPED_UNICODE);
        }
        $validationStr = '';
        foreach ($validator->errors()->getMessages() as $k => $error) {
            foreach ($error as $err) {
                $validationStr .= $err .'<br/>';
            }
            
        }

        return response()->json($validationStr, 400, [], JSON_UNESCAPED_UNICODE);
    }

    private function step3(Request $request) {
        
        $booking = new \App\Booking;
        $validator = $booking->validate($request->input(), $booking->step3Rules);
        if ($validator->passes()) {
            $customer = \App\Customer::firstOrNew(['email' => $request->session()->get('booking.email')]);
            $rules = $customer->rules;
            if ($customer->id)
                $rules['email'] = $rules['email'] .','. $customer->id . ',id';

            $validator = $customer->validate($request->session()->get('booking'), $rules);
            if ($validator->passes()) {
                $booking->refId = time();
                $roomRate = $request->session()->get('booking.totalRoomRate');
                $noOfAdults = $request->session()->get('booking.adult', 1);
                $noOfChild = $request->session()->get('booking.child', 0);
                $noOfRooms = $request->session()->get('booking.noOfRooms', 0);
                $totalAmount = $request->session()->get('booking.totalAmount', 0);
                $booking->checkInTime = $booking::CHECK_IN_TIME;
                $booking->checkOutTime = $booking::CHECK_OUT_TIME;
                $booking->roomID = $request->session()->get('booking.roomId');
                $booking->checkIn = $request->session()->get('booking.checkIn');
                $booking->checkOut = $request->session()->get('booking.checkOut');
                $booking->rateCode = $request->session()->get('booking.roomId');
                $booking->mealType = $request->session()->get('booking.roomId');
                $booking->roomTypeCode = $request->session()->get('booking.roomId');
                $booking->companyCode = $request->session()->get('booking.roomId');
                $booking->noOfRooms = $request->session()->get('booking.noOfRooms');
                $booking->noOfNights = $request->session()->get('booking.noOfNights');
                $booking->noOfAdults = $request->session()->get('booking.noOfAdults');
                $booking->noOfChild = $request->session()->get('booking.child');
                $booking->roomRate = $request->session()->get('booking.roomRate');
                $booking->totalAmount = $request->session()->get('booking.totalAmount');
                $booking->specialInstructions = $request->session()->get('booking.specialInstructions');
                $booking->billingInstructions = $request->session()->get('booking.billingInstructions');
                $booking->status = $booking::BOOKING_PENDING;

                $input = $request->session()->get('booking');
                $input['customerID'] = $customer->id;
                $input['totalAmount'] = $booking->totalAmount;
                $validator = $booking->validate($input);
                if ($validator->passes()) {

                    try {
                        $customer->salutation = $request->session()->get('booking.salutation');
                        $customer->firstname = $request->session()->get('booking.firstname');
                        $customer->middleName = $request->session()->get('booking.middleName');
                        $customer->lastname = $request->session()->get('booking.lastname');
                        $customer->email = $request->session()->get('booking.email');
                        $customer->address1 = $request->session()->get('booking.address1');
                        $customer->address2 = $request->session()->get('booking.address2');
                        $customer->city = $request->session()->get('booking.city');
                        $customer->state = $request->session()->get('booking.state');
                        $customer->zipcode = $request->session()->get('booking.zipcode');
                        $customer->countryCode = $request->session()->get('booking.country');
                        $customer->contact = $request->session()->get('booking.contact');
                        if ($customer->save()) {
                            if ($booking = $customer->bookings()->save($booking)) {
                                // $request->session()->forget('booking');
                                $request->session()->put('orderRef', $booking->refId);
                                return response()->json('success', 200, [], JSON_UNESCAPED_UNICODE);
                            }
                        }
                    }catch(\Exception $e) {
                        if (\App::environment('local')) {
                            return response()->json($e->getMessage(), 400, [], JSON_UNESCAPED_UNICODE);
                        }
                        return response()->json('Oops!. Something went wrong with your booking. :(', 400, [], JSON_UNESCAPED_UNICODE);
                    }
                }
            }
        }

        $validationStr = '';
        foreach ($validator->errors()->getMessages() as $k => $error) {
            foreach ($error as $err) {
                $validationStr .= $err .'<br/>';
            }
            
        }

        return response()->json($validationStr, 400, [], JSON_UNESCAPED_UNICODE);
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
