<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AminitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(\App\Aminities::all()->toArray(), 200, [], JSON_UNESCAPED_UNICODE);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $aminities = new \App\Aminities;
        $aminities->name = $request->input('name');
        try {
            if ($aminities->save()) {
                return response()->json(\App\Aminities::all()->toArray(), 200, [], JSON_UNESCAPED_UNICODE);
            }
        } catch(\Exception $e) {
            return response()->json($e->getMessage(), 200, [], JSON_UNESCAPED_UNICODE);
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
        $aminities = \App\Aminities::find($id);
        if ($aminities) {
            $aminities->delete();
            return response()->json(\App\Aminities::all()->toArray(), 200, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json('Not found!', 400, [], JSON_UNESCAPED_UNICODE);
        
    }
}
