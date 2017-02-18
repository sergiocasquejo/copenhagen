<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $customer = new \App\Customer;
        $validator = $customer->validate(Request::all(), $customer->rules);
        if ($validator->passes()) {
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
                return response()->json($customer, 200, [], JSON_UNESCAPED_UNICODE);
            }
        } else {
            return response()->json($validator->errors()->getMessages(), 400, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = \App\Customer::findOrFail($id);
        if ($customer) {
            $rules = $customer->rules;
            $rules['email'] = $rules['email'] . ','. $customer->id . ',id';

            $validator = $customer->validate(Request::all(), $rules);
            if ($validator->passes()) {
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
                    return response()->json($customer, 200, [], JSON_UNESCAPED_UNICODE);
                }
            } else {
                return response()->json($validator->errors()->getMessages(), 400, [], JSON_UNESCAPED_UNICODE);
            }
        }
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
