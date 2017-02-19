<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoomController extends Controller
{
	
	/**
	* Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
		return response()->json(\App\Room::lazyLoad()->get(), 200);
	}
	
	
	/**
	* Store a newly created resource in storage.
        *
        * @param  \Illuminate\Http\Request  $request
        * @return \Illuminate\Http\Response
        */
    public function store(Request $request)
    {
		$room = new \App\Room;
        $name = 'Studio Standard-' .time();
        $request->merge(array(
            'name' => $name,
            'slug' => $name,
        ));

        $room->name = $request->input('name');
        $room->slug = $request->input('slug');
        $room->roomSize = $request->input('roomSize', 0);
        $room->totalRooms = $request->input('totalRooms', 0);
        $room->minimumRate = $request->input('minimumRate', 0);
        $room->totalPerson = $request->input('totalPerson', 1);
        $room->location = $request->input('location', '');
        $room->bed = $request->input('bed', '');
        $room->extraBed = $request->input('extraBed', 0);
        $room->bathrooms = $request->input('bathrooms', 0);
        $room->building = $request->input('building', 0);
        $room->isActive = $request->input('isActive', 0);
        $room->sort = $request->input('sort', 0);
        
        if ($room->save()) {
            return response()->json(\App\Room::lazyLoad()->get(), 200, [], JSON_UNESCAPED_UNICODE);   
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
        $room = \App\Room::findOrFail($id);
        $room->rules['name'] = $room->rules['name'] . ','. $room->id . ',id';
        $room->rules['slug'] = $room->rules['slug'] . ','. $room->id . ',id';
		$validator = $room->validate($request->input(), $room->rules);
		if ($validator->passes()) {
			$room->name = $request->input('name');
			$room->slug = str_slug($request->input('name'));
			$room->roomSize = $request->input('roomSize', 0);
			$room->totalRooms = $request->input('totalRooms', 0);
			$room->minimumRate = $request->input('minimumRate', 0);
			$room->totalPerson = $request->input('totalPerson', 1);
			$room->location = $request->input('location', '');
			$room->bed = $request->input('bed', '');
			$room->extraBed = $request->input('extraBed', 0);
			$room->bathrooms = $request->input('bathrooms', 0);
			$room->building = $request->input('building', 0);
			$room->isActive = $request->input('isActive', 0);
			$room->sort = $request->input('sort', 0);
			
			if ($room->save()) {
                $roomRates = $request->input('roomRates');
                if ($roomRates) {
                    foreach ($roomRates as $i => $r) {
                        $rateId = array_keys($r)[0];
                        $a[$rateId] = array('price' => $r[$rateId]);
                        $room->rates()->sync($a);
                    }
                }
                
                return response()->json(\App\Room::lazyLoad()->get(), 200, [], JSON_UNESCAPED_UNICODE);   
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
        \App\Room::findOrFail($id)->delete();
        return response()->json(\App\Room::lazyLoad()->get(), 200, [], JSON_UNESCAPED_UNICODE);   
	}

    public function attachAminities(Request $request) {
        $room = \App\Room::find($request->input('roomID'));
        $aminites = json_decode($request->input('aminities'));
        if ($room && count($aminites) != 0) {
            if ($room->aminities()->sync($aminites)) {

                // $aminities = $room->aminities()->get()->toArray();
                // $r = array();
                // foreach($aminities as $i => $a) {
                //     $r[] = $a['id'];
                // }

                $r = $room->aminities()->pluck('id')->all();

                return response()->json($r, 200, [], JSON_UNESCAPED_UNICODE);
            }
        }

        return response()->json('failed', 400);
    }

    public function types(Request $request) {
        $types = \App\Room::select(['*', \DB::raw('CONCAT(name, " ", building, " - ID ", id) AS title')])->get()->toArray();
        return response()->json($types, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function showBySlug(Request $request, $slug) {
         $room = \App\Room::lazyLoad()->where(['slug' => $slug, 'isActive' => 1])->first();
         
         return response()->json($room->toArray(), 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function showAvailable()
    {
		return response()->json(\App\Room::lazyLoad()->where('isActive', 1)->get(), 200);
	}

    
}
