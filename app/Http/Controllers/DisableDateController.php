<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DisableDateController extends Controller
{

      /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');//->only(['store', 'update', 'destroy']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json($this->getAllDisabledDates(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $date = new \App\DisableDate;
        $validator = $date->validate($request->input(), $date->rules);
        if ($validator->passes()) {
            $selected_date = date('Y-m-d', strtotime($request->input('selected_date')));
            $room_id = $request->input('room');
            $count = \App\DisableDate::where(array('room_id' => $room_id, 'selected_date' => $selected_date))->count();
            if (!$count) {
                $date->room_id = $room_id;
                $date->selected_date = $selected_date;
                $date->created_at = date('Y-m-d H:i:s');
                try {
                    if ($date->save()) {
                        return response()->json($this->getAllDisabledDates(), 200, [], JSON_UNESCAPED_UNICODE);   
                    }
                } catch(\Exception $e) {
                    \Log::info('ERROR: '.$e->getMessage());
                    return response()->json('Oops! Error please report to administrator.', 400, [], JSON_UNESCAPED_UNICODE);
                }
            } else {
                return response()->json('Oops! already exists.', 400, [], JSON_UNESCAPED_UNICODE);
            }

        }
        
        $errorMsg = "";

        foreach($validator->errors()->getMessages() as $k => $msgs) {
            foreach($msgs as $msg) {
                $errorMsg .= $msg;
            }
        }
        return response()->json($errorMsg, 400, [], JSON_UNESCAPED_UNICODE);
    }

   

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $date = \App\DisableDate::findOrFail($id);
        try {
            if ($date->delete()) {
                return response()->json($this->getAllDisabledDates(), 200, [], JSON_UNESCAPED_UNICODE);
            }
        } catch(\Exception $e) {
            \Log::info('ERROR: '.$e->getMessage());
            return response()->json('Oops! Error please report to administrator.', 400, [], JSON_UNESCAPED_UNICODE);
        }
        return response()->json('Failed', 400, [], JSON_UNESCAPED_UNICODE);
    }

    private function getAllDisabledDates() {
        return \App\DisableDate::all()->toArray();
    }

   
}
