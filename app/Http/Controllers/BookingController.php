<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\Reservation;
use Illuminate\Support\Facades\Mail;

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
            $booking->checkInTime = $booking->checkInTime;
            $booking->checkOutTime = $booking->checkOutTime;
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
            $booking->status = $booking->bookingStatusSuccess;
            try {
                if ($booking->save()) {
                    $result = $booking->setupPrimeSoftData($booking);
                    return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
                }
            } catch (\Exception $e) {
                \Log::info('ERROR: '.$e->getMessage());
                return response()->json('Oops! Error please report to administrator.', 400, [], JSON_UNESCAPED_UNICODE);
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
        // Check if room is available
        $room = \App\Room::find($request->input('roomId'));
        if (!$room || !$room->isActive || !$room->isAvailable) {
            return response()->json('Selected room is not available', 400, [], JSON_UNESCAPED_UNICODE);
        }

        $disable_dates = \App\DisableDate::where('room_id', '=', $request->input('roomId'))
        ->where('selected_date','>=', $request->input('checkIn'))
        ->where('selected_date','<=', $request->input('checkOut'));
        
        if ($disable_dates->count()) {
            $dates = '';
            foreach($disable_dates->get() as $item) {
                $dates .= date('F j, Y', strtotime($item->selected_date)) . "\n";
            }
            return response()->json('Selected room is not available on selected dates: '. $dates, 400, [], JSON_UNESCAPED_UNICODE);
        }

        
		if ($validator->passes()) {
            // Calculate total rates
            $roomRate = $booking->calculateTotalPrice(
                $request->input('checkIn'), 
                $request->input('checkOut'), 
                $request->input('roomId'), 
                $request->input('rateId')
            );

            $totalAmount = ($roomRate * $request->input('noOfRooms', 1));
            $extraPerson = $request->input('noOfAdults', 1) - $request->input('maxTotalPerson', 1);
            $extraPersonAmount = 0;
            if ($extraPerson > 0) {
                $totalAmount += $extraPerson * 500;
            }

            $step1Data = [
                'roomId' => $request->input('roomId'),
                'rateId' => $request->input('rateId'),
                'checkIn' =>  $request->input('checkIn'),
                'checkOut' => $request->input('checkOut'),
                'noOfRooms' => $request->input('noOfRooms'),
                'noOfAdults' => $request->input('noOfAdults', 1),
                'noOfChild' => $request->input('noOfChild', 0),
                'extraPerson' => $extraPerson,
                'noOfNights' => $totalNights,
                'roomRate' => $roomRate,
                'totalAmount' => $totalAmount
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
        $rules = $booking->step3Rules;
        if (config('pesopay.enable') == false) {
            $rules = array_except($rules, ['paymentMethod']);
        }

        $validator = $booking->validate($request->input(), $rules);
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
                $booking->checkInTime = $booking->bookingCheckInTime;
                $booking->checkOutTime = $booking->bookingCheckOutTime;
                $booking->roomID = $request->session()->get('booking.roomId');
                $booking->checkIn = $request->session()->get('booking.checkIn');
                $booking->checkOut = $request->session()->get('booking.checkOut');
                $booking->rateId = $request->session()->get('booking.rateId');
                $booking->noOfRooms = $request->session()->get('booking.noOfRooms');
                $booking->noOfNights = $request->session()->get('booking.noOfNights');
                $booking->noOfAdults = $request->session()->get('booking.noOfAdults');
                $booking->noOfChild = $request->session()->get('booking.child');
                $booking->extraPerson = $request->session()->get('booking.extraPerson');
                $booking->roomRate = $request->session()->get('booking.roomRate');
                $booking->totalAmount = $request->session()->get('booking.totalAmount');
                $booking->specialInstructions = $request->session()->get('booking.specialInstructions');
                $booking->billingInstructions = $request->session()->get('booking.billingInstructions');
                $booking->status = $booking->bookingStatusPending;

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
                        \Log::info('ERROR: '.$e->getMessage());
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

    public function notify(Request $request) {

        $ref 			= $request->input('Ref');

        $booking = \App\Booking::where(['refId' => $ref])->first();
        if ($booking) {

            $data = array(
                'name' => 'Admin',
                'pageHeading' => 'New Reservation',
                'message' => 'Please see below information',
                'amountPaid' => $booking->lastPayment ? $booking->nf($booking->lastPayment->totalAmount, true) : 0,
                'refId' => $booking->refId,
                'checkIn' => date('l F d Y', strtotime($booking->checkIn)) .' ' . $booking->checkInTime,
                'checkOut' => date('l F d Y', strtotime($booking->checkOut)) .' ' .  $booking->checkOutTime,
                'noOfAdults' => $booking->noOfAdults,
                'extraPerson' => $booking->extraPerson,
                'noOfChild' => $booking->noOfChild ? $booking->noOfChild : 0,
                'roomRate' => $booking->nf($booking->roomRate, true),
                'noOfNights' => $booking->noOfNights,
                'noOfRooms' => $booking->noOfRooms,
                'totalAmount' => $booking->nf($booking->totalAmount, true),
                'status' => $booking->status,
                'lastPayment' => $booking->lastPayment ?  true : false,
                'paymentMethod' => $booking->lastPayment ? $booking->lastPayment->method : 0,
                'amountPaid' => $booking->lastPayment ? $booking->lastPayment->totalAmount : 0,
                'paymentStatus' => $booking->lastPayment ? $booking->lastPayment->status : 0,
                'customerName' => $booking->customer->salutation .' '. $booking->customer->firstName .' '. $booking->customer->middleName .' '. $booking->customer->lastName,
                'customerEmail' => $booking->customer->email,
                'customerContact' => $booking->customer->contact,
                'customerAddress1' => $booking->customer->address1,
                'customerAddress2' => $booking->customer->address2,
                'customerState' => $booking->customer->state,
                'customerCity' => $booking->customer->city,
                'customerZipcode' => $booking->customer->zipcode,
                'customerCountryCode' => $booking->customer->countryCode,
                'specialInstructions' => $booking->specialInstructions,
                'billingInstructions' => $booking->billingInstructions
            );

        
            \Mail::to(Config('mail.emails.info'))
            ->send(new Reservation($data));
        
            $data['name'] = $booking->customer->firstName;
            $data['pageHeading'] = 'You have successfully booked';

            
            \Mail::to($booking->customer->email)
            ->send(new Reservation($data));
        }
    }
}
