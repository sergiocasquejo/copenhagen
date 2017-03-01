<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RateController extends Controller
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
        return response()->json($this->getAllRates(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rate = new \App\Rate;
        $validator = $rate->validate($request->input(), $rate->rules);
        if ($validator->passes()) {
            $rate->name = $request->input('name');
            $rate->rateCode = $request->input('rateCode');
            $rate->roomCode = $request->input('roomCode');
            $rate->mealType = $request->input('mealType');
            $rate->description = $request->input('description');
            $rate->isMonthly = $request->input('isMonthly', 0);
            $rate->active = $request->input('active', 0);
            if ($rate->save()) {
                return response()->json($this->getAllRates(), 200, [], JSON_UNESCAPED_UNICODE);   
            }

        }

        return response()->json($validator->errors()->getMessages(), 400, [], JSON_UNESCAPED_UNICODE);
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
        $rate = \App\Rate::findOrFail($id);
        $rules = $rate->rules;
        $rules['rateCode'] = $rules['rateCode'] .','. $id .',id';
        $rules['name'] = $rules['name'] .','. $id .',id';
        $validator = $rate->validate($request->input(), $rules);
        if ($validator->passes()) {
            $rate->name = $request->input('name');
            $rate->rateCode = $request->input('rateCode');
            $rate->roomCode = $request->input('roomCode');
            $rate->mealType = $request->input('mealType');
            $rate->description = $request->input('description');
            $rate->isMonthly = $request->input('isMonthly', 0);
            $rate->active = $request->input('active', 0);
            if ($rate->save()) {
                return response()->json($this->getAllRates(), 200, [], JSON_UNESCAPED_UNICODE);   
            }

        }

        return response()->json($validator->errors()->getMessages(), 400, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rate = \App\Rate::findOrFail($id);
        if ($rate->delete()) {
            return response()->json($this->getAllRates(), 200, [], JSON_UNESCAPED_UNICODE);
        }
        return response()->json('Failed', 400, [], JSON_UNESCAPED_UNICODE);
    }

    private function getAllRates() {
        return \App\Rate::all()->toArray();
    }
}
