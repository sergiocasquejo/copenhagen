<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(\App\Calendar::lazyLoad()->get(), 200);
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $calendar = new \App\Calendar;
        
        $validator = $calendar->validate($request->input(), $calendar->rules);
		if ($validator->passes()) {
            $date = $request->input('from');
            $end_date = $request->input('to');
            try {
                while(strtotime($date) <= strtotime($end_date)) {
                    $date = date('Y-m-d', strtotime($date));
                    $calendar = \App\Calendar::firstOrNew(array('selectedDate' => $date, 'roomID' => $request->input('roomID')));
                    $calendar->roomID = $request->input('roomID');
                    $calendar->selectedDate = $date;
                    $calendar->availability = $request->input('availability', 0);
                    $calendar->isActive = $request->input('isActive', 0);
                    if ($calendar->save()) {
                        $rates = $request->input('rates');
                        if ($rates) {
                            foreach($rates as $rateID => $r) {
                                if ($r) {
                                    $a[$rateID] = array('price' => (float)$r['price'], 'active'  => $r['active'] == true );
                                    $calendar->rates()->sync($a);
                                }
                            }
                        }
                    }

                    $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
                }
                return response()->json('success', 200, [], JSON_UNESCAPED_UNICODE);
            }catch(\Exception $e) {
                return response()->json($e->getMessage(), 400, [], JSON_UNESCAPED_UNICODE);
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
    public function showByRoomId($id)
    {
        //
    }

    public function fetchCalendarByRoomIdAndDate($roomID, $start, $end) {
        $calendar =  \App\Calendar::lazyLoad()->where([
            'roomID' => $roomID, 
            array('selectedDate', '>=', date('Y-m-d', strtotime($start))), 
            array('selectedDate', '<=', date('Y-m-d', strtotime($end)))
            ])->orderBy('selectedDate')->get();
        return response()->json($calendar, 200, [], JSON_UNESCAPED_UNICODE);
        
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

    public function notAvailableDateByRoomId($roomId) {
        $calendar = \App\Calendar::lazyload()->where([
            'roomID' => $roomId
        ])->where(function($q){
            $q->where('availability', 0)
                ->orWhere('isActive', 0);
        })->get();

        return response()->json($calendar, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function availability(Request $request) {
        $noOfRooms = $request->input('noOfRooms', 0);
        $hasNoRoomAvailable = \App\Calendar::where([
            'roomID' => $request->input('roomID'), 
            ['selectedDate', '>=', date('Y-m-d', strtotime($request->input('checkIn')))],
            ['selectedDate', '<=', date('Y-m-d', strtotime($request->input('checkOut')))]
        ])->where(function($q) use($noOfRooms) {
            $q->where('availability', '<', $noOfRooms)
                ->orWhere('isActive', 0);
        })->get();

        if ($hasNoRoomAvailable->count() == 0) {
            return response()->json('available', 200, [], JSON_UNESCAPED_UNICODE);
        }

        $str = '<table class="table table-striped">'. '<tr><td>Date</td><td>Quantity</td><td>Status</td></tr>';
        foreach ($hasNoRoomAvailable as $d) {
            $str .= '<tr><td>'. date('D F d Y', strtotime($d->selectedDate)) . '</td><td>' . $d->availability . '</td><td>' . (!$d->isActive ? 'not' : '' ) . ' available' . '</td></tr>';
        }
        $str .= '</table>';
        return response()->json('<p>Selected date has no enough room available.</p>' . $str, 400, [], JSON_UNESCAPED_UNICODE);
    }
}
