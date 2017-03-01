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
                Request::input('checkIn'), 
                Request::input('checkOut')
            );
            $roomRate = Request::input('roomRate');
            $noOfAdults = Request::input('noOfAdults');
            $noOfChild = Request::input('noOfChild', 0);
            $noOfRooms = Request::input('noOfRooms');

            $totalAmount = $roomRate * $noOfRooms * $totalNights;

            $booking->customerID = Request::input('customerID');
            $booking->checkInTime = $booking::CHECK_IN_TIME;
            $booking->checkOutTime = $booking::CHECK_OUT_TIME;
            $booking->roomID = Request::input('roomID');
            $booking->checkIn = Request::input('checkIn');
            $booking->checkOut = Request::input('checkOut');
            $booking->noOfRooms = $noOfRooms;
            $booking->noOfNights = $totalNights;
            $booking->noOfAdults = $noOfAdults;
            $booking->noOfChild = $noOfChild;
            $booking->roomRate = $roomRate;
            $booking->totalAmount = $totalAmount;
            $booking->specialInstructions = Request::input('specialInstructions');
            $booking->billingInstructions = Request::input('billingInstructions');
            $booking->status = $booking::BOOKING_SUCCESS;

            if ($booking->save()) {
                $result = $booking->setupPrimeSoftData($booking);
                return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
            }
        } else {
            return response()->json($validator->errors()->getMessages(), 400, [], JSON_UNESCAPED_UNICODE);
        }
    }


    public function step1(Request $request) {
        $booking = new \App\Booking;
        $validator = $booking->validate($request->input(), $booking->step1Rules);
		if ($validator->passes()) {

            $total = $booking->calculateTotalPrice(
                $request->input('checkIn'), 
                $request->input('checkOut'),
                $request->input('noOfRooms'),
                $request->input('roomId'),
                $request->input('rateId')
            );
            session([
                'roomId' => $request->input('roomId'),
                'rateId' => $request->input('rateId'),
                'checkIn' =>  $request->input('checkIn'),
                'checkOut' => $request->input('checkOut'),
                'noOfRooms' => $request->input('noOfRooms'),
                'adult' => $request->input('adult'),
                'child' => $request->input('child', 0),
                'totalAmount' => $total
            ]);
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
